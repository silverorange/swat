<?php

require_once 'Swat/SwatTreeNode.php';

/**
 * A tree node containing a value and a title
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDataTreeNode extends SwatTreeNode
{
	// {{{ public properties

	/**
	 * The value of this node
	 *
	 * The value is used for processing. It is either a string or an integer.
	 *
	 * @var mixed
	 */
	public $value;

	/**
	 * The title of this node
	 *
	 * The title is used for display.
	 *
	 * @var string
	 */
	public $title;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new data node
	 *
	 * @param string $title
	 * @param mixed $value
	 */
	public function __construct($title = null, $value = null)
	{
		$this->title = $title;
		$this->value = $value;
	}

	// }}}
	// {{{ public function toArray()

	/**
	 * Returns this branch as a flat array
	 *
	 * Thius utility method gets all child nodes of this node as a flat array
	 * of the form:
	 *    value1/value2/value3 => title
	 * Where 'value1/value2/value3' is a flat representation of the path from
	 * this node to the node with the title 'title'.
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
	// {{{ private static function expandNode()

	/**
	 * Recursivly expands a tree node
	 *
	 * Adds the tree node to an array with the a string representation of the
	 * current path as the array key and the node's title as a value. Then
	 * calls itself on each child node of the node with the node's value added
	 * to the path.
	 *
	 * @param array $options a reference to an array where the flat tree is
	 *                        stored.
	 * @param SwatDataTreeNode $node the node to begin recursion with.
	 * @param array $path an array of node values representing the current path
	 *                     in the tree.
	 */
	private static function expandNode(&$options, SwatDataTreeNode $node,
		$path = array())
	{
		if (count($path) > 0)
			$options[implode('/', $path)] = $node->title;

		foreach ($node->getChildren() as $index => $child_node)
			self::expandNode($options, $child_node,
				self::appendPath($path, $child_node->value));
	}

	// }}}
	// {{{ private static function appendPath()

	/**
	 * Adds a node value to an array of node values forming a path in this tree
	 *
	 * The current path is passed by value on purpose.
	 *
	 * @param array $path the current path.
	 * @param mixed $value the node value to add to the path.
	 *
	 * @return array a reference to the path array with the new id added.
	 */
	private static function &appendPath($path, $value)
	{
		if (!is_array($path))
			$path = array($value);
		else
			$path[] = $value;

		return $path;
	}

	// }}}
}

?>
