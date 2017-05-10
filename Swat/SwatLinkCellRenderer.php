<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * A link cell renderer
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatLinkCellRenderer extends SwatCellRenderer
{
	// {{{ public properties

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
	 * A value or array of values to substitute into the link of this cell. The
	 * value will automatically be url encoded when it is included in the link.
	 *
	 * @var mixed
	 *
	 * @see SwatLinkCellRenderer::$link
	 * @see SwatLinkCellRenderer::$value
	 */
	public $link_value = null;

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

		parent::render();

		if ($this->isSensitive())
			$this->renderSensitive();
		else
			$this->renderInsensitive();
	}

	// }}}
	// {{{ protected function isSensitive()

	/**
	 * Whether or not this link is sensitive
	 *
	 * Depends on the value of the sensitive property and whether or not a
	 * link is set.
	 *
	 * @return boolean true if this link cell renderer is sensitive and false
	 *                  if it is not.
	 */
	protected function isSensitive()
	{
		return ($this->sensitive && ($this->link !== null));
	}

	// }}}
	// {{{ protected function renderSensitive()

	/**
	 * Renders this link as sensitive
	 */
	protected function renderSensitive()
	{
		$anchor_tag = new SwatHtmlTag('a');
		$anchor_tag->setContent($this->getText(), $this->content_type);
		$anchor_tag->href = $this->getLink();
		$anchor_tag->title = $this->getTitle();
		$anchor_tag->class = $this->getCSSClassString();
		$anchor_tag->display();
	}

	// }}}
	// {{{ protected function renderInsensitive()

	/**
	 * Renders this link as not sensitive
	 */
	protected function renderInsensitive()
	{
		$span_tag = new SwatHtmlTag('span');
		$span_tag->setContent($this->getText(), $this->content_type);
		$span_tag->title = $this->getTitle();
		$span_tag->class = $this->getCSSClassString();
		$span_tag->display();
	}

	// }}}
	// {{{ protected function getTitle()

	protected function getTitle()
	{
		return null;
	}

	// }}}
	// {{{ protected function getText()

	protected function getText()
	{
		if ($this->value === null)
			$text = $this->text;
		elseif (is_array($this->value))
			$text = vsprintf($this->text, $this->value);
		else
			$text = sprintf($this->text, $this->value);

		return $text;
	}

	// }}}
	// {{{ protected function getLink()

	protected function getLink()
	{
		if ($this->link_value === null) {
			$link = $this->link;
		} elseif (is_array($this->link_value)) {
			$link_values = array();

			foreach ($this->link_value as $value)
				$link_values[] = urlencode($value);

			$link = vsprintf($this->link, $link_values);
		} else {
			$link_value = urlencode($this->link_value);
			$link = sprintf($this->link, $link_value);
		}

		return $link;
	}

	// }}}
	// {{{ public function getDataSpecificCSSClassNames()

	/**
	 * Gets the data specific CSS class names for this cell renderer
	 *
	 * @return array the array of base CSS class names for this cell renderer.
	 */
	public function getDataSpecificCSSClassNames()
	{
		$classes = array();

		if (!$this->isSensitive())
			$classes[] = 'swat-link-cell-renderer-insensitive';

		return $classes;
	}

	// }}}
}

?>
