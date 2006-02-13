<?php

require_once 'Swat/SwatCellRenderer.php';

/**
 * An image renderer
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatImageCellRenderer extends SwatCellRenderer
{
	/**
	 * The relative uri of the image file for this image renderer
	 *
	 * This is the src attribute in the XHTML img tag. It optionally uses
	 * vsprintf() syntax, for example:
	 * <code>
	 * $renderer->image = 'mydir/%s.%s';
	 * $renderer->value = array('myfilename', 'ext');
	 * </code>
	 *
	 * @var string
	 *
	 * @see SwatImageCellRenderer::$value
	 */
	public $image;

	/**
	 * A value or array of values to substitute into the
	 * {@link SwatImageCellRenderer:;$image} property of this cell
	 *
	 * The value property may be specified either as an array of values or as
	 * a single value. If an array is passed, a call to vsprintf() is done
	 * on the {@link SwatImageCellRenderer::$image} property. If the value
	 * is a string a single sprintf() call is made.
	 *
	 * @var mixed
	 *
	 * @see SwatImageCellRenderer::$image
	 */
	public $value = null;

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
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		$image_tag = new SwatHtmlTag('img');

		if ($this->value === null)
			$image_tag->src = $this->image;
		elseif (is_array($this->value))
			$image_tag->src = vsprintf($this->image, $this->value);
		else
			$image_tag->src = sprintf($this->image, $this->value);

		if ($this->height > 0)
			$image_tag->height = $this->height;

		if ($this->width > 0)
			$image_tag->width = $this->width;

		if (strlen($this->title) > 0)
			$image_tag->title = $this->title;

		$image_tag->alt = $this->alt;
		$image_tag->class = 'swat-image-cell-renderer';

		$image_tag->display();
	}
}

?>
