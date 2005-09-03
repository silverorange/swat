<?php

require_once('Swat/SwatTableViewRow.php');
require_once('Swat/SwatCheckAll.php');

/**
 * A an extra row containing a "check all" tool
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewCheckAllRow extends SwatTableViewRow
{
	private $column_id;

	public function __construct($column_id)
	{
		$this->column_id = $column_id;
	}

	/**
	 * Initializes this check-all row
	 */
	public function init()
	{
		if ($this->view !== null)
			$this->view->addJavaScript('swat/javascript/swat-check-all.js');
	}

	public function display(&$columns)
	{
		if ($this->view->model->getRowCount() < 2)
			return;

		echo '<tr>';

		// find column
		$count = 0;
		foreach ($columns as $column) {
			if ($column->id == $this->column_id) {

				$check_all = new SwatCheckAll();
				$check_all->controller = $column;

				break;

			} else {
				$count++;
			}
		}

		if ($count) {

			$td_before_tag = new SwatHtmlTag('td');
			if ($count > 1)
				$td_before_tag->colspan = $count;

			$td_before_tag->open();
			echo '&nbsp;';
			$td_before_tag->close();

		}

		$td_tag = new SwatHtmlTag('td');
		if (count($columns) - $count > 1)
			$td_tag->colspan = count($columns) - $count;

		$td_tag->open();
		$check_all->display();
		$td_tag->close();

		echo '</tr>';
	}

}

?>
