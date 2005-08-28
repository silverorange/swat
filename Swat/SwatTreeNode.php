<?php

require_once 'Swat/SwatObject.php';

/**
 * A simple class for building a tree structure
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTreeNode extends SwatObject
{
	// {{{ public properties

	/**
	 * An array of data used for display
	 *
	 * Data is the actual content of this node. Many Swat widgets use the
	 * data array to store a value and a title for example.
	 *
	 * @var array
	 */
	public $data = array();

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

	/**
	 * An array of children tree nodes
	 *
	 * This array is indexed numerically and starts at 0.
	 *
	 * @var array
	 */
	private $children = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new tree node
	 *
	 * The data property is set to a blank array if unspecified.
	 *
	 * @param array $data an array of data used for display.
	 */
	public function __construct($data = array())
	{
		$this->data = $data;
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a child node to this node
	 *
	 * The parent of the child node is set to this node.
	 *
	 * @paran SwatTreeNode $child the child node to add to this node.
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
	 * return @array an array of indexes that is the path to this node from the
	 *                root of the current tree.
	 */
	public function &getPath()
	{
		$path = array($this->id);

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
	// {{{ public function toArray()

	/**
	 * Returns this branch as a flat array
	 *
	 * Thius utility method gets all child nodes of this node as a flat array
	 * of the form:
	 *    index1/index2/index3 => data
	 * Where 'index1/index2/index3' is a flat representation of the path from
	 * this node to the node containing 'data'.
	 *
	 * @return array a reference to an array containing all child elements of
	 *                this node indexed by path.
	 */
	public function &toArray()
	{
		$flat_array = array();

		self::expandNode($flat_array, $this);

		return $flat_array;
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
	// {{{ private static function expandNode()

	/**
	 * Recursivly expands a tree node
	 *
	 * Adds the tree node to an array with the current path as a key and the
	 * node's data as a value. Then calls itself on each child node of the
	 * node with the node's id added to the path.
	 *
	 * @param array $options a reference to an array where the flat tree is
	 *                        stored.
	 * @param SwatTreeNode the node to begin recursion with.
	 * @param array $path an array of ids representing the current path in the
	 *                     tree.
	 */
	private static function expandNode(&$options, $node, $path = array())
	{
		if (count($path))
			$options[implode('/', $path)] = $node->data;

		foreach ($node->children as $index => $child_node)
			self::expandNode($options, $child_node,
				self::appendPath($path, $index));
	}

	// }}}
	// {{{ private static function appendPath()

	/**
	 * Adds an id to an array of ids forming a path in this tree
	 *
	 * The current path is passed by value on purpose.
	 *
	 * @param array $path the current path.
	 * @param string $id the id to add to the path.
	 *
	 * @return array a reference to the path array with the new id added.
	 */
	private static function &appendPath($path, $index)
	{
		if (!is_array($path))
			$path = array($index);
		else
			$path[] = $index;

		return $path;
	}

	// }}}
}

?>
