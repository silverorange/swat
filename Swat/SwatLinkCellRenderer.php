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
	 * The href attribute in the XHTML anchor tag
	 *
	 * Optionally uses vsprintf() syntax, for example:
	 * <code>
	 * $renderer->link = 'MySection/MyPage/%s?id=%s';
	 * </code>
	 *
	 * @var string
	 *
	 * @see SwatLinkCellRenderer::$link_value
	 */
	public $link;

	/**
	 * The visible content to place within the XHTML anchor tag
	 *
	 * Optionally uses vsprintf() syntax, for example:
	 * <code>
	 * $renderer->text = 'Page %s of %s';
	 * </code>
	 *
	 * @var string
	 *
	 * @see SwatLinkCellRenderer::$value
	 */
	public $text = '';

	/**
	 * Optional content type
	 *
	 * Default text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = 'text/plain';

	/**
	 * A value or array of values to substitute into the text of this cell
	 *
	 * The value property may be specified either as an array of values or as
	 * a single value. If an array is passed, a call to vsprintf() is done
	 * on the {@link SwatLinkCellRenderer::$text} property. If the value
	 * is a string a single sprintf() call is made.
	 *
	 * @var mixed
	 *
	 * @see SwatLinkCellRenderer::$text
	 */
	public $value = null;

	/**
	 * A value or array of values to substitute into the link of this cell
	 *
	 * @var mixed
	 *
	 * @see SwatLinkCellRenderer::$link, SwatLinkCellRenderer::$value
	 */
	public $link_value = null;

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
		if ($this->sensitive && ($this->link !== null)) {
			$anchor = new SwatHtmlTag('a');

			if ($this->class !== null)
				$anchor->class = $this->class;

			if ($this->value === null)
				$text = $this->text;
			elseif (is_array($this->value))
				$text = vsprintf($this->text, $this->value);
			else
				$text = sprintf($this->text, $this->value);

			$anchor->setContent($text, $this->content_type);

			if ($this->link_value === null)
				$anchor->href = $this->link;
			elseif (is_array($this->link_value))
				$anchor->href = vsprintf($this->link, $this->link_value);
			else
				$anchor->href = sprintf($this->link, $this->link_value);

			$anchor->display();
		} else {
			$span_tag = new SwatHtmlTag('span');

			if ($this->class !== null)
				$span_tag->class =
					$this->class.' swat-link-cell-renderer-insensitive';
			else
				$span_tag->class = 'swat-link-cell-renderer-insensitive';

			if ($this->value === null)
				$text = $this->text;
			elseif (is_array($this->value))
				$text = vsprintf($this->text, $this->value);
			else
				$text = sprintf($this->text, $this->value);

			$span_tag->setContent($text, $this->content_type);
			$span_tag->display();
		}
	}
}

?>
