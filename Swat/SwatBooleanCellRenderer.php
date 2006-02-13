<?php

require_once 'Swat/SwatCellRenderer.php';

/**
 * A cell renderer for a boolean value
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatBooleanCellRenderer extends SwatCellRenderer
{
	/**
	 * Value of this cell
	 *
	 * The boolean value to display in this cell.
	 *
	 * @var boolean
	 */
	public $value;

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		if ((boolean)$this->value) {
			$image_tag = new SwatHtmlTag('img');
			$image_tag->src = 'swat/images/check.png';
			$image_tag->alt = Swat::_('Yes');
			$image_tag->height = '14';
			$image_tag->width = '14';
			$image_tag->display();
		} else {
			echo '&nbsp;';
		}
	}
}

?>
