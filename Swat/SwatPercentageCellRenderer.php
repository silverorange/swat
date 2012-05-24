<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatNumericCellRenderer.php';
require_once 'Swat/SwatString.php';

/**
 * A percentage cell renderer
 *
 * @package   Swat
 * @copyright 20062012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatPercentageCellRenderer extends SwatNumericCellRenderer
{
	// {{{ public function render()

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render(SwatDisplayContext $context)
	{
		if (!$this->visible) {
			return;
		}

		SwatCellRenderer::render($context);

		$old_value = $this->value;
		$this->value = $this->value * 100;
		$context->out(sprintf('%s%%', $this->getDisplayValue()));
		$this->value = $old_value;
	}

	// }}}
}

?>
