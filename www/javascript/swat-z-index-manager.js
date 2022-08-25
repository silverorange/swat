class SwatZIndexNode {
  constructor(element) {
    if (element) {
      this.element = element;
      this.element._swat_z_index_node = this;
    } else {
      this.element = null;
    }

    this.parent = null;
    this.nodes = [];
  }

  add(node) {
    for (var i = 0; i < this.nodes.length; i++) {
      if (this.nodes[i] === node || this.nodes[i].id === node) {
        return;
      }
    }

    this.nodes.push(node);
    node.parent = this;
  }

  remove(node) {
    var found = false;

    for (var i = 0; i < this.nodes.length; i++) {
      if (this.nodes[i] === node) {
        found = this.nodes[i];
        found.parent = null;
        this.nodes.splice(i, 1);
        break;
      }
    }

    return found;
  }
}

/**
 * An object to manage element z-indexes for a webpage
 */
class SwatZIndexManager {
  static tree = new SwatZIndexNode();
  static groups = {};

  /**
   * Default starting z-index for elements
   *
   * @var {number}
   */
  static start = 10;

  static reindexNode(node, index) {
    if (node.element) {
      node.element.style.zIndex = index;
      index++;
    }
    for (var i = 0; i < node.nodes.length; i++) {
      index = SwatZIndexManager.reindexNode(node.nodes[i], index);
    }
    return index;
  }

  static reindex() {
    SwatZIndexManager.reindexNode(
      SwatZIndexManager.tree,
      SwatZIndexManager.start
    );
  }

  static unindexNode(node) {
    if (node.element) {
      node.element.style.zIndex = 0;
    }

    for (var i = 0; i < node.nodes.length; i++) {
      index = SwatZIndexManager.unindexNode(node.nodes[i]);
    }

    return index;
  }

  /**
   * Raises an element to the top
   *
   * Sets the element's z-index to one greater than the current highest z-index
   * in the list of elements.
   *
   * @param {Element} element the element to raise.
   */
  static raiseElement(element, group) {
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
  }

  /**
   * Lowers an element to the bottom
   *
   * Sets the element's z-index to 0 and removes the element's from the current
   * list of elements. Shifts all elements down so that the first element in the
   * list has a z-index of zero and all other elements retain their relative
   * ordering to the first element.
   *
   * @param {Element} element the element to lower.
   * @param {string} group optional. The group name.
   *
   * @return {Element|null} the element that was lowered or null if the element
   *                        was not found.
   */
  static lowerElement(element, group) {
    element.style.zIndex = 0;

    return SwatZIndexManager.removeElement(element, group);
  }

  static raiseGroup(group) {
    if (!SwatZIndexManager.groups[group]) {
      return;
    }

    var group = SwatZIndexManager.groups[group];

    var parent = group.parent;
    parent.remove(group);
    parent.add(group);

    SwatZIndexManager.reindex();
  }

  static lowerGroup(group) {
    if (!SwatZIndexManager.groups[group]) {
      return;
    }

    var group = SwatZIndexManager.groups[group];
    SwatZIndexManager.unindexNode(group);
    SwatZIndexManager.removeGroup(group);
  }

  static removeGroup(group) {
    if (!SwatZIndexManager.groups[group]) {
      return;
    }

    var group = SwatZIndexManager.groups[group];
    group.parent.remove(group);
    SwatZIndexManager.groups[group] = null;

    SwatZIndexManager.reindex();
  }

  /**
   * Removes an element from the list of managed elements
   *
   * @param {Element} element the element to remove
   * @param {string} group optional. The group name.
   *
   * @return {Element|null} the element that was removed or null if the element
   *                        was not found.
   */
  static removeElement(element, group) {
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
  }
}
