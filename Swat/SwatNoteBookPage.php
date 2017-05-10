<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * A page in a {@link SwatNoteBook}
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
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

	/**
	 * Optional content type
	 *
	 * Default text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $title_content_type = 'text/plain';

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
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();
		parent::display();
		$div_tag->close();
	}

	// }}}
	// {{{ public function getPages()

	/**
	 * Gets the notebook pages of this notebook page
	 *
	 * Implements the {@link SwatNoteBookChild::getPages()} interface.
	 *
	 * @return array an array containing this page.
	 */
	public function getPages()
	{
		return array($this);
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this page
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                page.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-note-book-page');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
