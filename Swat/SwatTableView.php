<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatTableViewColumn.php');
require_once('Swat/SwatTableViewRow.php');
require_once('Swat/SwatTableViewRowCheckAll.php');

/**
 * A widget to display data in a tabular form.
 */
class SwatTableView extends SwatControl {
	
	/**
	 * A SwatTableModel to display, or null.
	 * @var SwatTableModel
	 */
	public $model = null;

	/**
	 * Whether to show a "check all" widget.  For this option to work, the
	 * table view must contain a column named "checkbox".
	 * @var boolean
	 */
	public $show_check_all = true;

	/**
	 * The values of the checked checkboxes.  For this to be set, the table
	 * view must contain a SwatCellRendererCheckbox named "items".
	 * @var Array
	 */
	public $checked_items = array();

	private $extra_rows = array();
	private $columns;

	public function init() {
		$this->columns = array();

		if ($this->show_check_all)
			$this->appendRow(new SwatTableViewRowCheckAll());
	}

	public function appendColumn(SwatTableViewColumn $column) {
		$this->columns[] = $column;
		$column->view = $this;
	}

	private function appendRow(SwatTableViewRow $row) {
		$this->extra_rows[] = $row;
	}

	public function display() {
		if ($this->model == null) return;

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

		foreach ($this->model->getRows() as $id => $row) {

			$count++;
			echo ($count % 2 == 1) ? '<tr class="odd">' : '<tr>';

			foreach ($this->columns as $column)
				$column->display($row);

			echo '</tr>';

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
