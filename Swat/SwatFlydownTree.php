<?php
require_once('Swat/SwatFlydown.php');

/**
 * A flydown (aka combo-box) selection widget formatted into a tree
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatFlydownTree extends SwatFlydown {

	/**
	 * Flydown options
	 *
	 * An tree collection of {@link SwatTreeNode}s for the flydown.
	 * @var SwatTreeNode
	 */
	public $tree = null;
	
	/**
	 * Tree Path
	 *
	 * An array containing the branch of the selected node.
	 * @var array
	 */
	public $path;
	
	public function display() {
		if ($this->tree != null)
			$this->options = $this->tree->toArray();

		foreach ($this->options as $key => $data) {
			$key_array = explode('/',$key);
			$pad = str_repeat('&nbsp;&nbsp;', (count($key_array) - 1));
			$this->options[$key] = $pad.$data['title'];		
		}
		
		parent::display();
	}

	public function process() {
		parent::process();
		$this->path = explode('/',$this->value);
		$this->value = $this->path[count($this->path)-1];
	}
}

?>
