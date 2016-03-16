<?php
namespace CSSEditor;
use Omeka\Module\AbstractModule;
use Omeka\Service\HtmlPurifier;
use Zend\Mvc\Controller\AbstractController;
use Zend\View\Renderer\PhpRenderer;
use Zend\Form\Element\Textarea;
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

    public function getConfigForm(PhpRenderer $renderer)
    {
      xdebug_break();
      $serviceLocator = $this->getServiceLocator();
      $translator = $serviceLocator->get('MvcTranslator');
      $textarea = new Textarea('css', ['rows' => 25, 'cols' => 50]);
      $textarea->setValue($serviceLocator->get('Omeka\Settings')->get('css_editor_css'));
      return $renderer->render('config_form',['translator' => $translator,
                                              'textarea' => $textarea
                                              ]);
    }

    public function handleConfigForm(AbstractController $controller)
    {

        require_once dirname(__FILE__) . '/src/CSSTidy/class.csstidy.php';

        $purifier=new HTMLPurifier(true);
        $config = $purifier->getConfig();
        $config->set('Filter.ExtractStyleBlocks', TRUE);
        $config->set('CSS.AllowImportant', TRUE);
        $config->set('CSS.AllowTricky', TRUE);
        $config->set('CSS.Proprietary', TRUE);
        $config->set('CSS.Trusted', TRUE);
        $purifier->setConfig($config);

        $html=$purifier->purify('<style>' . $_POST['css'] . '</style>');

        $clean_css = $purifier->getPurifier()->context->get('StyleBlocks');
        $clean_css = $clean_css[0];
//        var_dump('clean='.$html);exit;
        $this->setOption('css_editor_css', $clean_css);
    }

    public function setOption($name, $value) {
      $serviceLocator = $this->getServiceLocator();
      return  $serviceLocator->get('Omeka\Settings')->set($name,$value);
    }


    public function hookPublicHead($args)
    {
      $serviceLocator = $this->getServiceLocator();
      $css = $serviceLocator->get('Omeka\Settings')->get('css_editor_css');
      if ($css) {
        queue_css_string($css);
      }
    }

    public function getConfig() {
      return include __DIR__ . '/config/module.config.php';
    }
}
