<?php
namespace CSSEditor\Controller;

use CSSEditor\Service\CssCleaner;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * The plugin controller for css editor.
 *
 * @package CSSEditor
 */
class CSSEditorController extends AbstractActionController
{
    /**
     * @var CssCleaner
     */
    protected $cssCleaner;

    /**
     * @param CssCleaner $cssCleaner
     */
    public function __construct(CssCleaner $cssCleaner)
    {
        $this->cssCleaner = $cssCleaner;
    }

    public function browseAction()
    {
        $response = $this->getResponse();
        $response->setContent('');

        if ($this->getRequest()->isPost()) {
            $css = $this->params()->fromPost('css');
            $siteId = $this->params()->fromPost('site');

            $css = $this->cssCleaner->clean($css);
            if ($siteId) {
                $siteSettings = $this->getSiteSettings($siteId);
                $siteSettings->set('csseditor_css', $css);
            } else {
                $this->settings()->set('csseditor_css', $css);
            }
        }

        $site_id = $this->params('id', '');
        if ($site_id) {
            $settings = $this->getSiteSettings($site_id);
        } else {
            $settings = $this->settings();
        }

        if ($settings) {
            $response->setContent($settings->get('csseditor_css'));
        }

        return $response;
    }

    protected function getSiteSettings($siteId)
    {
        $site = $this->api()->read('sites', $siteId)->getContent();
        $siteSettings = $this->siteSettings();
        $siteSettings->setTargetId($siteId);

        return $siteSettings;
    }
}
