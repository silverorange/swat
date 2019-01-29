<?php

/**
 * A Swat container that can replicate its contents
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatReplicable
{
	// {{{ public function getWidget()

	/**
	 * Retrives a reference to a replicated widget
	 *
	 * @param string $widget_id the unique id of the original widget.
	 * @param string $replicator_id the replicator id of the replicated widget.
	 *
	 * @returns SwatWidget a reference to the replicated widget
	 *
	 * @throws SwatWidgetNotFoundException
	 */
	public function getWidget($widget_id, $replicator_id);

	// }}}
}
