<?php
namespace Application\Service\Sincerely;

use Application\Service\HasHttpClient;

class Client
{
    const API_BASE = 'https://snapi.sincerely.com/shiplib';
    const SEND_PATH = '/create';

    use HasHttpClient;

    /**
     * The API Key
     *
     * @var string
     */
    protected $key;

    protected $base;

    protected $message;
    protected $url;
    protected $sender;
    protected $recipients;

    protected $requiredAddress = ['name', 'company', 'street1', 'street2', 'city', 'state', 'postalcode', 'country', 'id'];

    public function __construct($key, $base = self::API_BASE)
    {
        $this->key = $key;
        $this->base = $base;
        $this->clear();
    }

    public function clear()
    {
        $this->message = null;
        $this->url = null;
        $this->sender = null;
        $this->recipients = null;
    }

    public function setSender($address, $email)
    {
        $this->sender = $this->makeAddress($address);
        $this->sender['email'] = $email;
        return $this;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function addRecipient($address)
    {
        $this->recipients[] = $this->makeAddress($address);
        return $this;
    }

    public function send(array $data = array(), $path = self::SEND_PATH)
    {
        $defaults = [
            'appkey' => $this->key,
            'message' => $this->message,
            'frontPhotoUrl' => $this->url,
            'sender' => $this->sender,
            'recipients' => $this->recipients,
            'testMode' => true,
        ];

        $data = array_merge($defaults, $data);

        if(is_array($data['sender'])){
            $data['sender'] = json_encode($data['sender']);
        }

        if(is_array($data['recipients'])){
            $data['recipients'] = json_encode($data['recipients']);
        }

        $request = $this->getHttpClient()->getRequest();
        $request->setMethod($request::METHOD_POST);
        $request->setUri($this->base . $path);
        $request->getPost()->fromArray($data);

        $response = $this->getHttpClient()->send($request);

        if(!$response->isSuccess()){
            throw new \RuntimeException('bad response from ship API: ' . $response->getBody());
        }

        $data = json_decode($response->getBody(), true);

        if(!$data){
            throw new \RuntimeException('could not parse ship API response: ' . $response->getBody());
        }

        if(!isset($data['success']) OR !$data['success']){
            throw new \RuntimeException('ship API error: ' . $response->getBody());
        }

        return $data;
    }

    protected function makeAddress($address)
    {
        $address = (array) $address;

        if(isset($address['street'])){
            $address['street1'] = $address['street'];
        }

        if(isset($address['postal'])){
            $address['postalcode'] = $address['postal'];
        }

        $defaults = [
            'name' => '',
            'company' => '',
            'street2' => '',
            'country' => 'United States',
            'id' => ''
        ];

        $address = array_merge($defaults, $address);
        $address = array_intersect_key($address, array_flip($this->requiredAddress));

        $missing = array_diff_key(array_flip($this->requiredAddress), $address);
        if(count($missing)){
            throw new \InvalidArgumentException('missing required address fields: ' . implode(', ', $missing));
        }

        return $address;
    }

}