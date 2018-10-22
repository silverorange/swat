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
	 * Before the message is added, the content is updated with the name of
	 * this controls's parent title field if the parent implements the
	 * {@link SwatTitleable} interface.
	 *
	 * @param SwatMessage $message the message to add.
	 *
	 * @see SwatWidget::addMessage()
	 */
	public function addMessage(SwatMessage $message)
	{
		if ($this->parent instanceof SwatTitleable) {
			$title = $this->parent->getTitle();
			if ($title === null) {
				$field_title = '';
			} else {
				if ($this->parent->getTitleContentType() === 'text/xml') {
					$field_title =
						'<strong>'.$this->parent->getTitle().'</strong>';
				} else {
					$field_title =
						'<strong>'.
						SwatString::minimizeEntities($this->parent->getTitle()).
						'</strong>';
				}
			}
		} else {
			$field_title = '';
		}

		if ($message->content_type === 'text/plain') {
			$content = SwatString::minimizeEntities($message->primary_content);
		} else {
			$content = $message->primary_content;
		}

		$message->primary_content = sprintf($content, $field_title);
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

?>
