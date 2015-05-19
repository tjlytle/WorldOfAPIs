<?php
namespace Application\Controller;

use Application\Service\Storage\StorageInterface;
use Nexmo\Verify;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Allow someone to login with their phone number (if they've been sent a gitgram) and view it.
 * @package Application\Controller
 */
class ClaimController extends AbstractActionController
{
    /**
     * @var Verify
     */
    protected $verify;

    /**
     * @var StorageInterface
     */
    protected $storageService;

    /**
     * Need Nexmo 's Verify API and storage for this.
     *
     * @param StorageInterface $storageService
     * @param Verify $verify
     */
    public function __construct(StorageInterface $storageService, Verify $verify)
    {
        $this->storageService = $storageService;
        $this->verify = $verify;
    }

    /**
     * Let a user login with just a phone number, by sending a verification code.
     *
     * @return void|\Zend\Http\Response|ViewModel
     */
    public function verifyAction()
    {
        //Just show the form if there's no data
        if(!$this->getRequest()->isPost()){
            return;
        }

        //If a request ID is sent, assume this completes a verification with the code
        if($id = $this->getRequest()->getPost('request')){
            $response = $this->verify->check([
                'request_id' => $id,
                'code' => $this->getRequest()->getPost('pin')
            ]);

            if($response['status'] !== '0'){
                return new ViewModel([
                    'request' => $id,
                    'number' => $this->getRequest()->getPost('number'),
                    'error' => 'That code does not match.'
                ]);
            }

            //Find the address by the number
            $address = $this->storageService->getAddressByPhone($this->getRequest()->getPost('number'));

            if(!$address){
                return new ViewModel([
                    'error' => 'No Gitgrams found for that number.'
                ]);
            }

            //Redirect, using the address ID
            return $this->redirect()->toRoute('claim/result', ['id' => $address->getKey()]);
        }

        //If there's no request ID, we should send create one
        $response = $this->verify->verify([
            'number' => $this->getRequest()->getPost('number'),
            'brand' => 'Gitgram'
        ]);

        //Based on the API response, add data to the view model.
        switch($response['status']){
            case 0:
                return new ViewModel([
                    'request' => $response['request_id'],
                    'number'   => $this->getRequest()->getPost('number')
                ]);
            case 15:
                $error = 'Sorry, that number can not be used as a login.';
                break;
            case 10:
                $error = 'Please wait a few minutes and try again.';
                break;
            default:
                $error = 'Sorry, something went wrong. err: ' . $response['status'];
        }

        return new ViewModel([
            'error' => $error,
        ]);
    }

    /**
     * Lookup all the cards an address was sent.
     *
     * Break avoiding any understanding of Orchestra, use the collection for paging.
     *
     * @return void|ViewModel
     */
    public function claimAction()
    {
        //Setup some params and defaults
        $id = $this->params('id');
        $page = $this->getRequest()->getQuery('page', 1);

        //Lookup the address by ID, and get the shipments sent to it
        $address = $this->storageService->getAddress($id);
        $shipments = $address->relations('shipment');

        //Just render if there's no shipments
        if(!$shipments->get(1, $page -1)){
            return;
        }

        //Check for paging
        return new ViewModel([
            'shipment' => $shipments[0],
            'next' => $shipments->getNextUrl() ? $page + 1 : false,
            'prev' => $shipments->getPrevUrl() ? $page - 1 : false,

        ]);
    }
}