<?php
namespace CSSEditor\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Omeka\Mvc\Exception\NotFoundException;
use Zend\View\Model\ViewModel;
/**
 * The plugin controller for css editor.
 *
 * @package CSSEditor
 */
class IndexController extends AbstractActionController {

    public function browseAction() {
        $site_id=$this->params('id','');
        $serviceLocator = $this->getServiceLocator();
        $response = $this->getResponse();
        $response->setContent('');


        if ($site_id == '') {
            $response->setContent($this->getCssForAllSites());
            return $response;
        }
        if ($this->getRequest()->isPost()) {
            if ($this->savecss($site_id)) {
                $response->setStatusCode('400');
                $response->setContent('Css couldn\'t be saved');
                return $response;
            }
            $site_id=$this->getRequest()->getPost('site');
        }
        $settings = $serviceLocator->get('Omeka\SiteSettings');
        if (!$site=$serviceLocator->get('Omeka\EntityManager')->find('Omeka\Entity\Site',$site_id))
            return $response;
        $settings->setSite($site);

        $css=$settings->get('css_editor_css');
        $response->setContent($css);
        return $response;
    }

    public function savecss($site_id) {
        $moduleObject = $this->getServiceLocator()
            ->get('ModuleManager')->getModule('CSSEditor');
        return $moduleObject->handleConfigForm($this);
    }

    protected function getCssForAllSites() {
        return ($this->getServiceLocator()->get('Omeka\Settings')->get('css_editor_css'));
    }

}
