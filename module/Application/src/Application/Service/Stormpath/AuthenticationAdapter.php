<?php
namespace Application\Service\Stormpath;

use Stormpath\Resource\Application;
use Stormpath\Resource\ResourceError;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class AuthenticationAdapter implements AdapterInterface
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * User name for auth
     * @var string
     */
    protected $user;

    /**
     * Password for auth
     * @var string
     */
    protected $password;

    public function __construct(Application $application, $user = null, $password = null)
    {
        $this->application = $application;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @param null $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param null $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        try{
            //Stormpath result will contain the account on success
            $result = $this->application->authenticate($this->user, $this->password);
            $result = new Result(Result::SUCCESS, $result->account->username);
        } catch (ResourceError $e) {
            //Message will contain the login error
            $result = new Result(Result::FAILURE, $this->user, [$e->getMessage()]);
        }

        return $result;
    }
}