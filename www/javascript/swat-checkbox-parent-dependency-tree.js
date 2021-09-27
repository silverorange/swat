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
    return new SwatCheckboxTree(
        id,
        function(input, parents, children) {
            if (input.checked) {
                parents(true);
            } else {
                children(false);
            }
        }
    );
}
