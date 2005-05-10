<?php

require_once('Swat/SwatCellRenderer.php');
require_once('Swat/SwatDate.php');

/**
 * A text renderer.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatDateCellRenderer extends SwatCellRenderer {

	/**
	 * Date
	 *
	 * Can be either a Date object, or an ISO-formatted date.
	 * @var mixed
	 */
	public $date = null;

	/**
	 * Format
	 *
	 * Either a {@link SwatDate} format mask, or class constant. Class
	 * constants are preferable for sites that require translation.
	 * @var mixed
	 */
	public $format = SwatDate::DF_DATE_TIME;


	public function render($prefix) {
		if ($this->date !== null) {
			$date = new SwatDate($this->date);
			echo $date->format($this->format);
		}
	}
}

?>
