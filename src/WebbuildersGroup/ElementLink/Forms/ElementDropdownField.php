<?php
namespace WebbuildersGroup\ElementLink\Forms;

use Sheadawson\DependentDropdown\Forms\DependentDropdownField;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\Model\List\Map;
use SilverStripe\View\Requirements;

class ElementDropdownField extends DependentDropdownField
{
    protected $schemaComponent = 'ElementDropdownField';

    /**
     * Sets the source field
     * @param FormField $field
     * @return $this
     */
    public function setDepends(FormField $field): self
    {
        if (isset($this->depends)) {
            $this->depends
                ->removeExtraClass('depended-on-field')
                ->setAttribute('data-dependent-field', null);
        }

        parent::setDepends($field);

        $this->depends
            ->addExtraClass('depended-on-field')
            ->setAttribute('data-dependent-field', $this->getName());

        return $this;
    }

    /**
     * @return array|\ArrayAccess|mixed
     */
    public function getSource()
    {
        if (!is_a($this->depends, TreeDropdownField::class)) {
            return parent::getSource();
        }

        $val = $this->depends->getValue();

        if (!$val) {
            $source = [];
        } else {
            $source = call_user_func($this->sourceCallback, $val);
            if ($source instanceof Map) {
                $source = $source->toArray();
            }
        }

        if ($this->getHasEmptyDefault()) {
            return ['' => $this->getEmptyString()] + (array) $source;
        } else {
            return $source;
        }
    }

    /**
     * @param array $properties
     * @return string
     */
    public function Field($properties = [])
    {
        $result = parent::Field($properties);

        Requirements::javascript('webbuilders-group:silverstripe-element-link: javascript/ElementDropdownField.js');

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchemaDataDefaults()
    {
        $schemaData = parent::getSchemaDataDefaults();

        $schemaData['link'] = $this->Link('load');
        $schemaData['depends'] = $this->getDepends()->getName();
        $schemaData['unselected'] = $this->getUnselectedString();

        return $schemaData;
    }
}
