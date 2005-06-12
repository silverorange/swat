<?php

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A link cell renderer
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatLinkCellRenderer extends SwatCellRenderer
{
	/**
	 * Link href
	 *
	 * The href attribute in the HTML anchor tag.
	 *
	 * @var string
	 */
	public $href;

	/**
	 * Link title
	 *
	 * The visible content to place within the HTML anchor tag.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Link value
	 *
	 * A value to substitute into the href.
	 * For example href: "MySection/MyPage?id=%s"
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Renders the contents of this cell
	 *
	 * @param string $prefix an optional prefix to name XHTML controls with.
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render($prefix)
	{
		$anchor = new SwatHtmlTag('a');
		$anchor->content = $this->title;

		if ($this->value === null)
			$anchor->href = $this->href;
		else
			$anchor->href = sprintf($this->href, $this->value);

		$anchor->display();
	}
}

?>
