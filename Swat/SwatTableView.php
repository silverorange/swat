<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatTableViewColumn.php';
require_once 'Swat/SwatTableViewGroup.php';
require_once 'Swat/SwatTableViewRow.php';
require_once 'Swat/SwatUIParent.php';

/**
 * A widget to display data in a tabular form
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
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
	public $default_orderby_column = null;

	// }}}
	// {{{ private properties

	/**
	 * The columns of this table-view
	 *
	 * @var array
	 */
	private $columns = array();

	/**
	 * The grouping object to use for this table
	 *
	 * @var SwatTableViewGroup
	 *
	 * @see SwatTableView::setGroup()
	 */
	private $group = null;

	/**
	 * Any extra rows that were appended to this view
	 *
	 * This array does not include rows that are displayed based on this
	 * table-view's model.
	 *
	 * @var array
	 */
	private $extra_rows = array();

	// }}}
	// {{{ public function appendColumn()
	
	/**
	 * Appends a column to this table-view
	 *
	 * @param SwatTableViewColumn $column the column to append.
	 */
	public function appendColumn(SwatTableViewColumn $column)
	{
		$this->columns[] = $column;

		$column->view = $this;
		$column->init();
	}

	// }}}
	// {{{ public function setGroup()
	
	/**
	 * Sets the grouping object for this table-view
	 *
	 * The grouping object affects how the data in the table model is displayed
	 * in this table-view. With a grouping, rows are split into groups with
	 * special group headers above each group. 
	 *
	 * @param SwatTableViewGroup $group the table-view grouping to use for this
	 *                                   table-view.
	 *
	 * @see SwatTableViewGroup
	 */
	public function setGroup(SwatTableViewGroup $group)
	{
		$this->group = $group;
		$group->view = $this;
	}

	/**
	 * Gets the grouping object for this table-view
	 *
	 * The grouping object affects how the data in the table model is displayed
	 * in this table-view. With a grouping, rows are split into groups with
	 * special group headers above each group. 
	 *
	 * @returns SwatTableViewGroup the table-view grouping object used for this
	 *                              table-view.
	 *
	 * @see SwatTableViewGroup
	 */
	public function getGroup()
	{
		return $this->group;
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
	 */
	public function appendRow(SwatTableViewRow $row)
	{
		$this->extra_rows[] = $row;

		$row->view = $this;
		$row->init();
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
	// {{{ public function getColumns()
	
	/**
	 * Gets all columns of this table-view as an array
	 *
	 * @return array the columns of this view.
	 */
	public function &getColumns()
	{
		return $this->columns;
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
		$columns = $this->getColumns();
		foreach ($columns as $column)
			if ($id == $column->id)
				return $column;

		throw new SwatException(__CLASS__.": no column with an id of '{$id}' ".
			'exists.');
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

		if ($this->model->getRowCount() == 0) {
			$div = new SwatHtmlTag('div');
			$div->class = 'swat-table-view-no-rows';
			$div->content = 'No Records';
			$div->display();
			return;
		}	

		$table_tag = new SwatHtmlTag('table');
		$table_tag->class = 'swat-table-view';
		$table_tag->id = $this->id;

		$table_tag->open();
		$this->displayHeader();
		$this->displayContent();
		$table_tag->close();
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
		foreach ($this->columns as $column)
			$column->process();

		// TODO: Make this fail gracefully
		$items = $this->getColumn('checkbox');
		$this->checked_items = $items->getItems();
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
	 * or {@link SwatTableView::appendRow()}.
	 *
	 * @param $child a reference to a child object to add.
	 *
	 * @throws SwatException
	 *
	 * @see SwatUIParent, SwatUI, SwatTableView::appendColumn(),
	 *       SwatTableView::setGroup(), SwatTableView::appendRow()
	 */
	public function addChild($child)
	{
		if ($child instanceof SwatTableViewGroup)
			$this->setGroup($child);
		elseif ($child instanceof SwatTableViewRow)
			$this->appendRow($child);
		elseif ($child instanceof SwatTableViewColumn)
			$this->appendColumn($child);
		else
			throw new SwatException(__CLASS__.': Only SwatTableViewColumn, '.
				'SwatTableViewGroup, or SwatTableViewRow objects can be '.
				'nested within SwatTableView objects.');
	}

	// }}}
	// {{{ private function displayHeader()

	/**
	 * Displays the column headers for this table-view
	 *
	 * Each column is asked to display its own header.
	 */
	private function displayHeader()
	{
		echo '<tr>';

		foreach ($this->columns as $column)
			echo '<th>', $column->displayHeader(), '</th>';

		echo '</tr>';
	}

	// }}}
	// {{{ private function displayContent()

	/**
	 * Displays the contents of this view
	 *
	 * The contents reflect the data stored in the model of this table-view.
	 * Things like row highlighting are done here.
	 */
	private function displayContent()
	{
		$count = 0;
		$tr_tag = new SwatHtmlTag('tr');

		foreach ($this->model->getRows() as $id => $row) {

			// display the group, if there is one
			if ($this->group !== null)
				$this->group->display($row);

			// display a row of data
			$count++;
			$tr_tag->class = ($count % 2 == 1) ? 'odd': null;
			$tr_tag->open();

			foreach ($this->columns as $column)
				$column->display($row);

			$tr_tag->close();
		}

		$this->displayJavascript();

		foreach ($this->extra_rows as $row)
			$row->display($this->columns);
	}

	// }}}
	// {{{ private function displayJavascript()

	private function displayJavascript()
	{
		echo '<script type="text/javascript" src="swat/javascript/swat-table-view.js"></script>';

		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";

		echo "\n var {$this->id} = new SwatTableView('{$this->id}');";

		echo "\n//]]>";
		echo '</script>';

		foreach ($this->columns as $column)
			echo $column->displayJavascript();
	}

	// }}}
}

?>
