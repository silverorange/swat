<?php

require_once 'Swat/SwatObject.php';

/**
 * A simple class for building a tree structure
 *
 * To create a tree data structure, sub-class this class.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatTreeNode extends SwatObject
{
	// {{{ protected properties

	/**
	 * An array of children tree nodes
	 *
	 * This array is indexed numerically and starts at 0.
	 *
	 * @var array
	 */
	protected $children = array();

	// }}}
	// {{{ private properties

	/**
	 * The parent tree node of this tree node
	 *
	 * @var SwatTreeNode
	 */
	private $parent = null;

	/**
	 * The index of this child node it its parent array.
	 *
	 * The index of this node is used like an identifier and is used when
	 * building paths in the tree.
	 *
	 * @var integer
	 */
	private $index = 0;

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a child node to this node
	 *
	 * The parent of the child node is set to this node.
	 *
	 * @param SwatTreeNode $child the child node to add to this node.
	 */
	public function addChild($child)
	{
		$child->parent = $this;
		$child->index = count($this->children);
		$this->children[] = $child;
	}

	// }}}
	// {{{ public function getPath()

	/**
	 * Gets the path to this node
	 *
	 * This method travels up the tree until it reaches a node with a parent
	 * of 'null', building a path of ids along the way.
	 *
	 * @return array an array of indexes that is the path to the given node
	 *                from the root of the current tree.
	 */
	public function &getPath()
	{
		$path = array($this->index);

		$parent = $this->parent;
		while ($parent !== null) {
			$path[] = $parent->index;
			$parent = $parent->parent;
		}

		// we built the path backwards
		$path = array_reverse($path);

		return $path;
	}

	// }}}
	// {{{ public function getParent()

	/**
	 * Gets the parent node of this node
	 *
	 * @return SwatTreeNode the parent node of this node.
	 */
	public function getParent()
	{
		return $this->parent;
	}

	// }}}
	// {{{ public function getChildren()

	/**
	 * Gets this node's children
	 *
	 * @return this node's children.
	 */
	public function getChildren()
	{
		return $this->children;
	}
	
	// }}}
	// {{{ public function hasChildren()

	/**
	 * Whether or not this tree node has children
	 *
	 * @return boolean true if this node has children or false if this node
	 *                  does not have children.
	 */
	public function hasChildren()
	{
		return (count($this->children) > 0);
	}

	// }}}
	// {{{ public function getIndex()

	/**
	 * Gets this node's index
	 *
	 * @return integer this node's index.
	 */
	public function getIndex()
	{
		return $this->index;
	}

	// }}}
}

?>
