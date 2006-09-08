/**
 * Simple color entry widget
 *
 * @copyright 2005 silverorange Inc.
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

	this.palette_div = document.getElementById(this.id + '_palette');
	this.swatch_span = document.getElementById(this.id + '_swatch');
	this.input_tag = document.getElementById(this.id + '_value');

	// try to make a square palette
	this.columns = Math.ceil(Math.sqrt(this.colors.length));

	this.current_color = null;

	for (i = 0; i < this.colors.length; i++) {
		if (this.input_tag.value == this.colors[i]) {
			this.setColor(i);
			break;
		}
	}
}

/**
 * Closes this color palette
 */
SwatSimpleColorEntry.prototype.close = function()
{
	this.palette_div.style.display = 'none';
	SwatZIndexManager.lowerElement(this.palette_div);

	this.is_open = false;
}

/**
 * Opens this color palette
 */
SwatSimpleColorEntry.prototype.open = function()
{
	this.draw();

	this.palette_div.style.display = 'block';
	SwatZIndexManager.raiseElement(this.palette_div);

	this.is_open = true;
}

/**
 * Draws this color palette
 */
SwatSimpleColorEntry.prototype.draw = function()
{
	if (!this.is_drawn) {
		var output =
			'<div class="swat-simple-color-palette"><table cellspacing="1">';

		if (this.colors.length % this.columns == 0)
			var num_cells = this.colors.length
		else
			var num_cells = this.colors.length +
				(this.columns - (this.colors.length % this.columns));
		
		for (i = 0; i < num_cells; i++) {
			if (i % this.columns == 0)
				output = output + '<tr>';

			if (i < this.colors.length) {

				if (i == this.current_color) {
					output = output +
						'<td id="' + this.id + '_palette_' + i + '" ' +
						'style="background: #' + this.colors[i] + ';" ' +
						'class="swat-simple-color-palette-selected">';
				} else {
					output = output +
						'<td id="' + this.id + '_palette_' + i + '" ' +
						'style="background: #' + this.colors[i] + ';">';
				}
				
				output = output +
					'<a href="javascript:' + this.id +
					'_obj.setColor(' + i + ');">' +
					'&nbsp;' +
					'</a>' +
					'</td>';

			} else {
				output = output +
					'<td class="swat-simple-color-palette-blank">' +
					'&nbsp;' +
					'</td>';
			}

			if ((i + 1) % this.columns == 0)
				output = output + '</tr>';
		}

		output = output + '</table></div>';

		this.palette_div.innerHTML = output;
		this.is_drawn = true;
	}
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
	if (this.is_drawn) {
		if (this.current_color !== null) {
			var old_palette_entry =
				document.getElementById(this.id +
					'_palette_' + this.current_color);

			old_palette_entry.className = '';
		}
	}

	this.input_tag.value = this.colors[color_index];
	this.swatch_span.style.background = '#' + this.colors[color_index];
	this.current_color = color_index;

	if (this.is_drawn) {
		var palette_entry =
			document.getElementById(this.id + '_palette_' + color_index);

		palette_entry.className = 'swat-simple-color-palette-selected';
	}

	this.close();
}
