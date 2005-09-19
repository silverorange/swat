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
	 * The relative uri of the image file for this image renderer
	 *
	 * This is the src attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $image;

	/**
	 * An optional array of values to substitute into $image
	 *
	 * Uses vsprintf() syntax, for example:
	 *
	 * <code>
	 * $image = 'mydir/%s.%s';
	 * $values = array('myfilename', 'ext');
	 * </code>
	 *
	 * @var array
	 */
	public $values = null;

	/**
	 * The height of the image for this image renderer
	 *
	 * The height attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $height = 0;

	/**
	 * The width of the image for this image renderer
	 *
	 * The width attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $width = 0;

	/**
	 * The title of the image for this image renderer
	 *
	 * The title attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * The alternate text for this image renderer
	 *
	 * This text is used by screen-readers and is required.
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
		$image_tag->src = $this->image;

		if ($this->align !== null)
			$image_tag->align = $this->align;

		if ($this->values !== null)
			$image_tag->image = vsprintf($image_tag->image, $this->values);

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
