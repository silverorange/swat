<?php

/**
 * A child of a {@link SwatNoteBook}.
 *
 * @copyright 2008-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       SwatNoteBook
 * @see       SwatNoteBookPage
 */
interface SwatNoteBookChild
{
    /**
     * Gets the notebook pages of this child.
     *
     * @return array an array of {@link SwatNoteBookPage} objects
     *
     * @see SwatNoteBookPage
     */
    public function getPages();
}
