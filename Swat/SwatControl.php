<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatWidget.php');
require_once('Swat/SwatFormField.php');
require_once('Swat/SwatErrorMessage.php');

/**
 * Abstract base class for control widgets (non-container).
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
	 * Gather error messages.
	 * @return array Array of SwatErrorMessage objects.
	 */
	public function gatherErrorMessages() {
		return $this->error_messages;
	}

	/**
	 * Check for error messages.
	 * @return boolean True if any error messages exist.
	 */
	public function hasErrorMessage() {
		return (count($this->error_messages) > 0);
	}

}
?>
