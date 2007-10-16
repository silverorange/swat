function SwatSearchEntry(id)
{
	this.id = id;
	this.input = document.getElementById(this.id);

	var labels = document.getElementsByTagName('label');
	var label = null;

	for (var i = 0; i < labels.length; i++) {
		if (labels[i].htmlFor == this.id) {
			label = labels[i];
			break;
		}
	}

	if (label != null) {
		this.label_text =
			(label.innerText) ? label.innerText : label.textContent;

		label.style.display = 'none';

		YAHOO.util.Event.addListener(this.input, 'focus', this.handleFocus,
			this, true);

		YAHOO.util.Event.addListener(this.input, 'blur', this.handleBlur,
			this, true);

		this.faux_input = document.createElement('input');
		this.faux_input.setAttribute('type', 'text');
		this.faux_input.value = this.label_text;
		this.faux_input.size = this.input.size;

		YAHOO.util.Dom.addClass(this.faux_input, 'swat-entry');
		YAHOO.util.Dom.addClass(this.faux_input, 'swat-search-entry-empty');
		YAHOO.util.Event.addListener(this.faux_input, 'mousedown',
			this.handleMouseDown, this, true);

		YAHOO.util.Event.addListener(this.faux_input, 'focus',
			this.handleMouseDown, this, true);

		if (this.input.value == '')
			this.showFauxInput();
		else
			this.hideFauxInput();
	}
}

SwatSearchEntry.prototype.handleMouseDown = function(e)
{
	YAHOO.util.Event.preventDefault(e);
	this.hideFauxInput();
	this.input.focus();
}

SwatSearchEntry.prototype.handleFocus = function(e)
{
	this.hideFauxInput();
}

SwatSearchEntry.prototype.handleBlur = function(e)
{
	if (this.input.value == '')
		this.showFauxInput();
}

SwatSearchEntry.prototype.showFauxInput = function()
{
	// update computed styles to match real input
	var styles = [
		'paddingTop',        'paddingRight',
		'paddingBottom',     'paddingLeft',

		'marginTop',         'marginRight',
		'marginBottom',      'marginLeft',

		'borderTopWidth',    'borderRightWidth',
		'borderBottomWidth', 'borderLeftWidth',

		'borderTopStyle',    'borderRightStyle',
		'borderBottomStyle', 'borderLeftStyle'
	];

	for (var i = 0; i < styles.length; i++) {
		var current_style = YAHOO.util.Dom.getStyle(this.input, styles[i]);
		this.faux_input.style[styles[i]] = current_style;
	}

	// hack for IE and WebKit default border styles
	var border_styles = [
		'borderTopStyle',    'borderRightStyle',
		'borderBottomStyle', 'borderTopStyle'
	];

	var inset_border = false;
	for (var i = 0; i < border_styles.length; i++) {
		var current_style = YAHOO.util.Dom.getStyle(this.input,
			border_styles[i]);

		if (current_style == 'inset') {
			inset_border = true;
			break;
		}
	}

	// only set border color if border is not inset (hack for IE and WebKit)
	if (!inset_border) {
		var border_colors = [
			'borderTopColor',    'borderRightColor',
			'borderBottomColor', 'borderLeftColor'
		];


		for (var i = 0; i < border_colors.length; i++) {
			var current_style = YAHOO.util.Dom.getStyle(this.input,
				border_colors[i]);

			this.faux_input.style[border_colors[i]] = current_style;
		}
	}

	if (this.input.parentNode)
		this.input.parentNode.replaceChild(this.faux_input, this.input);
}

SwatSearchEntry.prototype.hideFauxInput = function()
{
	if (this.faux_input.parentNode)
		this.faux_input.parentNode.replaceChild(this.input, this.faux_input);
}
