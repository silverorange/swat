<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatWidget.php');
require_once('Swat/SwatErrorMessage.php');

/**
 * Abstract base class for control widgets (non-container).
 */
abstract class SwatControl extends SwatWidget {

	public $required = false;
	
	private $error_messages = array();

	protected function addErrorMessage($msg) {
		if ($this->parent instanceof SwatFormField)
			$title = '<strong>'.$this->parent->title.'</strong>';
		else
			$title = '';
 
		$err = new SwatErrorMessage(sprintf($msg, $title));
		$this->error_messages[] = $err;
	}

	/**
	 * Gather error messages.
	 *
	 * @return array Array of SwatErrorMessage objects.
	 */
	public function gatherErrorMessages() {
		return $this->error_messages;
	}
}
?>
