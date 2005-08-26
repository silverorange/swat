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
	
	/**
	 * A label tag used for displaying tree nodes
	 *
	 * @var SwatHtmltag
	 *
	 * @see SwatCheckboxTree::displayNode()
	 */
	private $label_tag = null;

	/**
	 * An input tag used for displaying tree nodes
	 *
	 * @var SwatHtmltag
	 *
	 * @see SwatCheckboxTree::displayNode()
	 */
	private $input_tag = null;
	
	public function display()
	{
		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id.'_div';
		$div_tag->class = 'swat-checkbox-tree';

		$this->label_tag = new SwatHtmlTag('label');
		$this->label_tag->class = 'swat-control';

		$this->input_tag = new SwatHtmlTag('input');
		$this->input_tag->type = 'checkbox';
		$this->input_tag->name = $this->id.'[]';

		$div_tag->open();	

		if ($this->tree !== null)
			$num_nodes = $this->displayNode($this->tree);
		else
			$num_nodes = 0;

		$this->displayJavascript();

		if ($num_nodes > 1) {
			$chk_all = new SwatCheckAll();
			$chk_all->controller = $this;
			$chk_all->display();
		}

		$div_tag->close();
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

	/**
	 * Displays a node in a tree as a checkbox input
	 *
	 * @param SwatTreeNode $node the node to display.
	 * @param integer $nodes the current number of nodes.
	 * @param string $parent_index the path of the parent node.
	 *
	 * @return integer the number of checkable nodes in the tree.
	 */
	private function displayNode($node, $nodes = 0, $parent_index = '')
	{
		// build a unique id of the indexes of the tree
		if (strlen($parent_index) == 0) {
			// index of the first node is just the node index
			$index = $node->getIndex();
		} else {
			// index of other nodes is a combination of parent indexes
			$index = $parent_index.'/'.$node->getIndex();

			echo '<li>';

			// TODO: this is not always set. Make another more specific Swat
			//       data class.
			if (isset($node->data['value'])) {
				$this->input_tag->id = $index;
				$this->input_tag->value = $node->data['value'];

				if (in_array($node->data['value'], $this->values))
					$this->input_tag->checked = 'checked';

				$this->label_tag->for = $index;
				$this->label_tag->content = $node->data['title'];

				$this->input_tag->display();
				$this->label_tag->display();
			} else {
				echo $node->data['title'];
			}
		}

		// display children
		$child_nodes = $node->getChildren();
		if (count($child_nodes) > 0) {
			echo '<ul>';
			foreach ($child_nodes as $child_node) {
				$nodes = $this->displayNode($child_node, $nodes, $index);
			}
			echo '</ul>';
		}

		if (strlen($parent_index) != 0)
			echo '</li>';

		// count checkable nodes
		if (isset($node->data['value']))
			$nodes++;
		
		return $nodes;
	}
}

?>
