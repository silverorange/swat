<?php

require_once 'Swat/SwatCellRenderer.php';

/**
 * An image renderer
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatImageCellRenderer extends SwatCellRenderer
{
	/**
	 * Image src
	 *
	 * The src attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $src;

	/**
	 * Optional array of values to for $src
	 *
	 * Uses vsprintf() syntax, for example: $src = mydir/%s.%s; $values =
	 * array('myfilename', 'ext');
	 *
	 * @var array
	 */
	public $values = null;

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
	 * Image alt text
	 *
	 * The alt attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $alt = '';

	/**
	 * How text should align with the images rendered by this renderer
	 *
	 * Valid values are:
	 *
	 * - middle
	 * - left
	 * - right
	 *
	 * @var string
	 */
	public $align = null;

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		$image_tag = new SwatHtmlTag('img');
		$image_tag->src = $this->src;

		if ($this->align !== null)
			$image_tag->align = $this->align;

		if ($this->values !== null)
			$image_tag->src = vsprintf($image_tag->src, $this->values);

		if ($this->height > 0)
			$image_tag->height = $this->height;

		if ($this->width > 0)
			$image_tag->width = $this->width;

		if (strlen($this->title) > 0)
			$image_tag->title = $this->title;

		$image_tag->alt = $this->alt;

		$image_tag->display();
	}
}

?>
