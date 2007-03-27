/**
 * Simple color entry widget
 *
 * @copyright 2005-2007 silverorange Inc.
 */

/**
 * Creates a SwatSimpleColorEntry object
 *
 * @param string id
 * @param Array colors
 */
function SwatSimpleColorEntry(id, colors)
{
	this.id = id;
	this.colors = colors;
	this.is_open = false;
	this.is_drawn = false;
	this.positioned = false;

	this.palette_div = document.getElementById(this.id + '_palette');
	this.input_tag = document.getElementById(this.id + '_value');
	this.swatch = document.getElementById(this.id + '_swatch');

	// try to make a square palette
	this.columns = Math.ceil(Math.sqrt(this.colors.length));

	this.current_color = null;
	this.color_change_event = new YAHOO.util.CustomEvent('colorchange');

	for (i = 0; i < this.colors.length; i++) {
		if (this.input_tag.value == this.colors[i]) {
			this.setColor(i);
			break;
		}
	}

	this.drawButton();
	this.drawPalette();
	YAHOO.util.Event.onContentReady(this.id + '_palette',
		this.createOverlay, this, true)
}

/**
 * Displays the toggle button for this simple color entry
 */
SwatSimpleColorEntry.prototype.drawButton = function()
{
	var anchor = document.createElement('a');
	anchor.setAttribute('href', 'javascript:' + this.id + '_obj.toggle();');
	anchor.setAttribute('title', SwatSimpleColorEntry.open_text);

	var image = document.createElement('img');
	image.setAttribute('id', this.id + '_toggle');
	image.setAttribute('src', 'packages/swat/images/color-palette.png');
	image.setAttribute('alt', SwatSimpleColorEntry.toggle_alt_text);
	YAHOO.util.Dom.addClass(image, 'swat-simple-color-entry-toggle');

	anchor.appendChild(image);

	var palette_div = document.createElement('div');
	palette_div.setAttribute('id', this.id + '_palette');
	YAHOO.util.Dom.addClass(palette_div, 'swat-simple-color-entry-palette');

	var overlay_header = document.createElement('div');
	YAHOO.util.Dom.addClass(overlay_header, 'hd');

	var overlay_body = document.createElement('div');
	YAHOO.util.Dom.addClass(overlay_body, 'bd');

	var overlay_footer = document.createElement('div');
	YAHOO.util.Dom.addClass(overlay_footer, 'ft');

	palette_div.appendChild(overlay_header);
	palette_div.appendChild(overlay_body);
	palette_div.appendChild(overlay_footer);

	var container = document.getElementById(this.id);
	container.appendChild(anchor);
	container.appendChild(palette_div);

}

SwatSimpleColorEntry.open_text = 'open palette';
SwatSimpleColorEntry.close_text = 'close palette';
SwatSimpleColorEntry.toggle_alt_text = 'toggle palette graphic.';

/**
 * Creates simple color entry overlay widget when toggle button has been drawn
 */
SwatSimpleColorEntry.prototype.createOverlay = function(event)
{
	this.overlay = new YAHOO.widget.Overlay(this.id + '_palette',
		{ visible: false, constraintoviewport: true });

	this.overlay.render(document.body);
	this.is_drawn = true;
}

/**
 * Closes this color palette
 */
SwatSimpleColorEntry.prototype.close = function()
{
	this.overlay.hide();
	this.is_open = false;
	document.getElementById(this.id + '_toggle').setAttribute(
		'title', SwatSimpleColorEntry.open_text);
}

/**
 * Opens this color palette
 */
SwatSimpleColorEntry.prototype.open = function()
{
	if (!this.positioned) {
		var toggle_button = document.getElementById(this.id + '_toggle');
		this.overlay.cfg.setProperty('context',
			[toggle_button, 'tl', 'bl']);

		this.positioned = true;
	}

	this.is_open = true;
	this.overlay.show();
	document.getElementById(this.id + '_toggle').setAttribute(
		'title', SwatSimpleColorEntry.close_text);
}

/**
 * Draws this color palette
 */
SwatSimpleColorEntry.prototype.drawPalette = function()
{
	var table = document.createElement('table');
	table.setAttribute('cellspacing', '1');

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
		
	for (i = 0; i < num_cells; i++) {
		if (i % this.columns == 0)
			trow = document.createElement('tr');

		tcell = document.createElement('td');
		text = document.createTextNode(' '); // non-breaking UTF-8 space

		if (i < this.colors.length) {
			tcell.setAttribute('id', this.id + '_palette_' + i);
			tcell.style.background = '#' + this.colors[i];
			if (i == this.current_color)
				YAHOO.util.Dom.addClass(tcell,
					'swat-simple-color-entry-palette-selected');

			anchor = document.createElement('a');
			anchor.setAttribute('href', '#')
			anchor.appendChild(text);

			YAHOO.util.Event.addListener(anchor, 'click',
				SwatSimpleColorEntry.handleClick,
				{ entry: this, color_index: i });

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

	var palette_div = document.getElementById(this.id + '_palette');
	palette_div.childNodes[1].appendChild(table);
}

SwatSimpleColorEntry.prototype.toggle = function()
{
	if (this.is_open)
		this.close();
	else
		this.open();
}

SwatSimpleColorEntry.handleClick = function(event, parameters)
{
	parameters['entry'].setColor(parameters['color_index']);
}

/**
 * Sets the value of the color entry input tag to the selected color and
 * highlights the selected color
 *
 * @param number color_index the index of the selected color.
 */
SwatSimpleColorEntry.prototype.setColor = function(color_index)
{
	if (this.is_drawn) {
		if (this.current_color !== null) {
			var old_palette_entry =
				document.getElementById(this.id +
					'_palette_' + this.current_color);

			YAHOO.util.Dom.removeClass(old_palette_entry,
				'swat-simple-color-entry-palette-selected');
		}
	}

	this.input_tag.value = this.colors[color_index];
	this.swatch.style.background = '#' + this.colors[color_index];
	this.current_color = color_index;
	this.color_change_event.fire(this.getColor());

	if (this.is_drawn) {
		var palette_entry =
			document.getElementById(this.id + '_palette_' + color_index);

		YAHOO.util.Dom.addClass(palette_entry,
			'swat-simple-color-entry-palette-selected');

		this.close();
	}
}

SwatSimpleColorEntry.prototype.getColor = function()
{
	return '#' + this.colors[this.current_color];
}
