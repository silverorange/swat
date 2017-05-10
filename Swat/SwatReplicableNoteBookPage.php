<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * A container that replicates itself and its children as pages of a notebook
 *
 * @package    Swat
 * @copyright  2008 silverorange
 * @license    http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @deprecated Use a regular {@link SwatNoteBook} containing a
 *             {@link SwatReplicableNoteBookChild}. Within the
 *             SwatReplicableNoteBookChild, place one or more
 *             SwatNoteBookPage objects to be replicated. The automatic
 *             title-setting functionality of SwatReplicableNoteBookPage is
 *             not available using this approach.
 */
class SwatReplicableNoteBookPage extends SwatReplicableContainer
	implements SwatNoteBookChild
{
	// {{{ public function init()

	/**
	 * Initilizes this replicable notebook page
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
	 * Gets the notebook pages of this replicable notebook page
	 *
	 * Implements the {@link SwatNoteBookChild::getPages()} interface.
	 *
	 * @return array an array containing all the replicated pages of this
	 *                container.
	 */
	public function getPages()
	{
		return $this->children;
	}

	// }}}
}

?>
