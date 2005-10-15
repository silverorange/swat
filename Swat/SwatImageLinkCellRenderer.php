<?php

require_once 'Swat/SwatImageCellRenderer.php';

/**
 * A renderer that displays a hyperlinked image
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatImageLinkCellRenderer extends SwatImageCellRenderer
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
	 * Link value
	 *
	 * A value to substitute into the link.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		if ($this->sensitive) {
			$anchor = new SwatHtmlTag('a');

			if ($this->value === null)
				$anchor->href = $this->link;
			else
				$anchor->href = sprintf($this->link, $this->value);

			$anchor->open();
		}

		parent::render();

		if ($this->sensitive) {
			$anchor->close();
		}
	}

	/**
	 * Gets TD-tag attributes
	 *
	 * @return array an array of attributes to apply to the TD tag of this cell
	 *                renderer.
	 *
	 * @see SwatCellRenderer::getTdAttributes()
	 */
	public function &getTdAttributes()
	{
		return array('class' => 'swat-image-link-cell-renderer');
	}
}

?>
