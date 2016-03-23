<?php
return [
    'controllers' => [
        'invokables' => [
                         'CSSEditor\Controller\Index' => 'CSSEditor\Controller\IndexController',
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
                                                                              'controller' => 'Index',
                                                                              'action' => 'browse',
                                                               ],
                                                 ],
                                 ]
                    ]]]],

        'view_manager' => array(
                                'template_path_stack' => array(
                                                                 __DIR__ . '/../view',
                                  ),
  ),];
