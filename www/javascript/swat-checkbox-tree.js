/**
 * JavaScript SwatCheckboxTree component
 *
 * @param id string Id of the matching {@link SwatCheckboxTree} object.
 */
function SwatCheckboxTree(id, maybeEffect) {
    function compose(second, first) {
        return function(x) {
            return second(first(x));
        };
    }

    function identity(x) {
        return x;
    }

    function nothing() {
        // We pass this empty function when we make the isChecked function
    }

    /*
     * This function walks all nodes of the check box tree and returns
     * a function that allows for operations on all check boexes in the
     * tree. You may also provide a side effect that will be executed
     * for each node in the tree.
     */
    function walk(container, parents, chain, effect) {
        return Array.from(container.querySelectorAll(':scope > ul > li'))
            .map(function(item) {
                return {
                    item,
                    input: item.querySelector(
                        ':scope > span > input[type="checkbox"]'
                    )
                };
            })
            .filter(function(obj) {
                return obj.input !== null;
            })
            .map(function(obj) {
                const item = obj.item;
                const input = obj.input;

                const children = walk(
                    item,
                    compose(
                        parents,
                        chain(input)
                    ),
                    chain,
                    effect
                );

                effect(input, parents, children);

                return compose(
                    chain(input),
                    children
                );
            })
            .reduce(compose, identity);
    }

    function init() {
        const container = document.getElementById(id);
        const effect = maybeEffect ? maybeEffect : nothing;

        /*
         * This call to walk makes us a nice little function of the form
         * (boolean) => boolean. When passed true the function will return
         * a boolean indicating whether or not all check boxes in the tree
         * are selected.
         */
        const isChecked = walk(
            container,
            identity,
            function(input) {
                return function(checked) {
                    return (input.disabled || input.checked) && checked;
                };
            },
            nothing
        );

        /*
         * We now use the isChecked function to set the state on the check
         * all function. If we have a check all we update its state with
         * whatever our isChecked function returns.
         */
        const that = this;
        const updateCheckAll = function() {
            if (that.check_all) {
                that.check_all.setState(isChecked(true));
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
        const checkAll = walk(
            container,
            identity,
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
                YAHOO.util.Event.on(input, 'change', updateCheckAll);
            }
        );

        // The this.updateCheckAll property is also required by SwatCheckAll.
        this.updateCheckAll = updateCheckAll;

        // The this.checkAll property is required by SwatCheckAll.
        this.checkAll = function(checked) {
            checkAll({ checked: checked });
        };
    }

    YAHOO.util.Event.onDOMReady(init, this, true);

    /*
     * This property needs to be set to something callable right away.
     * The SwatCheckAll code calls this before the widget can be
     * initialized. Just set it to something harmless to begin with.
     */
    this.updateCheckAll = nothing;
}
