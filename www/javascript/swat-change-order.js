/**
 * An orderable list control widget
 *
 * Some of the drag and drop code is adapted from Nat Friedman's drag.js
 * script.
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */

class SwatChangeOrder {
  /**
   * An orderable list control widget
   *
   * @param {string} id the unique identifier of this object.
   * @param {boolean} sensitive the initial sensitive of this object.
   */
  constructor(id, sensitive) {
    this.is_webkit = /AppleWebKit|Konqueror|KHTML/gi.test(navigator.userAgent);

    this.id = id;

    this.list_div = document.getElementById(this.id + '_list');
    this.buttons = document.getElementsByName(this.id + '_buttons');

    // the following two lines must be split on two lines to
    // handle a Firefox bug.
    var hidden_value = document.getElementById(this.id + '_value');
    var value_array = hidden_value.value.split(',');
    var count = 0;
    var node = null;

    // re-populate list with dynamic items if page is refreshed
    var items_value = document.getElementById(this.id + '_dynamic_items').value;
    if (items_value !== '') {
      this.list_div.innerHTML = items_value;
    }

    // remove text nodes and set value on nodes
    for (var i = 0; i < this.list_div.childNodes.length; i++) {
      node = this.list_div.childNodes[i];
      if (node.nodeType == 3) {
        this.list_div.removeChild(node);
        i--;
      } else if (node.nodeType == 1) {
        // remove sentinel node and drop-shadow
        if (node.id == this.id + '_sentinel' || node.id == 'drop') {
          this.list_div.removeChild(node);
          i--;
          continue;
        }

        node.order_value = value_array[count];
        node.order_index = count;
        // assign a back reference for event handlers
        node.controller = this;
        // add click handlers to the list items
        node.addEventListener(
          'mousedown',
          SwatChangeOrder.mousedownEventHandler
        );
        node.classList.remove('swat-change-order-item-active');

        count++;
      }
    }

    // since the DOM only has an insertBefore() method we use a sentinel node
    // to make moving nodes down easier.
    var sentinel_node = document.createElement('div');
    sentinel_node.id = this.id + '_sentinel';
    sentinel_node.style.display = 'block';
    this.list_div.appendChild(sentinel_node);

    // while not a real semaphore, this does prevent the user from breaking
    // things by clicking buttons or items while an animation is occuring.
    this.semaphore = true;

    this.active_div = null;

    // this is hard coded to true so we can chose the first element
    if (this.list_div.firstChild !== sentinel_node) {
      this.sensitive = true;

      this.choose(this.list_div.firstChild);
      this.scrollList(this.getScrollPosition(this.list_div.firstChild));
    }

    this.sensitive = sensitive;
    this.orderChangeEvent = new YAHOO.util.CustomEvent('orderChange');

    // add grippies
    window.addEventListener('DOMContentLoaded', () => {
      var node, grippy, height;
      // exclude last item because it is the sentinel node
      for (var i = 0; i < this.list_div.childNodes.length - 1; i++) {
        node = this.list_div.childNodes[i];

        grippy = document.createElement('span');
        grippy.className = 'swat-change-order-item-grippy';
        height = YAHOO.util.Dom.getRegion(node).height - 4;
        grippy.style.height = height + 'px';
        node.insertBefore(grippy, node.firstChild);
      }
    });
  }

  /**
   * Height in pixels of auto-scroll hotspots
   *
   * @var {number}
   */
  static hotspot_height = 40;

  /**
   * Exponential value to use to auto-scroll hotspots
   *
   * @var {number}
   */
  static hotspot_exponent = 1.15;

  /**
   * Delay in milliseconds to use for animations
   *
   * @var {number}
   */
  static animation_delay = 10;

  /**
   * The number of frames of animation to use
   *
   * @var {number}
   */
  static animation_frames = 5;

  static shadow_item_padding = 0;
  static dragging_item = null;
  static is_dragging = false;

  /**
   * Handles moving a dragged item
   *
   * Updates the position of the shadow item as well as updating the position
   * of the drop target.
   *
   * TODO: Fix when user uses mouse scrollwheel when dragging.
   *
   * @param {Event} event the event that triggered this function.
   *
   * @return {boolean} false.
   */
  static mousemoveEventHandler(event) {
    var shadow_item = SwatChangeOrder.dragging_item;
    var drop_marker = SwatChangeOrder.dragging_drop_marker;
    var list_div = shadow_item.original_item.parentNode;

    if (shadow_item.style.display == 'none') {
      SwatChangeOrder.is_dragging = true;
      shadow_item.style.display = 'block';
      shadow_item.scroll_timer = setInterval(
        SwatChangeOrder.scrollTimerHandler(),
        100
      );

      shadow_item.update_timer = setInterval(
        SwatChangeOrder.updateTimerHandler(),
        300
      );
    }

    var left = YAHOO.util.Event.getPageX(event) - shadow_item.mouse_offset_x;
    var top = YAHOO.util.Event.getPageY(event) - shadow_item.mouse_offset_y;
    shadow_item.style.top = top + 'px';
    shadow_item.style.left = left + 'px';

    SwatChangeOrder.updateDropPosition();

    return false;
  }

  /**
   * Handles keydown events for dragged items
   *
   * @param {Event} event
   *
   * @return {boolean} false;
   */
  static keydownEventHandler(event) {
    // user pressed escape
    if (event.key === 'Escape') {
      document.removeEventListener(
        'mousemove',
        SwatChangeOrder.mousemoveEventHandler
      );
      document.removeEventListener(
        'mouseup',
        SwatChangeOrder.mouseupEventHandler
      );
      document.removeEventListener(
        'keydown',
        SwatChangeOrder.keydownEventHandler
      );

      var shadow_item = SwatChangeOrder.dragging_item;
      var drop_marker = SwatChangeOrder.dragging_drop_marker;
      var list_div = shadow_item.original_item.parentNode;

      clearInterval(shadow_item.timer);
      clearInterval(shadow_item.scroll_timer);
      clearInterval(shadow_item.update_timer);

      shadow_item.parentNode.removeChild(shadow_item);
      if (drop_marker.parentNode !== null) {
        drop_marker.parentNode.removeChild(drop_marker);
      }
      SwatChangeOrder.dragging_item = null;
      SwatChangeOrder.dragging_drop_marker = null;
      SwatChangeOrder.is_dragging = false;
    }

    return false;
  }

  /**
   * Handles auto-scrolling timeout events on a dragged item
   *
   * This does the auto scrolling of the main list
   */
  static scrollTimerHandler() {
    var shadow_item = SwatChangeOrder.dragging_item;
    var list_div = shadow_item.original_item.parentNode;

    var list_div_top = YAHOO.util.Dom.getY(list_div);
    var middle =
      YAHOO.util.Dom.getY(shadow_item) +
      Math.floor(shadow_item.offsetHeight / 2);

    // top hot spot scrolls list up
    if (
      middle > list_div_top &&
      middle < list_div_top + SwatChangeOrder.hotspot_height &&
      list_div.scrollTop > 0
    ) {
      // hot spot is exponential
      var delta = Math.floor(
        Math.pow(
          SwatChangeOrder.hotspot_exponent,
          SwatChangeOrder.hotspot_height - middle + list_div_top
        )
      );

      list_div.scrollTop -= delta;
    }

    var list_bottom = list_div.offsetHeight + list_div_top;

    // TODO: don't do this if the list is already at the bottom
    // bottom hot spot scrolls list down
    if (
      middle > list_bottom - SwatChangeOrder.hotspot_height &&
      middle < list_bottom
    ) {
      // hot spot is exponential
      var delta = Math.floor(
        Math.pow(
          SwatChangeOrder.hotspot_exponent,
          SwatChangeOrder.hotspot_height - list_bottom + middle
        )
      );

      list_div.scrollTop += delta;
    }
  }

  /**
   * Handles position update timer events on a dragged item
   */
  static updateTimerHandler() {
    SwatChangeOrder.updateDropPosition();
  }

  /**
   * Updates the drop position of the current dragging item
   */
  static updateDropPosition() {
    var shadow_item = SwatChangeOrder.dragging_item;
    var drop_marker = SwatChangeOrder.dragging_drop_marker;
    var list_div = shadow_item.original_item.parentNode;

    var y_middle =
      YAHOO.util.Dom.getY(shadow_item) +
      Math.floor(shadow_item.offsetHeight / 2) -
      YAHOO.util.Dom.getY(list_div) +
      list_div.scrollTop;

    var x_middle =
      YAHOO.util.Dom.getX(shadow_item) +
      Math.floor(shadow_item.offsetWidth / 2) -
      YAHOO.util.Dom.getX(list_div) +
      list_div.scrollLeft;

    var is_grid = shadow_item.original_item.controller.isGrid();

    for (var i = 0; i < list_div.childNodes.length; i++) {
      var node = list_div.childNodes[i];

      if (is_grid) {
        var node_top = node.offsetTop;
        var node_bottom = node_top + node.offsetHeight;
        var node_left = node.offsetLeft;
        var node_right = node_left + node.offsetWidth;
        var node_middle = node_left + Math.floor(node.offsetWidth / 2);

        if (
          node !== drop_marker &&
          y_middle > node_top &&
          y_middle < node_bottom &&
          x_middle > node_left &&
          x_middle < node_right
        ) {
          var next_sibling =
            drop_marker === shadow_item.original_item.nextSibling
              ? drop_marker.nextSibling
              : shadow_item.original_item.nextSibling;

          // hide the drop marker if no move is taking place
          if (node === shadow_item.original_item) {
            drop_marker.style.display = 'none';
          } else {
            drop_marker.style.display = 'block';
            drop_marker.style.paddingTop = '2px';
            drop_marker.style.height = node.offsetHeight - 4 + 'px';
          }

          if (x_middle < node_middle) {
            // dragging-object is on the left side of the grid item
            node.parentNode.insertBefore(drop_marker, node);
          } else if (list_div.childNodes.length == i + 1) {
            // dragging-object is on the right side of the last grid item
            node.parentNode.appendChild(drop_marker);
          } else {
            // dragging-object is on the right side of the grid item
            node.parentNode.insertBefore(
              drop_marker,
              list_div.childNodes[i + 1]
            );
          }
          break;
        }
      } else {
        if (
          node !== drop_marker &&
          y_middle < node.offsetTop + Math.floor(node.offsetHeight / 2)
        ) {
          var next_sibling =
            drop_marker === shadow_item.original_item.nextSibling
              ? drop_marker.nextSibling
              : shadow_item.original_item.nextSibling;

          // hide the drop marker if no move is taking place
          if (node === shadow_item.original_item || node === next_sibling) {
            drop_marker.style.display = 'none';
          } else {
            drop_marker.style.display = 'block';
          }

          node.parentNode.insertBefore(drop_marker, node);

          break;
        }
      }
    }
  }

  /**
   * Handles drop action on dragged items
   *
   * Updates the list order and destroys the shadow item and the target marker.
   * Also resets timers and event handlers.
   *
   * @param {Event} event the drop event.
   *
   * @return {boolean} false.
   */
  static mouseupEventHandler(event) {
    // only allow left click to do things
    if (event.button !== 0) {
      return false;
    }
    document.removeEventListener(
      'mousemove',
      SwatChangeOrder.mousemoveEventHandler
    );
    document.removeEventListener(
      'mouseup',
      SwatChangeOrder.mouseupEventHandler
    );

    // document.removeEventListener('keydown', SwatChangeOrder.keydownEventHandler);

    var shadow_item = SwatChangeOrder.dragging_item;
    var drop_marker = SwatChangeOrder.dragging_drop_marker;
    var list_div = shadow_item.original_item.parentNode;

    clearInterval(shadow_item.scroll_timer);
    clearInterval(shadow_item.update_timer);

    // reposition the item
    // TODO: don't update this if the position is the same as originally
    if (drop_marker.parentNode !== null) {
      list_div.insertBefore(shadow_item.original_item, drop_marker);
      shadow_item.original_item.controller.updateValue();
      shadow_item.original_item.controller.updateDynamicItemsValue();
    }

    shadow_item.parentNode.removeChild(shadow_item);

    if (drop_marker.parentNode !== null) {
      drop_marker.parentNode.removeChild(drop_marker);
    }
    SwatChangeOrder.dragging_item = null;
    SwatChangeOrder.dragging_drop_marker = null;
    SwatChangeOrder.is_dragging = false;

    return false;
  }

  /**
   * Handles an click event for an item in the list
   *
   * @param {Event} event the event to handle.
   *
   * @return {boolean} false.
   */
  static mousedownEventHandler(event) {
    // prevent text selection
    event.preventDefault();

    // only allow left click to do things
    if (event.button !== 0) {
      return false;
    }

    if (!this.controller.sensitive) {
      return false;
    }

    // select the node
    this.controller.choose(this);

    // prime for dragging
    var shadow_item = this.cloneNode(true);
    shadow_item.original_item = this;
    document.getElementsByTagName('body')[0].appendChild(shadow_item);

    SwatZIndexManager.raiseElement(shadow_item);

    shadow_item.style.display = 'none';
    shadow_item.className += ' swat-change-order-item-shadow';
    shadow_item.style.width = this.offsetWidth - 4 + 'px';

    shadow_item.mouse_offset_x =
      YAHOO.util.Event.getPageX(event) - YAHOO.util.Dom.getX(this);

    shadow_item.mouse_offset_y =
      YAHOO.util.Event.getPageY(event) - YAHOO.util.Dom.getY(this);

    var drop_marker = document.createElement('div');

    if (this.controller.isGrid()) {
      drop_marker.style.borderLeftStyle = 'solid';
      drop_marker.style.borderLeftColor = '#aaa';
      drop_marker.style.borderLeftWidth = '1px';
      drop_marker.style.cssFloat = 'left';
    } else {
      drop_marker.style.borderBottomStyle = 'solid';
      drop_marker.style.borderBottomColor = '#aaa';
      drop_marker.style.borderBottomWidth = '1px';
    }

    drop_marker.style.display = 'none';
    drop_marker.id = 'drop';

    SwatChangeOrder.dragging_item = shadow_item;
    SwatChangeOrder.dragging_drop_marker = drop_marker;

    document.addEventListener(
      'mousemove',
      SwatChangeOrder.mousemoveEventHandler
    );
    document.addEventListener('mouseup', SwatChangeOrder.mouseupEventHandler);
    document.addEventListener('keydown', SwatChangeOrder.keydownEventHandler);

    return false;
  }

  /**
   * A static callback function for the move-to-top window timeout.
   *
   * @param {SwatChangeOrder} change_order the change-order widget to work with.
   * @param {number} steps the number of steps to skip when moving the active
   *                       element.
   */
  static staticMoveToTop(change_order, steps) {
    change_order.moveToTopHelper(steps);
  }

  /**
   * A static callback function for the move-to-bottom window timeout.
   *
   * @param {SwatChangeOrder} change_order the change-order widget to work with.
   * @param {number} steps the number of steps to skip when moving the active
   *                       element.
   */
  static staticMoveToBottom(change_order, steps) {
    change_order.moveToBottomHelper(steps);
  }

  /**
   * Dynamically adds an item to this change-order
   *
   * @param {Element} el    the element to add to the select list.
   * @param {string}  value the value to save for the order of the element.
   *
   * @return {boolean} true if the element was added, otherwise false.
   */
  add(el, value) {
    if (!this.semaphore) {
      // TODO queue elements and add when semaphore is available
      return false;
    }

    el.classList.add('swat-change-order-item');
    el.addEventListener('mousedown', SwatChangeOrder.mousedownEventHandler);

    var order_index = this.count();

    el.controller = this;
    el.order_index = order_index;
    el.order_value = value;

    this.list_div.insertBefore(el, this.list_div.childNodes[order_index]);

    // update hidden value
    var value_array;
    var hidden_value = document.getElementById(this.id + '_value');
    if (hidden_value.value === '') {
      value_array = [];
    } else {
      value_array = hidden_value.value.split(',');
    }
    value_array.push(value);
    hidden_value.value = value_array.join(',');

    this.updateDynamicItemsValue();

    return true;
  }

  /**
   * Dynamically removes an item from this change-order
   *
   * @param {Element} el the element to remove.
   *
   * @return {boolean} true if the element was removed, otherwise false.
   */
  remove(el) {
    if (!this.semaphore) {
      // TODO queue elements and remove when semaphore is available
      return false;
    }

    YAHOO.util.Event.purgeElement(el);

    // remove from hidden value
    var hidden_value = document.getElementById(this.id + '_value');
    var value_array = hidden_value.value.split(',');
    value_array.splice(el.order_index, 1);
    hidden_value.value = value_array.join(',');

    if (this.active_div === el) {
      this.active_div = null;
    }

    this.list_div.removeChild(el);

    this.updateDynamicItemsValue();

    return true;
  }

  /**
   * Gets the number of items in this change-order
   *
   * @return {number} the number of items in this change-order.
   */
  count() {
    return this.list_div.childNodes.length - 1;
  }

  /**
   * Gets whether or not this change-order contains an item with the given value
   *
   * @param {string} value the value to check for.
   *
   * @return {boolean} true if there is an item with the given value in this
   *                 change-order, otherwise false.
   */
  containsValue(value) {
    var value_array;
    var hidden_value = document.getElementById(this.id + '_value');
    if (hidden_value.value === '') {
      value_array = [];
    } else {
      value_array = hidden_value.value.split(',');
    }

    for (var i = 0; i < value_array.length; i++) {
      if (value_array[i] === value) {
        return true;
      }
    }

    return false;
  }

  /**
   * Choses an element in this change order as the active div
   *
   * Only allows chosing if the semaphore is not set.
   *
   * @param {Element} div the element to chose.
   */
  choose(div) {
    if (
      this.semaphore &&
      this.sensitive &&
      div !== this.active_div &&
      !SwatChangeOrder.is_dragging
    ) {
      if (this.active_div !== null) {
        this.active_div.className = 'swat-change-order-item';
      }

      div.className = 'swat-change-order-item swat-change-order-item-active';

      this.active_div = div;

      // update the index value of this element
      for (var i = 0; i < this.list_div.childNodes.length; i++) {
        if (this.list_div.childNodes[i] === this.active_div) {
          this.active_div.order_index = i;
          break;
        }
      }
    }
  }

  /**
   * Moves the active element to the top of the list
   *
   * Only functions if the semaphore is not set. Sets the semaphore.
   */
  moveToTop() {
    if (this.semaphore && this.sensitive) {
      this.semaphore = false;
      this.setButtonsSensitive(false);

      var steps = Math.ceil(
        this.active_div.order_index / SwatChangeOrder.animation_frames
      );

      this.moveToTopHelper(steps);
    }
  }

  /**
   * A helper method that moves the active element up and sets a timeout callback
   * to move it up again until it reaches the top
   *
   * Unsets the semaphore after the active element is at the top.
   *
   * @param number steps the number of steps to skip when moving the active
   *                      element.
   */
  moveToTopHelper(steps) {
    if (this.moveUpHelper(steps)) {
      setTimeout(() => {
        SwatChangeOrder.staticMoveToTop(this, steps);
      }, SwatChangeOrder.animation_delay);
    } else {
      this.semaphore = true;
      this.setButtonsSensitive(true);
      this.updateDynamicItemsValue();
    }
  }

  /**
   * Moves the active element to the bottom of the list
   *
   * Only functions if the semaphore is not set. Sets the semaphore.
   */
  moveToBottom() {
    if (this.semaphore && this.sensitive) {
      this.semaphore = false;
      this.setButtonsSensitive(false);

      var steps = Math.ceil(
        (this.list_div.childNodes.length - this.active_div.order_index - 1) /
          SwatChangeOrder.animation_frames
      );

      this.moveToBottomHelper(steps);
    }
  }

  /**
   * A helper method that moves the active element down and sets a timeout
   * callback to move it down again until it reaches the bottom
   *
   * Unsets the semaphore after the active element is at the bottom.
   *
   * @param number steps the number of steps to skip when moving the active
   *                      element.
   */
  moveToBottomHelper(steps) {
    if (this.moveDownHelper(steps)) {
      setTimeout(() => {
        SwatChangeOrder.staticMoveToBottom(this, steps);
      }, SwatChangeOrder.animation_delay);
    } else {
      this.semaphore = true;
      this.setButtonsSensitive(true);
      this.updateDynamicItemsValue();
    }
  }

  /**
   * Moves the active element up one space
   *
   * Only functions if the semaphore is not set.
   */
  moveUp() {
    if (this.semaphore && this.sensitive) {
      this.moveUpHelper(1);
      this.updateDynamicItemsValue();
    }
  }

  /**
   * Moves the active element down one space
   *
   * Only functions if the semaphore is not set.
   */
  moveDown() {
    if (this.semaphore && this.sensitive) {
      this.moveDownHelper(1);
      this.updateDynamicItemsValue();
    }
  }

  /**
   * Moves the active element up a number of steps
   *
   * @param {number} steps the number of steps to move the active element up by.
   *
   * @return {boolean} true if the element is not hitting the top of the list,
   *                   false otherwise.
   */
  moveUpHelper(steps) {
    // can't move the top of the list up
    if (this.list_div.firstChild === this.active_div) {
      return false;
    }

    var return_val = true;

    var prev_div = this.active_div;
    for (var i = 0; i < steps; i++) {
      prev_div = prev_div.previousSibling;
      if (prev_div === this.list_div.firstChild) {
        return_val = false;
        break;
      }
    }

    this.list_div.insertBefore(this.active_div, prev_div);

    this.active_div.order_index = Math.max(
      this.active_div.order_index - steps,
      0
    );

    this.updateValue();
    this.scrollList(this.getScrollPosition(this.active_div));

    return return_val;
  }

  /**
   * Moves the active element down a number of steps
   *
   * @param {number} steps the number of steps to move the active element down by.
   *
   * @return {boolean} true if the element is not hitting the bottom of the list,
   *                   false otherwise.
   */
  moveDownHelper(steps) {
    // can't move the bottom of the list down
    if (this.list_div.lastChild.previousSibling === this.active_div) {
      return false;
    }

    var return_val = true;

    var prev_div = this.active_div;
    for (var i = 0; i < steps + 1; i++) {
      prev_div = prev_div.nextSibling;
      if (prev_div === this.list_div.lastChild) {
        return_val = false;
        break;
      }
    }

    this.list_div.insertBefore(this.active_div, prev_div);

    // we take the minimum of the list length - 1 to get the highest index
    // and then - 1 again for the sentinel.
    this.active_div.order_index = Math.min(
      this.active_div.order_index + steps,
      this.list_div.childNodes.length - 2
    );

    this.updateValue();
    this.scrollList(this.getScrollPosition(this.active_div));

    return return_val;
  }

  /**
   * Sets the sensitivity on buttons for this control
   *
   * @param {boolean} sensitive whether the buttons are sensitive.
   */
  setButtonsSensitive(sensitive) {
    for (var i = 0; i < this.buttons.length; i++) {
      this.buttons[i].disabled = !sensitive;
    }
  }

  /**
   * Sets whether this control is sensitive
   *
   * @param {boolean} sensitive whether this control is sensitive.
   */
  setSensitive(sensitive) {
    this.setButtonsSensitive(sensitive);
    this.sensitive = sensitive;

    if (sensitive) {
      document.getElementById(this.id).className = 'swat-change-order';
    } else {
      document.getElementById(this.id).className =
        'swat-change-order swat-change-order-insensitive';
    }
  }

  /**
   * Updates the value of the hidden field containing the ordering of elements
   */
  updateValue() {
    var temp = '';
    var index = 0;
    var drop_marker = SwatChangeOrder.dragging_drop_marker;

    // one less than list length so we don't count the sentinal node
    for (var i = 0; i < this.list_div.childNodes.length - 1; i++) {
      // ignore drop marker node
      if (this.list_div.childNodes[i] != drop_marker) {
        if (index > 0) {
          temp += ',';
        }

        temp += this.list_div.childNodes[i].order_value;

        // update node indexes
        this.list_div.childNodes[i].order_index = index;
        index++;
      }
    }

    var hidden_field = document.getElementById(this.id + '_value');

    // fire order-changed event
    if (temp != hidden_field.value) {
      this.orderChangeEvent.fire(temp);
    }

    // update a hidden field with current order of keys
    hidden_field.value = temp;
  }

  /**
   * Updates the value of the hidden field containing the dynamic item nodes
   *
   * This allows the changeorder state to stay consistent when the page is
   * soft-refreshed after adding or removing items.
   */
  updateDynamicItemsValue() {
    var items_value = document.getElementById(this.id + '_dynamic_items');
    items_value.value = this.list_div.innerHTML;
  }

  /**
   * Gets the y-position of the active element in the scrolling section
   */
  getScrollPosition(element) {
    // this conditional is to fix behaviour in IE
    if (this.list_div.firstChild.offsetTop > this.list_div.offsetTop) {
      var y_position =
        element.offsetTop - this.list_div.offsetTop + element.offsetHeight / 2;
    } else {
      var y_position = element.offsetTop + element.offsetHeight / 2;
    }

    return y_position;
  }

  /**
   * Scrolls the list to a y-position
   *
   * This method acts the same as scrollTo() but it acts on a div instead of the
   * window.
   *
   * @param {number} y_coord the y value to scroll the list to in pixels.
   */
  scrollList(y_coord) {
    // clientHeight is the height of the visible scroll area
    var half_list_height = parseInt(this.list_div.clientHeight / 2);

    if (y_coord < half_list_height) {
      this.list_div.scrollTop = 0;
      return;
    }

    // scrollHeight is the height of the contents inside the scroll area
    if (this.list_div.scrollHeight - y_coord < half_list_height) {
      this.list_div.scrollTop =
        this.list_div.scrollHeight - this.list_div.clientHeight;

      return;
    }

    // offsetHeight is clientHeight + padding
    var factor =
      (y_coord - half_list_height) /
      (this.list_div.scrollHeight - this.list_div.offsetHeight);

    this.list_div.scrollTop = Math.floor(
      (this.list_div.scrollHeight - this.list_div.clientHeight) * factor
    );
  }

  /**
   * Whether this SwatChangeOrder widget represents a vertical list (default) or
   * a grid of items.
   */
  isGrid() {
    var node = this.list_div.firstElementChild;
    return getComputedStyle(node).cssFloat != 'none';
  }
}
