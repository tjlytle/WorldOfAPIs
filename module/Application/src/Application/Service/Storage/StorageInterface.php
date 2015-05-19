<?php
namespace Application\Service\Storage;

use Application\Service\Github\Snippet;

/**
 * Defines the interactions with the data layer.
 * @package Application\Service\Storage
 */
interface StorageInterface
{
    /**
     * Get an address (to send a card to) by ID.
     *
     * @param $id
     * @return mixed
     */
    public function getAddress($id);


    /**
     * Get all the addresses that belong to a user.
     *
     * @param $email
     * @return mixed
     */
    public function getAddresses($email);

    /**
     * Add an address to a user's account.
     *
     * @param $email
     * @param $name
     * @param $street
     * @param $city
     * @param $state
     * @param $postal
     * @param $phone
     * @return mixed
     */
    public function addAddress($email, $name, $street, $city, $state, $postal, $phone);

    /**
     * Add an order to a user's account.
     *
     * @param $email
     * @param $url
     * @param Snippet $snippet
     * @param array $addresses
     * @return mixed
     */
    public function addOrder($email, $url, Snippet $snippet, array $addresses);

    /**
     * Get an order (card, sent to an address) by ID.
     *
     * @param $id
     * @return mixed
     */
    public function getOrder($id);

    /**
     * Get the user that created an order.
     *
     * @param $order
     * @return mixed
     */
    public function getOrderUser($order);

    /**
     * Get the addresses an order is being sent to.
     *
     * @param $order
     * @return mixed
     */
    public function getOrderAddresses($order);

    /**
     * Add a shipment to and order (includes preview images).
     *
     * @param $order
     * @param $address
     * @param $shipId
     * @param $front
     * @param $back
     * @return mixed
     */
    public function addShipment($order, $address, $shipId, $front, $back);

    /**
     * Get the last shipment for a user's account.
     *
     * @param $email
     * @return mixed
     */
    public function getLastShipment($email);

    /**
     * Get an address by phone number.
     *
     * @param $phone
     * @return mixed
     */
    public function getAddressByPhone($phone);

    /**
     * Get a user's account, using their email address (ID).
     *
     * @param $email
     * @return mixed
     */
    public function getUser($email);
}