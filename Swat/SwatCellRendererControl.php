<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatCellRenderer.php');

/**
 * A renderer for navigational control.
 */
class SwatCellRendererControl extends SwatCellRenderer {

	/**
	 * The href attribute in the HTML anchor tag.
	 * @var string
	 */
	public $href;

	/**
	 * The src attribute in the HTML img tag.
	 * @var string
	 */
	public $src;

	/**
	 * The height attribute in the HTML img tag.
	 * @var int
	 */
	public $height = 0;

	/**
	 * The width attribute in the HTML img tag.
	 * @var int
	 */
	public $width = 0;

	/**
	 * The title attribute in the HTML img tag.
	 * @var string
	 */
	public $title = '';

	/**
	 * The alt attribute in the HTML img tag.
	 * @var string
	 */
	public $alt = '';

	/**
	 * A value to substitute into the href.
	 * @var string
	 */
	public $value = null;

	public function render($prefix) {
		$anchor = new SwatHtmlTag('a');

		if ($this->value == null)
			$anchor->href = $this->href;
		else
			$anchor->href = sprintf($this->href, $this->value);

		$image_tag = new SwatHtmlTag('img');
		$image_tag->src = $this->src;

		if ($this->height > 0)
			$image_tag->height = $this->height;

		if ($this->width > 0)
			$image_tag->width = $this->width;

		if (strlen($this->title) > 0)
			$image_tag->title = _S($this->title);

		if (strlen($this->alt) > 0)
			$image_tag->alt = _S($this->alt);

		$anchor->open();
		$image_tag->display();
		$anchor->close();
	}
}
