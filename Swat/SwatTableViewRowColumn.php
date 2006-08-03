<?php

require_once 'Swat/SwatTableViewColumn.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * This is a table view column that gets its own row.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewRowColumn extends SwatTableViewColumn
{
	// {{{ public properties

	/**
	 * The number of columns to offset to the right 
	 *
	 * @var integer
	 */
	public $offset = 0;

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
		parent::setupRenderers($data);

		$this->visible = false;

		foreach ($this->renderers as $renderer) {
			if ($renderer->visible === true) {
				$this->visible = true;
				break;
			}
		}
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
		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->class = 'swat-table-view-row-column';
		$tr_tag->open();

		if ($this->offset > 0) {
			$td_tag = new SwatHtmlTag('td');
			$td_tag->colspan = $this->offset;
			$td_tag->display();
		}

		$first_renderer = $this->renderers->getFirst();
		$td_tag = new SwatHtmlTag('td', $first_renderer->getTdAttributes());
		$td_tag->colspan = ($this->view->getVisibleColumnCount() - $this->offset);
		$td_tag->open();
		$this->displayRenderersInternal($row);
		$td_tag->close();
		$tr_tag->close();
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
