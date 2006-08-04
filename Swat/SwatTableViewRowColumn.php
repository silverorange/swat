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
		if ($this->offset > 0) {
			$td_tag = new SwatHtmlTag('td');
			$td_tag->colspan = $this->offset;
			$td_tag->setContent('&nbsp', 'text/xml');
			$td_tag->display();
		}

		$first_renderer = $this->renderers->getFirst();
		$td_tag = new SwatHtmlTag('td', $first_renderer->getTdAttributes());
		$td_tag->colspan =
			$this->view->getVisibleColumnCount() - $this->offset;

		$td_tag->open();
		$this->displayRenderersInternal($row);
		$td_tag->close();
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
	// {{{ public function hasVisibleRenderer()

	/**
	 * Whether or not this row-column has one or more visible cell renderers
	 *
	 * @param mixed $row a data object containing the data for a single row
	 *                    in the table store for this group. This object may
	 *                    affect the visibility of renderers in this row-
	 *                    column.
	 *
	 * @return boolean true if this row-column has one or more visible cell
	 *                  renderers and false if it does not.
	 */
	public function hasVisibleRenderer($row)
	{
		$this->setupRenderers($row);

		$visible_renderers = false;

		foreach ($this->renderers as $renderer) {
			if ($renderer->visible) {
				$visible_renderers = true;
				break;
			}
		}

		return $visible_renderers;
	}

	// }}}
}

?>
