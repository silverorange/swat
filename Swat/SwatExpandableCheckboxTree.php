<?php

require_once 'Swat/SwatCheckboxTree.php';

/**
 * A checkbox array widget formatted into a tree where each branch can
 * be expanded
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatExpandableCheckboxTree extends SwatCheckboxTree
{
	/**
	 * The initial state of the disclosure
	 *
	 * @var boolean
	 */
	public $open = true;

	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->addJavaScript('swat/javascript/swat-expandable-checkbox-tree.js');
	}

	public function display()
	{
		if (!$this->visible)
			return;

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id.'_div';
		$div_tag->class = 'swat-expandable-checkbox-tree';

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

		$div_tag->close();
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
		$child_nodes = $node->getChildren();

		// build a unique id of the indexes of the tree
		if (strlen($parent_index) == 0) {
			// index of the first node is just the node index
			$index = $node->getIndex();
		} else {
			// index of other nodes is a combination of parent indexes
			$index = $parent_index.'/'.$node->getIndex();

			$li_tag = new SwatHtmlTag('li');

			// display expander
			if (count($child_nodes) > 0) {

				$li_tag->class = 'swat-expandable-checkbox-tree-expander';
				$li_tag->open();
				
				$anchor = new SwatHtmlTag('a');
				$anchor->href =
					sprintf("javascript:%s_obj.toggleBranch('%s');",
						$this->id,
						$index);

				$anchor->open();
				
				$img = new SwatHtmlTag('img');
				
				if ($this->open) {
					$img->src = 'swat/images/disclosure-open.png';
					$img->alt = Swat::_('close');
				} else {
					$img->src = 'swat/images/disclosure-closed.png';
					$img->alt = Swat::_('open');
				}

				$img->width = 16;
				$img->height = 16;
				$img->id = $this->id.'_'.$index.'_img';
				$img->class = 'swat-expandable-checkbox-tree-image';

				$img->display();

				$anchor->close();
			} else {
				$li_tag->class = null;
				$li_tag->open();
			}

			// TODO: this is not always set. Make another more specific Swat
			//       data class.
			if (isset($node->data['value'])) {
				$this->input_tag->id = $this->id.'_'.$index;
				$this->input_tag->value = $node->data['value'];

				if (in_array($node->data['value'], $this->values))
					$this->input_tag->checked = 'checked';

				$this->label_tag->for = $this->id.'_'.$index;
				$this->label_tag->content = $node->data['title'];

				$this->input_tag->display();
				$this->label_tag->display();
			} else {
				echo $node->data['title'];
			}
		}

		// display children
		if (count($child_nodes) > 0) {

			$ul_tag = new SwatHtmlTag('ul');

			// don't make expandable if it is the root node
			if (strlen($parent_index) != 0) {
				$ul_tag->id = $this->id.'_'.$index.'_branch';
				$ul_tag->class = ($this->open) ?
					'swat-expandable-checkbox-tree-opened' :
					'swat-expandable-checkbox-tree-closed';
			}

			$ul_tag->open();

			foreach ($child_nodes as $child_node) {
				$nodes = $this->displayNode($child_node, $nodes, $index);
			}

			$ul_tag->close();
		}

		if (strlen($parent_index) != 0)
			echo '</li>';

		// count checkable nodes
		if (isset($node->data['value']))
			$nodes++;

		return $nodes;
	}

	/**
	 * Displays the javascript for this check-all widget
	 */
	protected function displayJavascript()
	{
		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";

		echo "var {$this->id}_obj = new SwatExpandableCheckboxTree('{$this->id}');\n";

		echo "\n//]]>";
		echo '</script>';
	}
}

?>
