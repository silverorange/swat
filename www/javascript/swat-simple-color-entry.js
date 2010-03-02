/**
 * Simple color entry widget
 *
 * @copyright 2005-2010 silverorange Inc.
 */

/**
 * Creates a SwatSimpleColorEntry object
 *
 * @param string id
 * @param Array colors
 * @param string none_option_title
 */
function SwatSimpleColorEntry(id, colors, none_option_title)
{
	this.id = id;
	this.colors = colors;
	this.is_open = false;
	this.is_drawn = false;

	this.none_option_title = none_option_title;

	this.input_tag = document.getElementById(this.id + '_value');
	this.swatch = document.getElementById(this.id + '_swatch');

	this.hex_input_tag = document.createElement('input');
	this.hex_input_tag.type = 'text';
	this.hex_input_tag.id = this.id + '_hex_color';
	this.hex_input_tag.size = 6;

	YAHOO.util.Event.addListener(this.hex_input_tag, 'change',
		this.handleInputChange, this, true);

	YAHOO.util.Event.addListener(this.hex_input_tag, 'keyup',
		this.handleInputChange, this, true);

	// this tries to make a square palette
	this.columns = Math.ceil(Math.sqrt(this.colors.length));

	this.current_color = null;
	this.colorChangeEvent = new YAHOO.util.CustomEvent('colorChange');

	this.setColor(this.input_tag.value);

	this.drawButton();
	this.drawPalette();
	this.drawInput();

	YAHOO.util.Event.onContentReady(this.id + '_palette',
		this.createOverlay, this, true)
}

SwatSimpleColorEntry.set_text = 'Set';

/**
 * Displays the toggle button for this simple color entry
 */
SwatSimpleColorEntry.prototype.drawButton = function()
{
	this.toggle_button = document.createElement('button');
	YAHOO.util.Dom.addClass(this.toggle_button,
		'swat-simple-color-entry-toggle-button');

	// the type property is readonly in IE so use setAttribute() here
	this.toggle_button.setAttribute('type', 'button');

	this.swatch.parentNode.replaceChild(this.toggle_button, this.swatch);
	this.toggle_button.appendChild(this.swatch);
	YAHOO.util.Event.addListener(this.toggle_button, 'click', this.toggle,
		this, true);

	this.palette = document.createElement('div');
	this.palette.id = this.id + '_palette';
	YAHOO.util.Dom.addClass(this.palette, 'swat-simple-color-entry-palette');
	this.palette.style.display = 'none';

	var overlay_header = document.createElement('div');
	YAHOO.util.Dom.addClass(overlay_header, 'hd');

	var overlay_body = document.createElement('div');
	YAHOO.util.Dom.addClass(overlay_body, 'bd');

	var overlay_footer = document.createElement('div');
	YAHOO.util.Dom.addClass(overlay_footer, 'ft');

	this.palette.appendChild(overlay_header);
	this.palette.appendChild(overlay_body);
	this.palette.appendChild(overlay_footer);

	var container = document.getElementById(this.id);
	container.appendChild(this.palette);
}

/**
 * Creates simple color entry overlay widget when toggle button has been drawn
 */
SwatSimpleColorEntry.prototype.createOverlay = function(event)
{
	this.overlay = new YAHOO.widget.Overlay(this.id + '_palette',
		{ visible: false, constraintoviewport: true });

	this.overlay.render(document.body);
	this.palette.style.display = 'block';
	this.is_drawn = true;

	this.close_div = document.createElement('div');
	this.close_div.style.top = 0;
	this.close_div.style.left = 0;
	this.close_div.style.position = 'absolute';
	this.close_div.style.display = 'none';

	YAHOO.util.Event.addListener(this.close_div, 'click',
		this.close, this, true);

	document.getElementsByTagName('body')[0].appendChild(this.close_div);
}

/**
 * Closes this color palette
 */
SwatSimpleColorEntry.prototype.close = function()
{
	this.close_div.style.display = 'none';
	SwatZIndexManager.lowerElement(this.close_div);

	this.overlay.hide();
	SwatZIndexManager.lowerElement(this.palette);
	this.is_open = false;
}

/**
 * Opens this color palette
 */
SwatSimpleColorEntry.prototype.open = function()
{
	this.close_div.style.width = YAHOO.util.Dom.getDocumentWidth() + 'px';
	this.close_div.style.height = YAHOO.util.Dom.getDocumentHeight() + 'px';
	this.close_div.style.display = 'block';

	SwatZIndexManager.raiseElement(this.close_div);

	this.overlay.cfg.setProperty('context',
		[this.toggle_button, 'tl', 'bl']);

	this.overlay.show();
	SwatZIndexManager.raiseElement(this.palette);
	this.is_open = true;
}

/**
 * Draws the hex input box
 */
SwatSimpleColorEntry.prototype.drawInput = function()
{
	var div_tag = document.createElement('div');
	div_tag.className = 'swat-simple-color-entry-palette-hex-color';

	var label_tag = document.createElement('label');
	label_tag.htmlFor = this.id + '_hex_color';
	var title = document.createTextNode('#');
	label_tag.appendChild(title);

	var button_tag = document.createElement('button');
	button_tag.setAttribute('type', 'button');
	var title = document.createTextNode(SwatSimpleColorEntry.set_text);
	button_tag.appendChild(title);
	YAHOO.util.Event.addListener(button_tag, 'click', this.toggle, this, true);

	div_tag.appendChild(label_tag);
	div_tag.appendChild(this.hex_input_tag);
	div_tag.appendChild(button_tag);

	this.palette.childNodes[1].appendChild(div_tag);
}

SwatSimpleColorEntry.prototype.handleInputChange = function()
{
	var color = this.hex_input_tag.value;

	if (color[0] == '#') {
		color = color.slice(1, color.length - 1);
	}

	if (color.length == 3) {
		var hex3 = /^[0-9a-f]{3}$/i;
		if (!hex3.test(color)) {
			color = null;
		}
	} else if (color.length == 6) {
		var hex6 = /^[0-9a-f]{6}$/i;
		if (!hex6.test(color)) {
			color = null;
		}
	} else {
		color = null;
	}

	this.setColor(color);
}

/**
 * Draws this color palette
 */
SwatSimpleColorEntry.prototype.drawPalette = function()
{
	var table = document.createElement('table');
	table.cellSpacing = '1';

	var tbody = document.createElement('tbody');

	if (this.colors.length % this.columns == 0)
		var num_cells = this.colors.length
	else
		var num_cells = this.colors.length +
			(this.columns - (this.colors.length % this.columns));

	var trow;
	var tcell;
	var anchor;
	var text;

	if (this.none_option_title !== null) {
		trow = document.createElement('tr');
		tcell = document.createElement('td');
		tcell.id = this.id + '_palette_null';
		tcell.colSpan = this.columns;
		YAHOO.util.Dom.addClass(tcell,
			'swat-simple-color-entry-palette-blank');

		text = document.createTextNode(this.none_option_title);

		anchor = document.createElement('a');
		anchor.href = '#';
		anchor.appendChild(text);
		tcell.appendChild(anchor);
		trow.appendChild(tcell);
		tbody.appendChild(trow);

		YAHOO.util.Event.addListener(anchor, 'click', this.selectNull,
			this, true);
	}

	for (var i = 0; i < num_cells; i++) {
		if (i % this.columns == 0)
			trow = document.createElement('tr');

		tcell = document.createElement('td');
		text = document.createTextNode(' '); // non-breaking UTF-8 space

		if (i < this.colors.length) {
			tcell.id = this.id + '_palette_' + i;
			tcell.style.background = '#' + this.colors[i];

			anchor = document.createElement('a');
			anchor.href = '#';
			anchor.appendChild(text);

			YAHOO.util.Event.addListener(anchor, 'click', this.selectColor,
				this, true);

			tcell.appendChild(anchor);
		} else {
			YAHOO.util.Dom.addClass(tcell,
				'swat-simple-color-entry-palette-blank');

			tcell.appendChild(text);
		}

		trow.appendChild(tcell);

		if ((i + 1) % this.columns == 0)
			tbody.appendChild(trow);
	}

	table.appendChild(tbody);
	this.palette.childNodes[1].appendChild(table);
}

SwatSimpleColorEntry.prototype.toggle = function()
{
	if (this.is_open)
		this.close();
	else
		this.open();
}

/**
 * Sets the value of the color entry input tag to the selected color and
 * highlights the selected color
 *
 * @param number color the hex value of the color
 */
SwatSimpleColorEntry.prototype.setColor = function(color)
{
	var changed = (this.current_color != color);

	if (changed) {
		this.input_tag.value = color;

		if (color === null) {
			this.swatch.style.background = null;
			this.hex_input_tag.value = '';
		} else {
			this.hex_input_tag.value = color;
			this.swatch.style.background = '#' + color;
		}

		this.current_color = color;

		if (color === null)
			this.colorChangeEvent.fire(null);
		else
			this.colorChangeEvent.fire('#' + color);

		this.highlightPalleteEntry(color);
	}
}

/**
 * Event handler that sets the color to null
 *
 * @param Event the event that triggered this select.
 */
SwatSimpleColorEntry.prototype.selectNull = function(event)
{
	YAHOO.util.Event.preventDefault(event);
	this.setColor(null);
}

/**
 * Event handler that sets the color to the selected color
 *
 * @param Event the event that triggered this select.
 */
SwatSimpleColorEntry.prototype.selectColor = function(event)
{
	YAHOO.util.Event.preventDefault(event);
	var cell = YAHOO.util.Event.getTarget(event);
	var color_index = cell.parentNode.id.split('_palette_')[1];

	this.setColor(this.colors[color_index]);
}

/**
 * Highlights a pallete entry
 *
 * @param number color the hex value of the color
 */
SwatSimpleColorEntry.prototype.highlightPalleteEntry = function(color)
{
	if (this.none_option_title !== null) {
		var null_entry = document.getElementById(this.id + '_palette_null');

		if (color === null) {
			YAHOO.util.Dom.addClass(null_entry,
				'swat-simple-color-entry-palette-selected');
		} else {
			YAHOO.util.Dom.removeClass(null_entry,
				'swat-simple-color-entry-palette-selected');
		}
	}

	for (var i = 0; i < this.colors.length; i++) {
		var palette_entry =
			document.getElementById(this.id + '_palette_' + i);

		if (this.current_color !== null &&
			this.colors[i].toLowerCase() ==
			this.current_color.toLowerCase()) {

			YAHOO.util.Dom.addClass(palette_entry,
				'swat-simple-color-entry-palette-selected');
		} else {
			YAHOO.util.Dom.removeClass(palette_entry,
				'swat-simple-color-entry-palette-selected');
		}
	}
}

