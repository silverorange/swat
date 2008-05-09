<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatNoteBookChild.php';
require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A page in a {@link SwatNoteBook}
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatNoteBook
 */
class SwatNoteBookPage extends SwatContainer implements SwatNoteBookChild
{
	// {{{ public properties

	/**
	 * The title of this page
	 *
	 * @var string
	 */
	public $title;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new notebook page
	 *
	 * @param string $id a non-visable id for this page.
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this notebook page
	 *
	 * Displays this notebook page as well as recursively displaying all child-
	 * widgets of this page.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->open();
		parent::display();
		$div_tag->close();
	}

	// }}}
	// {{{ public function getPages()

	/**
	 * Get all note book pages in this child
	 *
	 * Implements the SwatNoteBookChild interface.
	 *
	 * @return array an array of {@link SwatNoteBookPage} objects.
	 * @see SwatNoteBookChild
	 */
	public function getPages()
	{
		return array($this);;
	}

	// }}}
}

?>
