<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A button widget
 *
 * This widget displays an HTML form submit button, so it must be used within
 * {@link SwatForm}.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatButton extends SwatControl
{
	/**
	 * Title
	 *
	 * The visible text on this button.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Clicked
	 *
	 * This is set to true after processing if this button was clicked.
	 * The form will also contain a refernce to the clicked button in the
	 * {@link SwatForm::$button} class variable.
	 *
	 * @var boolean
	 */
	private $clicked = false;

	/**
	 * Initializes this button
	 *
	 * Sets a default stock title.
	 */
	public function init()
	{
		$this->setTitleFromStock('submit');
	}
	
	/**
	 * Displays this button
	 *
	 * Outputs an XHTML input tag.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'submit';
		$input_tag->name = $this->id;
		$input_tag->value = $this->title;

		$input_tag->display();
	}

	/**
	 * Does button processing
	 *
	 * Sets whether this button has been clicked and also updates the form
	 * this button belongs to with a reference to this button if this button
	 * submitted the form.
	 */
	public function process()
	{
		if (isset($_POST[$this->id])) {
			$this->clicked = true;
			$ancestor = $this->parent;

			while ($ancestor !== null) {
				if ($ancestor instanceof SwatForm)
					$ancestor->button = $this;

				$ancestor = $ancestor->parent;
			}
		}
	}
	
	/**
	 * Returns whether this button has been clicked
	 *
	 * @return boolean whether this button has been clicked.
	 */
	public function hasBeenClicked()
	{
		return $this->clicked;
	}
	
	/**
	 * Sets a stock title
	 *
	 * Looks up a stock title for this button and set it as the current title.
	 *
	 * @param string $name the shortname of the stock title.
	 */
	public function setTitleFromStock($name)
	{
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
