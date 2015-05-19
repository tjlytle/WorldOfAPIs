<?php
namespace Application\Controller;

use Application\Service\Github\ExportService;
use Application\Service\Storage\StorageInterface;
use CloudConvert\Api as CloudConvert;
use CloudConvert\Process;
use Lob\Lob;
use Nexmo\Sms;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Provide HTML to render into an image, finish processing the order once the image is rendered.
 *
 * @package Application\Controller
 */
class RenderController extends AbstractActionController
{
    /**
     * @var CloudConvert
     */
    protected $cloudconvert;

    /**
     * @var Lob
     */
    protected $lob;

    /**
     * @var StorageInterface
     */
    protected $storageService;

    /**
     * @var Sms
     */
    protected $sms;

    /**
     * Setup all the services, CloudConvert for image generation, Lob to send the cards, Storage to store stuff, and
     * Nexmo to send some MS alerts.
     *
     * @param CloudConvert $cloudconvert
     * @param Lob $lob
     * @param StorageInterface $storageService
     * @param Sms $sms
     */
    public function __construct(CloudConvert $cloudconvert, Lob $lob, StorageInterface $storageService, Sms $sms)
    {
        $this->cloudconvert = $cloudconvert;
        $this->lob = $lob;
        $this->storageService = $storageService;
        $this->sms = $sms;
    }

    /**
     * Take some code, show it on a page. Expects someone to turn this into an image.
     *
     * @return ViewModel
     */
    public function renderAction()
    {
        //Once again fetch the code from GitHub (this could be cached)
        $github = new ExportService();
        $url = $this->getRequest()->getQuery('url');
        $snippet = $github->getSnippet($url);

        $view = new ViewModel([
            'snippet' => $snippet
        ]);

        //Simple view script (syntax highlighting would be a nice addition)
        $view->setTemplate('application/render/render')->setTerminal(true);
        return $view;
    }

    /**
     * Complete a render, expect the ID of the order to be in the request. Send the image to LOB, and store some
     * relevant information.
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function resultAction()
    {
        //Get the render data, including the download URL
        $process = new Process($this->cloudconvert, $this->getRequest()->getQuery('url'));
        $process->refresh();
        $output = $process->output;
        $image = 'https:' . $output->url . '/' . $output->filename;

        //Grab the order, and the user.
        $order = $this->storageService->getOrder($this->params('id'));
        $user = $this->storageService->getOrderUser($order);

        //Create personalized LOB order data
        $print = [
            'description' => $order->getKey(),
            'front' => $image,
            'message' => $user->email . " sent you this from Github. Isn't it wonderful?",
            'from' => [
                'name' => 'Gitgram',
                'address_line1' => 'PO Box 23',
                'address_city' => 'Hamburg',
                'address_state' => 'PA',
                'address_zip' => '19526',
                'address_country' => 'US',
            ]
        ];

        //Add the address, and send to each one
        foreach($this->storageService->getOrderAddresses($order) as $address){
            $print['to'] = [
                'name' => $address->name,
                'address_line1' => $address->street,
                'address_city' => $address->city,
                'address_state' => $address->state,
                'address_zip' => $address->postal,
                'address_country' => 'US',
            ];

            $data = $this->lob->postcards()->create($print);
            $this->storageService->addShipment($order, $address, $data['id'], $data['thumbnails'][0]['medium'], $data['thumbnails'][1]['medium']);

            //Send an SMS, so they know something is coming
            $result = $this->sms->send([
                'from' => '12404284163',
                'to' => $address->phone,
                'text' => "You were just sent a Gitgram. It's in the mail, but you can view it online: " . $this->getRequest()->getUri()->getHost()
            ]);

            $address->event('sms')->post($result);
        }

        //Just give back a 200, so CloudConvert doesn't deliver again.
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent('');

        return $response;
   }
}