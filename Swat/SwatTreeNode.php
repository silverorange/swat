<?php

/**
 * A simple class for building a tree structure.
 *
 * To create a tree data structure, sub-class this class.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatTreeNode extends SwatObject implements RecursiveIterator, Countable
{
    /**
     * An array of children tree nodes.
     *
     * This array is indexed numerically and starts at 0.
     */
    protected array $children = [];

    /**
     * The parent tree node of this tree node.
     *
     * @var SwatTreeNode
     */
    private $parent;

    /**
     * The index of this child node it its parent array.
     *
     * The index of this node is used like an identifier and is used when
     * building paths in the tree.
     */
    private int $index = 0;

    /**
     * Adds a child node to this node.
     *
     * The parent of the child node is set to this node.
     *
     * @param SwatTreeNode $child the child node to add to this node
     */
    public function addChild($child)
    {
        $child->parent = $this;
        $child->index = count($this->children);
        $this->children[] = $child;
    }

    /**
     * Adds a full tree structure to this node.
     *
     * Identical to addChild() except that it removes the root node from
     * the passed tree.
     *
     * @param SwatTreeNode $tree the tree to add to this node
     */
    public function addTree($tree)
    {
        foreach ($tree->getChildren() as $child) {
            $this->addChild($child);
        }
    }

    /**
     * Gets the path to this node.
     *
     * This method travels up the tree until it reaches a node with a parent
     * of 'null', building a path of ids along the way.
     *
     * @return array an array of indexes that is the path to the given node
     *               from the root of the current tree
     */
    public function &getPath()
    {
        $path = [$this->index];

        $parent = $this->parent;
        while ($parent !== null) {
            $path[] = $parent->index;
            $parent = $parent->parent;
        }

        // we built the path backwards
        $path = array_reverse($path);

        return $path;
    }

    /**
     * Gets the parent node of this node.
     *
     * @return SwatTreeNode the parent node of this node
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Gets this node's children.
     *
     * This method is needed to fulfill the RecursiveIterator interface.
     *
     * @return array this node's children
     */
    #[ReturnTypeWillChange]
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Whether or not this tree node has children.
     *
     * This method is needed to fulfill the RecursiveIterator interface.
     *
     * @return bool true if this node has children or false if this node
     *              does not have children
     */
    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    /**
     * Gets this node's index.
     *
     * @return int this node's index
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Gets the current child node in this node.
     *
     * This method is needed to fulfill the RecursiveIterator interface.
     *
     * @return mixed the current child node in this node as a
     *               {@link SwatTreeNode} object. If the current child node is
     *               invalid, false is returned.
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        return current($this->children);
    }

    /**
     * Gets the key of the current child node in this node.
     *
     * This method is needed to fulfill the RecursiveIterator interface.
     *
     * @return int the key (index) of the current child node in this node
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return key($this->children);
    }

    /**
     * Gets the next child node in this node and moves the internal array
     * pointer forward.
     *
     * This method is needed to fulfill the RecursiveIterator interface.
     *
     * @return mixed the next child node in this node as a
     *               {@link SwatTreeNode} object. If the next child node is
     *               invalid, false is returned.
     */
    #[ReturnTypeWillChange]
    public function next()
    {
        return next($this->children);
    }

    /**
     * Sets the internal pointer in the child nodes array back to the
     * beginning.
     *
     * This method is needed to fulfill the RecursiveIterator interface.
     *
     * @return mixed the first child node in this node as a
     *               {@link SwatTreeNode} object. If there are no child nodes,
     *               false is returned.
     */
    #[ReturnTypeWillChange]
    public function rewind()
    {
        return reset($this->children);
    }

    /**
     * Whether the current child node in this node is valid.
     *
     * This method is needed to fulfill the RecursiveIterator interface.
     *
     * @return bool true if the current child node is valid and false if it
     *              is not
     */
    public function valid(): bool
    {
        return $this->current() !== false;
    }

    /**
     * Gets the number of nodes in this tree or subtree.
     *
     * This method is needed to fulfill the Countable interface.
     *
     * @return int the number of nodes in this tree or subtree
     */
    public function count(): int
    {
        $count = 1;
        foreach ($this->children as $child) {
            $count += count($child);
        }

        return $count;
    }
}
