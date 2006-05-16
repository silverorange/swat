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
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewColumn extends SwatCellRendererContainer
	implements SwatUIParent
{
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

	public function process()
	{
		foreach ($this->renderers as $renderer)
			$renderer->process();
	}

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

	/**
	 * Displays the contents of the header cell for this column
	 */
	public function displayHeader()
	{
		echo SwatString::minimizeEntities($this->title);
	}

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

		if ($this->renderers->getCount() == 0)
			throw new SwatException('No renderer has been provided for this '.
				'column.');

		$sensitive = $this->view->isSensitive();

		// Set the properties of the renderers to the value of the data field.
		foreach ($this->renderers as $renderer) {
			$this->renderers->applyMappingsToRenderer($renderer, $row);
			$renderer->sensitive = $sensitive;
		}

		$this->displayRenderers($row);
	}

	/**
	 * Renders each cell renderer in this column
	 *
	 * @param mixed $data the data object being used to render the cell
	 *                     renderers of this field.
	 */
	protected function displayRenderers($data)
	{
		$first_renderer = $this->renderers->getFirst();
		$td_tag = new SwatHtmlTag('td', $first_renderer->getTdAttributes());
		$td_tag->open();

		foreach ($this->renderers as $renderer) {
			$renderer->render();
			echo ' ';
		}

		$td_tag->close();
	}

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

	/**
	 * Gathers the SwatHtmlHeadEntry objects needed by this column 
	 *
	 * @return array the SwatHtmlHeadEntry objects needed by this column.
	 *
	 * @see SwatUIObject::getHtmlHeadEntries()
	 */
	public function getHtmlHeadEntries()
	{
		$out = $this->html_head_entries;
		$renderers = $this->getRenderers();
		foreach ($renderers as $renderer)
			$out = array_merge($out, $renderer->getHtmlHeadEntries());

		if ($this->input_cell !== null)
			$out = array_merge($out,
				$this->input_cell->getHtmlHeadEntries());

		return $out;
	}
}

?>
