<?php
namespace WebbuildersGroup\ElementLink\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Class \WebbuildersGroup\ElementLink\Extensions\LinkFieldExtension
 *
 * @property \SilverStripe\LinkField\Form\AbstractLinkField|\WebbuildersGroup\ElementLink\Extensions\LinkFieldExtension $owner
 */
class LinkFieldExtension extends Extension
{
    /**
     * Injects the scripts for dependent dropdown field
     */
    public function onBeforeRender()
    {
        Requirements::javascript('sheadawson/silverstripe-dependentdropdownfield: client/js/dependentdropdownfield.js');
        Requirements::javascript('webbuilders-group/silverstripe-element-link: javascript/ElementDropdownField.js');
    }
}
