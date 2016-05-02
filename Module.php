<?php
namespace CSSEditor;
use Omeka\Module\AbstractModule;
use Zend\Mvc\Controller\AbstractController;
use Zend\View\Renderer\PhpRenderer;
use Zend\Form\Element\Textarea;
use Zend\EventManager\SharedEventManagerInterface;
use Omeka\Event\Event;

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

class Module extends AbstractModule {
    public function getConfigForm(PhpRenderer $renderer) {
        $serviceLocator = $this->getServiceLocator();
        $settings = $serviceLocator->get('Omeka\Settings');

        $textarea = new Textarea('css');
        $textarea->setAttribute('rows', 15);
        $textarea->setValue($settings->get('css_editor_css'));
        $textarea->setAttribute('id', 'csseditor_cssvalue');

        return $renderer->render('config_form', ['textarea' => $textarea]);
    }

    public function handleConfigForm(AbstractController $controller) {
        require_once dirname(__FILE__) . '/src/CSSTidy/class.csstidy.php';

        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Filter.ExtractStyleBlocks', TRUE);
        $config->set('CSS.AllowImportant', TRUE);
        $config->set('CSS.AllowTricky', TRUE);
        $config->set('CSS.Proprietary', TRUE);
        $config->set('CSS.Trusted', TRUE);
        $purifier = new \HTMLPurifier($config);

        $css = $controller->getRequest()->getPost('css', '');
        $purifier->purify("<style>$css</style>");

        $clean_css = $purifier->context->get('StyleBlocks');
        $clean_css = $clean_css[0];
        $site_selected = $controller->getRequest()->getPost('site', '');
        if ($site_selected == '') {
            $this->setOption('css_editor_css', $clean_css);
            return true;
        }

        $this->setSiteOption($site_selected, 'css_editor_css', $clean_css);

        return true;
    }

    public function setOption($name, $value) {
        $serviceLocator = $this->getServiceLocator();
        return $serviceLocator->get('Omeka\Settings')->set($name,$value);
    }

    protected function setSiteOption($site_id, $name, $value) {
        $serviceLocator = $this->getServiceLocator();
        $siteSettings = $serviceLocator->get('Omeka\SiteSettings');
        $entityManager = $serviceLocator->get('Omeka\EntityManager');

        if ($site = $entityManager->find('Omeka\Entity\Site', $site_id)) {
            $siteSettings->setSite($site);
            return $siteSettings->set($name, $value);
        }

        return false;
    }

    public function appendCss(Event $event) {
        $serviceLocator = $this->getServiceLocator();
        $siteSettings = $serviceLocator->get('Omeka\SiteSettings');
        $settings = $serviceLocator->get('Omeka\Settings');
        $routeMatch = $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch();
        $isSite = $routeMatch->getParam('__SITE__');
        $view = $event->getTarget();

        if ($isSite && $css = $siteSettings->get('css_editor_css')) {
            $view->headStyle()->appendStyle($css);
        } elseif ($css = $settings->get('css_editor_css')) {
            $view->headStyle()->appendStyle($css);
        }
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager) {
        $sharedEventManager->attach('*', 'view.layout', [$this, 'appendCss']);
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }
}
