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

        //Based on the API response, add data to the view model.

        $error = 'Not implemented';

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

        //Just render if there's no shipments

        //Check for paging
    }
}