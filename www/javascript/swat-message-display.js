class SwatMessageDisplay {
  constructor(id, hideable_messages) {
    this.id = id;
    this.messages = [];

    // create message objects for this display
    for (var i = 0; i < hideable_messages.length; i++) {
      var message = new SwatMessageDisplayMessage(
        this.id,
        hideable_messages[i]
      );

      this.messages[i] = message;
    }
  }

  getMessage(index) {
    if (this.messages[index]) {
      return this.messages[index];
    } else {
      return false;
    }
  }
}

class SwatMessageDisplayMessage {
  /**
   * A message in a message display
   *
   * @param {string}
   * @param {number} message_index the message to hide from this list.
   */
  constructor(message_display_id, message_index) {
    this.id = message_display_id + '_' + message_index;
    this.message_div = document.getElementById(this.id);
    this.handleClick = this.handleClick.bind(this);
    this.drawDismissLink();
  }

  static close_text = 'Dismiss message';
  static fade_duration = 0.3;
  static shrink_duration = 0.3;

  drawDismissLink() {
    var text = document.createTextNode(SwatMessageDisplayMessage.close_text);

    this.dismiss_link = document.createElement('a');
    this.dismiss_link.href = '#';
    this.dismiss_link.title = SwatMessageDisplayMessage.close_text;
    this.dismiss_link.classList.add('swat-message-display-dismiss-link');
    this.dismiss_link.addEventListener('click', this.handleClick);
    this.dismiss_link.appendChild(text);

    var container = this.message_div.firstChild;
    container.insertBefore(this.dismiss_link, container.firstChild);
  }

  /**
   * Hides this message
   *
   * Uses the self-healing transition pattern described at
   * {@link http://developer.yahoo.com/ypatterns/pattern.php?pattern=selfhealing}.
   */
  hide() {
    if (this.message_div !== null) {
      this.message_div
        .animate([{ opacity: 1 }, { opacity: 0 }], {
          duration: SwatMessageDisplayMessage.fade_duration * 1000,
          easing: 'ease-out'
        })
        .finished.then(() => {
          this.message_div.style.opacity = 0;
          this.shrink();
        });
    }
  }

  shrink() {
    var duration = SwatMessageDisplayMessage.shrink_duration * 1000;
    var easing = 'ease-in';

    var endKeyframeAttributes = {
      height: 0,
      marginBottom: 0
    };

    var height = this.message_div.firstElementChild.getBoundingClientRect()
      .height;

    // collapse margins
    if (this.message_div.nextSibling) {
      // shrink top margin of next message in message display
      this.message_div.nextSibling
        .animate([{ marginTop: 0 }], {
          duration,
          easing
        })
        .finished.then(() => {
          this.message_div.nextSibling.style.marginTop = 0;
        });
    } else {
      // shrink top margin of element directly below message display
      // find first element node
      var script_node = this.message_div.parentNode.nextSibling;
      var node = script_node.nextSibling;
      while (node && node.nodeType != 1) {
        node = node.nextSibling;
      }

      if (node) {
        node
          .animate([{ marginTop: 0 }], {
            duration,
            easing
          })
          .finished.then(() => {
            node.style.marginTop = 0;
          });
      }
    }

    // if this is the last message in the display, shrink the message display
    // top margin to zero.
    if (this.message_div.parentNode.childNodes.length === 1) {
      // collapse top margin of last message
      endKeyframeAttributes.marginTop = 0;

      this.message_div.parentNode
        .animate([{ marginTop: 0 }], {
          duration,
          easing
        })
        .finished.then(() => {
          this.message_div.parentNode.style.marginTop = 0;
        });
    }

    // disappear this message
    this.message_div
      .animate([{ height: height + 'px' }, endKeyframeAttributes], {
        duration,
        easing
      })
      .finished.then(() => {
        Object.entries(endKeyframeAttributes).forEach(([key, value]) => {
          this.message_div.style[key] = value;
        });
        this.remove();
      });
  }

  remove() {
    this.dismiss_link.removeEventListener('click', this.handleClick);
    this.message_div.parentNode.removeChild(this.message_div);
  }

  handleClick(e) {
    e.preventDefault();
    this.hide();
  }
}
