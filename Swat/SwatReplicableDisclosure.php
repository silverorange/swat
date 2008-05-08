<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatDisclosure.php';
require_once 'Swat/SwatReplicableContainer.php';

/**
 * A disclosure that replicates its children
 *
 * The disclosure can dynamically create widgets based on an array of
 * replicators identifiers.
 *
 * @package    Swat
 * @copyright  2008 silverorange
 * @license    http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @deprecated Use a SwatReplicableContainer with a SwatDisclosure as the only
 *             child widget. Automatic title-setting functionality will need to
 *             be implemented manually.
 */
class SwatReplicableDisclosure extends SwatReplicableContainer
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
		$disclosure = new SwatDisclosure();
		$disclosure->title = $title;
		return $disclosure;
	}

	// }}}
}

?>
