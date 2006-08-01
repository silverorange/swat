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
	// {{{ public function display()

	/**
	 * Displays this column using a data object
	 *
	 * The properties of the cell renderers are set from the data object
	 * through the datafield property mappings. A SwatTableViewRowColumn is
	 * automatically hidden if no visible cell renderers are provided.
	 *
	 * @param mixed $row a data object used to display the cell renderers in
	 *                    this column.
	 */
	public function display($row)
	{
		if ($this->renderers->getCount() == 0)
			throw new SwatException('No renderer has been provided for this '.
				'column.');

		$visible_renderers = false;

		foreach ($this->renderers as $renderer) {
			$this->renderers->applyMappingsToRenderer($renderer, $row);
			if ($renderer->visible == true)
				$visible_renderers = true;
		}

		$this->visible = $visible_renderers;

		parent::display($row);
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
