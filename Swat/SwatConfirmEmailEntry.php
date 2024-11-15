<?php

/**
 * An email address confirmation entry widget.
 *
 * Automatically compares the value of the confirmation with the matching
 * email entry widget to see if they match.
 *
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatConfirmEmailEntry extends SwatEmailEntry
{
    /**
     * A reference to the matching email entry widget.
     *
     * @var SwatEmailEntry
     */
    public $email_widget;

    /**
     * Checks to make sure email addresses match.
     *
     * Checks to make sure the values of the two email address fields are the
     * same. If an associated email entry widget is not set, an exception is
     * thrown. If the addresses do not match, an error is added to this widget.
     *
     * @throws SwatException
     */
    public function process()
    {
        parent::process();

        if ($this->value === null) {
            return;
        }

        if ($this->email_widget === null) {
            throw new SwatException(
                "Property 'email_widget' is null. " .
                    'Expected a reference to a SwatEmailEntry.',
            );
        }

        if ($this->email_widget->value !== null) {
            if ($this->email_widget->value !== $this->value) {
                $message = Swat::_(
                    'Email address and confirmation email ' .
                        'address do not match.',
                );

                $this->addMessage(new SwatMessage($message, 'error'));
            }
        }
    }

    /**
     * Gets the array of CSS classes that are applied to this entry.
     *
     * @return array the array of CSS classes that are applied to this
     *               entry
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-confirm-email-entry'];

        return array_merge($classes, parent::getCSSClassNames());
    }
}
