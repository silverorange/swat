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
    return new SwatCheckboxTree(
        id,
        function(input, parents, children) {
            if (input.checked) {
                children(true);
            } else {
                parents(false);
            }
        }
    );
}
