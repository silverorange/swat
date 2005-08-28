<?php

require_once 'Swat/SwatTableViewColumn.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A visible grouping of rows in a table view
 *
 * This is a table view column that gets its own row. It usually makes sense
 * to place it before other table view columns as it is always displayed on a
 * row by itself and never mixed with other columns. This special column is
 * only displayed when the value of the group_by field changes; it is not
 * displayed once for every row.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewGroup extends SwatTableViewColumn
{
	// {{{ public properties

	/**
	 * The field of the table store to group rows by
	 *
	 * @var string
	 */
	public $group_by = null;

	// }}}
	// {{{ private properties

	/**
	 * The current value of the group_by field of the table store
	 *
	 * This value is used so that this column is not displayed for every row
	 * and is only displayed when the value of the table store changes.
	 *
	 * @var mixed
	 */
	private $current = null;

	// }}}
	// {{{ protected function displayRenderers()
	
	/**
	 * Displays the renderers for this column
	 *
	 * The renderes are only displayed once for every time the value of the
	 * group_by field changes and the renderers are displayed on their own
	 * separate table row.
	 *
	 * @param Object $row a data object containing the data for a single row
	 *                     in the table store for this group.
	 *
	 * @throws SwatException
	 */
	protected function displayRenderers($row)
	{

		if ($this->group_by === null)
			throw new SwatException(__CLASS__.': group_by attribute not set');

		$group_by = $this->group_by;

		// only display the group header if the value of the group-by field has changed
		if ($row->$group_by == $this->current)
			return;

		$this->current = $row->$group_by;

		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->open();

		$first_renderer = reset($this->renderers);
		$td_tag = new SwatHtmlTag('td', $first_renderer->getTdAttributes());
		$td_tag->colspan = $this->view->getColumnCount();
		$td_tag->class = 'swat-table-view-group';
		$td_tag->open();

		foreach ($this->renderers as $renderer) {
			$renderer->render();
			echo ' ';
		}

		$td_tag->close();

		$tr_tag->close();
	}

	// }}}
}

?>
