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
	 * The href attribute in the XHTML anchor tag.
	 *
	 * The link may include a sprintf substitution tag. For example:
	 * <code>
	 * $renderer->link = 'MySection/MyPage?id=%s';
	 * </code>
	 *
	 * @var string
	 */
	public $link;

	/**
	 * Link text
	 *
	 * The visible content to place within the XHTML anchor tag.
	 *
	 * @var string
	 */
	public $text;

	/**
	 * Link value
	 *
	 * A value to substitute into the link.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * The CSS class to use for this link cell renderer
	 *
	 * This allows subclasses to set a custom style.
	 *
	 * @var string
	 */
	protected $class = null;

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		if ($this->sensitive) {
			$anchor = new SwatHtmlTag('a');
			$anchor->content = $this->text;

			if ($this->class !== null)
				$anchor->class = $this->class;

			if ($this->value === null)
				$anchor->href = $this->link;
			else
				$anchor->href = sprintf($this->link, $this->value);

			$anchor->display();
		} else {
			$span_tag = new SwatHtmlTag('span');

			if ($this->class !== null)
				$span_tag->class =
					$this->class.' swat-link-cell-renderer-insensitive';
			else
				$span_tag->class = 'swat-link-cell-renderer-insensitive';

			$span_tag->content = $this->text;

			$span_tag->display();
		}
	}
}

?>
