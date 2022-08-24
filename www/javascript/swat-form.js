function SwatForm(id, connection_close_url) {
  this.id = id;
  this.form_element = document.getElementById(id);
  this.connection_close_url = connection_close_url;

  if (this.connection_close_url) {
    YAHOO.util.Event.on(
      this.form_element,
      'submit',
      this.handleSubmit,
      this,
      true
    );
  }
}

SwatForm.prototype.setDefaultFocus = function(element_id) {
  // TODO: check if another element in this form is already focused

  function isFunction(obj) {
    return typeof obj == 'function' || typeof obj == 'object';
  }

  var element = document.getElementById(element_id);
  if (element && !element.disabled && isFunction(element.focus))
    element.focus();
};

SwatForm.prototype.setAutocomplete = function(state) {
  this.form_element.setAttribute('autocomplete', state ? 'on' : 'off');
};

SwatForm.prototype.closePersistentConnection = function() {
  var is_safari_osx = /^.*mac os x.*safari.*$/i.test(navigator.userAgent);
  if (is_safari_osx && this.connection_close_url && XMLHttpRequest) {
    var request = new XMLHttpRequest();
    request.open('GET', this.connection_close_url, false);
    request.send(null);
  }
};

SwatForm.prototype.handleSubmit = function(e) {
  if (this.form_element.enctype == 'multipart/form-data') {
    this.closePersistentConnection();
  }
};
