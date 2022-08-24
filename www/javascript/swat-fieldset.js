function SwatFieldset(id) {
  this.id = id;

  window.addEventListener('DOMContentLoaded', () => {
    this.init();
  });
}

SwatFieldset.prototype.init = function() {
  var container = document.getElementById(this.id);
  if (
    container.currentStyle &&
    typeof container.currentStyle.hasLayout != 'undefined'
  ) {
    /*
     * This fix is needed for IE6/7 and fixes display of relative
     * positioned elements below this fieldset during and after
     * animations.
     */

    var empty_div = document.createElement('div');
    var peekaboo_div = document.createElement('div');
    peekaboo_div.style.height = '0';
    peekaboo_div.style.margin = '0';
    peekaboo_div.style.padding = '0';
    peekaboo_div.style.border = 'none';
    peekaboo_div.appendChild(empty_div);

    if (container.nextSibling) {
      container.parentNode.insertBefore(peekaboo_div, container.nextSibling);
    } else {
      container.parentNode.appendChild(peekaboo_div);
    }
  }
};
