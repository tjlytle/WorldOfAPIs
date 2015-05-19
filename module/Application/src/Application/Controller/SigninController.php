<?php
namespace Application\Controller;

use Application\Form\SigninForm;
use Application\Form\SignupForm;
use Application\Service\Stormpath\AuthenticationAdapter;
use Stormpath\Client;
use Stormpath\Resource\Account;
use Stormpath\Resource\Application;
use Stormpath\Resource\ResourceError;
use Stormpath\Stormpath;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Handle signup and signin.
 *
 * @package Application\Controller
 */
class SigninController extends AbstractActionController
{
    /**
     * The Stormpath API Client
     * @var Client
     */
    protected $client;

    /**
     * The specific Stormpath Applications
     * @var Application
     */
    protected $application;

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    public function __construct(Client $client, Application $application, AuthenticationService $authenticationService)
    {
        $this->client      = $client;
        $this->application = $application;
        $this->authenticationService = $authenticationService;
    }

    /**
     * Create a new user.
     * @return \Zend\Http\Response|ViewModel
     */
    public function signupAction()
    {
        $form = new SignupForm();
        $request = $this->getRequest();

        //If it's not a post, show the form.
        if(!$request->isPost() OR !$form->setData($request->getPost())->isValid()){
            return new ViewModel([
                'form' => $form
            ]);
        }

        //Create a new Stormpath account
        $account = $this->client->dataStore->instantiate(\Stormpath\Stormpath::ACCOUNT);

        //Add the details
        $account->givenName = $form->get('first')->getValue();
        $account->surname   = $form->get('last')->getValue();
        $account->email     = $form->get('email')->getValue();
        $account->password  = $form->get('password')->getValue();

        //Some Custom Data
        $account->customData->phone = $form->get('phone')->getValue();

        //Persist the account
        try{
            $result = $this->application->createAccount($account);
        } catch (ResourceError $e) {
            //Message is the validation issue
            return new ViewModel([
                'form' => $form,
                'error' => $e->getMessage()
            ]);
        }

        //New account created, let them signin
        return $this->redirect()->toRoute('signin');
    }

    /**
     * Auth a user (leverage Zend's auth service)
     * @return \Zend\Http\Response|ViewModel
     */
    public function signinAction()
    {
        $form = new SigninForm();
        $request = $this->getRequest();

        //No post, just show the form
        if(!$request->isPost() OR !$form->setData($request->getPost())->isValid()){
            return new ViewModel([
                'form' => $form
            ]);
        }

        //Stormpath auth adapter, just needs a username and password
        $adapter = new AuthenticationAdapter($this->application);
        $adapter->setUser($request->getPost('email'))
                ->setPassword($request->getPost('password'));

        $result = $this->authenticationService->authenticate($adapter);

        //Populate the form with the error
        if(!$result->isValid()){
            return new ViewModel([
                'error' => implode("\n", $result->getMessages()),
                'form' => $form
            ]);
        }

        //Auth service will keep identity in storage, so redirect to the home page
        return $this->redirect()->toRoute('home');
    }
}
