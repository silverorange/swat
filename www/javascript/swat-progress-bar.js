/**
 * Progress bar
 *
 * The progress bar is accurate to four decimal places. This translates
 * one-hundredth of a percent.
 *
 * @copyright 2007-2016 silverorange
 */
class SwatProgressBar {
	constructor(id, orientation, value) {
		this.id = id;
		this.orientation = orientation;
		this.value = value;

		this.pulse_step = 0.05;
		this.pulse_position = 0;
		this.pulse_width = 0.15;
		this.pulse_direction = 1;

		this.full = document.getElementById(this.id + '_full');
		this.empty = document.getElementById(this.id + '_empty');
		this.text = document.getElementById(this.id + '_text');
		this.container = document.getElementById(this.id);

		this.changeValueEvent = new YAHOO.util.CustomEvent('changeValue');
		this.pulseEvent = new YAHOO.util.CustomEvent('pulse');

		this.animation = null;

		YAHOO.util.Event.onDOMReady(function() {
			// Hack for Gecko and WebKit to load background images for full
			// part of progress bar. If the bar starts at zero, these browsers
			// don't load the background image, even when the bar's value
			// changes.
			this.full_stubb = document.createElement('div');
			this.full_stubb.className = this.full.className;
			this.full_stubb.style.width = '100px';
			this.full_stubb.style.height = '100px';
			this.full_stubb.style.position = 'absolute';
			this.full_stubb.style.top = '-10000px';
			document.body.appendChild(this.full_stubb);
		}, this, true);
	}

	setValue(value) {
		if (this.value === value) {
			return;
		}

		this.value = value;

		var full_width = 100 * value;
		var empty_width = 100 - (100 * value);
		full_width = (full_width > 100) ? 100 : full_width;
		empty_width = (empty_width < 0) ? 0 : empty_width;

		// reset position if bar was set to pulse-mode
		if (this.orientation !== SwatProgressBar.ORIENTATION_BOTTOM_TO_TOP) {
			this.full.style.position = 'static';
		}

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

		this.changeValueEvent.fire(this.value);
	}

	setValueWithAnimation(value) {
		if (this.value === value) {
			return;
		}

		var old_full_width = 100 * this.value;
		var old_empty_width = 100 - (100 * this.value);
		old_full_width = (old_full_width > 100) ? 100 : old_full_width;
		old_empty_width = (old_empty_width < 0) ? 0 : old_empty_width;

		// set new value
		this.value = value;

		var new_full_width = 100 * value;
		var new_empty_width = 100 - (100 * value);
		new_full_width = (new_full_width > 100) ? 100 : new_full_width;
		new_empty_width = (new_empty_width < 0) ? 0 : new_empty_width;

		// reset position if bar was set to pulse-mode
		if (this.orientation !== SwatProgressBar.ORIENTATION_BOTTOM_TO_TOP) {
			this.full.style.position = 'static';
		}

		// reset empty div if bar was set to pulse mode
		this.empty.style.display = 'block';

		var full_attributes = {};

		switch (this.orientation) {
			case SwatProgressBar.ORIENTATION_LEFT_TO_RIGHT:
			case SwatProgressBar.ORIENTATION_RIGHT_TO_LEFT:
			default:
				full_attributes['width'] = {
					from: old_full_width,
					to: new_full_width,
					unit: '%'
				};
				break;

			case SwatProgressBar.ORIENTATION_BOTTOM_TO_TOP:
				full_attributes['top'] = {
					from: old_empty_width,
					to: new_empty_width,
					unit: '%'
				};
				// fall through

			case SwatProgressBar.ORIENTATION_TOP_TO_BOTTOM:
				full_attributes['height'] = {
					from: old_full_width,
					to: new_full_width,
					unit: '%'
				};
				break;
		}

		// stop existing animation
		if (this.animation !== null && this.animation.isAnimated()) {
			this.animation.stop();
		}

		this.animation = new YAHOO.util.Anim(
			this.full,
			full_attributes,
			SwatProgressBar.ANIMATION_DURATION
		);

		// Synchronize empty div resizing with full div animation. Don't do
		// this with a separate animation.
		this.animation.onTween.subscribe(function() {
			var percent = this.animation.currentFrame /
				this.animation.totalFrames;

			var full_percent = old_full_width +
				((new_full_width - old_full_width) * percent);

			var empty_percent = 100.00 - full_percent;

			switch (this.orientation) {
				case SwatProgressBar.ORIENTATION_LEFT_TO_RIGHT:
				case SwatProgressBar.ORIENTATION_RIGHT_TO_LEFT:
				default:
					this.empty.style.width = empty_percent + '%';
					break;

				case SwatProgressBar.ORIENTATION_BOTTOM_TO_TOP:
					this.empty.style.top = -full_percent + '%';
					// fall through

				case SwatProgressBar.ORIENTATION_TOP_TO_BOTTOM:
					this.empty.style.height = empty_percent + '%';
					break;
			}
		}, this, true);

		this.animation.animate();

		this.changeValueEvent.fire(this.value);
	}

	setText(text) {
		if (this.text.innerText) {
			this.text.innerText = text;
		} else {
			this.text.textContent = text;
		}
	}

	getValue = function() {
		return this.value;
	}

	pulse() {
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

		if (this.pulse_direction === 1 &&
			this.compare(new_pulse_position + this.pulse_width, 1) > 0
		) {
			this.pulse_direction = -1;
		}

		if (this.pulse_direction === -1 &&
			this.compare(new_pulse_position, 0) < 0
		) {
			this.pulse_direction = 1;
		}

		this.pulse_position += (this.pulse_step * this.pulse_direction);

		this.pulseEvent.fire();

		// preserve precision across multiple calls to pulse()
		this.pulse_position =
			Math.round(this.pulse_position / SwatProgressBar.EPSILON) *
			SwatProgressBar.EPSILON;
	}

	compare(x, y) {
		if (Math.abs(x - y) < SwatProgressBar.EPSILON) {
			return 0;
		}

		if (x > y) {
			return 1;
		}

		return -1;
	}
}

SwatProgressBar.ORIENTATION_LEFT_TO_RIGHT = 1;
SwatProgressBar.ORIENTATION_RIGHT_TO_LEFT = 2;
SwatProgressBar.ORIENTATION_BOTTOM_TO_TOP = 3;
SwatProgressBar.ORIENTATION_TOP_TO_BOTTOM = 4;

SwatProgressBar.EPSILON = 0.0001;

SwatProgressBar.ANIMATION_DURATION = 0.5;

export default SwatProgressBar;
