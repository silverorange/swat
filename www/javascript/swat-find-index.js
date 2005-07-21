function find_index(selectbox,value) {
	for (i = 0; i < selectbox.options.length; i++) {
		if (selectbox.options[i].value == value)
			return i;
	}
	return false;
}