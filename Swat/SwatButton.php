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
	 * The visible text on the button.
	 * @var string
	 */
	public $title;

	public function init() {
		$this->title = $this->getStockTitle('submit');
	}
	
	public function display() {
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'submit';
		$input_tag->name = $this->name;
		$input_tag->value = $this->title;

		$input_tag->display();
	}

	public function getStockTitle($id) {
		switch ($id) {
			case 'submit':
				return _S('Submit');

			case 'create':
				return _S('Create');

			case 'apply':
				return _S('Apply');
		}
	}
}

?>
