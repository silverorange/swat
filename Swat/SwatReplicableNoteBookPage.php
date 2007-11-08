<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatReplicableContainer.php';
require_once 'Swat/SwatNoteBook.php';
require_once 'Swat/SwatNoteBookPage.php';

/**
 * A container that replicates itself and its children as pages of a notebook
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatReplicableNoteBookPage extends SwatReplicableContainer
{
	// {{{ protected function getContainer()

	/**
	 * Gets a container to contain replicated widgets for this replicable
	 * container
	 *
	 * @param string $id the replicator id for the container.
	 * @param stirng $title the title of the container. The container may or
	 *                       may not use this title.
	 *
	 * @return SwatContainer the container object to which replciated widgets
	 *                        are added. The container is added to the widget
	 *                        tree after adding the replicated widgets to the
	 *                        container. If null is returned, the widgets are
	 *                        replicated directly in the widget tree.
	 */
	protected function getContainer($id, $title)
	{
		$page = new SwatNoteBookPage($id);
		$page->title = $title;
		return $page;
	}

	// }}}
	// {{{ protected function getContainerParent()

	/**
	 * Gets the notebook object that contains replicated notebook pages
	 *
	 * @return SwatUIParent the parent object of replicated containers.
	 */
	protected function getContainerParent()
	{
		return new SwatNoteBook($this->id.'_notebook');
	}

	// }}}
}

?>
