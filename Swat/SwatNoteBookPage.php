<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * Page class for entry into SwatNoteBook
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatNoteBookPage extends SwatContainer
{
	// {{{ public properties	
	
	/**
	 * Title
	 *
	 * The title of the tab.
	 *
	 * @var string
	 */
	public $title = null;

	// }}}
	// {{{ public function __construct

	/**
	 * Creates a new SwatNoteBookPage
	 *
	 * @param string $id a non-visable id for this widget.
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;
	}

	// }}}
	// {{{ public function display

	/**
	 * Displays this widget
	 *
	 * Displays this widget as well as recursively displays and child
	 * widgets of this widget.
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
}
?>
