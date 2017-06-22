<?php

/**
 * A text cell renderer
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTextCellRenderer extends SwatCellRenderer
{
	// {{{ public properties

	/**
	 * The textual content to place within this cell
	 *
	 * Optionally uses vsprintf() syntax, for example:
	 * <code>
	 * $renderer->text = 'Page %s of %s';
	 * </code>
	 *
	 * @var string
	 *
	 * @see SwatTextCellRenderer::$value
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
	 * on the {@link SwatTextCellRenderer::$text} property. If the value
	 * is a string a single sprintf() call is made.
	 *
	 * @var mixed
	 *
	 * @see SwatTextCellRenderer::$text
	 */
	public $value = null;

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

		if ($this->value === null)
			$text = $this->text;
		elseif (is_array($this->value))
			$text = vsprintf($this->text, $this->value);
		else
			$text = sprintf($this->text, $this->value);

		if ($this->content_type === 'text/plain')
			echo SwatString::minimizeEntities($text);
		else
			echo $text;
	}

	// }}}
}

?>
