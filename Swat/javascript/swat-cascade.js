function SwatCascade(parent, child) {
	this.parent = document.getElementById(parent);
	this.child = document.getElementById(child);
	this.children = new Array();
	this.init();
}

SwatCascade.prototype.update = function() {
	var display = this.children[this.parent.value];
	
	for (i = 0; i < this.child.length; i++)
		this.child[i] = null;
	
	for (i = 0; i < display.length; i++) {
		this.child[i] = new Option(display[i].title, display[i].value);
	}
}

SwatCascade.prototype.addChild = function(parent, value, title, selected) {
	if (!this.children[parent]) this.children[parent] = new Array();
	this.children[parent].push(new SwatCascadeChild(value, title, selected));
}

SwatCascade.prototype.init = function() {
	var child_id = this.child.id;

	this.parent.onchange = function() {
		obj = eval(child_id + '_cascade');
		obj.update();
	}
}

function SwatCascadeChild(value, title, selected) {
	this.value = value;
	this.title = title;
	this.selected = selected;
}
