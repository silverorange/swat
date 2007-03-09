<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/exceptions/SwatUndefinedStockTypeException.php';

/**
 * A button widget
 *
 * This widget displays as an XHTML form submit button, so it should be used
 * within {@link SwatForm}.
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatButton extends SwatInputControl
{
	// {{{ public properties

	/**
	 * Title
	 *
	 * The visible text on this button.
	 *
	 * @var string
	 */
	public $title = null;

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
	 * Access key
	 *
	 * Access key for this button, for keyboard nagivation.
	 *
	 * @var string
	 */
	public $access_key = null;

	/**
	 * Tab index
	 *
	 * The ordinal tab index position of the XHTML input tag, or null.
	 *
	 * @var integer
	 */
	public $tab_index = null;

	// }}}
	// {{{ protected properties

	/**
	 * A CSS class set by the stock_id of this button
	 *
	 * @var string
	 */
	protected $stock_class = null;

	// }}}
	// {{{ private properties

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

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new button 
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;
	}

	// }}}
	// {{{ public function init()

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

	// }}}
	// {{{ public function display()

	/**
	 * Displays this button
	 *
	 * Outputs an XHTML input tag.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		// We don't use a button element because it is broken differently in
		// IE6 and IE7
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'submit';
		$input_tag->name = $this->id;
		$input_tag->id = $this->id;
		$input_tag->value = $this->title;
		$input_tag->class = $this->getCSSClassString();
		$input_tag->tabindex = $this->tab_index;
		$input_tag->accesskey = $this->access_key;

		if (!$this->isSensitive())
			$input_tag->disabled = 'disabled';

		$input_tag->display();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Does button processing
	 *
	 * Sets whether this button has been clicked and also updates the form
	 * this button belongs to with a reference to this button if this button
	 * submitted the form.
	 */
	public function process()
	{
		parent::process();

		$data = &$this->getForm()->getFormData();

		if (isset($data[$this->id])) {
			$this->clicked = true;
			$this->getForm()->button = $this;
		}
	}

	// }}}
	// {{{ public function hasBeenClicked()

	/**
	 * Returns whether this button has been clicked
	 *
	 * @return boolean whether this button has been clicked.
	 */
	public function hasBeenClicked()
	{
		return $this->clicked;
	}

	// }}}
	// {{{ public function setFromStock()

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

		$this->stock_class = $class;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this button
	 *
	 * @return array the array of CSS classes that are applied to this button.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-button');

		$form = $this->getFirstAncestor('SwatForm');
		$primary = ($form !== null &&
			$form->getFirstDescendant('SwatButton') === $this);

		if ($primary)
			$classes[] = 'swat-primary';

		if (!$this->isSensitive())
			$classes[] = 'swat-insensitive';

		if ($this->stock_class !== null)
			$classes[] = $this->stock_class;

		$classes = array_merge($classes, $this->classes);

		return $classes;
	}

	// }}}
}

?>
