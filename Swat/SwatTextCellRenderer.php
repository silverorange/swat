<?php

require_once 'Swat/SwatCellRenderer.php';

/**
 * A text cell renderer
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTextCellRenderer extends SwatCellRenderer
{
	/**
	 * Cell content
	 *
	 * The textual content to place within this cell.
	 *
	 * @var string
	 */
	public $text = '';

	/**
	 * A value to substitute into the text of this cell
	 *
	 * The value is substituted using a call to printf. For example, if the
	 * {@link SwatCellRendererText::$text} property is set to 'My %s example'
	 * and the value property is set to 'awesome' the cell renderer will render
	 * as 'My awesome example'.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Renders the contents of this cell
	 *
	 * @see swatcellrenderer::render()
	 */
	public function render()
	{
		if ($this->value === null)
			$text = $this->text;
		else
			$text = sprintf($this->text, $this->value);

		echo SwatString::minimizeEntities($text);
	}
}

?>
