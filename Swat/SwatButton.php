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

	/**
	 * Set true after processing if this button was clicked (read only).
	 * The form will also contain a refernce to the clicked button in the
	 * SwatForm::$button class variable.
	 * @var boolean
	 */
	public $clicked = false;

	public function init() {
		$this->setTitleFromStock('submit');
	}
	
	public function display() {
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'submit';
		$input_tag->name = $this->name;
		$input_tag->value = $this->title;

		$input_tag->display();
	}

	public function process() {
		if (isset($_POST[$this->name])) {
			$this->clicked = true;
			$ancestor = $this->parent;

			while ($ancestor != null) {
				if ($ancestor instanceof SwatForm)
					$ancestor->button = $this;

				$ancestor = $ancestor->parent;
			}
		}
	}

	/**
	 * Set a stock title.
	 * Lookup a stock title for the button and set it as the current title.
	 * @param id string The shortname of the stock title.
	 */
	public function setTitleFromStock($id) {
		switch ($id) {
			case 'submit':
				$this->title = _S('Submit');
				break;

			case 'create':
				$this->title = _S('Create');
				break;

			case 'apply':
				$this->title = _S('Apply');
				break;
		}
	}
}

?>
