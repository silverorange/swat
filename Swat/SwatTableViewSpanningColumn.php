<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatTableViewColumn.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * This is a table view column that gets its own row.
 *
 * @package   Swat
 * @copyright 2005-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewSpanningColumn extends SwatTableViewColumn
{
	// {{{ public properties

	/**
	 * The number of columns to offset to the right
	 *
	 * @var integer
	 */
	public $offset = 0;

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
	// {{{ protected function displayRenderers()

	/**
	 * Renders each cell renderer in this column inside a wrapping XHTML
	 * element
	 *
	 * @param mixed $data the data object being used to render the cell
	 *                     renderers of this field.
	 *
	 * @throws SwatException
	 */
	protected function displayRenderers($row)
	{
		$offset = $this->offset;

		if ($this->title != '') {
			if ($offset == 0)
				$offset = 1;

			$th_tag = new SwatHtmlTag('th', $this->getThAttributes());
			$th_tag->colspan = $offset;
			$th_tag->setContent(
				sprintf(
					Swat::_('%s:'),
					$this->title
				),
				$this->title_content_type
			);
			$th_tag->display();
		} elseif ($offset > 0) {
			$td_tag = new SwatHtmlTag('td');
			$td_tag->colspan = $offset;
			$td_tag->setContent('&nbsp;', 'text/xml');
			$td_tag->display();
		}

		$td_tag = new SwatHtmlTag('td', $this->getTdAttributes());
		$td_tag->colspan =
			$this->view->getXhtmlColspan() - $offset;

		$td_tag->open();
		$this->displayRenderersInternal($row);
		$td_tag->close();
	}

	// }}}
	// {{{ public function getXhtmlColspan()

	/**
	 * Gets how many XHTML table columns this column object spans on display
	 *
	 * @return integer the number of XHTML table columns this column object
	 *                  spans on display.
	 */
	public function getXhtmlColspan()
	{
		// If spanning column has a title or an offset is uses at least 2
		// columns. Otherwise it uses 1 column.
		if ($this->offset > 0 || $this->title != '') {
			$colspan = 2;
		} else {
			$colspan = 1;
		}

		return $colspan;
	}

	// }}}
}

?>
