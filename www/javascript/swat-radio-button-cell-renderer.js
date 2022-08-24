/**
 * Radio button cell renderer controller
 *
 * @param string id the unique identifier of this radio button cell renderer.
 * @param SwatView view the view containing this radio button cell renderer.
 */
function SwatRadioButtonCellRenderer(id, view) {
  this.id = id;
  this.view = view;
  this.radio_list = [];
  this.current_node = null;

  /*
   * Get all radio buttons with name = id and that are contained in the
   * currect view. Note: getElementsByName() does not work from a node
   * element.
   */
  var view_node = document.getElementById(this.view.id);
  var input_nodes = view_node.getElementsByTagName('input');
  for (var i = 0; i < input_nodes.length; i++) {
    if (input_nodes[i].name == id) {
      if (input_nodes[i].checked) this.current_node = input_nodes[i];

      this.radio_list.push(input_nodes[i]);
      this.updateNode(input_nodes[i]);
      YAHOO.util.Event.addListener(
        input_nodes[i],
        'click',
        this.handleClick,
        this,
        true
      );

      YAHOO.util.Event.addListener(
        input_nodes[i],
        'dblclick',
        this.handleClick,
        this,
        true
      );
    }
  }
}

SwatRadioButtonCellRenderer.prototype.handleClick = function(e) {
  if (this.current_node) {
    this.updateNode(this.current_node);
  }

  this.current_node = YAHOO.util.Event.getTarget(e);
  this.updateNode(this.current_node);
};

SwatRadioButtonCellRenderer.prototype.updateNode = function(radio_button_node) {
  if (radio_button_node.checked) {
    this.view.selectItem(radio_button_node, this.id);
  } else {
    this.view.deselectItem(radio_button_node, this.id);
  }
};
