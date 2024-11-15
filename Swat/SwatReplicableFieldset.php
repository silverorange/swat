<?php

/**
 * A fieldset container that replicates itself and its children.
 *
 * This widget can dynamically create widgets based on an array of
 * replicator identifiers.
 *
 * @copyright  2005-2008 silverorange
 * @license    http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @deprecated Use a SwatReplicableContainer with a SwatFieldset as the only
 *             child widget. Automatic title-setting functionality will need to
 *             be implemented manually.
 */
class SwatReplicableFieldset extends SwatReplicableContainer
{
    /**
     * Initilizes this replicable fieldset.
     */
    public function init()
    {
        $children = [];
        foreach ($this->children as $child_widget) {
            $children[] = $this->remove($child_widget);
        }

        $fieldset = new SwatFieldset();
        $fieldset->id = $fieldset->getUniqueId();
        $prototype_id = $fieldset->id;

        foreach ($children as $child_widget) {
            $fieldset->add($child_widget);
        }

        $this->add($fieldset);

        parent::init();

        foreach ($this->replicators as $id => $title) {
            $fieldset = $this->getWidget($prototype_id, $id);
            $fieldset->title = $title;
        }
    }
}
