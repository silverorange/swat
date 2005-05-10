<?php

require_once('Swat/SwatCellRenderer.php');

/**
 * A renderer for navigational control.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatControlCellRenderer extends SwatCellRenderer {

	/**
	 * Href anchor
	 *
	 * The href attribute in the HTML anchor tag.
	 * @var string
	 */
	public $href;

	/**
	 * Image source
	 *
	 * The src attribute in the HTML img tag.
	 * @var string
	 */
	public $src;

	/**
	 * Image height
	 *
	 * The height attribute in the HTML img tag.
	 * @var int
	 */
	public $height = 0;

	/**
	 * Image width
	 *
	 * The width attribute in the HTML img tag.
	 * @var int
	 */
	public $width = 0;

	/**
	 * Image title
	 *
	 * The title attribute in the HTML img tag.
	 * @var string
	 */
	public $title = '';

	/**
	 * Alt text
	 *
	 * The alt attribute in the HTML img tag.
	 * @var string
	 */
	public $alt = '';

	/**
	 * HREF value
	 *
	 * A value to substitute into the href using sprintf()
	 * example href: "MySection/MyPage?id=%s"
	 * @var string
	 */
	public $value = null;

	public function render($prefix) {
		$anchor = new SwatHtmlTag('a');

		if ($this->value === null)
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

?>
