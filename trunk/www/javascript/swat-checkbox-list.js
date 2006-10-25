/**
 * JavaScript SwatCheckboxList component
 *
 * @param id string Id of the matching {@link SwatCheckboxList} object.
 */
function SwatCheckboxList(id)
{
	this.check_list = document.getElementsByName(id + '[]');
	this.check_all = null; // a reference to a check-all js object 

	for (i = 0; i < this.check_list.length; i++) {
		YAHOO.util.Event.addListener(this.check_list[i], 'change',
			SwatCheckboxList.clickHandler, this);
	}
}

SwatCheckboxList.clickHandler = function(event, object)
{
	object.checkAllInit();
}

SwatCheckboxList.prototype.checkAllInit = function ()
{
	if (this.check_all == null)
		return;

	var count = 0;
	for (i = 0; i < this.check_list.length; i++)
		if (this.check_list[i].checked)
			count++;
		else if (count > 0)
			break; // can't possibly be all checked or none checked

	this.check_all.setState(count == this.check_list.length);
}

SwatCheckboxList.prototype.checkAll = function(checked)
{
	for (i = 0; i < this.check_list.length; i++)
		this.check_list[i].checked = checked;
}
