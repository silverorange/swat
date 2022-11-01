class SwatForm {
  constructor(id, connection_close_url) {
    this.id = id;
    this.form_element = document.getElementById(id);
    this.connection_close_url = connection_close_url;

    if (this.connection_close_url) {
      this.form_element.addEventListener('submit', () => {
        this.handleSubmit();
      });
    }
  }

  setDefaultFocus(element_id) {
    // TODO: check if another element in this form is already focused
    function isFunction(obj) {
      return typeof obj == 'function' || typeof obj == 'object';
    }

    var element = document.getElementById(element_id);
    if (element && !element.disabled && isFunction(element.focus)) {
      element.focus();
    }
  }

  setAutocomplete(state) {
    this.form_element.setAttribute('autocomplete', state ? 'on' : 'off');
  }

  closePersistentConnection() {
    var is_safari_osx = /^.*mac os x.*safari.*$/i.test(navigator.userAgent);
    if (is_safari_osx && this.connection_close_url && XMLHttpRequest) {
      var request = new XMLHttpRequest();
      request.open('GET', this.connection_close_url, false);
      request.send(null);
    }
  }

  handleSubmit(e) {
    if (this.form_element.enctype == 'multipart/form-data') {
      this.closePersistentConnection();
    }
  }
}
