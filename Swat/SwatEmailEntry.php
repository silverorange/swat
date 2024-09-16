<?php

/**
 * An email entry widget
 *
 * Automatically verifies that the value of the widget is a valid
 * email address.
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatEmailEntry extends SwatEntry
{


    /**
     * Processes this email entry
     *
     * Ensures this email address is formatted correctly. If the email address
     * is not formatted correctly, adds an error message to this entry widget.
     */
    public function process()
    {
        parent::process();

        if ($this->value === null) {
            return;
        }

        if ($this->value == '') {
            $this->value = null;
            return;
        }

        if (!$this->validateEmailAddress()) {
            $this->addMessage($this->getValidationMessage('email'));
        }
    }



    /**
     * Validates the email address value of this entry
     *
     * @return boolean true if this entry's value is a valid email address and
     *                  false if it is not.
     */
    protected function validateEmailAddress()
    {
        return SwatString::validateEmailAddress($this->value);
    }



    /**
     * Gets a validation message for this email entry
     *
     * @see SwatEntry::getValidationMessage()
     * @param string $id the string identifier of the validation message.
     *
     * @return SwatMessage the validation message.
     */
    protected function getValidationMessage($id)
    {
        switch ($id) {
            case 'email':
                $text = Swat::_(
                    'The email address you have entered is not ' .
                        'properly formatted.',
                );

                $message = new SwatMessage($text, 'error');
                break;

            default:
                $message = parent::getValidationMessage($id);
                break;
        }

        return $message;
    }



    /**
     * Get the input tag to display
     *
     * @return SwatHtmlTag the input tag to display.
     */
    protected function getInputTag()
    {
        $tag = parent::getInputTag();
        $tag->type = 'email';
        return $tag;
    }



    /**
     * Gets the array of CSS classes that are applied to this entry
     *
     * @return array the array of CSS classes that are applied to this
     *                entry.
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-email-entry'];
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

}
