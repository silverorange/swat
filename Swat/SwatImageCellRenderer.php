<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

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
	// {{{ public properties

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
	public $height = null;

	/**
	 * The width of the image for this image renderer
	 *
	 * The width attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $width = null;

	/**
	 * The title of the image for this image renderer
	 *
	 * The title attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * The alternate text for this image renderer
	 *
	 * This text is used by screen-readers and is required.
	 *
	 * The alt attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $alt = null;

	// }}}
	// {{{ public function render()

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		if (!$this->visible)
			return;

		$image_tag = new SwatHtmlTag('img');

		if ($this->value === null)
			$image_tag->src = $this->image;
		elseif (is_array($this->value))
			$image_tag->src = vsprintf($this->image, $this->value);
		else
			$image_tag->src = sprintf($this->image, $this->value);

		$image_tag->height = $this->height;
		$image_tag->width = $this->width;
		$image_tag->title = $this->title;

		// alt is a required XHTML attribute. We should always display it even
		// if it is not specified.
		$image_tag->alt = ($this->alt === null) ? '' : $this->alt;

		$image_tag->class = $this->getCSSClassString();

		$image_tag->display();
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this cell renderer 
	 *
	 * @return array the array of CSS classes that are applied to this cell renderer.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-image-cell-renderer');
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
}

?>
