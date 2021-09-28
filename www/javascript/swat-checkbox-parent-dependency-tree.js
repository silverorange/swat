/**
 * JavaScript SwatCheckboxParentDependencyTree component
 *
 * @param id string Id of the matching {@link SwatCheckboxParentDependencyTree} object.
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
            children({ disabled: true });
        }

        if (input.checked) {
            parents({ checked: true });
        }

        YAHOO.util.Event.on(input, 'change', onChange);
    });
}
