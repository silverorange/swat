<?php

require_once('Swat/SwatWidget.php');
require_once('Swat/SwatFormField.php');

/**
 * Abstract base class for control widgets (non-container)
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
abstract class SwatControl extends SwatWidget {

	public function addMessage($msg) {
		if ($this->parent instanceof SwatFormField)
			$field_title = '<strong>'.$this->parent->title.'</strong>';
		else
			$field_title = '';
 
 		$msg->content = sprintf($msg->content, $field_title);
		
		$this->messages[] = $msg;
	}

	public function gatherMessages() {
		return $this->messages;
	}

	public function hasMessage() {
		return (count($this->messages) > 0);
	}

}

?>
