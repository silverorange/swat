<?php
require_once('Swat/SwatCellRenderer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A link renderer
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatCellRendererLink extends SwatCellRenderer {

	/**
	 * Link href
	 *
	 * The href attribute in the HTML anchor tag.
	 * @var string
	 */
	public $href;

	/**
	 * Link content
	 *
	 * The content to place within the HTML anchor tag. In a SwatUI XML file 
	 * this comes from the content of the SwatCellRendererLink tag.
	 * @var string
	 */
	public $content;

	/**
	 * Link value
	 *
	 * A value to substitute into the href.
	 * example href: "MySection/MyPage?id=%s"
	 * @var string
	 */
	public $value = null;

	public function render($prefix) {
		$anchor = new SwatHtmlTag('a');
		$anchor->content = $this->content;

		if ($this->value == null)
			$anchor->href = $this->href;
		else
			$anchor->href = sprintf($this->href, $this->value);

		$anchor->display();
	}
}
