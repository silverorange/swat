class SwatCheckboxTree {
  /**
   * JavaScript SwatCheckboxTree component
   *
   * @param {string} Id of the matching {@link SwatCheckboxTree} object.
   */
  constructor(id, maybe_effect) {
    this.id = id;
    this.maybe_effect = maybe_effect;

    /*
     * This property needs to be set to something callable right away.
     * The SwatCheckAll code calls this before the widget can be
     * initialized. Just set it to something harmless to begin with.
     */
    this.updateCheckAll = SwatCheckboxTree.nothing;

    window.addEventListener('DOMContentLoaded', () => {
      this.init();
    });
  }

  static compose(second, first) {
    return function(x) {
      return second(first(x));
    };
  }

  static identity(x) {
    return x;
  }

  static nothing() {
    // We pass this empty function when we make the isChecked function
  }

  /*
   * This function walks all nodes of the check box tree and returns
   * a function that allows for operations on all check boexes in the
   * tree. You may also provide a side effect that will be executed
   * for each node in the tree.
   */
  static walk(container, parents, chain, effect) {
    return Array.from(container.querySelectorAll(':scope > ul > li'))
      .map(function(item) {
        return {
          item,
          input: item.querySelector(':scope > span > input[type="checkbox"]')
        };
      })
      .map(obj => {
        const item = obj.item;
        const input = obj.input;
        const link = input !== null ? chain(input) : SwatCheckboxTree.identity;

        const children = SwatCheckboxTree.walk(
          item,
          SwatCheckboxTree.compose(
            parents,
            link
          ),
          chain,
          effect
        );

        if (input !== null) {
          effect(input, parents, children);
        }

        return SwatCheckboxTree.compose(
          link,
          children
        );
      })
      .reduce(SwatCheckboxTree.compose, SwatCheckboxTree.identity);
  }

  init() {
    const container = document.getElementById(this.id);
    const effect = this.maybe_effect ?? SwatCheckboxTree.nothing;

    /*
     * This call to walk makes us a nice little function of the form
     * (boolean) => boolean. When passed true the function will return
     * a boolean indicating whether or not all check boxes in the tree
     * are selected.
     */
    const isChecked = SwatCheckboxTree.walk(
      container,
      SwatCheckboxTree.identity,
      function(input) {
        return function(checked) {
          return (input.disabled || input.checked) && checked;
        };
      },
      SwatCheckboxTree.nothing
    );

    /*
     * We now use the isChecked function to set the state on the check
     * all function. If we have a check all we update its state with
     * whatever our isChecked function returns.
     */
    const updateCheckAll = () => {
      if (this.check_all) {
        this.check_all.setState(isChecked(true));
      }
    };

    /*
     * This second call to walk makes us another function of the form
     * (boolean) => boolean. When passed a boolean this function will
     * update the checked attribute of all check boxes in the tree
     * with the provided value.
     *
     * At the same time it also executes the passed in side effect
     * on each check box in the tree. In this case the side effect
     * is setting up a event listener on each check box in the tree
     * that will preform the desired behavior when the input is changed.
     */
    const checkAll = SwatCheckboxTree.walk(
      container,
      SwatCheckboxTree.identity,
      function(input) {
        return function(state) {
          if (state.disabled !== undefined) {
            input.disabled = state.disabled;
            input.checked = false;
          }

          if (state.checked !== undefined) {
            input.checked = input.disabled ? false : state.checked;
          }

          return state;
        };
      },
      // After we call the side effect we also update the state of the check all
      function(input, parents, children) {
        effect(input, parents, children);
        input.addEventListener('change', updateCheckAll);
      }
    );

    // The this.updateCheckAll property is also required by SwatCheckAll.
    this.updateCheckAll = updateCheckAll;

    // The this.checkAll property is required by SwatCheckAll.
    this.checkAll = checked => {
      checkAll({ checked: checked });
    };
  }
}

/**
 * JavaScript SwatCheckboxChildDependencyTree component
 *
 * @param {string} Id of the matching {@link SwatCheckboxChildDependencyTree} object.
 */
function SwatCheckboxChildDependencyTree(id) {
  /*
   * When an option is checked we check all children.
   * When an option is unchecked we uncheck all parents.
   */
  return new SwatCheckboxTree(id, function(input, parents, children) {
    const onChange = function() {
      if (input.checked) {
        children({ checked: true });
      } else {
        parents({ checked: false });
      }
    };

    if (input.disabled) {
      input.checked = false;
      parents({ disabled: true });
    }

    if (input.checked) {
      children({ checked: true });
    }

    input.addEventListener('change', onChange);
  });
}

/**
 * JavaScript SwatCheckboxParentDependencyTree component
 *
 * @param {string} Id of the matching {@link SwatCheckboxParentDependencyTree} object.
 */
function SwatCheckboxParentDependencyTree(id) {
  /*
   * When an option is checked we check all parents.
   * When an option is unchecked we uncheck all children.
   */
  return new SwatCheckboxTree(id, function(input, parents, children) {
    const onChange = function() {
      if (input.checked) {
        parents({ checked: true });
      } else {
        children({ checked: false });
      }
    };

    if (input.disabled) {
      input.checked = false;
      children({ disabled: true });
    }

    if (input.checked) {
      parents({ checked: true });
    }

    input.addEventListener('change', onChange);
  });
}
