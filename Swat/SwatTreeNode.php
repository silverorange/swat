<?php

require_once('Swat/SwatObject.php');

/**
 * A simple class for building a tree structure
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatTreeNode extends SwatObject {
	/**
	 * Array of data used for display
	 * @var array
	 */
	var $data;
	
	/**
	 * An array of children nodes
	 * @var string
	 */
	var $children = array();

	// TODO: add parent and a getPath() method would return the path of this
	//       node.

	public function __construct($data = array()) {
		$this->data = $data;
	}

	/**
	 * Return this branch as an array
	 *
	 * A utility method to return all child elements in a flat array with 
	 * keys in the form of: id1/id2/id3
	 */
	public function toArray() {
		$options = array();
		
		$this->expandNode($options, $this);

		return $options;
	}

	private function expandNode(&$options, $node, $path = array()) {
		if (count($path)) $options[implode('/',$path)] = $node->data;

		foreach ($node->children as $id => $child_node)
			$this->expandNode($options, $child_node, $this->appendPath($path, $id));
	}
	
	private function appendPath($path, $id) {
		$path[] = $id;
		return $path;
	}
}

?>
