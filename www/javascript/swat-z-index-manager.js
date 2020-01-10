import SwatZIndexNode from './swat-z-index-node';

/**
 * An object to manage element z-indexes for a webpage
 */
class SwatZIndexManager {
}

SwatZIndexManager.tree = new SwatZIndexNode();

SwatZIndexManager.groups = {};

/**
 * Default starting z-index for elements
 *
 * @var number
 */
SwatZIndexManager.start = 10;

SwatZIndexManager.reindex = function() {
	SwatZIndexManager.reindexNode(
		SwatZIndexManager.tree,
		SwatZIndexManager.start
	);
};

SwatZIndexManager.reindexNode = function(node, index) {
	if (node.element) {
		node.element.style.zIndex = index;
		index++;
	}

	for (var i = 0; i < node.nodes.length; i++) {
		index = SwatZIndexManager.reindexNode(node.nodes[i], index);
	}

	return index;
};

SwatZIndexManager.unindexNode = function(node) {
	if (node.element) {
		node.element.style.zIndex = 0;
	}

	for (var i = 0; i < node.nodes.length; i++) {
		index = SwatZIndexManager.unindexNode(node.nodes[i]);
	}

	return index;
};

/**
 * Raises an element to the top
 *
 * Sets the element's z-index to one greater than the current highest z-index
 * in the list of elements.
 *
 * @param DOMElement element the element to raise.
 */
SwatZIndexManager.raiseElement = function(element, group) {
	var node;

	// create node if it does not exist
	if (element._swat_z_index_node) {
		node = element._swat_z_index_node;
	} else {
		node = new SwatZIndexNode(element);
	}

	// create group node if it does not exist
	if (group) {
		var group_node;

		if (SwatZIndexManager.groups[group]) {
			group_node = SwatZIndexManager.groups[group];
		} else {
			group_node = new SwatZIndexNode();
			SwatZIndexManager.groups[group] = group_node;
			// add group to root
			SwatZIndexManager.tree.add(group_node);
		}

		// add element node to end of group node
		group_node.remove(node);
		group_node.add(node);
	} else {
		// add element node to end of root
		SwatZIndexManager.tree.remove(node);
		SwatZIndexManager.tree.add(node);
	}

	SwatZIndexManager.reindex();
};

SwatZIndexManager.raiseGroup = function(group) {
	if (!SwatZIndexManager.groups[group]) {
		return;
	}

	var group = SwatZIndexManager.groups[group];

	var parent = group.parent;
	parent.remove(group);
	parent.add(group);

	SwatZIndexManager.reindex();
};

SwatZIndexManager.lowerGroup = function(group) {
	if (!SwatZIndexManager.groups[group]) {
		return;
	}

	var group = SwatZIndexManager.groups[group];
	SwatZIndexManager.unindexNode(group);
	SwatZIndexManager.removeGroup(group);
};

SwatZIndexManager.removeGroup = function(group) {
	if (!SwatZIndexManager.groups[group]) {
		return;
	}

	var group = SwatZIndexManager.groups[group];
	group.parent.remove(group);
	SwatZIndexManager.groups[group] = null;

	SwatZIndexManager.reindex();
};

/**
 * Lowers an element to the bottom
 *
 * Sets the element's z-index to 0 and removes the element's from the current
 * list of elements. Shifts all elements down so that the first element in the
 * list has a z-index of zero and all other elements retain their relative
 * ordering to the first element.
 *
 * @param DOMElement element the element to lower.
 * @param String group optional. The group name.
 *
 * @return mixed the element that was lowered or null if the element was not
 *                found.
 */
SwatZIndexManager.lowerElement = function(element, group) {
	element.style.zIndex = 0;
	return SwatZIndexManager.removeElement(element, group);
};

/**
 * Removes an element from the list of managed elements
 *
 * @param DOMElement element the element to remove
 * @param String group optional. The group name.
 *
 * @return mixed the element that was removed or null if the element was not
 *                found.
 */
SwatZIndexManager.removeElement = function(element, group) {
	if (!element._swat_z_index_node) {
		return null;
	}

	var node = element._swat_z_index_node;
	if (node.parent) {
		node.parent.remove(node);
	}

	if (group) {
		// if group is empty, remove it
		if (SwatZIndexManager.groups[group]) {
			var group_node = new SwatZIndexNode();
			if (group_node.nodes.length === 0) {
				group_node.parent.remove(group_node);
				SwatZIndexManager.groups[group] = null;
			}
		}
	}

	SwatZIndexManager.reindex();

	return element;
};

export default SwatZIndexManager;
