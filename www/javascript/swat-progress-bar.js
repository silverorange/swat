/**
 * Progress bar
 *
 * The progress bar is accurate to four decimal places. This translates
 * one-hundredth of a percent.
 *
 * @copyright 2007 silverorange
 */
function SwatProgressBar(id, orientation, value)
{
	this.id = id;
	this.orientation = orientation;
	this.value = value;

	this.pulse_step = 0.05;
	this.pulse_position = 0;
	this.pulse_width = 0.15;
	this.pulse_direction = 1;

	this.full = document.getElementById(this.id + '_full');
	this.empty = document.getElementById(this.id + '_empty');

	this.onValueChange = new YAHOO.util.CustomEvent('valuechange');
}

SwatProgressBar.ORIENTATION_LEFT_TO_RIGHT = 1;
SwatProgressBar.ORIENTATION_RIGHT_TO_LEFT = 2;
SwatProgressBar.ORIENTATION_BOTTOM_TO_TOP = 3;
SwatProgressBar.ORIENTATION_TOP_TO_BOTTOM = 4;

SwatProgressBar.EPSILON = 0.0001;

SwatProgressBar.prototype.setValue = function(value)
{
	this.value = value;

	var full_width = 100 * value;
	var empty_width = 100 - (100 * value);
	full_width = (full_width > 100) ? 100 : full_width;
	empty_width = (empty_width < 0) ? 0 : empty_width;

	// reset position if bar was set to pulse-mode
	if (this.orientation !== SwatProgressBar.ORIENTATION_BOTTOM_TO_TOP)
		this.full.style.position = 'static';

	// reset empty div if bar was set to pulse mode
	this.empty.style.display = 'block';

	switch (this.orientation) {
	case SwatProgressBar.ORIENTATION_LEFT_TO_RIGHT:
	case SwatProgressBar.ORIENTATION_RIGHT_TO_LEFT:
	default:
		this.full.style.width = full_width + '%';
		this.empty.style.width = empty_width + '%';
		break;

	case SwatProgressBar.ORIENTATION_BOTTOM_TO_TOP:
		this.full.style.top = empty_width + '%';
		this.empty.style.top = '-' + full_width + '%';
		// fall through

	case SwatProgressBar.ORIENTATION_TOP_TO_BOTTOM:
		this.full.style.height = full_width + '%';
		this.empty.style.height = empty_width + '%';
		break;
	}

	this.onValueChange.fire(this.value);
}

SwatProgressBar.prototype.getValue = function()
{
	return this.value;
}

SwatProgressBar.prototype.pulse = function()
{
	this.full.style.position = 'relative';
	this.empty.style.display = 'none';

	switch (this.orientation) {
	case SwatProgressBar.ORIENTATION_LEFT_TO_RIGHT:
	default:
		this.full.style.width = (this.pulse_width * 100) + '%';
		this.full.style.left = (this.pulse_position * 100) + '%';
		break;

	case SwatProgressBar.ORIENTATION_RIGHT_TO_LEFT:
		this.full.style.width = (this.pulse_width * 100) + '%';
		this.full.style.left = '-' + (this.pulse_position * 100) + '%';
		break;
	
	case SwatProgressBar.ORIENTATION_BOTTOM_TO_TOP:
		this.full.style.height = (this.pulse_width * 100) + '%';
		this.full.style.top =
			((1 - (this.pulse_position + this.pulse_width)) * 100) + '%';

		break;

	case SwatProgressBar.ORIENTATION_TOP_TO_BOTTOM:
		this.full.style.height = (this.pulse_width * 100) + '%';
		this.full.style.top = (this.pulse_position * 100) + '%';
		break;
	}

	var new_pulse_position =
		this.pulse_position + this.pulse_step * this.pulse_direction;

	if (this.pulse_direction ==  1 &&
		this.compare(new_pulse_position + this.pulse_width, 1) > 0)
		this.pulse_direction = -1;

	if (this.pulse_direction == -1 && this.compare(new_pulse_position, 0) < 0)
		this.pulse_direction = 1;

	this.pulse_position += (this.pulse_step * this.pulse_direction);

	// preserve precision across multiple calls to pulse()
	this.pulse_position =
		Math.round(this.pulse_position / SwatProgressBar.EPSILON) *
		SwatProgressBar.EPSILON;
}

SwatProgressBar.prototype.compare = function(x, y)
{
	if (Math.abs(x - y) < SwatProgressBar.EPSILON) return 0;
	if (x > y) return 1;
	return -1;
}
