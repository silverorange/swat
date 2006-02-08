<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/exceptions/SwatUndefinedStockTypeException.php';

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
	public $title = null;

	/**
	 * The custom CSS class of this tool link
	 *
	 * This optional class is added on top of the default 'swat-button'
	 * class.
	 *
	 * @var string
	 *
	 * @see SwatButton::setFromStock()
	 */
	public $class = null;

	/**
	 * The stock id of this button
	 *
	 * Specifying a stock id initializes this button with a set of stock values.
	 *
	 * @var string
	 *
	 * @see SwatToolLink::setFromStock()
	 */
	public $stock_id = null;

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
	 * Initializes this widget
	 *
	 * Loads properties from stock if $stock_id is set, otherwise sets a 
	 * default stock title.
	 *
	 * @see SwatWidget::init()
	 */
	public function init()
	{
		parent::init();

		if ($this->stock_id === null) 
			$this->setFromStock('submit', false);
		else
			$this->setFromStock($this->stock_id, false);
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

		$form = $this->getFirstAncestor('SwatForm');
		$primary = ($form !== null &&
			$form->getFirstDescendant('SwatButton') === $this);

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'submit';
		$input_tag->name = $this->id;
		$input_tag->value = $this->title;

		if ($primary)
			$input_tag->class = 'swat-button swat-primary';
		else
			$input_tag->class = 'swat-button';

		if ($this->class !== null)
			$input_tag->class.= ' '.$this->class;

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
	 * Sets the values of this button to a stock type
	 *
	 * Valid stock type ids are:
	 *
	 * - submit
	 * - create
	 * - add
	 * - apply
	 * - delete
	 * - cancel
	 *
	 * @param string $stock_id the identifier of the stock type to use.
	 * @param boolean $overwrite_properties whether to overwrite properties if
	 *                                       they are already set.
	 *
	 * @throws SwatUndefinedStockTypeException
	 */
	public function setFromStock($stock_id, $overwrite_properties = true)
	{
		switch ($stock_id) {
		case 'submit':
			$title = Swat::_('Submit');
			$class = 'swat-button-submit';
			break;

		case 'create':
			$title = Swat::_('Create');
			$class = 'swat-button-create';
			break;

		case 'add':
			$title = Swat::_('Add');
			$class = 'swat-button-add';
			break;

		case 'apply':
			$title = Swat::_('Apply');
			$class = 'swat-button-apply';
			break;

		case 'delete':
			$title = Swat::_('Delete');
			$class = 'swat-button-delete';
			break;

		case 'cancel':
			$title = Swat::_('Cancel');
			$class = 'swat-button-cancel';
			break;

		default:
			throw new SwatUndefinedStockTypeException(
				"Stock type with id of '{$stock_id}' not found.",
				0, $stock_id);
		}

		if ($overwrite_properties || ($this->title === null))
			$this->title = $title;

		if ($overwrite_properties || ($this->class === null))
			$this->class = $class;
	}
}

?>
