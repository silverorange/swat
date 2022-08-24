/**
 * Creates a new check-all object
 *
 * The check-all object is responsible for handling change events and
 * notifying its controller on state change.
 *
 * @param string id the unique identifier of this check-all object.
 * @param integer extended_count the count of all selected items including
 *                               those not currently visible.
 * @param string extended_title a title to display next to the checkbox for
 *                              selecting extended items
 */
function SwatCheckAll(id, extended_count, extended_title) {
  this.id = id;
  this.check_all = document.getElementById(id + '_value');
  this.controller = null;
  this.extended_count = extended_count;
  this.extended_title = extended_title;
}

/**
 * Set the state of this check-all object
 *
 * SwatCheckboxList uses this method to update the check-all's state when all
 * checkbox list items are checked/unchecked.
 *
 * @param boolean checked the new state of this check-all object.
 */
SwatCheckAll.prototype.setState = function(checked) {
  this.check_all.checked = checked;
  this.updateExtendedCheckbox();
};

SwatCheckAll.prototype.updateExtendedCheckbox = function() {
  var container = document.getElementById(this.id + '_extended');

  if (!container) {
    return;
  }

  if (this.check_all.checked) {
    var in_attributes = { opacity: { from: 0, to: 1 } };
    var in_animation = new YAHOO.util.Anim(
      container,
      in_attributes,
      0.5,
      YAHOO.util.Easing.easeIn
    );

    container.style.opacity = 0;
    container.style.display = 'block';
    in_animation.animate();
  } else {
    container.style.display = 'none';
    container.getElementsByTagName('input')[0].checked = false;
  }
};

/**
 * Sets the controlling checkbox list
 *
 * This adds an event handler to the check-all to update the list when this
 * check-all is checked/unchecked.
 *
 * @param SwatCheckboxList controller the JavaScript object that represents the
 *                                     checkbox list controlling this check-all
 *                                     object.
 */
SwatCheckAll.prototype.setController = function(controller) {
  // only add the event handler the first time
  if (this.controller === null) {
    YAHOO.util.Event.addListener(
      this.check_all,
      'click',
      this.clickHandler,
      this,
      true
    );

    YAHOO.util.Event.addListener(
      this.check_all,
      'dblclick',
      this.clickHandler,
      this,
      true
    );
  }

  this.controller = controller;
  this.controller.check_all = this;
  this.controller.updateCheckAll();
};

/**
 * Handles click events for this check-all object
 */
SwatCheckAll.prototype.clickHandler = function() {
  // check all checkboxes in the controller object
  this.controller.checkAll(this.check_all.checked);
  this.updateExtendedCheckbox();
};
