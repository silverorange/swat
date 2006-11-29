<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatControl.php';

/**
 * Image display control
 *
 * This control simply displays a static image.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatImageDisplay extends SwatControl
{
	// {{{ public properties

	/**
	 * Image
	 *
	 * The src attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $image;

	/**
	 * Optional array of values to substitute into the image property
	 *
	 * Uses vsprintf() syntax, for example:
	 *
	 * <code>
	 * $my_image->image = 'mydir/%s.%s';
	 * $my_image->values = array('myfilename', 'ext');
	 * </code>
	 *
	 * @var array
	 */
	public $values = array();

	/**
	 * Image height
	 *
	 * The height attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $height = null;

	/**
	 * Image width
	 *
	 * The width attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $width = null;

	/**
	 * Image title
	 *
	 * The title attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * Image alt text
	 *
	 * The alt attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $alt = null;

	// }}}
	// {{{ public function display()

	/**
	 * Displays this image
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$image_tag = new SwatHtmlTag('img');
		$image_tag->id = $this->id;
		$image_tag->class = $this->getCSSClassString();

		if (count($this->values))
			$image_tag->src = vsprintf($this->image, $this->values);
		else
			$image_tag->src = $this->image;

		if ($this->height !== null)
			$image_tag->height = $this->height;

		if ($this->width !== null)
			$image_tag->width = $this->width;

		if ($this->title !== null)
			$image_tag->title = $this->title;

		// alt is a required XHTML attribute. We should always display it even
		// if it is not specified.
		$image_tag->alt = ($this->alt === null) ? '' : $this->alt;

		$image_tag->display();
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this image display 
	 *
	 * @return array the array of CSS classes that are applied to this image
	 *                display.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-image-display');
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
}

?>
