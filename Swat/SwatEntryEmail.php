<?php
require_once('Swat/SwatControl.php');

/**
 * An email entry widget
 *
 * Automatically verifies that the value of the widget is a valid
 * email address.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatEntryEmail extends SwatEntry {
	
	public function process() {
		parent::process();
		
		if (($this->required || strlen($this->value)) &&
			!ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'
				.'@'
				.'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'
				.'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$'
				, trim($this->value))
		)
		$this->addErrorMessage(_S("The email address you have entered ".
			"is not properly formatted."));
	}
}
?>
