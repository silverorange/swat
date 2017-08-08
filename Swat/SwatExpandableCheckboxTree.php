<?php

/**
 * A checkbox array widget formatted into a tree where each branch can
 * be expanded
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatExpandableCheckboxTree extends SwatCheckboxTree
{

	/**
	 * All branches are open
	 */
	const BRANCH_STATE_OPEN   = 1;

	/**
	 * All branches are closed
	 */
	const BRANCH_STATE_CLOSED = 2;

	/**
	 * Branches with checked options are open (default)
	 */
	const BRANCH_STATE_AUTO   = 3;

	/**
	 * The initial branch state of the tree
	 *
	 * @var boolean
	 *
	 * @see SwatExpandableCheckboxTree::$branch_state
	 *
	 * @deprecated This property is replaced by the
	 *             {@link SwatExpandableCheckboxTree::$branch_state} property.
	 *             If this boolean property is specified, it will override the
	 *             <i>$branch_state</i> property with either
	 *             {@link SwatExpandableCheckboxTree:BRANCH_STATE_OPEN} or
	 *             {@link SwatExpandableCheckboxTree:BRANCH_STATE_CLOSED}. This
	 *             property will be removed in later versions of Swat.
	 */
	public $open;

	/**
	 * Initial state of tree branches
	 *
	 * Should be set to one of the SwatExpandableCheckboxTree::BRANCH_STATE_*
	 * constants. The default branch state is
	 * {@link SwatExpandableCheckboxTree::BRANCH_STATE_AUTO}.
	 *
	 * @var integer
	 */
	public $branch_state = self::BRANCH_STATE_AUTO;

	/**
	 * Whether or not the state of child boxes depends on the state of
	 * its parent boxes and the state of parent boxes depends on the state of
	 * all its children
	 *
	 * @var boolean
	 */
	public $dependent_boxes = true;

	protected $checked_parents = array();

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
			'packages/swat/javascript/swat-expandable-checkbox-tree.js'
		);
	}

	/**
	 * Displays this expandable checkbox tree
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		SwatWidget::display();

		$this->getForm()->addHiddenField($this->id.'_submitted', 1);

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();

		$this->label_tag = new SwatHtmlTag('label');

		$this->input_tag = new SwatHtmlTag('input');
		$this->input_tag->type = 'checkbox';
		$this->input_tag->name = $this->id.'[]';

		$div_tag->open();

		if ($this->tree !== null) {
			$this->checked_parents = $this->values;
			$num_nodes = $this->displayNode($this->tree);
		} else {
			$num_nodes = 0;
		}

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

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
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	/**
	 * Gets the inline JavaScript for this expandable checkbox tree
	 *
	 * @return string the inline JavaScript for this expandable checkbox tree.
	 */
	protected function getInlineJavaScript()
	{
		$dependent_boxes = ($this->dependent_boxes) ? 'true' : 'false';

		if ($this->open !== null) {
			$branch_state = ($this->open) ?
				self::BRANCH_STATE_OPEN : self::BRANCH_STATE_CLOSED;
		} else {
			if ($this->branch_state !== self::BRANCH_STATE_OPEN &&
				$this->branch_state !== self::BRANCH_STATE_CLOSED &&
				$this->branch_state !== self::BRANCH_STATE_AUTO)
				$branch_state = self::BRANCH_STATE_AUTO;
			else
				$branch_state = $this->branch_state;
		}

		$expandable_node_ids = array_map(
			array('SwatString', 'quoteJavaScriptString'),
			$this->getExpandableNodeIds($this->tree));

		$expandable_node_ids = implode(', ', $expandable_node_ids);

		$javascript = sprintf(
			"var %s_obj = new SwatExpandableCheckboxTree('%s', %s, %s, [%s]);",
			$this->id,
			$this->id,
			$dependent_boxes,
			$branch_state,
			$expandable_node_ids);

		return $javascript;
	}

	/**
	 * Displays a node in a tree as a checkbox input
	 *
	 * @param SwatDataTreeNode $node the node to display.
	 * @param integer $nodes the current number of nodes.
	 * @param string $parent_index the path of the parent node.
	 *
	 * @return integer the number of checkable nodes in the tree.
	 */
	private function displayNode(
		SwatDataTreeNode $node,
		$nodes = 0,
		$parent_index = ''
	) {
		$child_nodes = $node->getChildren();

		// build a unique id of the indexes of the tree
		if ($parent_index === '' || $parent_index === null) {
			// index of the first node is just the node index
			$index = $node->getIndex();
		} else {
			// index of other nodes is a combination of parent indexes
			$index = $parent_index.'.'.$node->getIndex();

			$li_tag = new SwatHtmlTag('li');
			if (count($child_nodes) > 0)
				$li_tag->id = $this->id.'_'.$index.'_container';

			$li_tag->open();

			if ($node->value === null) {
				if ($this->dependent_boxes) {
					// show a checkbox just for the check-all functionality
					$this->input_tag->id = $this->id.'_'.$index;
					$this->input_tag->value = null;
					$this->input_tag->checked = ($this->nodeIsChecked($node)) ?
						'checked' : null;

					if (!$this->isSensitive())
						$this->input_tag->disabled = 'disabled';

					$this->label_tag->for = $this->id.'_'.$index;
					$this->label_tag->class =
						'swat-control swat-expandable-checkbox-tree-null';

					$this->label_tag->setContent($node->title);

					$this->input_tag->display();
					$this->label_tag->display();
				} else {
					$span_tag = new SwatHtmlTag('span');
					$span_tag->id = $this->id.'_'.$index;
					$span_tag->class =
						'swat-expandable-checkbox-tree-null-node';

					$span_tag->setContent($node->title);
					$span_tag->display();
				}
			} else {
				$this->input_tag->id = $this->id.'_'.$index;
				$this->input_tag->value = $node->value;
				$this->input_tag->checked = ($this->nodeIsChecked($node)) ?
					'checked' : null;

				if (!$this->isSensitive())
					$this->input_tag->disabled = 'disabled';

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
			if ($parent_index !== '') {
				$ul_tag->id = $this->id.'_'.$index.'_branch';
				$ul_tag->class = 'swat-expandable-checkbox-tree-opened';
			}

			$div_tag->open();
			$ul_tag->open();

			foreach ($child_nodes as $child_node) {
				$nodes = $this->displayNode($child_node, $nodes, $index);
			}

			$ul_tag->close();
			$div_tag->close();
		}

		if ($parent_index !== '' && $parent_index !== null) {
			$li_tag->close();
		}

		// count checkable nodes
		if ($node->value !== null)
			$nodes++;

		return $nodes;
	}

	/**
	 * Check whether the checkbox should be checked
	 *
	 * @param SwatDataTreeNode $node the node to check.
	 *
	 * @return boolean Whether the checkbox is checked or not.
	 */
	private function nodeIsChecked(SwatDataTreeNode $node)
	{
		$checked = false;

		if (in_array($node->value, $this->values)) {
			$checked = true;
		} elseif ($this->dependent_boxes && $node->getParent() !== null) {
			if (in_array($node->value, $this->checked_parents)) {
				$checked = true;
			} else {
				$checked = $this->nodeIsChecked($node->getParent());

				if ($checked) {
					// cache checked parents to make recursion faster
					$this->checked_parents[$node->value] = $node->value;
				}
			}
		}

		return $checked;
	}

	/**
	 * Gets the node XHTML ids of all expandable nodes in this expandable
	 * checkbox tree
	 *
	 * @param SwatDataTreeNode $node the root node.
	 * @param string $parent_id optional. The XHTML id of the parent node. Not
	 *                           required for the checkbox tree root node.
	 *
	 * @return array an array of expandable node XHTML ids.
	 */
	private function getExpandableNodeIds(
		SwatDataTreeNode $node,
		$parent_id = ''
	) {
		$expandable_ids = array();

		$child_nodes = $node->getChildren();

		if (count($child_nodes) > 0) {

			if ($parent_id === '') {
				// id of the first node is just the node index and is not
				// expandable
				$id = $node->getIndex();
			} else {
				// id of other nodes is a combination of parent id
				$id = $parent_id.'.'.$node->getIndex();
				$expandable_ids[] = $id;
			}

			foreach ($child_nodes as $child_node) {
				$expandable_ids = array_merge($expandable_ids,
					$this->getExpandableNodeIds($child_node, $id));
			}

		}

		return $expandable_ids;
	}

}

?>
