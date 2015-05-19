<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class SigninForm extends Form
{
    public function __construct($name = 'signup')
    {
        parent::__construct($name);

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
    }
}