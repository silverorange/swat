<?php

/**
 * Abstract base class for control widgets (non-container)
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatControl extends SwatWidget
{
    // {{{ public function addMessage()

    /**
     * Adds a message to this control
     *
     * @param SwatMessage $message the message to add.
     *
     * @see SwatWidget::addMessage()
     */
    public function addMessage(SwatMessage $message)
    {
        if ($message->content_type === 'text/plain') {
            $message->primary_content = SwatString::minimizeEntities(
                $message->primary_content,
            );
        }

        $message->content_type = 'text/xml';

        parent::addMessage($message);
    }

    // }}}
    // {{{ public function printWidgetTree()

    public function printWidgetTree()
    {
        echo get_class($this), ' ', $this->id;
    }

    // }}}
    // {{{ public function getNote()

    /**
     * Gets an informative note of how to use this control
     *
     * By default, controls return null, meaning no note.
     *
     * @return SwatMessage an informative note of how to use this control or
     *                      null if this control has no note.
     */
    public function getNote()
    {
        return null;
    }

    // }}}
}
