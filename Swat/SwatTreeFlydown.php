<?php

require_once 'Swat/SwatFlydown.php';

/**
 * A flydown (aka combo-box) selection widget formatted into a tree
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTreeFlydown extends SwatFlydown
{
	/**
	 * Flydown options
	 *
	 * An tree collection of {@link SwatTreeNode} objects for the flydown.
	 * This property overwrites the public $options property in the display
	 * method.
	 *
	 * @var SwatTreeNode
	 */
	public $tree = null;
	
	/**
	 * Tree Path
	 *
	 * An array containing the branch of the selected node.
	 *
	 * @var array
	 */
	public $path = array();
	
	/**
	 * Displays this tree flydown
	 *
	 * The tree is represented by playing spaces in front of nodes on different
	 * levels.
	 */
	public function display()
	{
		if ($this->tree !== null)
			$this->options = $this->tree->toArray();

		foreach ($this->options as $key => $data) {
			$key_array = explode('/', $key);
			$pad = str_repeat('&nbsp;&nbsp;', (count($key_array) - 1));
			$this->options[$key] = $pad.$data['title'];		
		}
		
		parent::display();
	}

	/**
	 * Processes this tree flydown
	 *
	 * Populates the path property of this flydown with the path to the node
	 * selected by the user. The widget value is set to the last id in the
	 * path array.
	 */
	public function process()
	{
		parent::process();
		$this->path = explode('/', $this->value);
		$this->value = $this->path[count($this->path) - 1];
	}
}

?>
