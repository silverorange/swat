<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatCheckboxTree.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/SwatYUI.php';

/**
 * A checkbox array widget formatted into a tree where each branch can
 * be expanded
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatExpandableCheckboxTree extends SwatCheckboxTree
{
	// {{{ public properties

	/**
	 * The initial state of the tree
	 *
	 * All branches are either open or closed.
	 *
	 * @var boolean
	 */
	public $open = true;

	/**
	 * Whether or not the state of child boxes depends on the state of
	 * its parent boxes and the state of parent boxes depends on the state of
	 * all its children
	 *
	 * @var boolean
	 */
	public $dependent_boxes = true;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new expandable checkbox tree
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$yui = new SwatYUI(array('dom', 'event', 'animation'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript(
			'packages/swat/javascript/swat-expandable-checkbox-tree.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this expandable checkbox tree
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$this->getForm()->addHiddenField($this->id.'_submitted', 1);

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();

		$this->label_tag = new SwatHtmlTag('label');

		$this->input_tag = new SwatHtmlTag('input');
		$this->input_tag->type = 'checkbox';
		$this->input_tag->name = $this->id.'[]';

		$div_tag->open();

		if ($this->tree !== null)
			$num_nodes = $this->displayNode($this->tree);
		else
			$num_nodes = 0;

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this expandable
	 * checkbox tree 
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                expandable checkbox tree.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-expandable-checkbox-tree');
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this widget
	 *
	 * @return string the inline JavaScript for this widget
	 */
	protected function getInlineJavaScript()
	{
		$dependent_boxes = ($this->dependent_boxes) ? 'true' : 'false';
		return sprintf(
			"var %s_obj = new SwatExpandableCheckboxTree('%s', %s);\n",
			$this->id,
			$this->id,
			$dependent_boxes);
	}

	// }}}
	// {{{ private function displayNode()

	/**
	 * Displays a node in a tree as a checkbox input
	 *
	 * @param SwatDataTreeNode $node the node to display.
	 * @param integer $nodes the current number of nodes.
	 * @param string $parent_index the path of the parent node.
	 *
	 * @return integer the number of checkable nodes in the tree.
	 */
	private function displayNode(SwatDataTreeNode $node, $nodes = 0,
		$parent_index = '')
	{
		$child_nodes = $node->getChildren();

		// build a unique id of the indexes of the tree
		if (strlen($parent_index) == 0) {
			// index of the first node is just the node index
			$index = $node->getIndex();
		} else {
			// index of other nodes is a combination of parent indexes
			$index = $parent_index.'.'.$node->getIndex();

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
					$img->src = 'packages/swat/images/swat-disclosure-open.png';
					$img->alt = Swat::_('close');
				} else {
					$img->src = 'packages/swat/images/swat-disclosure-closed.png';
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

			if ($node->value === null) {
				if ($this->dependent_boxes) {
					// show a checkbox just for the check-all functionality
					$this->input_tag->id = $this->id.'_'.$index;
					$this->input_tag->value = null;

					$this->label_tag->for = $this->id.'_'.$index;
					$this->label_tag->class =
						'swat-control swat-expandable-checkbox-tree-null';

					$this->label_tag->setContent($node->title);

					$this->input_tag->display();
					$this->label_tag->display();
				} else {
					echo SwatString::minimizeEntities($node->title);
				}
			} else {
				$this->input_tag->id = $this->id.'_'.$index;
				$this->input_tag->value = $node->value;

				if (in_array($node->value, $this->values))
					$this->input_tag->checked = 'checked';
				else
					$this->input_tag->checked = null;

				$this->label_tag->for = $this->id.'_'.$index;
				$this->label_tag->class = 'swat-control';
				$this->label_tag->setContent($node->title);

				$this->input_tag->display();
				$this->label_tag->display();
			}
		}

		// display children
		if (count($child_nodes) > 0) {

			$ul_tag = new SwatHtmlTag('ul');
			$div_tag = new SwatHtmlTag('div');

			// don't make expandable if it is the root node
			if (strlen($parent_index) != 0) {
				$ul_tag->id = $this->id.'_'.$index.'_branch';
				$ul_tag->class = ($this->open) ?
					'swat-expandable-checkbox-tree-opened' :
					'swat-expandable-checkbox-tree-closed';
			}

			$div_tag->open();
			$ul_tag->open();

			foreach ($child_nodes as $child_node) {
				$nodes = $this->displayNode($child_node, $nodes, $index);
			}

			$ul_tag->close();
			$div_tag->close();
		}

		if (strlen($parent_index) != 0)
			$li_tag->close();

		// count checkable nodes
		if ($node->value !== null)
			$nodes++;

		return $nodes;
	}

	// }}}
}

?>
