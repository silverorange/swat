<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatReplicableContainer.php';
require_once 'Swat/SwatNoteBookChild.php';
require_once 'Swat/SwatNoteBookPage.php';

/**
 * A container that replicates itself and its children as pages of a notebook
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @deprecated Use a SwatNoteBook with a SwatReplicableNoteBookChild within.
 *             Within the SwatReplicableNoteBookChild place one or more
 *             SwatNoteBookPage objects to be replicated. The automatic
 *             title-setting functionality has been removed and will need
 *             to be implemented manually.
 */
class SwatReplicableNoteBookPage extends SwatReplicableContainer implements SwatNoteBookChild
{
	// {{{ public function init()

	/**
	 * Initilizes this replicable note book page
	 */
	public function init()
	{
		$children = array();
		foreach ($this->children as $child_widget)
			$children[] = $this->remove($child_widget);

		$page = new SwatNoteBookPage();
		$page->id = $page->getUniqueId();
		$page_prototype_id = $page->id;

		foreach ($children as $child_widget)
			$page->add($child_widget);

		$this->add($page);

		parent::init();

		foreach ($this->replicators as $id => $title) {
			$page = $this->getWidget($page_prototype_id, $id);
			$page->title = $title;
		}

		$note_book = new SwatNoteBook($this->id.'_notebook');

		foreach ($this->children as $child_widget) {
			$page = $this->remove($child_widget);
			$note_book->addPage($page);
		}

		$this->add($note_book);
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
		return $this->children;
	}

	// }}}
}

?>
