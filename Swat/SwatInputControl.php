<?php

/**
 * Base class for controls that accept user input on forms.
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatInputControl extends SwatControl
{


    /**
     * Whether this entry widget is required or not
     *
     * Must have a non-empty value when processed.
     *
     * @var boolean
     */
    public $required = false;

    /**
     * Whether to use the field title in validation messages
     *
     * @var boolean
     */
    public $show_field_title_in_messages = true;



    /**
     * Initializes this widget
     *
     * Sets required property on the form field that contains this widget.
     *
     * @see SwatWidget::init()
     */
    public function init()
    {
        parent::init();

        if ($this->required && $this->parent instanceof SwatFormField) {
            $this->parent->required = true;
        }
    }



    /**
     * Gets the form that this control is contained in
     *
     * You can also get the parent form with the
     * {@link SwatUIObject::getFirstAncestor()} method but this method is more
     * convenient and throws an exception .
     *
     * @return SwatForm the form this control is in.
     *
     * @throws SwatException
     */
    public function getForm()
    {
        $form = $this->getFirstAncestor('SwatForm');
        if ($form === null) {
            $path = static::class;
            $object = $this->parent;
            while ($object !== null) {
                $path = $object::class . '/' . $path;
                $object = $object->parent;
            }
            throw new SwatException(
                'Input controls must reside inside a ' .
                    "SwatForm widget. UI-Object path:\n" .
                    $path,
            );
        }

        return $form;
    }



    /**
     * Gets a validation message for this control
     *
     * Can be used by sub-classes to change the validation messages.
     *
     * @param string $id the string identifier of the validation message.
     *
     * @return SwatMessage the validation message.
     */
    protected function getValidationMessage($id)
    {
        $text = match ($id) {
            'required' => $this->show_field_title_in_messages
                ? Swat::_('%s is required.')
                : Swat::_('This field is required.'),
            'too-long' => $this->show_field_title_in_messages
                ? Swat::_(
                    'The %%s field can be at most %s characters long.',
                )
                : Swat::_('This field can be at most %s characters long.'),
            default => $this->show_field_title_in_messages
                ? Swat::_('There is a problem with the %s field.')
                : Swat::_('There is a problem with this field.'),
        };

        $message = new SwatMessage($text, 'error');
        return $message;
    }

}
