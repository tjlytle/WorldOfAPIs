<?php
namespace Application\Controller;

use Application\Form\AddressForm;
use Application\Form\CreateForm;
use Application\Service\Github\ExportService;
use Application\Service\Storage\StorageInterface;
use CloudConvert\Api as CloudConvert;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Core of the app, manage addresses, create orders, etc.
 *
 * @package Application\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var StorageInterface
     */
    protected $storageService;

    /**
     * @var CloudConvert
     */
    protected $cloudconvert;

    /**
     * @var AddressForm
     */
    protected $addressForm;

    /**
     * @var CreateForm
     */
    protected $createForm;

    /**
     * The authenticated user's email address.
     * @var string
     */
    protected $email;

    /**
     * Ensure there's an identity, get the user's addresses, do some common setup on the form.
     *
     * @param AuthenticationService $authenticationService
     * @param StorageInterface $storageService
     * @param CloudConvert $cloudconvert
     */
    public function __construct(AuthenticationService $authenticationService, StorageInterface $storageService, CloudConvert $cloudconvert)
    {
        $this->authenticationService = $authenticationService;
        $this->cloudconvert = $cloudconvert;

        if(!$this->authenticationService->hasIdentity()){
            throw new \RuntimeException('user not logged in');
        }

        $this->email = $this->authenticationService->getIdentity();

        $this->storageService = $storageService;

        $this->addressForm = new AddressForm();
        $this->createForm = new CreateForm();

        $addresses = $this->storageService->getAddresses($this->email);

        $values = [];
        foreach($addresses as $address){
            $values[$address->getKey()] = $address->name . ': '  .$address->street;
        }

        $this->createForm->get('address')->setValueOptions($values);
    }

    /**
     * Setup the routes for the forms, get the last shipment (if there is one).
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->addressForm->setAttribute('action', $this->url()->fromRoute('address'));
        $this->createForm->setAttribute('action', $this->url()->fromRoute('preview'));

        $view = new ViewModel([
            'addressForm' => $this->addressForm,
            'createForm'  => $this->createForm,
            'shipment'   => $this->storageService->getLastShipment($this->email)
        ]);

        $view->setTemplate('application/index/index');

        return $view;
    }

    /**
     * Validate the data, create a new address.
     * @return \Zend\Http\Response|ViewModel
     */
    public function addressAction()
    {
        $request = $this->getRequest();
        if(!$request->isPost()){
            return $this->indexAction();
        }

        $this->addressForm->setData($request->getPost());

        //The index will render the form (with errors)
        if(!$this->addressForm->isValid()){
            return $this->indexAction();
        }

        $this->storageService->addAddress(
            $this->email,
            $this->addressForm->get('name')->getValue(),
            $this->addressForm->get('street')->getValue(),
            $this->addressForm->get('city')->getValue(),
            $this->addressForm->get('state')->getValue(),
            $this->addressForm->get('postal')->getValue(),
            $this->addressForm->get('phone')->getValue()
        );

        //On success redirect to home
        return $this->redirect()->toRoute('home');
    }

    /**
     * Get the code, show it to the user, let them confirm they want to send it.
     *
     * @return ViewModel
     */
    public function previewAction()
    {
        //Only work with POST
        $request = $this->getRequest();
        if(!$request->isPost()){
            return $this->indexAction();
        }

        //Validate the data
        $this->createForm->setData($request->getPost());

        //Index action can render the errors
        if(!$this->createForm->isValid()){
            return $this->indexAction();
        }

        //Fetch code from GitHub
        $github = new ExportService();
        $snippet = $github->getSnippet($this->createForm->get('link')->getValue());

        //Get the addresses the user selected
        $addresses = [];
        foreach($this->createForm->get('address')->getValue() as $id){
            $addresses[$id] = $this->storageService->getAddress($id);
        }

        //Hijack the create form, and send it someplace else
        $this->createForm->setAttribute('action', $this->url()->fromRoute('send'));

        return new ViewModel([
            'snippet'    => $snippet,
            'addresses'  => $addresses,
            'url'        => $this->createForm->get('link')->getValue(),
            'to'         => $this->createForm->get('address')->getValue(),
            'createForm' => $this->createForm
        ]);
    }

    /**
     * Create the order, and start the rendering job.
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function sendAction()
    {
        //Only work with POST
        $request = $this->getRequest();
        if(!$request->isPost()){
            return $this->indexAction();
        }

        //Validate the data
        $this->createForm->setData($request->getPost());

        //Index can render any issues (shouldn't be any, as data is passed on).
        if(!$this->createForm->isValid()){
            return $this->indexAction();
        }

        //Fetch the code from GitHub
        $github = new ExportService();
        $url = $this->createForm->get('link')->getValue();
        $snippet = $github->getSnippet($url);

        //Create the new order.
        $id = $this->storageService->addOrder($this->email, $url, $snippet, $this->createForm->get('address')->getValue());

        //Get a URL for the render action (shows the code).

        //Get a URL for the callback when the render is complete.

        //Get an image of the code.

        //Redirect to home.
    }
}
