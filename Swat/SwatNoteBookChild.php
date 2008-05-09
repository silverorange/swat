<?php

/**
 * A child of a {@link SwatNoteBook}
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatNoteBook
 */
interface SwatNoteBookChild
{
	// {{{ public function getPages()

	/**
	 * Get all note book pages in this child
	 *
	 * @return array an array of {@link SwatNoteBookPage} objects.
	 *
	 * @see SwatNoteBookPage
	 */
	public function getPages();

	// }}}
}

?>
