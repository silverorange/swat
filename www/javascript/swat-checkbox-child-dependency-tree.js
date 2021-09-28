/**
 * JavaScript SwatCheckboxChildDependencyTree component
 *
 * @param id string Id of the matching {@link SwatCheckboxChildDependencyTree} object.
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

        YAHOO.util.Event.on(input, 'change', onChange);
    });
}
