<?php

require_once('Swat/SwatControl.php');

/**
 * A block of content in the widget tree
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatContentBlock extends SwatControl {

	/**
	 * Text content of the widget.
	 *
	 * @var string
	 */
	public $content = '';

	public function display() {
		echo $this->content;
	}	

}

?>
