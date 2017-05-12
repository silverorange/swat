<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * A cell renderer that displays a message if it is asked to display
 * null text
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatNullTextCellRenderer extends SwatTextCellRenderer
{
	// {{{ public properties

	/**
	 * The text to display in this cell if the
	 * {@link SwatTextCellRenderer::$text} proeprty is null when the render()
	 * method is called
	 *
	 * @var string
	 */
	public $null_text = '&lt;none&gt;';

	/**
	 * Whether to test the {@link SwatTextCellRenderer::$text} property for
	 * null using strict equality.
	 *
	 * @var boolean
	 */
	public $strict = false;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a null text cell renderer
	 */
	public function __construct()
	{
		parent::__construct();

		$this->addStyleSheet(
			'packages/swat/styles/swat-null-text-cell-renderer.css'
		);
	}

	// }}}
	// {{{ public function render()

	/**
	 * Renders this cell renderer
	 */
	public function render()
	{
		if (!$this->visible)
			return;

		$is_null = ($this->strict) ?
			($this->text === null) : ($this->text == null);

		if ($is_null) {
			$this->text = $this->null_text;

			echo '<span class="swat-null-text-cell-renderer">';
			parent::render();
			echo '</span>';

			// Reset the text so that subsequent $is_null checks pass.
			$this->text = null;
		} else {
			parent::render();
		}
	}

	// }}}
}

?>
