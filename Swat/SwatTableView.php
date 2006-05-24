<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatTableViewColumn.php';
require_once 'Swat/SwatTableViewOrderableColumn.php';
require_once 'Swat/SwatTableViewGroup.php';
require_once 'Swat/SwatTableViewRow.php';
require_once 'Swat/SwatTableViewInputRow.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/exceptions/SwatDuplicateIdException.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A widget to display data in a tabular form
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableView extends SwatControl implements SwatUIParent
{
	// {{{ public properties

	/**
	 * A data structure that holds the data to display in this view
	 *
	 * The data structure used is some form of {@link SwatTableModel}.
	 *
	 * @var SwatTableModel
	 */
	public $model = null;

	/**
	 * The values of the checked checkboxes
	 *
	 * This array is set in the {@link SwatTableView::process()} method. For
	 * this to be set, this table-view must contain a
	 * {@link SwatCellRendererCheckbox} with an id of "checkbox".
	 *
	 * TODO: Make this private with an accessor method
	 *
	 * @var array
	 */
	public $checked_items = array();

	/**
	 * The column of this table-view that data in the model is currently being
	 * sorted by
	 *
	 * If no sorting is currently happening, this can be null. Alternatively,
	 * this can be set and the column itself may be set to no sorting.
	 *
	 * TODO: Check if this documentation is correct.
	 *
	 * @var SwatTableViewOrderableColumn
	 *
	 * @see SwatTableViewOrderableColumn
	 */
	public $orderby_column = null;

	/**
	 * The column of this table-view that the data in the model is sorted by
	 * by default if no sorting is happening
	 *
	 * Setting this directly usually won't do what you want. Use the
	 * {@link SwatTableView::setDefaultOrderbyColumn()} method instead.
	 *
	 * If this is null then the default order of data in the model is some
	 * implicit order that the user cannot see. This results in tri-state
	 * column headers.
	 *
	 * If this is set then the data ordering is always explicit and visible to
	 * the user. This results in bi-state column headers.
	 *
	 * @var SwatTableViewOrderableColumn
	 *
	 * @see SwatTableViewOrderableColumn,
	 *      SwatTableView::setDefaultOrderbyColumn
	 */
	public $default_orderby_column = null;

	// }}}
	// {{{ private properties

	/**
	 * The columns of this table-view indexed by their unique identifier
	 *
	 * A unique identifier is not required so this array does not necessarily
	 * contain all columns in the view. It serves as an efficient data
	 * structure to lookup columns by their id.
	 *
	 * The array is structured as id => column reference.
	 *
	 * @var array
	 */
	private $columns_by_id = array();

	/**
	 * The groups of this table-view indexed by their unique identifier
	 *
	 * A unique identifier is not required so this array does not necessarily
	 * contain all groups in the view. It serves as an efficient data structure
	 * to lookup groups by their id.
	 *
	 * The array is structured as id => group reference.
	 *
	 * @var array
	 */
	private $groups_by_id = array();

	/**
	 * The extra rows of this table-view indexed by their unique identifier
	 *
	 * A unique identifier is not required so this array does not necessarily
	 * contain all extra rows in the view. It serves as an efficient data
	 * structure to lookup extra rows by their id.
	 *
	 * The array is structured as id => row reference.
	 *
	 * @var array
	 */
	private $rows_by_id = array();

	/**
	 * The columns of this table-view
	 *
	 * @var array
	 */
	private $columns = array();

	/**
	 * Grouping objects for this table view
	 *
	 * @var array
	 *
	 * @see SwatTableView::addGroup()
	 */
	private $groups = array();

	/**
	 * Any extra rows that were appended to this view
	 *
	 * This array does not include rows that are displayed based on this
	 * table-view's model.
	 *
	 * @var array
	 */
	private $extra_rows = array();

	/**
	 * Whether or not this table view has an input row
	 *
	 * Only one input row is allowed for each table-view.
	 *
	 * @var boolean
	 * @see SwatTableViewInputRow
	 */
	private $has_input_row = false;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new table view
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->addJavaScript('swat/javascript/swat-table-view.js');
		$this->addStyleSheet('swat/styles/swat-table-view.css');
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this table-view
	 *
	 * This initializes all columns, extra rows and groupsin this table-view.
	 *
	 * @see SwatWidget::init()
	 */
	public function init()
	{
		parent::init();

		foreach ($this->columns as $column) {
			$column->init();
			// index the column by id if it is not already indexed
			if (!array_key_exists($column->id, $this->columns_by_id))
				$this->columns_by_id[$column->id] = $column;
		}

		foreach ($this->extra_rows as $row)
			$row->init();

		foreach ($this->groups as $group) {
			$group->init();
			// index the group by id if it is not already indexed
			if (!array_key_exists($group->id, $this->groups_by_id))
				$this->groups_by_id[$group->id] = $group;
		}
	}

	// }}}
	// {{{ public function appendColumn()

	/**
	 * Appends a column to this table-view
	 *
	 * @param SwatTableViewColumn $column the column to append.
	 *
	 * @throws SwatDuplicateIdException
	 */
	public function appendColumn(SwatTableViewColumn $column)
	{
		$this->columns[] = $column;

		// note: This works because the id property is set before children are
		// added to parents in SwatUI.
		if ($column->id !== null) {
			if (array_key_exists($column->id, $this->columns_by_id))
				throw new SwatDuplicateIdException(
					"A column with the id '{$column->id}' already exists ".
					'in this table view.',
					0, $column->id);

			$this->columns_by_id[$column->id] = $column;
		}

		$column->view = $this;
		$column->parent = $this;
	}

	// }}}
	// {{{ public function appendGroup()

	/**
	 * Appends a grouping object to this table-view
	 *
	 * A grouping object affects how the data in the table model is displayed
	 * in this table-view. With a grouping, rows are split into groups with
	 * special group headers above each group.
	 *
	 * @param SwatTableViewGroup $group the table-view grouping to use for this
	 *                                   table-view.
	 *
	 * @see SwatTableViewGroup
	 */
	public function appendGroup(SwatTableViewGroup $group)
	{
		$this->groups[] = $group;
		$group->view = $this;
		$group->parent = $this;
	}

	// }}}
	// {{{ public function appendRow()

	/**
	 * Appends a single row to this table-view
	 *
	 * Rows appended to table-views are displayed after all the data from the
	 * table-view model is displayed.
	 *
	 * @param SwatTableViewRow $row the row to append.
	 *
	 * @throws SwatException
	 */
	public function appendRow(SwatTableViewRow $row)
	{
		if ($row instanceof SwatTableViewInputRow && $this->has_input_row)
			throw new SwatException('Only one input row may be added to a '.
				'table view.');
		elseif ($row instanceof SwatTableViewInputRow)
			$this->has_input_row = true;

		$this->extra_rows[] = $row;

		if ($row->id !== null) {
			if (array_key_exists($row->id, $this->rows_by_id))
				throw new SwatDuplicateIdException(
					"A row with the id '{$row->id}' already exists ".
					'in this table-view.',
					0, $row->id);

			$this->rows_by_id[$row->id] = $row;
		}

		$row->view = $this;
		$row->parent = $this;
	}

	// }}}
	// {{{ public function setDefaultOrderbyColumn()

	/**
	 * Sets a default column to use for ordering the data of this table-view
	 *
	 * @param SwatTableViewOrderableColumn the column in this view to use
	 *                                      for default ordering
	 * @param integer $direction the default direction of the ordered column.
	 *
	 * @throws SwatException
	 *
	 * @see SwatTableView::$default_orderby_column
	 */
	public function setDefaultOrderbyColumn(
		SwatTableViewOrderableColumn $column,
		$direction = SwatTableViewOrderableColumn::ORDER_BY_DIR_DESCENDING)
	{
		if ($column->view !== $this)
			throw new SwatException('Can only set the default orderby on '.
				'orderable columns in this view.');

		// this method sets properties on the table-view
		$column->setDirection($direction);
	}

	// }}}
	// {{{ public function getGroups()

	/**
	 * Gets all groups of this table-view as an array
	 *
	 * @return array a reference to the the groups of this view.
	 */
	public function &getGroups()
	{
		return $this->groups;
	}

	// }}}
	// {{{ public function getGroup()

	/**
	 * Gets a reference to a group in this table-view by its unique identifier
	 *
	 * @return SwatTableViewGroup the requested group.
	 *
	 * @throws SwatException
	 */
	public function getGroup($id)
	{
		if (!array_key_exists($id, $this->groups_by_id))
			throw new SwatException("Group with an id of '{$id}' not found.");

		return $this->groups_by_id[$id];
	}

	// }}}
	// {{{ public function hasGroup()

	/**
	 * Returns true if a group with the given id exists within this table-view
	 *
	 * @param string $id the unique identifier of the group within this table-
	 *                    view to check the existance of.
	 *
	 * @return boolean true if the group exists in this table-view and false if
	 *                  it does not.
	 */
	public function hasGroup($id)
	{
		return array_key_exists($id, $this->groups_by_id);
	}

	// }}}
	// {{{ public function getColumnCount()

	/**
	 * Gets the number of columns in this table-view
	 *
	 * @return integer the number of columns of this table-view.
	 */
	public function getColumnCount()
	{
		return count($this->columns);
	}

	// }}}
	// {{{ public function getVisibleColumnCount()

	/**
	 * Gets the number of visible columns in this table-view
	 *
	 * @return integer the number of visible columns of this table-view.
	 */
	public function getVisibleColumnCount()
	{
		return count($this->getVisibleColumns());
	}

	// }}}
	// {{{ public function getColumns()

	/**
	 * Gets all columns of this table-view as an array
	 *
	 * @return array a reference to the the columns of this view.
	 */
	public function &getColumns()
	{
		return $this->columns;
	}

	// }}}
	// {{{ public function getVisibleColumns()

	/**
	 * Gets all visible columns of this table-view as an array
	 *
	 * @return array a reference to the the visible columns of this view.
	 */
	public function &getVisibleColumns()
	{
		$columns = array();
		foreach ($this->columns as $column)
			if ($column->visible)
				$columns[] = $column;

		return $columns;
	}

	// }}}
	// {{{ public function getColumn()

	/**
	 * Gets a reference to a column in this table-view by its unique identifier
	 *
	 * @return SwatTableViewColumn the requested column.
	 *
	 * @throws SwatException
	 */
	public function getColumn($id)
	{
		if (!array_key_exists($id, $this->columns_by_id))
			throw new SwatException("Column with an id of '{$id}' not found.");

		return $this->columns_by_id[$id];
	}

	// }}}
	// {{{ public function hasColumn()

	/**
	 * Returns true if a column with the given id exists within this
	 * table view
	 *
	 * @param string $id the unique identifier of the column within this
	 *                    table view to check the existance of.
	 *
	 * @return boolean true if the column exists in this table view and
	 *                  false if it does not.
	 */
	public function hasColumn($id)
	{
		return array_key_exists($id, $this->columns_by_id);
	}

	// }}}
	// {{{ public function getRow()

	/**
	 * Gets a reference to a row in this table-view by its unique identifier
	 *
	 * @return SwatTableViewRow the requested row.
	 *
	 * @throws SwatException
	 */
	public function getRow($id)
	{
		if (!array_key_exists($id, $this->rows_by_id))
			throw new SwatException("Row with an id of '{$id}' not found.");

		return $this->rows_by_id[$id];
	}

	// }}}
	// {{{ public function getRowsByClass()

	/**
	 * Gets all the extra rows of the specified class from this table-view
	 *
	 * @param string $class_name the class name to filter by.
	 *
	 * @return array all the extra rows of the specified class.
	 */
	public function getRowsByClass($class_name)
	{
		$rows = array();
		foreach ($this->extra_rows as $row)
			if ($row instanceof $class_name)
				$rows[] = $row;

		return $rows;
	}

	// }}}
	// {{{ public function getFirstRowByClass()

	/**
	 * Gets the first extra row of the specified class from this table-view
	 *
	 * @param string $class_name the class name to filter by.
	 *
	 * @return SwatTableViewRow the first extra row of the specified class.
	 */
	public function getFirstRowByClass($class_name)
	{
		$my_row = null;
		foreach ($this->extra_rows as $row) {
			if ($row instanceof $class_name) {
				$my_row = $row;
				break;
			}
		}
		return $my_row;
	}

	// }}}
	// {{{ public function hasRow()

	/**
	 * Returns true if a row with the given id exists within this table-view
	 *
	 * @param string $id the unique identifier of the row within this
	 *                    table-view to check the existance of.
	 *
	 * @return boolean true if the row exists in this table-view and false if
	 *                  it does not.
	 */
	public function hasRow($id)
	{
		return array_key_exists($id, $this->rows_by_id);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this table-view
	 *
	 * The table view is displayed as an XHTML table.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		if ($this->model === null)
			return;

		$show_no_records = true;
		$row_count = $this->model->getRowCount();
		foreach ($this->extra_rows as $row) {
			if ($row->getVisibleByCount($row_count)) {
				$show_no_records = false;
				break;
			}
		}

		if ($row_count == 0 && $show_no_records) {
			$div = new SwatHtmlTag('div');
			$div->class = 'swat-none';
			$div->setContent('<No Records>');
			$div->display();
			return;
		}

		$table_tag = new SwatHtmlTag('table');
		$table_tag->class = 'swat-table-view';
		$table_tag->cellspacing = '0';
		$table_tag->id = $this->id;

		$table_tag->open();
		$this->displayHeader();
		$this->displayFooter();
		$this->displayBody();
		$table_tag->close();

		$this->displayJavaScript();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this table-view
	 *
	 * Processes each column in this view and then sets the checked items of
	 * this table view by getting the items from a special column called
	 * 'checkbox'. If a column with this unique identifier does not exist,
	 * the checked items of this view are set to an empty array.
	 */
	public function process()
	{
		parent::process();

		foreach ($this->columns as $column)
			$column->process();

		foreach ($this->extra_rows as $row)
			$row->process();

		if ($this->hasColumn('checkbox')) {
			$items = $this->getColumn('checkbox');
			$this->checked_items = $items->getItems();
		}
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a child object
	 *
	 * This method fulfills the {@link SwatUIParent} interface. It is used
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere.
	 *
	 * To add columns, rows, or a grouping to a table-view, use
	 * {@link SwatTableView::appendColumn()},
	 * {@link SwatTableView::appendRow()},
	 * or {@link SwatTableView::appendGroup()}.
	 *
	 * @param mixed $child a reference to a child object to add.
	 *
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatUIParent, SwatUI, SwatTableView::appendColumn(),
	 *       SwatTableView::appendGroup(), SwatTableView::appendRow()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatTableViewGroup)
			$this->appendGroup($child);
		elseif ($child instanceof SwatTableViewRow)
			$this->appendRow($child);
		elseif ($child instanceof SwatTableViewColumn)
			$this->appendColumn($child);
		else
			throw new SwatInvalidClassException(
				'Only SwatTableViewColumn, SwatTableViewGroup, or '.
				'SwatTableViewRow objects may be nested within SwatTableView '.
				'objects.', 0, $child);
	}

	// }}}
	// {{{ public function getHtmlHeadEntries()

	/**
	 * Gathers the SwatHtmlHeadEntry objects needed by this table
	 *
	 * @return array the SwatHtmlHeadEntry objects needed by this table.
	 *
	 * @see SwatUIObject::getHtmlHeadEntries()
	 */
	public function getHtmlHeadEntries()
	{
		$out = $this->html_head_entries;

		foreach ($this->columns as $column)
			$out = array_merge($out, $column->getHtmlHeadEntries());

		foreach ($this->extra_rows as $row)
			$out = array_merge($out, $row->getHtmlHeadEntries());

		foreach ($this->groups as $group)
			$out = array_merge($out, $group->getHtmlHeadEntries());

		return $out;
	}

	// }}}
	// {{{ protected function displayHeader()

	/**
	 * Displays the column headers for this table-view
	 *
	 * Each column is asked to display its own header.
	 * Rows in the header are outputted inside a <thead> HTML tag.
	 */
	protected function displayHeader()
	{
		echo '<thead>';
		echo '<tr>';

		foreach ($this->columns as $column)
			$column->displayHeaderCell();

		echo '</tr>';
		echo '</thead>';
	}

	// }}}
	// {{{ protected function displayBody()

	/**
	 * Displays the contents of this view
	 *
	 * The contents reflect the data stored in the model of this table-view.
	 * Things like row highlighting are done here.
	 *
	 * Table rows are displayed inside a <tbody> XHTML tag.
	 */
	protected function displayBody()
	{
		$count = 0;

		echo '<tbody>';

		$tr_tag = new SwatHtmlTag('tr');

		$rows = $this->model->getRows();
		if (is_array($rows))
			$rows = new ArrayIterator($rows);

		$rows->rewind();
		$row = ($rows->valid()) ? $rows->current() : null;

		$rows->next();
		$next_row = ($rows->valid()) ? $rows->current() : null;

		while ($row !== null) {
			$count++;

			// display the groupings
			foreach ($this->groups as $group)
				$group->display($row);

			// display a row of data
			$tr_tag->class = $this->getRowClass($row, $count);
			$tr_tag->open();

			foreach ($this->columns as $column)
				$column->display($row);

			$tr_tag->close();

			$row = $next_row;
			$rows->next();
			$next_row = ($rows->valid()) ? $rows->current() : null;
		}

		echo '</tbody>';
	}

	// }}}
	// {{{ protected function displayFooter()

	/**
	 * Displays any footer content for this table-view
	 *
	 * Rows in the footer are outputted inside a <tfoot> HTML tag.
	 * TODO: Mike, fix the Check-All js and row highlighting now that this has moved around
	 */
	protected function displayFooter()
	{
		ob_start();

		foreach ($this->extra_rows as $row)
			$row->display();

		$footer_content = ob_get_clean();

		if (strlen($footer_content))
			echo '<tfoot>', $footer_content, '</tfoot>';
	}

	// }}}
	// {{{ protected function getRowClass()

	/**
	 * Gets CSS class(es) for the XHTML tr tag.  Can be overridden by subclasses.
	 *
	 * @param mixed $row a data object containing the data to be displayed in
	 *                    this row.
	 * @param integer $count the ordinal position of this row in the table.
	 *
	 * @return string CSS class name(s).
	 */
	protected function getRowClass($row, $count)
	{
		$class = ($count % 2 == 1) ? 'odd': null;
		return $class;
	}

	// }}}
	// {{{ private function displayJavaScript()

	/**
	 * Displays JavaScript required by this table-view as well as any
	 * JavaScript required by columns and/or rows.
	 *
	 * Column JavaSscript is displayed before extra row JavaScript.
	 */
	private function displayJavaScript()
	{
		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";

		echo "var {$this->id} = new SwatTableView('{$this->id}');";

		foreach ($this->columns as $column) {
			$javascript = $column->getInlineJavaScript();
			if (strlen($javascript) > 0)
				echo "\n".$javascript;
		}

		foreach ($this->extra_rows as $row) {
			$javascript = $row->getInlineJavaScript();
			if (strlen($javascript) > 0)
				echo "\n".$javascript;
		}

		echo "\n//]]>";
		echo '</script>';
	}

	// }}}
}

?>
