<?php
require_once('Swat/SwatWidget.php');
require_once('Swat/SwatFormField.php');
require_once('Swat/SwatErrorMessage.php');

/**
 * Abstract base class for control widgets (non-container)
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
abstract class SwatControl extends SwatWidget {

	public function addErrorMessage($msg) {
		if ($this->parent instanceof SwatFormField)
			$field_title = '<strong>'.$this->parent->title.'</strong>';
		else
			$field_title = '';
 
		$err = new SwatErrorMessage(sprintf($msg, $field_title));
		$this->error_messages[] = $err;
	}

	/**
	 * Gather error messages
	 * @return array Array of SwatErrorMessage objects.
	 */
	public function gatherErrorMessages() {
		return $this->error_messages;
	}

	/**
	 * Check for error messages
	 * @return boolean True if any error messages exist.
	 */
	public function hasErrorMessage() {
		return (count($this->error_messages) > 0);
	}

	/**
	 * Set the state of the control
	 *
	 * Used to set the current state of the control back to a stored state.
	 * This implementation of this method should correspond to the implementation
	 * of getState(). Sub-classes should override and implement this method to 
	 * store their state.
	 *
	 * @param mixed $state The state to load into the control.
	 */
	public function setState($state) {

	}

	/**
	 * Get the state of the control
	 *
	 * Used to remember the current state of the control. For example, {@link SwatEntry}
	 * implements this method to return its ::$value property, but can return any 
	 * variable type, including array, that represents the control's current state.
	 * Sub-classes should override and implement this method to store their state.
	 *
	 * @return mixed The current state of the control.
	 */
	public function getState() {
		return null;
	}

}
?>
