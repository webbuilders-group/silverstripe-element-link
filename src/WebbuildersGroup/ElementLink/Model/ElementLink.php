<?php
namespace WebbuildersGroup\ElementLink\Model;

use DNADesign\Elemental\Models\BaseElement;
use Page;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\LinkField\Models\SiteTreeLink;
use WebbuildersGroup\ElementLink\Forms\ElementDropdownField;

/**
 * Class \WebbuildersGroup\ElementLink\Model\ElementLink
 *
 * @property int $ElementID
 * @method \DNADesign\Elemental\Models\BaseElement Element()
 */
class ElementLink extends SiteTreeLink
{
    private static $icon = 'font-icon-block-content';

    private static $has_one = [
        'Element' => BaseElement::class,
    ];

    private static $table_name = 'ElementLink';

    private static $block_page_class = Page::class;


    /**
     * Gets fields used in the cms
     * @return \SilverStripe\Forms\FieldList Fields to be used
     */
    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName('ElementID');

            $blockPageClass = $this->config()->block_page_class;

            $targetPageField = $fields->fieldByName('Root.Main.PageID')->setDisableFunction(function (SiteTree $page) use ($blockPageClass) {
                return !is_a($page, $blockPageClass);
            });

            $fields->replaceField(
                'Anchor',
                ElementDropdownField::create('ElementID', _t(__CLASS__ . '.has_one_ElementID', '_Block'))
                    ->setDepends($targetPageField)
                    ->setSource(function ($pageID) use ($blockPageClass) {
                        $page = $blockPageClass::get()->byID(intval($pageID));
                        if (!empty($page) && $page !== false && $page->exists()) {
                            return $page->ElementalArea()->Elements()->map();
                        }

                        return [];
                    })
                    ->setEmptyString(_t(__CLASS__ . '.SELECT_BLOCK', '_--- Select Block ---')),
                'HTML'
            );
        });

        return parent::getCMSFields();
    }

    /**
     * {@inheritDoc}
     */
    public function getMenuTitle(): string
    {
        return _t(__CLASS__ . '.LINKLABEL', 'Block on this site');
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultTitle(): string
    {
        $page = $this->Page();
        if (!$page->exists()) {
            return _t(SiteTreeLink::class . '.MISSING_DEFAULT_TITLE', '(Page missing)');
        }

        if (!$page->canView()) {
            return '';
        }

        $element = $this->Element();
        if (!$element?->exists()) {
            return _t(__CLASS__ . '.MISSING_DEFAULT_TITLE', '(Block missing)');
        } else if (!$element->canView()) {
            return '';
        }

        return $page->Title . ': ' . $element->Title;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        $page = $this->Page();
        if (!$page?->exists()) {
            return _t(SiteTreeLink::class . '.PAGE_DOES_NOT_EXIST', 'Page does not exist');
        } else if (!$page->canView()) {
            return _t(SiteTreeLink::class . '.CANNOT_VIEW_PAGE', 'Cannot view page');
        }

        $element = $this->Element();
        if (!$element?->exists()) {
            return _t(__CLASS__ . '.BLOCK_DOES_NOT_EXIST', 'Block does not exist');
        } else if (!$element->canView()) {
            return _t(__CLASS__ . '.CANNOT_VIEW_BLOCK', 'Cannot view Block');
        }

        return ($page->Title ?? '') . ' > ' . ($element->Title ?? '');
    }

    /**
     * {@inheritDoc}
     */
    public function getURL(): string
    {
        $page = $this->Page();
        $url = ($page->exists() ? $page->Link() : '');

        $element = $this->Element();
        $anchorSegment = ($element->exists() ? '#' . ($element->hasMethod('Anchor') ? $element->Anchor() : $element->getAnchor()): '');

        $queryStringSegment = ($this->QueryString ? '?' . $this->QueryString : '');

        $this->extend('updateGetURLBeforeAnchor', $url);

        return Controller::join_links($url, $anchorSegment, $queryStringSegment);
    }
}
