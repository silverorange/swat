function SwatChangeOrder(id) {
	this.active_div;
	this.id = id;
	this.warning_msg = "You must select an element before re-ordering.";
	this.style;
}

SwatChangeOrder.prototype.draw = function(elements) {
	this.num_elements = elements.length;
	
	myvar = '<body class="swat-order-control-iframe-body">';
	for (i = 0; i < elements.length; i++) {
		myvar = myvar + '<div id="' + this.id + '_' + i + '"';
		myvar = myvar + ' onclick="window.parent.' + this.id + '_obj.choose(this);"';
		myvar = myvar + ' class="swat-order-control">';
		myvar = myvar + elements[i] + '</div>';
	}
	myvar = myvar + '</body>';
	
	var iframe = document.getElementById(this.id + '_iframe');
	var doc = iframe.contentWindow.document;
	doc.open("text/html", "replace");
	doc.write('<style type="text/css" media="all">');
	doc.write('@import "' + this.stylesheet + '";');
	doc.write('</style>');
	doc.write(myvar);
	doc.close();
}

SwatChangeOrder.prototype.choose = function(div) {
	if (this.active_div)
		this.active_div.className = 'swat-order-control';
		
	div.className = 'swat-order-control-active';
	this.active_div = div;
	
}

SwatChangeOrder.prototype.updown = function(direction) {
	if (!this.active_div) {
		//no element selected, alert user
		alert(this.warning_msg);
		return;
	}
	
	rexp = new RegExp("[0-9]+$");
	var idx = parseInt(this.active_div.id.match(rexp));
	
	var nxt = idx + (direction=='up'? -1 : 1);
	
	//at the top or bottom - simply return
	if (nxt >= this.num_elements || nxt < 0)
		return;
	
	//swap the content of the current element and the next one
	var current_content = this.active_div.innerHTML;
	
	var iframe = document.getElementById(this.id + '_iframe');
	var iframedoc = iframe.contentWindow.document;
	
	var next_div = iframedoc.getElementById(this.id + '_' + nxt);
	var next_content = next_div.innerHTML;
	
	next_div.innerHTML = current_content;
	this.active_div.innerHTML = next_content;
	
	//change the current element to be the next one
	this.choose(next_div);
	
	//update a hidden field with current order of keys
	var hidden_vals = document.getElementById(this.id);
	var val_array = hidden_vals.value.split(',');
	var current_val = val_array[idx];
	val_array[idx] = val_array[nxt];
	val_array[nxt] = current_val;
	hidden_vals.value = val_array.toString();

	//change the offset of the iframe to follow
	var middle = (next_div.offsetHeight / 2);
	var offset = next_div.offsetTop - parseInt(iframe.height/2) + middle;
	iframe.contentWindow.scrollTo(0, offset);
}
