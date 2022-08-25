/**
 * JavaScript SwatCheckboxList component
 *
 * @param id string Id of the matching {@link SwatCheckboxList} object.
 */
function SwatCheckboxList(id) {
  this.id = id;
  this.check_list = [];
  this.check_all = null; // a reference to a check-all js object

  window.addEventListener('DOMContentLoaded', () => {
    this.init();
  });
}

SwatCheckboxList.prototype.init = function() {
  var id = this.id;
  var container = document.getElementById(this.id);
  var input_elements = container.getElementsByTagName('INPUT');
  for (var i = 0; i < input_elements.length; i++) {
    if (
      input_elements[i].type == 'checkbox' &&
      input_elements[i].id.substring(0, id.length) == id
    ) {
      this.check_list.push(input_elements[i]);
    }
  }

  for (var i = 0; i < this.check_list.length; i++) {
    this.check_list[i].addEventListener('click', e => {
      this.handleClick(e);
    });
    this.check_list[i].addEventListener('dblclick', e => {
      this.handleClick(e);
    });
  }

  this.updateCheckAll();
};

SwatCheckboxList.prototype.handleClick = function(event) {
  this.updateCheckAll();
};

SwatCheckboxList.prototype.updateCheckAll = function() {
  if (this.check_all === null) {
    return;
  }

  var count = 0;
  for (var i = 0; i < this.check_list.length; i++) {
    if (this.check_list[i].checked || this.check_list[i].disabled) {
      count++;
    } else if (count > 0) {
      // can't possibly be all checked or none checked
      break;
    }
  }

  this.check_all.setState(count == this.check_list.length);
};

SwatCheckboxList.prototype.checkAll = function(checked) {
  for (var i = 0; i < this.check_list.length; i++) {
    if (!this.check_list[i].disabled) {
      this.check_list[i].checked = checked;
    }
  }
};
