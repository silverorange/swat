<?php
require_once('Swat/SwatEntry.php');

/**
 * A float entry widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatEntryFloat extends SwatEntry {

	public function init() {
		$this->size = 10;
	}

	public function process() {
		parent::process();

		if (is_numeric($this->value))
			$this->value = floatval($this->value);
		else
			$this->addErrorMessage(_S("The %s field must be a number."));
	}
}

?>
