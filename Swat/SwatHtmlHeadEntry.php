<?php

require_once 'Swat/SwatObject.php';

/**
 * Stores and outputs an HTML head entry
 *
 * Head entries are things like scripts and styles that belong in the HTML
 * head section.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatHtmlHeadEntry extends SwatObject
{
	/**
	 * The uri of this head entry
	 *
	 * @var string
	 */
	protected $uri = '';

	/**
	 * The relative order in which to display this HTML head entry relative
	 * to other HTML head entries in the same collection
	 *
	 * By default, entries are created with a display order of 0. Lower numbers
	 * are displayed before higher numbers.
	 *
	 * @var integer
	 */
	protected $display_order = 0;

	/**
	 * Creates a new HTML head entry
	 *
	 * @param string  $uri the uri of the entry.
	 * @param integer $display_order the relative order in which to display
	 *                                this HTML head entry.
	 */
	public function __construct($uri, $display_order = 0)
	{
		$this->uri = $uri;
		$this->display_order = $display_order;
	}

	/**
	 * Displays this html head entry
	 *
	 * Entries are displayed differently based on type.
	 *
	 * @param string $path_prefix an optional string to prefix the URI with.
	 */
	public abstract function display($uri_prefix = '');

	/**
	 * Gets the URI of this HTML head entry
	 *
	 * @return string the URI of this HTML head entry.
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * Gets the display order of this HTML head entry
	 *
	 * @return integer the display order of this HTML head entry.
	 */
	public function getDisplayOrder()
	{
		return $this->display_order;
	}

	/**
	 * Statically compares two HTML head entries by their display order
	 *
	 * This method is useful for sorting HTML head entries.
	 *
	 * @param SwatHtmlHeadEntry $entry1 the first entry to compare.
	 * @param SwatHtmlHeadEntry $entry2 the second entry to compare.
	 *
	 * @return integer a tri-value with 0 meaning the two entries are equal,
	 *                  1 meaning entry1 is greater than entry2 and -1 meaning
	 *                  entry1 is less than entry 2.
	 */
	public static function compare($entry1, $entry2)
	{
		if ($entry1->getDisplayOrder() == $entry2->getDisplayOrder())
			return 0;

		return
			($entry1->getDisplayOrder() > $entry2->getDisplayOrder()) ? 1 : -1;
	}
}

?>
