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
	 * The time zone to render the date in
	 *
	 * The time zone may be specified either as a time zone identifier valid for
	 * PEAR::Date_TimeZone or as a Date_TimeZone object. If the display time zone
	 * is null, no time zone conversion is performed.
	 *
	 * @var string|Date_TimeZone 
	 */
	public $display_time_zone = null;

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

		if ($this->date !== null) {
			// time zone conversion mutates the original object so create a new
			// date for display
			$date = new SwatDate($this->date);
			if ($this->display_time_zone !== null) {
				if ($this->display_time_zone instanceof Date_TimeZone)
					$date->convertTZ($this->display_time_zone);
				else
					$date->convertTZbyID($this->display_time_zone);
			}

			echo SwatString::minimizeEntities($date->format($this->format));
		}
	}

	// }}}
}

?>
