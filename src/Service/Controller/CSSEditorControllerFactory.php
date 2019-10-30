<?php

namespace CSSEditor\Service\Controller;

use CSSEditor\Controller\CSSEditorController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CSSEditorControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $controller = new CSSEditorController(
            $services->get('CSSEditor\CssCleaner')
        );

        return $controller;
    }
}
