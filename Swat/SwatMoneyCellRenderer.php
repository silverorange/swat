<?php

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatString.php';

/**
 * A currency cell renderer
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMoneyCellRenderer extends SwatCellRenderer
{
	// {{{ public properties

	/**
	 * Optional locale for currency format
	 *
	 * @var string
	 */
	public $locale = null;

	/**
	 * Monetary value
	 * 
	 * @var float
	 */
	public $value;

	/**
	 * Whether to display currency unit
	 *
	 * If true, displays the international currency unit
	 *
	 * @var boolean
	 */
	public $display_currency = false;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a money cell renderer
	 */
	public function __construct()
	{
		parent::__construct();
		$this->addStyleSheet(
			'packages/swat/styles/swat-money-cell-renderer.css');
	}

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

		echo SwatString::minimizeEntities(
			SwatString::moneyFormat(
				$this->value, $this->locale, $this->display_currency));
	}

	// }}}
}

?>
