<?php
namespace Application\Service\Orchestrate;

use andrefelipe\Orchestrate\Application;
use andrefelipe\Orchestrate\Objects\KeyValueInterface;
use Application\Service\Github\Snippet;
use Application\Service\Storage\StorageInterface;

class StorageService implements StorageInterface
{
    const COLLECTION_USER       = 'users';
    const COLLECTION_ADDRESS    = 'addresses';
    const COLLECTION_ORDER      = 'orders';
    const COLLECTION_SHIPMENT   = 'shipments';

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var \andrefelipe\Orchestrate\Objects\Collection
     */
    protected $userCollection;

    /**
     * @var \andrefelipe\Orchestrate\Objects\Collection
     */
    protected $addressCollection;

    /**
     * @var \andrefelipe\Orchestrate\Objects\Collection
     */
    protected $orderCollection;

    /**
     * @var \andrefelipe\Orchestrate\Objects\Collection
     */
    protected $shipmentCollection;

    /**
     * Create a new Orchsetrate backed storage service.
     *
     * Inits the needed collections (which are lazy loaded / created)
     *
     * @param Application $application
     * @param array $collections
     */
    public function __construct(Application $application, $collections = [])
    {
        $collections = array_merge([
            'user'      => self::COLLECTION_USER,
            'address'   => self::COLLECTION_ADDRESS,
            'order'     => self::COLLECTION_ORDER,
            'shipment'  => self::COLLECTION_SHIPMENT
        ], $collections);

        $this->application = $application;

        $this->userCollection       = $application->collection($collections['user']);
        $this->addressCollection    = $application->collection($collections['address']);
        $this->orderCollection      = $application->collection($collections['order']);
        $this->shipmentCollection   = $application->collection($collections['shipment']);
    }

    /**
     * Simple lookup by ID, lazy create users that don't exist.
     *
     * @param $email
     * @return \andrefelipe\Orchestrate\Objects\KeyValueInterface
     */
    public function getUser($email)
    {
        $user = $this->userCollection->item($email);

        if(!$user->get()){
            $user->email = $email;
            $user->created = new \DateTime();

            if(!$user->put()){
                throw new \RuntimeException('could not persist new user');
            }

            $user->event('activity')->post(['type' => 'created']);
        }

        return $user;
    }

    /**
     * Lookup using relations.
     *
     * @param $email
     * @return \andrefelipe\Orchestrate\Objects\Relations
     */
    public function getAddresses($email)
    {
        $user = $this->getUser($email);

        $addresses = $user->relations('address');

        if(!$addresses->get()){
            throw new \RuntimeException('could not fetch addresses');
        }

        return $addresses;
    }

    /**
     * Create a new address, and relate it to the user.
     *
     * @param $email
     * @param $name
     * @param $street
     * @param $city
     * @param $state
     * @param $postal
     * @param $phone
     * @return string
     */
    public function addAddress($email, $name, $street, $city, $state, $postal, $phone)
    {
        $user = $this->getUser($email);

        $address = $this->addressCollection->item();
        $address->name      = $name;
        $address->street    = $street;
        $address->city      = $city;
        $address->state     = $state;
        $address->postal    = $postal;
        $address->phone     = $phone;

        if(!$address->post()){
            throw new \RuntimeException('could not persist address');
        }

        if(!$user->relation('address', $address)->put()){
            throw new \RuntimeException('could not link address to user');
        }

        $user->event('activity')->post(['type' => 'new address']);

        return $address->getKey();
    }

    /**
     * Simple lookup by ID.
     *
     * @param $id
     * @return KeyValueInterface
     */
    public function getAddress($id)
    {
        $item = $this->addressCollection->item($id);
        if(!$item->get()){
            throw new \RuntimeException('could not fetch address');
        }

        return $item;
    }

    /**
     * Create a new order, relate it to the email, and the addresses.
     *
     * @param $email
     * @param $url
     * @param Snippet $snippet
     * @param array $addresses
     * @return string
     */
    public function addOrder($email, $url, Snippet $snippet, array $addresses)
    {
    }

    /**
     * Simple lookup by ID.
     *
     * @param $id
     * @return KeyValueInterface
     */
    public function getOrder($id)
    {
    }

    /**
     * Lookup using relations.
     *
     * @param $order
     * @return null
     */
    public function getOrderUser($order)
    {
    }

    /**
     * Lookup using relations.
     *
     * @param $order
     * @return \andrefelipe\Orchestrate\Objects\Relations
     */
    public function getOrderAddresses($order)
    {
    }

    /**
     * Create a shipment, and relate it to the order and addresses.
     *
     * @param $order
     * @param $address
     * @param $shipId
     * @param $front
     * @param $back
     * @return KeyValueInterface
     */
    public function addShipment($order, $address, $shipId, $front, $back)
    {
    }

    /**
     * Get shipments by relations, then iterate through them all to find the 'last'.
     *
     * Could be refactored to use events, and reference the shipment ID.
     *
     * @param $email
     * @return null
     */
    public function getLastShipment($email)
    {
    }

    /**
     * Lookup by search.
     *
     * @param $phone
     * @return null
     */
    public function getAddressByPhone($phone)
    {
    }
}