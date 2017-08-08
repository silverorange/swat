<?php

/**
 * Interface for view selectors
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatViewSelection
 * @see       SwatView
 */
interface SwatViewSelector
{

	/**
	 * Gets the identifier of this selector
	 *
	 * @return string the identifier of this selector.
	 */
	public function getId();

}

?>
