/**
 * JavaScript for the SwatTileView widget
 *
 * @param id string Id of the matching {@link SwatTileView}.
 */
function SwatTileView(id) {
  SwatTileView.superclass.constructor.call(this, id);
  this.init();
}

YAHOO.lang.extend(SwatTileView, SwatView, {
  /**
   * Gets an item node in a tile view
   *
   * The item node is the parent div one level below the root tile view
   * element.
   *
   * @param DOMElement node the arbitrary descendant node.
   *
   * @return DOMElement the item node.
   */
  getItemNode: function(node) {
    var tile_node = node;

    // search for containing tile element
    while (
      tile_node.parentNode !== this.view_node &&
      tile_node.nodeName != 'BODY'
    ) {
      tile_node = tile_node.parentNode;
    }
    // we reached the body element without finding the tile node
    if (tile_node.nodeName == 'BODY') {
      tile_node = node;
    }

    return tile_node;
  }
});

SwatTileView.prototype.init = function() {
  this.items = [];
  this.view_node = document.getElementById(this.id);

  for (var i = 0; i < this.view_node.childNodes.length; i++) {
    var node_name = this.view_node.childNodes[i].nodeName.toLowerCase();
    if (node_name == 'div') {
      this.items.push(this.view_node.childNodes[i]);
    }
  }
};

/**
 * Selects an item node in this tile view
 *
 * For tile views, this method also highlights selected tiles.
 *
 * @param DOMElement node an arbitrary descendant node of the item node to be
 *                         selected.
 * @param String selector an identifier of the object that selected the item
 *                         node.
 */
SwatTileView.prototype.selectItem = function(node, selector) {
  SwatTileView.superclass.selectItem.call(this, node, selector);

  var tile_node = this.getItemNode(node);
  if (
    this.isSelected(tile_node) &&
    !tile_node.classList.contains('highlight')
  ) {
    tile_node.classList.add('highlight');
  }
};

/**
 * Deselects an item node in this tile view
 *
 * For tile views, this method also unhighlights deselected tiles.
 *
 * @param DOMElement node an arbitrary descendant node of the item node to be
 *                         deselected.
 * @param String selector an identifier of the object that deselected the item
 *                         node.
 */
SwatTileView.prototype.deselectItem = function(node, selector) {
  SwatTileView.superclass.deselectItem.call(this, node, selector);

  var tile_node = this.getItemNode(node);
  if (
    !this.isSelected(tile_node) &&
    tile_node.classList.contains('highlight')
  ) {
    tile_node.classList.remove('highlight');
  }
};
