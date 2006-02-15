<?php

require_once 'Swat/SwatCellRenderer.php';

/**
 * A cell renderer for a boolean value
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatBooleanCellRenderer extends SwatCellRenderer
{
	/**
	 * Value of this cell
	 *
	 * The boolean value to display in this cell.
	 *
	 * @var boolean
	 */
	public $value;

	/**
	 * Optional content to display for a true value.
	 *
	 * @var string
	 */
	public $true_content = null;

	/**
	 * Optional content to display for a false value.
	 *
	 * @var string
	 */
	public $false_content = null;

	/**
	 * Optional content type
	 *
	 * Default text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type;

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		if ((boolean)$this->value)
			$this->renderTrue();
		else
			$this->renderFalse();
	}

	protected function renderTrue()
	{
		if ($this->true_content !== null) {
			if ($this->content_type === 'text/plain')
				echo SwatString::minimizeEntities($this->true_content);
			else
				echo $this->true_content;
		} else {
			$image_tag = new SwatHtmlTag('img');
			$image_tag->src = 'swat/images/check.png';
			$image_tag->alt = Swat::_('Yes');
			$image_tag->height = '14';
			$image_tag->width = '14';
			$image_tag->display();
		}
	}

	protected function renderFalse()
	{
		if ($this->false_content !== null) {
			if ($this->content_type === 'text/plain')
				echo SwatString::minimizeEntities($this->false_content);
			else
				echo $this->false_content;
		} else {
			echo '&nbsp;';
		}
	}
}

?>
