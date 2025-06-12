class SwatButton {
  constructor(id, show_processing_throbber) {
    this.id = id;

    this.button = document.getElementById(this.id);

    // deprecated
    this.show_processing_throbber = show_processing_throbber;

    this.confirmation_message = '';
    this.throbber_container = null;

    if (show_processing_throbber) {
      this.initThrobber();
    }

    this.button.addEventListener('click', (e) => {
      this.handleClick(e);
    });
  }

  handleClick(e) {
    var confirmed = this.confirmation_message
      ? confirm(this.confirmation_message)
      : true;

    if (confirmed) {
      if (this.throbber_container !== null) {
        this.button.disabled = true;
        this.button.classList.add('swat-insensitive');

        // add button to form data manually since we disabled it above
        var div = document.createElement('div');
        var hidden_field = document.createElement('input');
        hidden_field.type = 'hidden';
        hidden_field.name = this.id;
        hidden_field.value = this.button.value;
        div.appendChild(hidden_field);
        this.button.form.appendChild(div);

        this.showThrobber();
        var form = this.button.closest('form');
        if (form) {
          form.submit(); // needed for IE and WebKit
        }
      }
    } else {
      e.preventDefault();
    }
  }

  initThrobber() {
    this.throbber_container = document.createElement('span');
    this.throbber_container.classList.add('swat-button-processing-throbber');
    this.button.parentNode.appendChild(this.throbber_container);
  }

  showThrobber() {
    this.throbber_container
      .animate([{ opacity: 0.5 }], {
        duration: 1000
      })
      .finished.then(() => {
        this.throbber_container.style.opacity = 0.5;
      });
  }

  setProcessingMessage(message) {
    if (this.throbber_container === null) {
      this.initThrobber();
    }

    if (message.length > 0) {
      this.throbber_container.appendChild(document.createTextNode(message));
      this.throbber_container.classList.add(
        'swat-button-processing-throbber-text'
      );
    } else {
      // the following string is a UTF-8 encoded non breaking space
      this.throbber_container.appendChild(document.createTextNode(' '));
    }
  }

  setConfirmationMessage(message) {
    this.confirmation_message = message;
  }
}
