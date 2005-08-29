<?php

require_once 'Swat/SwatCellRenderer.php';

/**
 * A cell renderer for a boolean value
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckCellRenderer extends SwatCellRenderer
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

	/**
	 * Gets TD-tag attributes
	 *
	 * @return array an array of attributes to apply to the TD tag of this cell
	 *                renderer.
	 *
	 * @see SwatCellRenderer::getTdAttributes()
	 */
	public function &getTdAttributes()
	{
		return array('style' => 'text-align: center;');
	}

	/**
	 * Gets TH-tag attributes
	 *
	 * @return array an array of attributes to apply to the TH tag in the
	 *                table header for this cell renderer.
	 *
	 * @see SwatCellRenderer::getThAttributes()
	 */
	public function &getThAttributes()
	{
		return array('style' => 'text-align: center;');
	}
}

?>
