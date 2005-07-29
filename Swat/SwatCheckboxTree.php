<?php

require_once 'Swat/SwatCheckboxList.php';
require_once 'Swat/SwatState.php';

/**
 * A checkbox array widget formatted into a tree
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckboxTree extends SwatCheckboxList implements SwatState
{
	/**
	 * Checkbox tree structure
	 *
	 * An tree structure of {@link SwatTreeNode} objects.
	 * This structure overwrites the public options property.
	 *
	 * @var SwatTreeNode
	 */
	public $tree = null;
	
	/**
	 * Tree Path
	 *
	 * An array containing the branch of the selected node.
	 * This is initialized at process time.
	 *
	 * TODO: This doesn't make any sense as any number of checkboxes
	 *       may be selected each with unique paths.
	 *
	 * @var array
	 */
	private $path = array();
	
	public function display()
	{
		if ($this->tree !== null)
			$this->options = $this->tree->toArray();

		$div_tag = new SwatHtmlTag('div');

		$label_tag = new SwatHtmlTag('label');
		$label_tag->class = 'swat-control';
		
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->id.'[]';
	
		foreach ($this->options as $key => $data) {
			$key_array = explode('/', $key);
		
			$div_tag->style = 'margin-left: '.((count($key_array) - 1)).'em;';
			$div_tag->open();
			
			if (isset($data['value'])) {
				$input_tag->id = $key;
				$input_tag->value = $data['value'];
			
				if (in_array($data['value'], $this->values))
					$input_tag->checked = 'checked';

				$label_tag->for = $key;
				$label_tag->content = $data['title'];

				$input_tag->display();
				$label_tag->display();
			} else {
				echo $data['title'];
			}
			
			$div_tag->close();
		}

		$this->displayJavascript();

		if (count($this->options) > 1) {
			$div_tag = new SwatHtmlTag('div');
			$div_tag->id = $this->id.'__div';
			$div_tag->open();

			$chk_all = new SwatCheckAll();
			$chk_all->controller = $this;
			$chk_all->display();

			$div_tag->close();
		}
	}

	/**
	 * Processes this checkbox tree
	 *
	 * Gets the selected checkbox path.
	 *
	 * TODO: Finish writing this.
	 */
	public function process()
	{
		parent::process();

		//$this->path = explode('/', $this->value);
		//$this->value = $this->path[count($this->path)-1];
	}

	/**
	 * Gets the path of the user selected elemtent
	 *
	 * This method only returns meaningful results after
	 * process() has been called.
	 *
	 * @return array the path of the current user selected element.
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Gets the current state of this checkbox tree
	 *
	 * @return array the current state of this checkbox tree.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->value;
	}

	/**
	 * Sets the current state of this checkbox tree
	 *
	 * @param array $state the new state of this checkbox tree.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = $state;
	}
}

?>
