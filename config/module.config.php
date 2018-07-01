<?php
namespace CSSEditor;

return [
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\CSSEditorController::class => Service\Controller\CSSEditorControllerFactory::class,
        ],
    ],
    'router' =>[
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'csseditor' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/csseditor/browse[/:id]',
                            'defaults' => [
                                '__NAMESPACE__' => 'CSSEditor\Controller',
                                'controller' => 'CSSEditorController',
                                'action' => 'browse',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'CSSEditor\CssCleaner' => Service\CssCleanerFactory::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'siteSelect' => View\Helper\SiteSelect::class,
        ],
    ],
    'csseditor' => [
        'config' => [
            'csseditor_css' => '',
        ],
        'site_settings' => [
            'csseditor_css' => '',
        ],
    ],
];
