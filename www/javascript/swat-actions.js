class SwatActions {
  constructor(id, values, selected) {
    this.id = id;
    this.flydown = document.getElementById(id + '_action_flydown');
    this.selected_element = selected
      ? document.getElementById(id + '_' + selected)
      : null;

    var button = document.getElementById(id + '_apply_button');

    this.values = values;
    this.message_shown = false;
    this.view = null;
    this.selector_id = null;

    // create message content area
    this.message_content = document.createElement('span');

    // create message dismiss link
    var message_dismiss = document.createElement('a');
    message_dismiss.href = '#';
    message_dismiss.title = SwatActions.dismiss_text;
    message_dismiss.classList.add('swat-actions-message-dismiss-link');

    message_dismiss.appendChild(
      document.createTextNode(SwatActions.dismiss_text)
    );

    message_dismiss.addEventListener('click', e => {
      e.preventDefault();
      this.handleMessageClose();
    });

    // create message span and add content area and dismiss link
    this.message_span = document.createElement('span');
    this.message_span.classList.add('swat-actions-message');
    this.message_span.style.visibility = 'hidden';
    this.message_span.appendChild(this.message_content);
    this.message_span.appendChild(message_dismiss);

    // add message span to document
    button.parentNode.appendChild(this.message_span);

    this.flydown.addEventListener('change', () => {
      this.handleChange();
    });

    this.flydown.addEventListener('keyup', () => {
      this.handleChange();
    });

    button.addEventListener('click', e => {
      this.handleButtonClick(e);
    });
  }

  static dismiss_text = 'Dismiss message.';
  static select_an_action_text = 'Please select an action.';
  static select_an_item_text = 'Please select one or more items.';
  static select_an_item_and_an_action_text =
    'Please select an action, and one or more items.';

  setViewSelector(view, selector_id) {
    if (view.getSelectorItemCount) {
      this.view = view;
      this.selector_id = selector_id;
    }
  }

  handleChange() {
    if (this.selected_element) {
      this.selected_element.classList.add('swat-hidden');
    }

    var id = this.id + '_' + this.values[this.flydown.selectedIndex];

    this.selected_element = document.getElementById(id);

    if (this.selected_element) {
      this.selected_element.classList.remove('swat-hidden');
    }
  }

  handleButtonClick(e) {
    var is_blank;
    var value_exp = this.flydown.value.split('|', 2);
    if (value_exp.length == 1) {
      is_blank = value_exp[0] === '';
    } else {
      is_blank = value_exp[1] == 'N;';
    }

    if (this.view) {
      var items_selected = this.view.getSelectorItemCount(this.selector_id) > 0;
    } else {
      var items_selected = true;
    }

    var message;
    if (is_blank && !items_selected) {
      message = SwatActions.select_an_item_and_an_action_text;
    } else if (is_blank) {
      message = SwatActions.select_an_action_text;
    } else if (!items_selected) {
      message = SwatActions.select_an_item_text;
    }

    if (message) {
      e.preventDefault();
      this.showMessage(message);
    }
  }

  handleMessageClose() {
    this.hideMessage();
  }

  showMessage(message_text) {
    if (this.message_content.firstChild) {
      this.message_content.removeChild(this.message_content.firstChild);
    }

    this.message_content.appendChild(
      document.createTextNode(message_text + ' ')
    );

    if (!this.message_shown) {
      this.message_span.style.opacity = 0;
      this.message_span.style.visibility = 'visible';

      this.message_span
        .animate([{ opacity: 0 }, { opacity: 1 }], {
          duration: 300,
          easing: 'ease-in'
        })
        .finished.then(() => {
          this.message_span.style.opacity = 1;
        });

      this.message_shown = true;
    }
  }

  hideMessage() {
    if (this.message_shown) {
      this.message_span
        .animate([{ opacity: 1 }, { opacity: 0 }], {
          duration: 300,
          easing: 'ease-out'
        })
        .finished.then(() => {
          this.message_span.style.opacity = 0;
          this.message_span.style.visibility = 'hidden';
          this.message_shown = false;
        });
    }
  }
}
