<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A button widget
 *
 * This widget displays an HTML form submit button, so it must be used within
 * {@link SwatForm}.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatButton extends SwatControl {

	/**
	 * Title
	 *
	 * The visible text on the button.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Clicked (read-only)
	 *
	 * This is set to true after processing if this button was clicked.
	 * The form will also contain a refernce to the clicked button in the
	 * {@link SwatForm::$button} class variable.
	 *
	 * @var boolean
	 */
	public $clicked = false;

	public function init() {
		$this->setTitleFromStock('submit');
	}
	
	public function display() {
		if (!$this->visible)
			return;

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
	 * Set a stock title
	 *
	 * Lookup a stock title for the button and set it as the current title.
	 * @param string $name The shortname of the stock title.
	 */
	public function setTitleFromStock($name) {
		switch ($name) {
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
