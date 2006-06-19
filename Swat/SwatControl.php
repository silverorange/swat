<?php

require_once 'Swat/SwatWidget.php';
require_once 'Swat/SwatTitleable.php';
require_once 'Swat/SwatString.php';

/**
 * Abstract base class for control widgets (non-container)
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatControl extends SwatWidget
{
	// {{{ public function addMessage()

	/**
	 * Adds a message
	 *
	 * Before the message is added, the content is updated with the name of
	 * this controls's parent field if the field exists.
	 *
	 * @param SwatMessage $message the message object to add.
	 *
	 * @see SwatWidget::addMessage()
	 */
	public function addMessage(SwatMessage $message)
	{
		if ($this->parent instanceof SwatTitleable) {
			$title = $this->parent->getTitle();
			if ($title === null)
				$field_title = '';
			else
				$field_title =
					'<strong>'.
					SwatString::minimizeEntities($this->parent->getTitle()).
					'</strong>';
		} else {
			$field_title = '';
		}
 
		if ($message->content_type === 'text/plain')
			$content = SwatString::minimizeEntities($message->primary_content);
		else
			$content = $message->primary_content;

		$message->primary_content = sprintf($content, $field_title);
		$message->content_type = 'text/xml';

		$this->messages[] = $message;
	}

	// }}}
	// {{{ public function getMessages()

	/**
	 * Gets all messages
	 *
	 * Gathers all messages from children of this widget and this widget 
	 * itself.
	 *
	 * @return array an array of {@link SwatMessage} objects.
	 *
	 * @see SwatWidget::getMessages()
	 * @see SwatMessage
	 */
	public function getMessages()
	{
		return $this->messages;
	}

	// }}}
	// {{{ public function hasMessage()

	/**
	 * Checks for the presence of messages
	 *
	 * @return boolean true if there is an message in the subtree.
	 *
	 * @see SwatWidget::hasMessage()
	 */
	public function hasMessage()
	{
		return (count($this->messages) > 0);
	}

	// }}}
	// {{{ public function getNote()

	/**
	 * Gets an informative note of how to use this control
	 *
	 * By default, controls return null, meaning no note.
	 *
	 * @return string an informative note of how to use this control.
	 */
	public function getNote()
	{
		return null;
	}

	// }}}
	// {{{ public function getHtmlHeadEntries()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this control
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this control.
	 *
	 * @see SwatUIObject::getHtmlHeadEntries()
	 */
	public function getHtmlHeadEntries()
	{
		return $this->html_head_entries;
	}

	// }}}
}

?>
