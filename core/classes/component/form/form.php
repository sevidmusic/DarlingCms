<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 12:51 AM
 */

namespace DarlingCms\classes\component\form;

class form extends \DarlingCms\classes\component\html\html
{
    /**
     * @var array Array of form elements.
     */
    private $formElements = array();
    private $formAttributes = array();
    private $supportedAttributes = array(
        'accept-charset',
        'action',
        'autocomplete',
        'enctype',
        'method',
        'name',
        'novalidate',
        'target'
    );

    public function __construct(array $attributes = array(), \DarlingCms\classes\component\form\element\formElement ...$formElement)
    {
        $this->setFormAttributes($attributes);
        /*
         * Dev note: PhpStorm complains that the parent constructor is not called. :|
         *           Ignore this, it is being called, it is called when generateForm() is called...
         */
        foreach ($formElement as $element) {
            $this->addFormElement($element);
        };
        $this->generateForm(); // parent constructor called from inside generateForm().
    }

    /**
     * Set the form attributes. For information on what attributes are supported,
     * call getSupportedAttributes() which returns an array of the form attributes
     * this component supports.
     * @param array $attributes An array of form attributes.
     * @return bool True if $attributes were set, false otherwise.
     */
    public function setFormAttributes(array $attributes)
    {
        foreach ($attributes as $attributeName => $attributeValue) {
            switch (gettype($attributeName)) {
                case 'integer':
                    $this->setFormAttribute($attributeValue);
                    break;
                case 'string':
                    $this->setFormAttribute($attributeName, $attributeValue);
                    break;
            }
        }
        return isset($this->formAttributes);
    }

    public function setFormAttribute(string $attributeName, string $attributeValue = '')
    {
        /* Make sure the attribute name or value is a supported attribute. */
        if (in_array($attributeName, $this->supportedAttributes, true) === true) {
            /* Construct the attribute string formatted appropriately for the attribute being set,
               and add it to the validatedAttributes array.
               i.e. distinguish between attributes with and without values. */
            switch ($attributeValue) {
                case '':
                    /* Note: Always use attribute name as index so attributes can be changed in the future, and
                             to prevent attribute duplication. i.e,  Do not use array_push(). */
                    $this->formAttributes[$attributeName] = $attributeName;
                    //var_dump("Added attribute $attributeName.");
                    break;
                default:
                    /* Note: Always use attribute name as index so attributes can be changed in the future, and
                             to prevent attribute duplication. i.e,  Do not use array_push(). */
                    $this->formAttributes[$attributeName] = "$attributeName=\"$attributeValue\"";
                    //var_dump("Set attribute \"$attributeName\" to \"$attributeValue\".");
                    break;
            }
            /* Re-generate form whenever an attribute is added to ensure form html reflects any changes. */
            return $this->generateForm();
        }
        return false;
    }

    private function generateForm()
    {
        parent::__construct('form', implode('', $this->formElements), $this->formAttributes);
        return ($this->html !== '');
    }

    /**
     * Adds a form element to the form.
     * Note: Calling this method will regenerate the form.
     *
     * @param element\formElement $formElement The form element to add.
     * @return bool
     */
    public function addFormElement(\DarlingCms\classes\component\form\element\formElement $formElement)
    {
        array_push($this->formElements, $formElement);
        return $this->generateForm(); /* regenerate form whenever an element is added. */
    }

    /**
     * Returns an array of visible debug info for a this object.
     *
     * @return array
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     */
    function __debugInfo()
    {
        return array(
            'html' => $this->html,
            'componentAttributes' => $this->getComponentAttributes(),
            'formElements' => $this->formElements,
        );
    }

    public function removeFormAttribute(string $attributeName)
    {
        unset($this->formAttributes[$attributeName]);
        /* Regenerate form whenever an attribute is removed to ensure form html reflects any changes. */
        $this->generateForm();
    }

    /**
     * @return array Array of supported form attributes.
     */
    public function getSupportedAttributes()
    {
        return $this->supportedAttributes;
    }

}
