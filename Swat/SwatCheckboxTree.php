<?php
require_once('Swat/SwatCheckboxArray.php');
require_once('Swat/SwatState.php');

/**
 * A checkbox array widget formatted into a tree
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatCheckboxTree extends SwatControl implements SwatState {

	/**
	 * Checkbox tree structure
	 *
	 * An tree collection of {@link SwatTreeNode}s.
	 * @var SwatTreeNode
	 */
	public $tree = null;
	
	/**
	 * Tree Path (read-only)
	 *
	 * An array containing the branch of the selected node.
	 * Set at process.
	 * @var array
	 */
	public $path;
	
	public function display() {
		if ($this->tree !== null)
			$this->options = $this->tree->toArray();

		$div_tag = new SwatHtmlTag('div');
		$label_tag = new SwatHtmlTag('label');
		$label_tag->class = 'swat-control';
		
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->name.'[]';
		
		foreach ($this->options as $key => $data) {
			$key_array = explode('/',$key);
		
			$div_tag->style='margin-left: '.((count($key_array) - 1)).'em;';
		
			$input_tag->id = $key;
			$input_tag->value = $key;
			
			if (in_array($this->values, $this->value))
				$input_tag->checked = "checked";

			$label_tag->for = $key;
			$label_tag->open();

			$div_tag->open();
				$input_tag->display();
			
				$label_tag->open();
					echo $data['title'];
				$label_tag->close();
			$div_tag->close();
		}
	}

	public function process() {
		$this->path = explode('/',$this->value);
		$this->value = $this->path[count($this->path)-1];
	}

	public setState($state) {
		$this->value = $state;
	}
	
	public getState() {
		return $this->value;
	}
}

?>
