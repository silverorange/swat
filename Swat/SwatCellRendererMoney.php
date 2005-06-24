<?php
require_once('Swat/SwatCellRenderer.php');
require_once('Swat/SwatCurrency.php');

/**
 * A currency renderer
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatCellRendererMoney extends SwatCellRenderer {

	/**
	 * Optional locale for currency format
	 *
	 * @var string
	 */
	public $locale = null;

	/**
	 * Monetary value
	 * 
	 * @var string
	 */
	public $value;

	public function render($prefix) {
		echo SwatString::moneyFormat($this->value, $this->locale);	
	}
}
