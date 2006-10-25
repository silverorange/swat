/**
 * An object to manage element z-indexes for a webpage
 */
function SwatZIndexManager()
{
}

SwatZIndexManager.elements = new Array();

/**
 * Default starting z-index for elements
 *
 * @var number
 */
SwatZIndexManager.start = 10;

/**
 * Raises an element to the top
 *
 * Sets the element's z-index to one greater than the current highest z-index
 * in the list of elements.
 *
 * @param DOMElement element the element to raise.
 */
SwatZIndexManager.raiseElement = function(element)
{
	SwatZIndexManager.removeElement(element);

	if (SwatZIndexManager.elements.length > 0) {
		var position = SwatZIndexManager.elements.length - 1;
		var index = parseInt(SwatZIndexManager.elements[position].style.zIndex);
		index++;
	} else {
		var index = SwatZIndexManager.start;
	}
	
	element.style.zIndex = index;

	SwatZIndexManager.elements.push(element);
}

/**
 * Lowers an element to the bottom
 *
 * Sets the element's z-index to 0 and removes the element's from the current
 * list of elements. Shifts all elements down so that the first element in the
 * list has a z-index of zero and all other elements retain their relative
 * ordering to the first element.
 *
 * @param DOMElement element the element to lower.
 */
SwatZIndexManager.lowerElement = function(element)
{
	element.style.zIndex = 0;

	SwatZIndexManager.removeElement(element);
}

/**
 * Removes an element from the list of managed elements
 *
 * @param DOMElement element the element to remove
 *
 * @return mixed the element that was removed or null if the element was not
 *                found.
 */
SwatZIndexManager.removeElement = function(element)
{
	// find element
	var position = -1;
	for (i = 0; i < SwatZIndexManager.elements.length; i++) {
		if (SwatZIndexManager.elements[i] === element) {
			position = i;
			break;
		}
	}

	// element was not found
	if (position == -1) {
		return null;
	}
	
	// remove element from list
	SwatZIndexManager.elements.splice(position, 1);

	// shift other elements down
	if (SwatZIndexManager.elements.length > 0) {
		var old_index = 0;
		var new_index = 0;
		var lowest_index = parseInt(SwatZIndexManager.elements[0].style.zIndex);

		for (i = 0; i < SwatZIndexManager.elements.length; i++) {
			old_index = parseInt(SwatZIndexManager.elements[i].style.zIndex);
			new_index = SwatZIndexManager.start + old_index - lowest_index;
			SwatZIndexManager.elements[i].style.zIndex = new_index;
		}
	}

	return element;
}
