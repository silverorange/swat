class SwatCheckboxEntryList extends SwatCheckboxList {
  constructor(id) {
    super(id);
    this.entry_list = [];
  }

  init() {
    super.init();

    for (var i = 0; i < this.check_list.length; i++) {
      var option = this.check_list[i];
      this.entry_list[i] = document.getElementById(
        this.id + '_entry_' + option.value
      );

      this.check_list[i]._index = i;
    }

    this.updateFields();
  }

  handleClick(e) {
    super.handleClick(e);
    this.toggleEntry(e.target._index);
  }

  checkAll(checked) {
    super.checkAll(checked);
    for (var i = 0; i < this.check_list.length; i++) {
      this.setEntrySensitivity(i, checked);
    }
  }

  toggleEntry(index) {
    if (this.entry_list[index]) {
      this.setEntrySensitivity(index, this.entry_list[index].disabled);
    }
  }

  setEntrySensitivity(index, sensitivity) {
    if (this.entry_list[index]) {
      if (sensitivity) {
        this.entry_list[index].disabled = false;
        this.entry_list[index].classList.remove('swat-insensitive');
      } else {
        this.entry_list[index].disabled = true;
        this.entry_list[index].classList.add('swat-insensitive');
      }
    }
  }

  updateFields() {
    for (var i = 0; i < this.check_list.length; i++) {
      this.setEntrySensitivity(i, this.check_list[i].checked);
    }
  }
}
