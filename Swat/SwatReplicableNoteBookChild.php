<?php

/**
 * A replicable container that replicates {@link SwatNoteBookChild} objects.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatReplicableNoteBookChild extends SwatReplicableContainer implements SwatNoteBookChild
{
    /**
     * Gets the notebook pages of this replicable notebook child.
     *
     * Implements the {@link SwatNoteBookChild::getPages()} interface.
     *
     * @return array an array containing all the replicated pages of this
     *               child
     */
    public function getPages()
    {
        $pages = [];

        foreach ($this->children as $child) {
            if ($child instanceof SwatNoteBookChild) {
                $pages = array_merge($pages, $child->getPages());
            }
        }

        return $pages;
    }

    /**
     * Adds a {@link SwatNoteBookChild} to this replicable notebook child.
     *
     * This method fulfills the {@link SwatUIParent} interface.
     *
     * @param SwatNoteBookChild $child the notebook child to add
     *
     * @throws SwatInvalidClassException if the given object is not an instance
     *                                   of SwatNoteBookChild
     *
     * @see SwatUIParent
     */
    public function addChild(SwatObject $child)
    {
        if (!$child instanceof SwatNoteBookChild) {
            throw new SwatInvalidClassException(
                'Only SwatNoteBookChild objects may be nested within a '
                    . 'SwatReplicableNoteBookChild object.',
                0,
                $child,
            );
        }

        parent::addChild($child);
    }
}
