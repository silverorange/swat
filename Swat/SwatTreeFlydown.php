<?php

/**
 * A flydown (aka combo-box) selection widget that displays a tree of flydown
 * options.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTreeFlydown extends SwatFlydown
{
    // {{{ public properties

    /**
     * An array containing the branch of the selected node formed by node
     * values.
     *
     * The value of this flydown is the same as the last element in this array.
     *
     * @var array
     */
    public $path = [];

    // }}}
    // {{{ protected properties

    /**
     * A tree collection of {@link SwatTreeFlydownNode} objects for this
     * tree flydown.
     *
     * This property is used in place of the {@link SwatFlydown::$options}
     * property. The options property is ignored.
     *
     * @var SwatTreeFlydownNode
     *
     * @see SwatTreeFlydown::getOptions()
     */
    protected $tree;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new tree flydown control.
     *
     * @param string $id a non-visible unique id for this widget
     *
     * @see SwatWidget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->setTree(new SwatTreeFlydownNode(null, 'root'));
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this tree flydown.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        $actual_value = $this->value;
        if (count($this->path) === 0 && $this->value !== null) {
            // If there is a value but not a path, assume the value is the
            // first element in the path.
            $this->value = [$this->value];
        } else {
            // temporarily set the value to the path for parent::display()
            $this->value = $this->path;
        }

        parent::display();

        // set value back to actual value after parent::display()
        $this->value = $actual_value;
    }

    // }}}
    // {{{ protected function &getOptions()

    /**
     * Gets this flydown's tree as a flat array used in the
     * {@link SwatFlydown::display()} method.
     *
     * @return array a reference to an array of {@link SwatOption}
     *               options
     */
    protected function &getOptions()
    {
        $options = [];

        foreach ($this->tree->getChildren() as $child_node) {
            $this->flattenTree($options, $child_node);
        }

        return $options;
    }

    // }}}
    // {{{ private function flattenTree()

    /**
     * Flattens this flydown's tree into an array of flydown options.
     *
     * The tree is represented by placing spaces in front of option titles for
     * different levels. The values of the options are set to an array
     * representing the tree nodes's paths in the tree.
     *
     * @param array               $options a reference to an array to add the flattened tree
     *                                     nodes to
     * @param SwatTreeFlydownNode $node    the tree node to flatten
     * @param int                 $level   the current level of recursion
     * @param array               $path    the current path represented as an array of tree
     *                                     node option values
     */
    private function flattenTree(
        &$options,
        SwatTreeFlydownNode $node,
        $level = 0,
        $path = [],
    ) {
        $tree_option = clone $node->getOption();

        $pad = str_repeat('&nbsp;', $level * 3);
        $path[] = $tree_option->value;

        $tree_option->title = $pad . $tree_option->title;
        $tree_option->value = $path;

        $options[] = $tree_option;

        foreach ($node->getChildren() as $child_node) {
            $this->flattenTree($options, $child_node, $level + 1, $path);
        }
    }

    // }}}
    // {{{ public function setTree()

    /**
     * Sets the tree to use for display.
     *
     * @param SwatDataTreeNode|SwatTreeFlydownNode $tree the tree to use for
     *                                                   display
     */
    public function setTree($tree)
    {
        if ($tree instanceof SwatDataTreeNode) {
            $tree = SwatTreeFlydownNode::convertFromDataTree($tree);
        } elseif (!$tree instanceof SwatTreeFlydownNode) {
            throw new SwatInvalidClassException(
                'Tree must be an intance of '
                    . 'either SwatDataTreeNode or SwatTreeFlydownNode.',
                0,
                $tree,
            );
        }

        $this->tree = $tree;
    }

    // }}}
    // {{{ public function getTree()

    /**
     * Gets the tree collection of {@link SwatTreeFlydownNode} objects for this
     * tree flydown.
     *
     * @return SwatFlydowTreeNode Tree of nodes
     */
    public function getTree()
    {
        return $this->tree;
    }

    // }}}
    // {{{ public function process()

    /**
     * Processes this tree flydown.
     *
     * Populates the path property of this flydown with the path to the node
     * selected by the user. The widget value is set to the last id in the
     * path array.
     */
    public function process()
    {
        parent::process();

        if ($this->value === null) {
            $this->path = [];
        } else {
            $this->path = $this->value;
            $this->value = end($this->path);
        }
    }

    // }}}
}
