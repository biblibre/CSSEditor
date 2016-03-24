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

        $settings = $serviceLocator->get('Omeka\SiteSettings');
        if (!$site=$serviceLocator->get('Omeka\EntityManager')->find('Omeka\Entity\Site',$site_id))
            return $response;
        $settings->setSite($site);

        $css=$settings->get('css_editor_css');
        $response->setContent($css);
        return $response;
    }

    protected function getCssForAllSites() {
        return ($this->getServiceLocator()->get('Omeka\Settings')->get('css_editor_css'));
    }

}
