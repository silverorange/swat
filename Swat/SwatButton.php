<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A form submit button.
 */
class SwatButton extends SwatControl {

	/**
	 * @var string The visible text on the button.
	 */
	public $title = 'Submit';
	
	function display() {
		$inputtag = new SwatHtmlTag('input');
		$inputtag->type = 'submit';
		$inputtag->name = $this->name;
		$inputtag->value = $this->title;

		$inputtag->display();
	}	

}

?>
