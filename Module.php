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

class Module extends AbstractModule
{

  public function getConfigForm(PhpRenderer $renderer) {
    $serviceLocator = $this->getServiceLocator();
    $translator = $serviceLocator->get('MvcTranslator');
    $textarea = new Textarea('css');
    $textarea->setAttribute('rows', 15);
    $textarea->setValue($serviceLocator->get('Omeka\Settings')->get('css_editor_css'));
    $textarea->setAttribute('id','csseditor_cssvalue');
    return $renderer->render('config_form',['translator' => $translator,
                                            'textarea' => $textarea
                                            ]);
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

    $css = $controller->getRequest()->getPost('css','');
    $purifier->purify("<style>$css</style>");

    $clean_css = $purifier->context->get('StyleBlocks');
    $clean_css = $clean_css[0];
    $site_selected = $controller->getRequest()->getPost('site','');
    if ($site_selected=='')
      return $this->setOption('css_editor_css', $clean_css);

    return $this->setSiteOption($site_selected,'css_editor_css', $clean_css);
  }

  public function setOption($name, $value) {
    $serviceLocator = $this->getServiceLocator();
    return  $serviceLocator->get('Omeka\Settings')->set($name,$value);
  }


  public function setSiteOption($site_id,$name, $value) {
    $serviceLocator = $this->getServiceLocator();
    $settings = $serviceLocator->get('Omeka\SiteSettings');
    if (!$site=$serviceLocator->get('Omeka\EntityManager')->find('Omeka\Entity\Site',$site_id))
      return false;
    $settings->setSite($site);
    return $settings->set($name,$value);
  }


  public function addCssToPublicHead(Event  $event) {
    $serviceLocator = $this->getServiceLocator();
    $css = $serviceLocator->get('Omeka\Settings')->get('css_editor_css');
    if ($css)
      $serviceLocator->get('Zend\View\Renderer\PhpRenderer')->headStyle()->appendStyle($css);

  }


  public function addCssToSite(Event $event) {

    $settings = $this->getServiceLocator()->get('Omeka\SiteSettings');
    try {
      if ($css=$settings->get('css_editor_css'))
        $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer')->headStyle()->appendStyle($css);
    } catch (Exception $e) {return false;}

  }


  public function attachListeners(SharedEventManagerInterface $sharedEventManager) {

    $sharedEventManager->attach('*',
                                'view.layout', [$this, 'addCssToPublicHead']);
    $sharedEventManager->attach(['Omeka\Controller\Site\Index',
                                 'Omeka\Controller\Site\Item',
                                 'Omeka\Controller\Site\ItemSet',
                                 'Omeka\Controller\Site\Media',
                                 'Omeka\Controller\Site\Page'],
                                'view.layout', [$this, 'addCssToSite']);


  }


  public function getConfig() {
    return include __DIR__ . '/config/module.config.php';
  }
}
