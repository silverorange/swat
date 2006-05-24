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
 * @copyright 2005 silverorange
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
	// {{{ protected function displayGroupHeader()

	/**
	 * Displays the group header for this grouping column
	 *
	 * The grouping header is displayed at the beginning of a group.
	 *
	 * @param mixed $row a data object containing the data for the first row in
	 *                    in the table store for this group.
	 */
	protected function displayGroupHeader($row)
	{
		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->open();

		$first_renderer = $this->renderers->getFirst();
		$td_tag = new SwatHtmlTag('td', $first_renderer->getTdAttributes());
		$td_tag->colspan = $this->view->getVisibleColumnCount();
		$td_tag->class = 'swat-table-view-group';
		$td_tag->open();
		$this->displayRenderersInternal($row);
		$td_tag->close();
		$tr_tag->close();
	}

	// }}}
	// {{{ protected function displayGroupFooter()

	/**
	 * Displays the group footer for this grouping column
	 *
	 * The grouping footer is displayed at the end of a group. By default, no
	 * footer is displayed. Subclasses may display a grouping footer by
	 * overriding this method.
	 *
	 * @param mixed $row a data object containing the data for the last row in
	 *                    in the table store for this group.
	 */
	protected function displayGroupFooter($row)
	{
	}

	// }}}
	// {{{ protected function displayRenderers()
	
	/**
	 * Displays the renderers for this column
	 *
	 * The renderes are only displayed once for every time the value of the
	 * group_by field changes and the renderers are displayed on their own
	 * separate table row.
	 *
	 * @param mixed $row a data object containing the data for a single row
	 *                    in the table store for this group.
	 *
	 * @throws SwatException
	 */
	protected function displayRenderers($row)
	{
		if ($this->group_by === null)
			throw new SwatException("Attribute 'group_by' must be set.");

		$group_by = $this->group_by;

		// only display the group header if the value of the group-by field has
		// changed
		if ($row->$group_by === $this->current)
			return;

		$this->current = $row->$group_by;

		$this->displayGroupHeader($row);
	}

	// }}}
	// {{{ protected function displayRenderersInternal()

	protected function displayRenderersInternal($row)
	{
		foreach ($this->renderers as $renderer) {
			$renderer->render();
			echo ' ';
		}	
	}

	// }}}
}

?>
