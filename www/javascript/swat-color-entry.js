function SwatColorEntry(id)
{
	this.id = id;
	this.colors =    ['0', '51', '102', '153', '204', '255'];
	this.grayscale = ['0',   '25',  '50',  '75',  '100', '125',
	                  '150', '175', '200', '225', '255'];

	this.hex =       ['0', '1', '2', '3', '4', '5', '6', '7',
	                  '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'];

	this.shades =    [1, 0.80, 0.60, 0.40, 0.20, 0];

	this.entryElement = document.getElementById(this.id + '_value');

	if (this.entryElement.value.length == 6) {
		this.setHex(this.entryElement.value);
		this.setRGB();
		this.entryElement.style.background = '#' + entryElement.value;
		this.entryElement.style.color = '#' + entryElement.value;
	}

	document.getElementById(this.id + '_color_palette').innerHTML =
		this.drawPalette();

	document.getElementById(this.id + '_grayscale').innerHTML =
		this.drawTintScale(127, 127, 127);
}

SwatColorEntry.prototype.toggle = function()
{
	var t = document.getElementById(this.id + '_wrapper');
	var o = document.getElementById(this.id + '_toggle');

	var x_offset = YAHOO.util.Dom.getX(o);
	t.style.left = x_offset + 'px';

	if (!t.style.display || t.style.display == 'none') {
		t.style.display = 'block';
		SwatZIndexManager.raiseElement(t);
	} else {
		t.style.display = 'none';
		SwatZIndexManager.lowerElement(t);
	}
};

SwatColorEntry.prototype.apply = function()
{
	var hex_color = document.getElementById(this.id + '_color_input_hex').value;
	this.entryElement.style.background = '#' + hex_color;
	this.entryElement.style.color = '#' + hex_color;
	this.entryElement.value = hex_color;
	this.toggle();
};

SwatColorEntry.prototype.none = function()
{
	this.entryElement.style.background =
		'url(packages/swat/images/color-entry-null.png)';

	this.entryElement.value = '';
	this.toggle();
};

/**
 * Methods to draw parts:
 * drawPalette()
 * drawTintScale()
 * drawDiv()
 */
SwatColorEntry.prototype.drawPalette = function()
{
	var start = 0;
	var ret = '';

	for (var row = start; row < 12; row ++) {
		for (var col = 0; col < 18; col++) {
			var r = Math.floor(row / 6) + ((Math.floor(col / 6) * 2));
			var g = (col % 12 < 6) ? (col % 6) : ((col - 1) - ((col % 6) * 2));
			var b = (row < 6) ? (row % 6) : ((row - 1) - ((row % 6) * 2));
			ret = ret + this.drawDiv(this.colors[r], this.colors[g], this.colors[b]);
		}
	}

	return ret;
};

SwatColorEntry.prototype.drawTintScale = function(r, g, b)
{
	var tint_scale = '';
	var R;
	var G;
	var B;

	for (var i = 0; i < this.shades.length; i++) {
		R = Math.round(r + (255 - r) * this.shades[i]);
		G = Math.round(g + (255 - g) * this.shades[i]);
		B = Math.round(b + (255 - b) * this.shades[i]);
		tint_scale += this.drawDiv(R, G, B);
	}

	for (var i = 0; i < this.shades.length; i++) {
		R = Math.round(r * this.shades[i]);
		G = Math.round(g * this.shades[i]);
		B = Math.round(b * this.shades[i]);
		tint_scale += this.drawDiv(R, G, B);
	}

	return tint_scale;
};

SwatColorEntry.prototype.drawDiv = function(r, g, b)
{
	var color = r + ',' + g + ',' + b;
	var title = 'rgb: (' + color + ') hex: #' + this.rgb_to_hex(r, g, b);
	return '<div class="option" title="' + title +
		'" onmouseover="' + this.id + '_obj.updateActiveSwatch(' + color +
		');" onclick="' + this.id + '_obj.setPalette(' + color +
		');" style="background: rgb(' + color +
		');">&nbsp;</div>';
};

/**
 * Methods to set colors from inputs:
 * setRGB()
 * setHex()
 * setPalette()
 */
SwatColorEntry.prototype.setRGB = function()
{
	var r = parseInt(document.getElementById(this.id + '_color_input_r').value);
	var g = parseInt(document.getElementById(this.id + '_color_input_g').value);
	var b = parseInt(document.getElementById(this.id + '_color_input_b').value);

	r = (r) ? r : 0;
	g = (g) ? g : 0;
	b = (b) ? b : 0;

	this.updateSwatch(r, g, b);
	this.updateHex(r, g, b);
};

SwatColorEntry.prototype.setHex = function(val)
{
	var r = this.hex_to_dec(val.slice(0,2));
	var g = this.hex_to_dec(val.slice(2,4));
	var b = this.hex_to_dec(val.slice(4,6));
	this.updateSwatch(r, g, b);
	this.updateRGB(r, g, b);
};

SwatColorEntry.prototype.setPalette = function(r, g, b)
{
	this.updateHex(r, g, b);
	this.updateRGB(r, g, b);
	this.updateSwatch(r, g, b);
};

/**
 * Update palette parts
 * updateSwatch()
 * updateActiveSwatch()
 * updateRGB()
 * updateHex()
 */
SwatColorEntry.prototype.updateSwatch = function(r, g, b)
{
	var color = r + ',' + g + ',' + b;
	var swatch_obj = document.getElementById(this.id + '_swatch');
	swatch_obj.style.background = 'rgb(' + color + ')';
	swatch_obj.title = 'rgb: (' + color + ') hex: #' + this.rgb_to_hex(r, g, b);

	document.getElementById(this.id + '_tintscale').innerHTML =
		this.drawTintScale(r, g, b);
};

SwatColorEntry.prototype.updateActiveSwatch = function(r, g, b)
{
	var color = r + ',' + g + ',' + b;
	swatch_obj = document.getElementById(this.id + '_active_swatch');
	swatch_obj.style.background = 'rgb(' + color + ')';
	swatch_obj.title = 'rgb: (" + color + ") hex: #' + this.rgb_to_hex(r, g, b);
};

SwatColorEntry.prototype.updateRGB = function(r, g, b)
{
	document.getElementById(this.id + '_color_input_r').value = r;
	document.getElementById(this.id + '_color_input_g').value = g;
	document.getElementById(this.id + '_color_input_b').value = b;
};

SwatColorEntry.prototype.updateHex = function(r, g, b)
{
	var hex_input = document.getElementById(this.id + '_color_input_hex');
	hex_input.value = this.rgb_to_hex(r, g, b);
};

/**
 * Utility methods
 * rgb_to_hex()
 * dec_to_hex()
 * hex_to_dec()
 * getHexPos()
 */
SwatColorEntry.prototype.rgb_to_hex = function(r, g, b)
{
	var hex_r = this.dec_to_hex(r);
	var hex_g = this.dec_to_hex(g);
	var hex_b = this.dec_to_hex(b);

	return hex_r + hex_g + hex_b;
};

SwatColorEntry.prototype.dec_to_hex = function(val)
{
	var v1 = Math.floor(val / 16);
	var v2 = Math.floor(val % 16);

	return this.hex[(v1 < 16) ? v1 : 0] + this.hex[(v2 < 16) ? v2 : 0];
};

SwatColorEntry.prototype.hex_to_dec = function(val)
{
	var v1 = (val.length >= 1) ? val.slice(0,1) : '0';
	var v2 = (val.length == 2) ? val.slice(1,2) : '0';
	var ret = (this.getHexPos(v1) * 16) + this.getHexPos(v2);
	return (isNaN(ret)) ? 0 : ret;
};

SwatColorEntry.prototype.getHexPos = function(val)
{
	for (var i = 0; i < this.hex.length; i++)
		if (this.hex[i] == val)
			return i;
};
