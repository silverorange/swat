<?php
require_once('Swat/SwatEntry.php');

/**
 * An integer entry widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatIntegerEntry extends SwatEntry {

	public function init() {
		$this->size = 5;
	}

	public function process() {
		parent::process();

		if (is_numeric($this->value))
			$this->value = intval($this->value);
		else {
			$msg = _S("The %s field must be an integer.");
			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
		}
	}
}

?>
