/**
 * Javascript SwatCheckAll component
 *
 * @param id string Id of the matching {@link SwatCheckAll} object.
 * @param controller SwatObject A reference to the javascript object that
 * 		  represents the checkboxlist
 **/
function SwatCheckAll(id, controller) {
	this.check_all = document.getElementById(id);
	var self = this;

	var is_ie = (this.check_all.addEventListener) ? false : true;
	controller.check_all = this;

	controller.checkAllInit();

	if (is_ie)
		this.check_all.attachEvent("onclick", eventHandler, false);
	else
		this.check_all.addEventListener("change", eventHandler, false);

	function eventHandler(event) {
		controller.checkAll(self.check_all.checked);
	}
}

SwatCheckAll.prototype.setState = function(checked) {
	this.check_all.checked = checked;
}
