<?php
namespace CSSEditor\View\Helper;
use Omeka\View\Helper\AbstractSelect;
use Zend\Form\Element\Select;


/**
 * A select menu containing all sites.
 */
class SiteSelect extends AbstractSelect
{

    public function getValueOptions()
    {
        $this->emptyOption = $this->getView()->translate('All sites');
        $sites = $this->getView()->api()->search('sites')->getContent();
        $options = [];
        foreach ($sites as $site) {
            $options[$site->id()] = $site->title();
        }

        return  $options;
    }


}
