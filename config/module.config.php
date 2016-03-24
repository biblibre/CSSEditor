<?php
return
    [ 'controllers' => [
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

     'view_helpers' => [
         'invokables' => [
             'SiteSelect' => 'CSSEditor\View\Helper\SiteSelect',
        ],
  ],

 'view_manager' => [
      'template_path_stack' => [
                                __DIR__ . '/../view',
      ]
 ]];
