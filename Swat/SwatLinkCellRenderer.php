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
class SwatLinkCellRenderer extends SwatCellRenderer {

	/**
	 * Link href
	 *
	 * The href attribute in the HTML anchor tag.
	 * @var string
	 */
	public $href;

	/**
	 * Link title
	 *
	 * The visible content to place within the HTML anchor tag.
	 * @var string
	 */
	public $title;

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
		$anchor->content = $this->title;

		if ($this->value === null)
			$anchor->href = $this->href;
		else
			$anchor->href = sprintf($this->href, $this->value);

		$anchor->display();
	}
}
