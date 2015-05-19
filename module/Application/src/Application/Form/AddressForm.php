<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class AddressForm extends Form
{
    public function __construct($name = 'address')
    {
        parent::__construct($name);

        $input = new InputFilter();

        $this->add([
            'name' => 'name',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Mom'
            ],
            'options' => [
                'label' => 'Name',
            ]
        ]);

        $input->add([
            'name' => 'name',
            'required' => true,
            'validators' => [
                ['name' => 'not_empty']
            ]
        ]);

        $this->add([
            'name' => 'street',
            'type' => 'text',
            'attributes' => [
                'placeholder' => '123 Anystreet'
            ],
            'options' => [
                'label' => 'Street',
            ]
        ]);

        $input->add([
            'name' => 'street',
            'required' => true,
            'validators' => [
                ['name' => 'not_empty']
            ]
        ]);

        $this->add([
            'name' => 'city',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'New York'
            ],
            'options' => [
                'label' => 'City',
            ]
        ]);

        $input->add([
            'name' => 'city',
            'required' => true,
            'validators' => [
                ['name' => 'not_empty']
            ]
        ]);
        
        $this->add([
            'name' => 'state',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'New York'
            ],
            'options' => [
                'label' => 'State',
            ]
        ]);

        $input->add([
            'name' => 'state',
            'required' => true,
            'validators' => [
                ['name' => 'not_empty']
            ]
        ]);

        $this->add([
            'name' => 'postal',
            'type' => 'text',
            'attributes' => [
                'placeholder' => '12345'
            ],
            'options' => [
                'label' => 'Postal Code',
            ]
        ]);

        $input->add([
            'name' => 'postal',
            'required' => true,
            'validators' => [
                ['name' => 'not_empty']
            ]
        ]);        
        
        $this->add([
            'name' => 'phone',
            'type' => 'text',
            'attributes' => [
                'placeholder' => '1-555-111-2323'
            ],
            'options' => [
                'label' => 'Phone',
                'type' => 'tel',
            ]
        ]);

        $input->add([
            'name' => 'phone',
            'required' => true,
            'validators' => [
                ['name' => 'not_empty']
            ]
        ]);

       $this->setInputFilter($input);
    }
}