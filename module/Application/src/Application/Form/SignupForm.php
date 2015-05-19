<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class SignupForm extends Form
{
    public function __construct($name = 'signup')
    {
        parent::__construct($name);

        $input = new InputFilter();

        $this->add([
            'name' => 'first',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Bob'
            ],
            'options' => [
                'label' => 'First',
            ]
        ]);

        $input->add([
            'name' => 'first',
            'required' => true,
            'validators' => [
                ['name' => 'not_empty']
            ]
        ]);

        $this->add([
            'name' => 'last',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Smith'
            ],
            'options' => [
                'label' => 'Last',
            ]
        ]);

        $input->add([
            'name' => 'last',
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

        $this->add([
            'name' => 'email',
            'type' => 'email',
            'attributes' => [
                'placeholder' => 'user@example.com'
            ],
            'options' => [
                'label' => 'Email',
            ]
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'password',
            'attributes' => [
                'placeholder' => '1234'
            ],
            'options' => [
                'label' => 'Password'
            ]
        ]);

        $input->add([
            'name' => 'password',
            'required' => true,
            'validators' => [
                ['name' => 'not_empty'],
                [
                    'name' => 'string_length',
                    'options' => [
                        'min' => 8
                    ]
                ]
            ]
        ]);

        $this->setInputFilter($input);
    }
}