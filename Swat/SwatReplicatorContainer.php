<?php

require_once 'Swat/SwatContainer.php';

/**
 * A container that store replication information. This container is
 * created by other replicator widgets.
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatReplicatorContainer extends SwatContainer
{ 
	public $widgets = array();

	/**
	 * Retrive a reference to a replicated widget
	 *
	 * @param string $widget_id the unique id of the original widget
	 * @param string $replicator_id the replicator id of the replicated widget
	 *
	 * @returns SwatWidget a reference to the replicated widget, or null if the
	 *                      widget is not found.
	 */
	public function getWidget($widget_id, $replicator_id)
	{
		if (isset($this->widgets[$replicator_id][$widget_id.$replicatorid])) {
			return $this->widgets[$replicator_id][$widget_id.$replicatorid];
		} else {
			return null;
		}
	}
}

?>
