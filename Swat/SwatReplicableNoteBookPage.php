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
	// {{{ public function init()

	/**
	 * Initilizes this replicable notebook page
	 *
	 * Goes through the internal widgets, clones them, and adds them to the
	 * widget tree.
	 */
	public function init()
	{
		$notebook = $this->getCompositeWidget('notebook');
		$children = array();

		if ($this->replicators === null)
			$this->replicators = array(0 => null);

		// first we add each child to the local array, and remove from the
		// widget tree
		foreach ($this->children as $child_widget)
			$children[] = $this->remove($child_widget);

		// then we clone, change the id and add back to the widget tree inside
		// a replicated container
		foreach ($this->replicators as $id => $title) {
			$container = $this->getContainer($id, $title);
			$suffix = '_'.$this->id.$id;
			$this->widgets[$id] = array();

			foreach ($children as $child) {
				$new_child = clone $child;

				if ($new_child->id !== null) {
					$this->widgets[$id][$new_child->id] = $new_child;
					$new_child->id.= $suffix;
				}

				// update ids of cloned child descendants
				if ($new_child instanceof SwatUIParent) {
					foreach ($new_child->getDescendants() as $descendant) {
						if ($descendant->id !== null) {
							$this->widgets[$id][$descendant->id] = $descendant;
							$descendant->id.= $suffix;
						}
					}
				}

				if ($container === null)
					$this->add($new_child);
				else
					$container->add($new_child);
			}

			if ($container !== null)
				$notebook->addPage($container);
		}

		parent::init();
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		SwatWidget::display();
		$notebook = $this->getCompositeWidget('notebook');
		$notebook->display();
	}

	// }}}
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
	// {{{ protected function createCompositeWidgets()

	/**
	 * Creates the composite notebook used by this replicable notebook page
	 *
	 * @see SwatWidget::createCompositeWidgets()
	 */
	protected function createCompositeWidgets()
	{
		$notebook = new SwatNoteBook($this->id.'_notebook');
		$this->addCompositeWidget($notebook, 'notebook');
	}

	// }}}
}

?>
