<?php
namespace CSSEditor;

use Omeka\Module\AbstractModule;
use Omeka\Module\Exception\ModuleCannotInstallException;
use Zend\Mvc\Controller\AbstractController;
use Zend\View\Renderer\PhpRenderer;
use Zend\Form\Element\Textarea;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\Event;
use Zend\ModuleManager\ModuleManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * CSS Editor
 * @copyright  Copyright 2014 Roy Rosenzweig Center for History and New Media
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The CSS Editor plugin
 *
 * @package  CSS Editor
 */

class Module extends AbstractModule
{
    public function init(ModuleManager $moduleManager)
    {
        require_once __DIR__ . '/vendor/autoload.php';
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function install(ServiceLocatorInterface $serviceLocator)
    {
        $t = $serviceLocator->get('MvcTranslator');

        $dependency = __DIR__ . '/vendor/cerdic/css-tidy/css_optimiser.php';
        if (!file_exists($dependency)) {
            throw new ModuleCannotInstallException(
                sprintf($t->translate('The dependency "%s" should be installed.'), 'css-tidy') // @translate
                . ' ' . $t->translate('See module’s installation documentation.')); // @translate
        }

        $this->manageSettings($serviceLocator->get('Omeka\Settings'), 'install');
    }

    public function uninstall(ServiceLocatorInterface $serviceLocator)
    {
        $this->manageSettings($serviceLocator->get('Omeka\Settings'), 'uninstall');
    }

    protected function manageSettings($settings, $process, $key = 'config')
    {
        $config = require __DIR__ . '/config/module.config.php';
        $defaultSettings = $config[strtolower(__NAMESPACE__)][$key];
        foreach ($defaultSettings as $name => $value) {
            switch ($process) {
                case 'install':
                    $settings->set($name, $value);
                    break;
                case 'uninstall':
                    $settings->delete($name);
                    break;
            }
        }
    }

    public function upgrade($oldVersion, $newVersion, ServiceLocatorInterface $serviceLocator)
    {
        require_once 'data/scripts/upgrade.php';
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            '*',
            'view.layout',
            [$this, 'appendCss']
        );
    }

    public function getConfigForm(PhpRenderer $renderer)
    {
        $serviceLocator = $this->getServiceLocator();
        $settings = $serviceLocator->get('Omeka\Settings');

        $textarea = new Textarea('css');
        $textarea->setAttribute('rows', 15);
        $textarea->setValue($settings->get('csseditor_css'));
        $textarea->setAttribute('id', 'csseditor_cssvalue');

        return $renderer->render('css-editor/module/config', ['textarea' => $textarea]);
    }

    public function handleConfigForm(AbstractController $controller)
    {
        /** @var \CSSEditor\Service\CssCleaner $cssCleaner */
        $cssCleaner = $this->getServiceLocator()->get('CSSEditor\CssCleaner');

        $css = $controller->getRequest()->getPost('css', '');
        $clean_css = $cssCleaner->clean($css);

        $site_selected = $controller->getRequest()->getPost('site', '');
        if ($site_selected) {
            $this->setSiteOption($site_selected, 'csseditor_css', $clean_css);
        } else {
            $this->setOption('csseditor_css', $clean_css);
        }

        return true;
    }

    protected function setOption($name, $value)
    {
        $serviceLocator = $this->getServiceLocator();
        return $serviceLocator->get('Omeka\Settings')->set($name,$value);
    }

    protected function setSiteOption($site_id, $name, $value)
    {
        $serviceLocator = $this->getServiceLocator();
        $siteSettings = $serviceLocator->get('Omeka\Settings\Site');
        $entityManager = $serviceLocator->get('Omeka\EntityManager');

        if ($site = $entityManager->find('Omeka\Entity\Site', $site_id)) {
            $siteSettings->setTargetId($site_id);
            return $siteSettings->set($name, $value);
        }

        return false;
    }

    /**
     * Append css to the current site.
     *
     * @param Event $event
     */
    public function appendCss(Event $event)
    {
        $serviceLocator = $this->getServiceLocator();
        $routeMatch = $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch();
        $isSite = $routeMatch->getParam('__SITE__');
        if (!$isSite) {
            return;
        }

        $siteSettings = $serviceLocator->get('Omeka\Settings\Site');
        $settings = $serviceLocator->get('Omeka\Settings');
        $view = $event->getTarget();
        if ($css = $siteSettings->get('csseditor_css')) {
            $view->headStyle()->appendStyle($css);
        } elseif ($css = $settings->get('csseditor_css')) {
            $view->headStyle()->appendStyle($css);
        }
    }
}
