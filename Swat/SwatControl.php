<?php

require_once 'Swat/SwatWidget.php';
require_once 'Swat/SwatFormField.php';

/**
 * Abstract base class for control widgets (non-container)
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatControl extends SwatWidget
{
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
	public function addMessage($message)
	{
		if ($this->parent instanceof SwatFormField)
			$field_title = '<strong>'.$this->parent->title.'</strong>';
		else
			$field_title = '';
 
 		$message->primary_content = sprintf($message->primary_content, $field_title);
		
		$this->messages[] = $message;
	}

	/**
	 * Gets all messages
	 *
	 * @return array the gathered SwatMessage objects.
	 *
	 * @see SwatWidget::getAllMessages()
	 */
	public function getAllMessages()
	{
		return $this->messages;
	}

	/**
	 * Checks for the presence of messages
	 *
	 * @return boolean true if there is a message in this control.
	 *
	 * @see SwatWidget::hasMessages()
	 */
	public function hasMessage()
	{
		return (count($this->messages) > 0);
	}
}

?>
