<?php

require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatCellRendererContainer.php';
require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatInputCell.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A visible column in a SwatTableView
 *
 * For styling purposes, if this table-view column has an identifier set, a CSS
 * class of this column's identifier is appended to the list of classes on this
 * column's displayed TD tag. The CSS class automatically replaces underscore
 * characters with dashes. For example, if an identifier of 'price_column' is
 * applied to this column, a CSS class of 'price-column' will be added to this
 * column's displayed TD tag.
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewColumn extends SwatCellRendererContainer
	implements SwatUIParent
{
	// {{{ public properties

	/**
	 * Unique identifier of this column
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * Title of this column
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * The {@link SwatTableView} associated with this column
	 *
	 * The table view is the parent of this object.
	 *
	 * @var SwatTableView
	 */
	public $view = null;

	/**
	 * Whether or not this column is displayed
	 *
	 * @var boolean
	 */
	public $visible = true;

	// }}}
	// {{{ protected properties

	/**
	 * An optional {@link SwatInputCell} object for this column
	 * 
	 * If this column's view has a {@link SwatTableViewInputRow} then this
	 * column can contain one input cell for the input row.
	 *
	 * @var array
	 * @see SwatTableViewColumn::setInputCell(),
	 *      SwatTableViewColumn::getInputCell()
	 */
	protected $input_cell = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new table-view column
	 *
	 * @param string $id an optional unique identifier for this column in the
	 *                    table view.
	 */
	public function __construct($id = null)
	{
		$this->id = $id;
		parent::__construct();
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this column
	 *
	 * Gets a unique identifier for this column if one is not provided
	 *
	 * This calls init on all cell renderers and input cells in this column
	 */
	public function init()
	{
		foreach ($this->renderers as $renderer)
			$renderer->init();

		if ($this->id === null)
			$this->id = $this->getUniqueId();

		// add the input cell to this column's view's input row
		if ($this->input_cell !== null) {
			$input_row = $this->parent->getFirstRowByClass('SwatTableViewInputRow');
			if ($input_row === null)
				throw new SwatException('Table-view does not have an input '.
					'row.');

			$input_row->addInputCell($this->input_cell, $this->id);
		}
	}

	// }}}
	// {{{ public function process()

	public function process()
	{
		foreach ($this->renderers as $renderer)
			$renderer->process();
	}

	// }}}
	// {{{ public function displayHeaderCell()

	/**
	 * Displays the table-view header cell for this column
	 */
	public function displayHeaderCell()
	{
		if (!$this->visible)
			return;

		$first_renderer = $this->renderers->getFirst();
		$th_tag = new SwatHtmlTag('th', $first_renderer->getThAttributes());
		$th_tag->scope = 'col';
		$th_tag->open();
		$this->displayHeader();
		$th_tag->close();
	}

	// }}}
	// {{{ public function displayHeader()

	/**
	 * Displays the contents of the header cell for this column
	 */
	public function displayHeader()
	{
		echo SwatString::minimizeEntities($this->title);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this column using a data object
	 *
	 * The properties of the cell renderers are set from the data object
	 * through the datafield property mappings.
	 *
	 * @param mixed $row a data object used to display the cell renderers in
	 *                    this column.
	 */
	public function display($row)
	{
		if (!$this->visible)
			return;

		$this->setupRenderers($row);
		$this->displayRenderers($row);
	}

	// }}}
	// {{{ public function getMessages()

	/**
	 * Gathers all messages from this column for the given data object
	 *
	 * @param mixed $data the data object to use to check this column for
	 *                     messages.
	 *
	 * @return array an array of {@link SwatMessage} objects.
	 */
	public function getMessages($data)
	{
		foreach ($this->renderers as $renderer)
			$this->renderers->applyMappingsToRenderer($renderer, $data);

		$messages = array();
		foreach ($this->renderers as $renderer)
			$messages = array_merge($messages, $renderer->getMessages());

		return $messages;
	}

	// }}}
	// {{{ public function hasMessage()

	/**
	 * Gets whether or not this column has any messages for the given data
	 * object
	 *
	 * @param mixed $data the data object to use to check this column for
	 *                     messages.
	 *
	 * @return boolean true if this table-view column has one or more messages
	 *                  for the given data object and false if it does not.
	 */
	public function hasMessage($data)
	{
		foreach ($this->renderers as $renderer)
			$this->renderers->applyMappingsToRenderer($renderer, $data);

		$has_message = false;
		foreach ($this->renderers as $renderer) {
			if ($renderer->hasMessage()) {
				$has_message = true;
				break;
			}
		}

		return $has_message;
	}

	// }}}
	// {{{ public function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required by this column
	 * 
	 * All inline JavaScript is displayed after the table-view has been
	 * displayed.
	 *
	 * @return string the inline JavaScript required by this column.
	 */
	public function getInlineJavaScript()
	{
		return '';
	}

	// }}}
	// {{{ public function setInputCell()

	/**
	 * Sets the input cell of this column
	 *
	 * @param SwatInputCell $cell the input cell to set for this column.
	 *
	 * @see SwatTableViewColumn::init(), SwatTableViewInputRow
	 */
	public function setInputCell(SwatInputCell $cell)
	{
		$this->input_cell = $cell;
		$cell->parent = $this;
	}

	// }}}
	// {{{ public function getTrAttributes()

	/**
	 * Gets TR-tag attributes
	 *
	 * Subclasses may redefine this to set attributes on the tr tag that wraps
	 * rows using this column.
	 *
	 * The returned array is of the form 'attribute' => 'value'.
	 *
	 * @param mixed $row a data object used to display the cell renderers in
	 *                    this column.
	 *
	 * @return array an array of attributes to apply to the tr tag of the
	 *                row that wraps this column display.
	 */
	public function getTrAttributes($row)
	{
		return array();
	}

	// }}}
	// {{{ public function getInputCell()

	/**
	 * Gets the input cell of this column
	 *
	 * This method is a useful way to get this column's input cell before
	 * init() is called on the UI tree. You can then modify the cell's
	 * prototype widget before init() is called.
	 *
	 * @return SwatInputCell the input cell of this column.
	 *
	 * @see SwatTableViewColumn::setInputCell(),
	 *      SwatInputCell::getPrototypeWidget(), SwatTableViewInputRow
	 */
	public function getInputCell()
	{
		return $this->input_cell;
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Add a child object to this object
	 * 
	 * @param SwatCellRenderer $child the reference to the child object to add.
	 *
	 * @throws SwatException, SwatInvalidClassException
	 *
	 * @see SwatUIParent::addChild()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatCellRenderer) {
			$this->addRenderer($child);
		} elseif ($child instanceof SwatInputCell) {
			if ($this->input_cell === null)
				$this->setInputCell($child);
			else
				throw new SwatException('Only one input cell may be added to '.
					'a table-view column.');
		} else {
			throw new SwatInvalidClassException(
				'Only SwatCellRenderer and SwatInputCell objects may be '.
				'nested within SwatTableViewColumn objects.', 0, $child);
		}
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this column 
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this column.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();
		$renderers = $this->getRenderers();
		foreach ($renderers as $renderer)
			$set->addEntrySet($renderer->getHtmlHeadEntrySet());

		if ($this->input_cell !== null)
			$set->addEntrySet($this->input_cell->getHtmlHeadEntrySet());

		return $set;
	}

	// }}}
	// {{{ protected function displayRenderers()

	/**
	 * Renders each cell renderer in this column
	 *
	 * @param mixed $data the data object being used to render the cell
	 *                     renderers of this field.
	 */
	protected function displayRenderers($data)
	{
		$td_tag = new SwatHtmlTag('td', $this->getTdAttributes());
		$td_tag->open();
		
		foreach ($this->renderers as $renderer) {
			$renderer->render();
			echo ' ';
		}

		$td_tag->close();
	}

	// }}}
	// {{{ protected function setupRenderers()

	/**
	 * Sets properties of renderers using data from current row
	 *
	 * @param mixed $data the data object being used to render the cell
	 *                     renderers of this field.
	 */
	protected function setupRenderers($data)
	{
		if ($this->renderers->getCount() == 0)
			throw new SwatException('No renderer has been provided for this '.
				'column.');

		$sensitive = $this->view->isSensitive();

		// Set the properties of the renderers to the value of the data field.
		foreach ($this->renderers as $renderer) {
			$this->renderers->applyMappingsToRenderer($renderer, $data);
			$renderer->sensitive = $sensitive;
		}
	}

	// }}}
	// {{{ protected function getTdAttributes()

	/**
	 * Gets the TD tag attributes for this column
	 *
	 * The returned array is of the form 'attribute' => value.
	 *
	 * @return array an array of attributes to apply to this column's TD tag.
	 */
	protected function getTdAttributes()
	{
		$first_renderer = $this->renderers->getFirst();
		$attributes = $first_renderer->getTdAttributes();

		if ($this->id !== null) {
			$column_class = str_replace('_', '-', $this->id);
			if (isset($attributes['class']))
				$attributes['class'].= ' '.$column_class;
			else
				$attributes['class'] = $column_class;
		}

		return $attributes;
	}

	// }}}
}

?>
