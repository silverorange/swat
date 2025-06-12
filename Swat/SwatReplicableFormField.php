<?php

/**
 * A form field container that replicates its children.
 *
 * The form field can dynamically create widgets based on an array of
 * replicators identifiers.
 *
 * @copyright  2005-2008 silverorange
 * @license    http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @deprecated Use a SwatReplicableContainer with a SwatFormField as the only
 *             child widget. Automatic title-setting functionality will need to
 *             be implemented manually.
 */
class SwatReplicableFormField extends SwatReplicableContainer
{
    // {{{ public function init()

    /**
     * Initilizes this replicable form field.
     */
    public function init()
    {
        $children = [];
        foreach ($this->children as $child_widget) {
            $children[] = $this->remove($child_widget);
        }

        $field = new SwatFormField();
        $field->id = $field->getUniqueId();
        $prototype_id = $field->id;

        foreach ($children as $child_widget) {
            $field->add($child_widget);
        }

        $this->add($field);

        parent::init();

        foreach ($this->replicators as $id => $title) {
            $field = $this->getWidget($prototype_id, $id);
            $field->title = $title;
        }
    }

    // }}}
}
