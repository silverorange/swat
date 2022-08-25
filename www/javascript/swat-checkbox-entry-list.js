function SwatCheckboxEntryList(id) {
  this.entry_list = [];
  SwatCheckboxEntryList.superclass.constructor.call(this, id);
}

YAHOO.lang.extend(SwatCheckboxEntryList, SwatCheckboxList, {
  init: function() {
    SwatCheckboxEntryList.superclass.init.call(this);

    for (var i = 0; i < this.check_list.length; i++) {
      var option = this.check_list[i];
      this.entry_list[i] = document.getElementById(
        this.id + '_entry_' + option.value
      );

      this.check_list[i]._index = i;
    }

    this.updateFields();
  },

  handleClick: function(e) {
    SwatCheckboxEntryList.superclass.handleClick.call(this, e);
    this.toggleEntry(e.target._index);
  },

  checkAll: function(checked) {
    SwatCheckboxEntryList.superclass.checkAll.call(this, checked);
    for (var i = 0; i < this.check_list.length; i++) {
      this.setEntrySensitivity(i, checked);
    }
  }
});

SwatCheckboxEntryList.prototype.toggleEntry = function(index) {
  if (this.entry_list[index]) {
    this.setEntrySensitivity(index, this.entry_list[index].disabled);
  }
};

SwatCheckboxEntryList.prototype.setEntrySensitivity = function(
  index,
  sensitivity
) {
  if (this.entry_list[index]) {
    if (sensitivity) {
      this.entry_list[index].disabled = false;
      this.entry_list[index].classList.remove('swat-insensitive');
    } else {
      this.entry_list[index].disabled = true;
      this.entry_list[index].classList.add('swat-insensitive');
    }
  }
};

SwatCheckboxEntryList.prototype.updateFields = function() {
  for (var i = 0; i < this.check_list.length; i++) {
    this.setEntrySensitivity(i, this.check_list[i].checked);
  }
};
