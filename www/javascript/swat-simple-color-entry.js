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
	this.is_positioned = false;

	this.input_tag = document.getElementById(this.id + '_value');
	this.swatch = document.getElementById(this.id + '_swatch');

	// this tries to make a square palette
	this.columns = Math.ceil(Math.sqrt(this.colors.length));

	this.current_color = null;
	this.colorChangeEvent = new YAHOO.util.CustomEvent('colorChange');

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

// preload images
SwatSimpleColorEntry.palette_image = new Image();
SwatSimpleColorEntry.palette_image.src =
	'packages/swat/images/color-palette.png';

/**
 * Displays the toggle button for this simple color entry
 */
SwatSimpleColorEntry.prototype.drawButton = function()
{
	var anchor = document.createElement('a');
	anchor.href = '#';
	anchor.title = SwatSimpleColorEntry.open_text;
	YAHOO.util.Event.addListener(anchor, 'click',
		function(e, color_entry)
		{
			YAHOO.util.Event.preventDefault(e);
			color_entry.toggle();
		}, this);

	this.toggle_button = document.createElement('img');
	this.toggle_button.id = this.id + '_toggle';
	this.toggle_button.src = SwatSimpleColorEntry.palette_image.src;
	this.toggle_button.alt = SwatSimpleColorEntry.toggle_alt_text;
	YAHOO.util.Dom.addClass(this.toggle_button,
		'swat-simple-color-entry-toggle');

	anchor.appendChild(this.toggle_button);

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
	container.appendChild(anchor);
	container.appendChild(this.palette);
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
	this.palette.style.display = 'block';
	this.is_drawn = true;
}

/**
 * Closes this color palette
 */
SwatSimpleColorEntry.prototype.close = function()
{
	this.overlay.hide();
	this.toggle_button.title = SwatSimpleColorEntry.open_text;
	SwatZIndexManager.lowerElement(this.palette);
	this.is_open = false;
}

/**
 * Opens this color palette
 */
SwatSimpleColorEntry.prototype.open = function()
{
	if (!this.is_positioned) {
		this.overlay.cfg.setProperty('context',
			[this.toggle_button, 'tl', 'bl']);

		this.is_positioned = true;
	}

	this.overlay.show();
	this.toggle_button.title = SwatSimpleColorEntry.close_text;
	SwatZIndexManager.raiseElement(this.palette);
	this.is_open = true;
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
		
	for (i = 0; i < num_cells; i++) {
		if (i % this.columns == 0)
			trow = document.createElement('tr');

		tcell = document.createElement('td');
		text = document.createTextNode(' '); // non-breaking UTF-8 space

		if (i < this.colors.length) {
			tcell.id = this.id + '_palette_' + i;
			tcell.style.background = '#' + this.colors[i];
			if (i == this.current_color)
				YAHOO.util.Dom.addClass(tcell,
					'swat-simple-color-entry-palette-selected');

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
 * @param number color_index the index of the selected color.
 */
SwatSimpleColorEntry.prototype.setColor = function(color_index)
{
	this.input_tag.value = this.colors[color_index];
	this.swatch.style.background = '#' + this.colors[color_index];
	var changed = (this.current_color != color_index);
	this.current_color = color_index;
	if (changed)
		this.colorChangeEvent.fire(this.getColor());
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

	if (this.current_color !== null) {
		var old_palette_entry =
			document.getElementById(this.id + '_palette_' + this.current_color);

		YAHOO.util.Dom.removeClass(old_palette_entry,
			'swat-simple-color-entry-palette-selected');
	}

	this.setColor(color_index);

	var palette_entry =
		document.getElementById(this.id + '_palette_' + color_index);

	YAHOO.util.Dom.addClass(palette_entry,
		'swat-simple-color-entry-palette-selected');

	this.close();
}

SwatSimpleColorEntry.prototype.getColor = function()
{
	return '#' + this.colors[this.current_color];
}
