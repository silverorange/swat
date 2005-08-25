<?php

require_once 'Swat/SwatCellRenderer.php';

/**
 * A renderer for navigational control
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatControlCellRenderer extends SwatCellRenderer
{
	/**
	 * Link
	 *
	 * The hypertext link this control follows. The href attribute in the
	 * XHTML anchor tag.
	 *
	 * The link may include a sprintf substitution tag. For example:
	 * <code>
	 * $renderer->link = 'MySection/MyPage?id=%s';
	 * </code>
	 *
	 * @var string
	 */
	public $link;

	/**
	 * Image source
	 *
	 * The src attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $image;

	/**
	 * Image height
	 *
	 * The height attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $height = 0;

	/**
	 * Image width
	 *
	 * The width attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $width = 0;

	/**
	 * Image title
	 *
	 * The title attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Alt text
	 *
	 * The alt attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $alt = '';

	/**
	 * Control value
	 *
	 * A value to substitute into the link.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		$anchor = new SwatHtmlTag('a');

		if ($this->value === null)
			$anchor->href = $this->link;
		else
			$anchor->href = sprintf($this->link, $this->value);

		$image_tag = new SwatHtmlTag('img');
		$image_tag->src = $this->image;

		if ($this->height > 0)
			$image_tag->height = $this->height;

		if ($this->width > 0)
			$image_tag->width = $this->width;

		if (strlen($this->title) > 0)
			$image_tag->title = $this->title;

		if (strlen($this->alt) > 0)
			$image_tag->alt = $this->alt;

		$anchor->open();
		$image_tag->display();
		$anchor->close();
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
		return array('class' => 'swat-control-cell-renderer');
	}
}

?>
