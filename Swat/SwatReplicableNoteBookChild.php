<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatReplicableContainer.php';
require_once 'Swat/SwatNoteBookChild.php';
require_once 'Swat/SwatNoteBookPage.php';

/**
 * A container that replicates itself and its children as pages of a notebook
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatReplicableNoteBookChild extends SwatReplicableContainer implements SwatNoteBookChild
{
	// {{{ public function getPages()

	/**
	 * Get all note book pages in this child
	 *
	 * Implements the SwatNoteBookChild interface.
	 *
	 * @return array an array of {@link SwatNoteBookPage} objects.
	 * @see SwatNoteBookChild
	 */
	public function getPages()
	{
		$pages = array();

		foreach ($this->children as $child)
			if ($child instanceof SwatNoteBookPage)
				$pages[] = $child;

		return $pages;
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a {@link SwatNoteBookPage} to this replicable notebook child
	 *
	 * This method fulfills the {@link SwatUIParent} interface. It is used
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To add a notebook page to a notebook, use
	 * {@link SwatNoteBook::addPage()}.
	 *
	 * @param SwatNoteBookPage $child the notebook page to add.
	 *
	 * @throws SwatInvalidClassException if the given object is not an instance
	 *                                    of SwatNoteBookPage.
	 *
	 * @see SwatUIParent
	 */
	public function addChild(SwatObject $child)
	{
		if (!($child instanceof SwatNoteBookPage))
			throw new SwatInvalidClassException(
				'Only SwatNoteBookChild objects may be nested within a '.
				'SwatNoteBook object.', 0, $child);

		parent::addChild($child);
	}

	// }}}
}

?>
