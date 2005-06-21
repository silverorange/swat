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
	 * Image height
	 *
	 * The height attribute in the XHTML img tag.
	 *
	 * @var int
	 */
	public $height = 0;

	/**
	 * Image width
	 *
	 * The width attribute in the XHTML img tag.
	 *
	 * @var int
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
	 * Renders the contents of this cell
	 *
	 * @param string $prefix an optional prefix to name XHTML controls with.
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render($prefix = null)
	{
		$image_tag = new SwatHtmlTag('img');
		$image_tag->src = $this->src;

		if ($this->height > 0)
			$image_tag->height = $this->height;

		if ($this->width > 0)
			$image_tag->width = $this->width;

		if (strlen($this->title) > 0)
			$image_tag->title = $this->title;

		if (strlen($this->alt) > 0)
			$image_tag->alt = $this->alt;

		$image_tag->display();
	}
}

?>
