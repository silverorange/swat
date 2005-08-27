<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A button widget
 *
 * This widget displays as an XHTML form submit button, so it should be used
 * within {@link SwatForm}.
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
	 * Looks up a stock title for this button and sets it as the current title.
	 *
	 * Valid stock title ids are:
	 *
	 * - submit
	 * - create
	 * - apply
	 *
	 * @param string $stock_id the identifier of the stock title to use.
	 *
	 * @throws SwatException
	 */
	public function setTitleFromStock($stock_id)
	{
		switch ($stock_id) {
		case 'submit':
			$this->title = Swat::_('Submit');
			break;

		case 'create':
			$this->title = Swat::_('Create');
			break;

		case 'apply':
			$this->title = Swat::_('Apply');
			break;

		default:
			throw new SwatException(sprintf("%s: no stock title with the id ".
				"of '%s' exists.",
				__CLASS__,
				$stock_id));
		}
	}
}

?>
