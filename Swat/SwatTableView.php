<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatTableViewColumn.php');
require_once('Swat/SwatTableViewRow.php');
require_once('Swat/SwatTableViewRowCheckAll.php');

//TODO: finish documentation for public methods

/**
 * A widget to display data in a tabular form
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatTableView extends SwatControl {
	
	/**
	 * A SwatTableModel to display, or null.
	 * @var SwatTableModel
	 */
	public $model = null;

	/**
	 * Show check all
	 *
	 * Whether to show a "check all" widget.  For this option to work, the
	 * table view must contain a column named "checkbox".
	 * @var boolean
	 */
	public $show_check_all = true;

	/**
	 * Values of the checked checkboxes
	 *
	 * For this to be set, the table view must contain a
	 * {@link SwatCellRendererCheckbox} named "items".
	 * @var Array
	 */
	public $checked_items = array();

	private $columns;
	private $group = null;
	private $extra_rows = array();

	public function init() {
		$this->columns = array();

		if ($this->show_check_all)
			$this->appendRow(new SwatTableViewRowCheckAll());
	}

	/**
	 * Append Column
	 * @param SwatTableViewColumn $column
	 */
	public function appendColumn(SwatTableViewColumn $column) {
		$this->columns[] = $column;
		$column->view = $this;
	}

	/**
	 * Set Group
	 * @param SwatTableViewGroup $group
	 */
	public function setGroup(SwatTableViewGroup $group) {
		$this->group = $group;
		$group->view = $this;
	}

	/**
	 * Append Row
	 * @param SwatTableViewRow $row
	 */
	private function appendRow(SwatTableViewRow $row) {
		$this->extra_rows[] = $row;
	}

	/**
	 * Count columns
	 * @return int Number of columns of the table
	 */
	public function getColumnCount() {
		return count($this->columns);
	}

	public function display() {
		if (!$this->visible)
			return;

		if ($this->model == null)
			return;

		$table_tag = new SwatHtmlTag('table');
		$table_tag->class = 'swat-table-view';

		$table_tag->open();
		$this->displayHeader();
		$this->displayContent();
		$table_tag->close();
	}

	private function displayHeader() {
		echo '<tr>';

		foreach ($this->columns as $column)
			echo '<th>', $column->title, '</th>';

		echo '</tr>';
	}

	private function displayContent() {
		$count = 0;
		$tr_tag = new SwatHtmlTag('tr');

		foreach ($this->model->getRows() as $id => $row) {

			// display the group, if there is one
			if ($this->group != null)
				$this->group->display($row);

			// display a row of data
			$count++;
			$tr_tag->class = ($count % 2 == 1)? 'odd': null;
			$tr_tag->open();

			foreach ($this->columns as $column)
				$column->display($row);

			$tr_tag->close();
		}

		foreach ($this->extra_rows as $row)
			$row->display($this->columns);
	}

	public function process() {
		$items_field = $this->name.'_items';

		if (isset($_POST[$items_field]) && is_array($_POST[$items_field]))
			$this->checked_items = $_POST[$items_field];
	}
}
?>
