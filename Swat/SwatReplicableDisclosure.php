<?php

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
	// {{{ public function init()

	/**
	 * Initilizes this replicable disclosure
	 */
	public function init()
	{
		$children = array();
		foreach ($this->children as $child_widget)
			$children[] = $this->remove($child_widget);

		$disclosure = new SwatDisclosure();
		$disclosure->id = $disclosure->getUniqueId();
		$prototype_id = $disclosure->id;

		foreach ($children as $child_widget)
			$disclosure->add($child_widget);

		$this->add($disclosure);

		parent::init();

		foreach ($this->replicators as $id => $title) {
			$disclosure = $this->getWidget($prototype_id, $id);
			$disclosure->title = $title;
		}
	}

	// }}}
}

?>
