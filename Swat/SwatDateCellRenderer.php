<?php

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatDate.php';
require_once 'Swat/SwatString.php';

/**
 * A text renderer.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDateCellRenderer extends SwatCellRenderer
{
	// {{{ public properties

	/**
	 * Date
	 *
	 * Can be either a Date object, or an ISO-formatted date.
	 *
	 * @var mixed
	 */
	public $date = null;

	/**
	 * Format
	 *
	 * Either a {@link SwatDate} format mask, or class constant. Class
	 * constants are preferable for sites that require translation.
	 *
	 * @var mixed
	 */
	public $format = SwatDate::DF_DATE_TIME;

	/**
	 * Time Zone
	 *
	 * The ISO time zone to adjust the date to.
	 *
	 * @var mixed
	 */
	public $time_zone = null;

	// }}}
	// {{{ public function render()

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		if ($this->date !== null) {
			$date = new SwatDate($this->date);
			if ($this->time_zone !== null)
				$date->convertTZbyID($this->time_zone);

			echo SwatString::minimizeEntities($date->format($this->format));
		}
	}

	// }}}
}

?>
