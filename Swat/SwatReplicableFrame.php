<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */


/**
 * A frame that replicates its children
 *
 * The frame can dynamically create widgets based on an array of
 * replicators identifiers.
 *
 * @package    Swat
 * @copyright  2005-2008 silverorange
 * @license    http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @deprecated Use a SwatReplicableContainer with a SwatFrame as the only child
 *             widget. Automatic title-setting functionality need will to be
 *             implemented manually.
 */
class SwatReplicableFrame extends SwatReplicableContainer
{
	// {{{ public function init()

	/**
	 * Initilizes this replicable frame
	 */
	public function init()
	{
		$children = array();
		foreach ($this->children as $child_widget)
			$children[] = $this->remove($child_widget);

		$frame = new SwatFrame();
		$frame->id = $frame->getUniqueId();
		$prototype_id = $frame->id;

		foreach ($children as $child_widget)
			$frame->add($child_widget);

		$this->add($frame);

		parent::init();

		if ($this->replication_ids === null && is_array($this->replicators)) {
			foreach ($this->replicators as $id => $title) {
				$frame = $this->getWidget($prototype_id, $id);
				$frame->title = $title;
			}
		}
	}

	// }}}
}

?>
