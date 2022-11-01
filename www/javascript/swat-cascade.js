class SwatCascade {
  constructor(from_flydown_id, to_flydown_id) {
    this.from_flydown = document.getElementById(from_flydown_id);
    this.to_flydown = document.getElementById(to_flydown_id);
    this.children = [];

    this.from_flydown.addEventListener('change', () => {
      this.handleChange();
    });

    this.from_flydown._cascade = this;
  }

  handleChange(e) {
    this.update();
  }

  update() {
    this._updateHelper(false);
  }

  addChild(from_flydown_value, value, title, selected) {
    if (!this.children[from_flydown_value]) {
      this.children[from_flydown_value] = [];
    }

    this.children[from_flydown_value].push(
      new SwatCascadeChild(value, title, selected)
    );
  }

  init() {
    this._updateHelper(true);
  }

  _updateHelper(init) {
    var child_options = this.children[this.from_flydown.value];

    // clear old options
    this.to_flydown.options.length = 0;

    // update any children
    if (this.to_flydown._cascade) {
      this.to_flydown._cascade.update();
    }

    if (child_options) {
      this.to_flydown.disabled = false;

      for (var i = 0; i < child_options.length; i++) {
        // only select default option if we are intializing
        if (init) {
          this.to_flydown.options[this.to_flydown.options.length] = new Option(
            child_options[i].title,
            child_options[i].value,
            child_options[i].selected
          );

          if (child_options[i].selected) {
            this.to_flydown.value = child_options[i].value;
          }
        } else {
          this.to_flydown.options[this.to_flydown.options.length] = new Option(
            child_options[i].title,
            child_options[i].value
          );
        }
      }
    } else {
      // the following string contains UTF-8 encoded non breaking spaces
      this.to_flydown.options[0] = new Option('      ', 0);
      this.to_flydown.disabled = true;
    }
  }
}

class SwatCascadeChild {
  constructor(value, title, selected) {
    this.value = value;
    this.title = title;
    this.selected = selected;
  }
}
