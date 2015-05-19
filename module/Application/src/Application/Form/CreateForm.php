<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class CreateForm extends Form
{
    public function __construct($name = 'create')
    {
        parent::__construct($name);

        $input = new InputFilter();

        $this->add([
            'name' => 'address',
            'type' => 'MultiCheckbox',
            'attributes' => [
                'placeholder' => 'Mom'
            ],
            'options' => [
                'label' => 'Address',
            ]
        ]);

        $input->add([
            'name' => 'address',
            'required' => true,
            'validators' => [
                ['name' => 'not_empty']
            ]
        ]);

        $this->add([
            'name' => 'link',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'https://github.com/me/myrepo..'
            ],
            'options' => [
                'label' => 'Your Code',
            ]
        ]);

        $input->add([
            'name' => 'link',
            'required' => true,
            'validators' => [
                ['name' => 'not_empty']
            ]
        ]);

      $this->setInputFilter($input);
    }
}