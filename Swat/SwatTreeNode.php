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
	/**
	 * An array of data used for display
	 *
	 * @var array
	 */
	public $data = null;
	
	/**
	 * An array of children nodes
	 *
	 * @var array
	 */
	public $children = array();

	// TODO: add parent and a getPath() method would return the path of this
	//       node.

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

	/**
	 * Returns this branch as an array
	 *
	 * A utility method that gets all child elements as a flat array of the
	 * form:
	 *    id1/id2/id3 => value
	 * Where 'id1/id2/id3' is the path to the 'value' node.
	 *
	 * @return array a reference to an array containing all child elements of
	 *                this node indexed by path.
	 */
	public function &toArray()
	{
		$options = array();
		
		$this->expandNode($options, $this);

		return $options;
	}

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
	private function expandNode(&$options, $node, $path = array())
	{
		if (count($path))
			$options[implode('/', $path)] = $node->data;

		foreach ($node->children as $id => $child_node)
			$this->expandNode($options, $child_node,
				$this->appendPath($path, $id));
	}

	/**
	 * Adds an id to an array of ids forming a path in this tree
	 *
	 * @param array $path a reference to the current path.
	 * @param string $id the id to add to the path.
	 *
	 * @return array a reference to the path array with the new id added.
	 */
	private function &appendPath(&$path, $id)
	{
		if (!is_array($path))
			$path = array($id);
		else
			$path[] = $id;

		return $path;
	}
}

?>
