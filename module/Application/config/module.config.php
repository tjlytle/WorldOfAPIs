<?php
return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'signup' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/signup',
                    'defaults' => [
                        'controller' => 'Application\Controller\Signin',
                        'action'     => 'signup',
                    ],
                ],
            ],
            'signin' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/signin',
                    'defaults' => [
                        'controller' => 'Application\Controller\Signin',
                        'action'     => 'signin',
                    ],
                ],
            ],
            'address' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/address',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'address',
                    ],
                ],
            ],
            'preview' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/preview',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'preview',
                    ],
                ],
            ],
            'send' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/send',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'send',
                    ],
                ],
            ],
            'render' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/render',
                    'defaults' => [
                        'controller' => 'Application\Controller\Render',
                        'action' => 'render'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'result' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/:id',
                            'defaults' => [
                                'controller' => 'Application\Controller\Render',
                                'action' => 'result'
                            ],
                        ],
                    ]
                ]
            ],
            'claim' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/claim',
                    'defaults' => [
                        'controller' => 'Application\Controller\Claim',
                        'action' => 'verify'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'result' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/:id',
                            'defaults' => [
                                'controller' => 'Application\Controller\Claim',
                                'action' => 'claim'
                            ],
                        ],
                    ]
                ]
            ]
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
            'Application\Service\Stormpath\ClientFactory',
            'Application\Service\Nexmo\ClientFactory'
        ],
        'factories' => [
            'Zend\Authentication\AuthenticationService' => 'Application\Factory\AuthenticationFactory',
            'andrefelipe\Orchestrate\Application' => 'Application\Service\Orchestrate\ClientFactory',
            'Application\Service\Orchestrate\StorageService' => 'Application\Service\Orchestrate\StorageFactory',
            'CloudConvert\Api' => 'Application\Service\CloudConvert\ClientFactory',
            'Lob\Lob' => 'Application\Service\Lob\ClientFactory',
        ]
    ],
    'controllers' => [
        'factories' => [
            'Application\Controller\Signin' => 'Application\Factory\Controller\SigninFactory',
            'Application\Controller\Index' => 'Application\Factory\Controller\IndexFactory',
            'Application\Controller\Render' => 'Application\Factory\Controller\RenderFactory',
            'Application\Controller\Claim' => 'Application\Factory\Controller\ClaimFactory',
        ]
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [
            ],
        ],
    ],
];
