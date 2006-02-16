<?php

/**
 * Objects that are titleable have a title that may be gotten
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatTitleable::getTitle()
 */
interface SwatTitleable
{
	/**
	 * Gets the title of this object
	 *
	 * @return string the title of this object.
	 */
	public function getTitle();
}

?>
