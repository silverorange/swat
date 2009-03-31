(function() {

	var DOM        = tinymce.DOM;
	var Event      = tinymce.dom.Event;
	var Dispatcher = tinymce.util.Dispatcher;

	// register language translations
	tinymce.PluginManager.requireLangPack('swat');

	// {{{ Swat

	var Swat = {

	isFunction: function(o)
	{
		return Object.prototype.toString.apply(o) === '[object Function]';
	},

	_IEEnumFix: (tinymce.isIE) ?
	function(r, s)
	{
		var i, fname, f;
		var ADD = ['toString', 'valueOf'];
		for (i = 0; i < ADD.length; i = i + 1) {
			fname = ADD[i];
			f = s[fname];

			if (Swat.isFunction(f) && f != Object.prototype[fname]) {
				r[fname] = f;
			}
		}
	} : function()
	{
	},

	hasOwnProperty: (Object.prototype.hasOwnProperty) ?
	function(o, prop)
	{
		return (o && o.hasOwnProperty(prop));
	} : function(o, prop)
	{
		return (   !Swat.isUndefined(o[prop])
				&& o.constructor.prototype[prop] !== o[prop]);
	},

	isUndefined: function(o)
	{
		return typeof o === 'undefined';
	},

	extend: function(subc, superc, overrides)
	{
		if (!superc || !subc) {
			throw new Error('extend failed, please check that ' +
							'all dependencies are included.');
		}

		var F = function() {}, i;
		F.prototype = superc.prototype;
		subc.prototype = new F();
		subc.prototype.constructor = subc;
		subc.superclass = superc.prototype;
		if (superc.prototype.constructor == Object.prototype.constructor) {
			superc.prototype.constructor = superc;
		}

		if (overrides) {
			for (i in overrides) {
				if (Swat.hasOwnProperty(overrides, i)) {
					subc.prototype[i] = overrides[i];
				}
			}

			Swat._IEEnumFix(subc.prototype, overrides);
		}
	}

	};

	// }}}
	// {{{ Swat.Dialog

	Swat.Dialog = function(ed)
	{
		this.editor = ed;

		this.drawOverlay();
		this.drawDialog();

		this.onConfirm = new Dispatcher(this);
		this.onCancel  = new Dispatcher(this);
	}

	Swat.Dialog.prototype.reset = function()
	{
	}

	Swat.Dialog.prototype.focus = function()
	{
	}

	Swat.Dialog.prototype.close = function(confirmed)
	{
		this.overlay.style.display = 'none';
		this.element.style.display = 'none';

		// hide select elements in IE6
		if (tinyMCE.isIE6) {
			selectElements = DOM.doc.getElementsByTagName('select');
			for (var i = 0; i < selectElements.length; i++) {
				if (typeof selectElements[i].style._visibility != 'undefined') {
					selectElements[i].style.visibility =
						selectElements[i].style._visibility;
				}
			}
		}

		if (confirmed) {
			this.onConfirm.dispatch(this, this.getData());
		} else {
			this.onCancel.dispatch(this);
		}

		this.reset();

		Event.remove(DOM.doc, 'keypress', this.handleKeyPress);
	}

	Swat.Dialog.prototype.open = function()
	{
		// show select elements in IE6
		if (tinyMCE.isIE6) {
			selectElements = DOM.doc.getElementsByTagName('select');
			for (var i = 0; i < selectElements.length; i++) {
				selectElements[i].style._visibility =
					selectElements[i].style.visibility;

				selectElements[i].style.visibility = 'hidden';
			}
		}

		var pos = DOM.getPos(this.editor.getContainer());
		var top = pos.y + 50;

		this.overlay.style.display = 'block';
		this.element.style.top = top + 'px';
		this.element.style.display = 'block';
		this.focus();

		Event.add(DOM.doc, 'keypress', this.handleKeyPress, this);
	}

	Swat.Dialog.prototype.drawDialog = function()
	{
		this.element = DOM.create('div');
		this.element.className = 'swat-frame swat-textarea-editor-dialog';

		DOM.doc.body.appendChild(this.element);
	}

	Swat.Dialog.prototype.drawOverlay = function()
	{
		this.overlay = DOM.create('div');
		this.overlay.className = 'swat-textarea-editor-overlay';
		DOM.doc.body.appendChild(this.overlay);
	}

	Swat.Dialog.prototype.getData = function()
	{
		return {};
	}

	Swat.Dialog.prototype.handleKeyPress = function(e)
	{
		var which = (e.which) ?
			e.which :
			((e.keyCode) ? e.keyCode : e.charCode);

		// handle escape key
		if (which == 27) {
			this.close(false);
		}
	}

	// }}}
	// {{{ Swat.LinkDialog

	Swat.LinkDialog = function(ed)
	{
		Swat.LinkDialog.superclass.constructor.call(this, ed);
	}

	Swat.extend(Swat.LinkDialog, Swat.Dialog, {

	focus: function()
	{
		this.uriEntry.focus();
	},

	reset: function()
	{
		this.uriEntry.value = '';
	},

	getData: function()
	{
		return { 'link_uri': this.uriEntry.value };
	},

	open: function()
	{
		// TODO: Update title based to update or insert
		this.insertButton.value = this.editor.getLang('swat.link_insert');
		Swat.LinkDialog.superclass.open.call(this);
	},

	drawDialog: function()
	{
		Swat.LinkDialog.superclass.drawDialog.call(this);

		var entryId = this.editor.id + '_link_entry';

		this.uriEntry = DOM.create('input', { id: entryId, type: 'text' });
		this.uriEntry.className = 'swat-entry';

		// select all on focus
		Event.add(this.uriEntry, 'focus', function(e)
		{
			this.select();
		}, this.uriEntry);

		var entryLabel = DOM.create('label');
		entryLabel.htmlFor = entryId;
		entryLabel.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.link_uri_field')
			)
		);

		var entryFormFieldContents = DOM.create('div');
		entryFormFieldContents.className = 'swat-form-field-contents';
		entryFormFieldContents.appendChild(this.uriEntry);

		var entryNote = DOM.create('div');
		entryNote.className = 'swat-note';
		entryNote.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.link_uri_field_note')
			)
		);

		var entryFormField = DOM.create('div');
		entryFormField.className = 'swat-form-field';
		entryFormField.appendChild(entryLabel);
		entryFormField.appendChild(entryFormFieldContents);
		entryFormField.appendChild(entryNote);

		this.insertButton = DOM.create('input', { type: 'button' });
		this.insertButton.className = 'swat-button swat-primary';
		this.insertButton.value = this.editor.getLang('swat.link_insert');
		Event.add(this.insertButton, 'click', function(e)
		{
			this.close(true);
		}, this);

		var cancel = DOM.create('input', { type: 'button' });
		cancel.className = 'swat-button';
		cancel.value = this.editor.getLang('swat.link_cancel')
		Event.add(cancel, 'click', function(e)
		{
			this.close(false);
		}, this);

		var footerFormFieldContents = DOM.create('div');
		footerFormFieldContents.className = 'swat-form-field-contents';
		footerFormFieldContents.appendChild(this.insertButton);
		footerFormFieldContents.appendChild(cancel);

		var footerFormField = DOM.create('div');
		footerFormField.className = 'swat-footer-form-field';
		footerFormField.appendChild(footerFormFieldContents);

		var form = DOM.create('form');
		form.className = 'swat-form';
		form.appendChild(entryFormField);
		form.appendChild(footerFormField);
		Event.add(form, 'submit', function(e)
		{
			Event.cancel(e);
			this.close(true);
		}, this);

		this.element.appendChild(form);
	}

	});

	// }}}
	// {{{ Swat.SnippetDialog

	Swat.SnippetDialog = function(ed)
	{
		Swat.SnippetDialog.superclass.constructor.call(this, ed);
	}

	Swat.extend(Swat.SnippetDialog, Swat.Dialog, {

	focus: function()
	{
		this.snippetEntry.focus();
	},

	reset: function()
	{
		this.snippetEntry.value = '';
	},

	getData: function()
	{
		return { 'snippet': this.snippetEntry.value };
	},

	drawDialog: function()
	{
		Swat.SnippetDialog.superclass.drawDialog.call(this);

		var entryId = this.editor.id + '_snippet_entry';

		this.snippetEntry = DOM.create(
			'textarea',
			{ id: entryId, type: 'text', rows: 4, cols: 50 }
		);

		this.snippetEntry.className = 'swat-textarea';

		var entryLabel = DOM.create('label');
		entryLabel.htmlFor = entryId;
		entryLabel.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.snippet_field')
			)
		);

		var entryFormFieldContents = DOM.create('div');
		entryFormFieldContents.className = 'swat-form-field-contents';
		entryFormFieldContents.appendChild(this.snippetEntry);

		var entryFormField = DOM.create('div');
		entryFormField.className = 'swat-form-field';
		entryFormField.appendChild(entryLabel);
		entryFormField.appendChild(entryFormFieldContents);

		insert = DOM.create('input', { type: 'button' });
		insert.className = 'swat-button swat-primary';
		insert.value = this.editor.getLang('swat.snippet_insert');
		Event.add(insert, 'click', function(e)
		{
			this.close(true);
		}, this);

		var cancel = DOM.create('input', { type: 'button' });
		cancel.className = 'swat-button';
		cancel.value = this.editor.getLang('swat.snippet_cancel')
		Event.add(cancel, 'click', function(e)
		{
			this.close(false);
		}, this);

		var footerFormFieldContents = DOM.create('div');
		footerFormFieldContents.className = 'swat-form-field-contents';
		footerFormFieldContents.appendChild(insert);
		footerFormFieldContents.appendChild(cancel);

		var footerFormField = DOM.create('div');
		footerFormField.className = 'swat-footer-form-field';
		footerFormField.appendChild(footerFormFieldContents);

		var form = DOM.create('form');
		form.className = 'swat-form';
		form.appendChild(entryFormField);
		form.appendChild(footerFormField);
		Event.add(form, 'submit', function(e)
		{
			Event.cancel(e);
			this.close(true);
		}, this);

		this.element.appendChild(form);
	}

	});

	// }}}

	// define plugin
	tinymce.create('tinymce.plugins.SwatPlugin', {

	init: function(ed, url)
	{
		// load plugin CSS
		ed.onBeforeRenderUI.add(function()
		{
			DOM.loadCSS(url + '/css/swat.css');
		});

		this.editor  = ed;

		this.dialogs = {
			'link':    new Swat.LinkDialog(ed),
			'snippet': new Swat.SnippetDialog(ed)
//			'image':   new Swat.ImageDialog(ed)
		};

		this.dialogs['link'].onConfirm.add(function(dialog, data)
		{
			var uri = data['link_uri'];
			this.insertLink(uri);
		}, this);

		this.dialogs['snippet'].onConfirm.add(function(dialog, data)
		{
			var content = data['snippet'];
			this.insertSnippet(content);
		}, this);

		var that = this;
		ed.addCommand('mceSwatLink', function()
		{
			var se = ed.selection;

			// if there is no selection, do nothing
			if (se.isCollapsed() && !ed.dom.getParent(se.getNode(), 'A')) {
				return;
			}

			that.dialogs['link'].open();
		});

		var that = this;
		ed.addCommand('mceSwatSnippet', function()
		{
			that.dialogs['snippet'].open();
		});

		// register button
		ed.addButton('link', {
			title: 'swat.link_desc',
			cmd:   'mceSwatLink'
		});

		// register keyboard shortcut
		ed.addShortcut('crtl+k', 'swat.link_desc', 'mceSwatLink');

		// register button
		ed.addButton('snippet', {
			title: 'swat.snippet_desc',
			cmd:   'mceSwatSnippet'
		});

		// register enable/disable event handler
		ed.onNodeChange.add(function(ed, cm, n, co) {
			cm.setDisabled('link', co && n.nodeName != 'A');
			cm.setActive('link', n.nodeName == 'A' && !n.name);
		});
	},

	insertSnippet: function(content)
	{
		this.editor.execCommand('mceBeginUndoLevel');
		this.editor.execCommand('mceInsertRawHTML', false, content);
		this.editor.execCommand('mceEndUndoLevel');
	},

	insertLink: function(href)
	{
		var e = this.editor.selection.getNode();
		//checkPrefix(href);

		e = this.editor.dom.getParent(e, 'A');

		// remove element if there is no href
		if (!href) {
			this.editor.execCommand('mceBeginUndoLevel');
			var i = this.editor.selection.getBookmark();
			this.editor.dom.remove(e, 1);
			this.editor.selection.moveToBookmark(i);
			this.editor.execCommand('mceEndUndoLevel');
			return;
		}

		this.editor.execCommand('mceBeginUndoLevel');

		// create new anchor elements
		if (e == null) {

			this.editor.getDoc().execCommand('unlink', false, null);

			this.editor.execCommand(
				'CreateLink',
				false,
				'#mce_temp_url#',
				{ skip_undo: 1 }
			);

			var dom = this.editor.dom;
			var elements = tinymce.grep(
				this.editor.dom.select('a'),
				function (n)
				{
					return (dom.getAttrib(n, 'href') == '#mce_temp_url#');
				}
			);

			for (var i = 0; i < elements.length; i++) {
				this.editor.dom.setAttrib(e = elements[i], 'href', href);
			}

		} else {
			this.editor.dom.setAttrib(e, 'href', href);
		}

		// don't move caret if selection was image
		if (e.childNodes.length != 1 || e.firstChild.nodeName != 'IMG') {
			this.editor.focus();
			this.editor.selection.select(e);
			this.editor.selection.collapse(0);
		}

		this.editor.execCommand('mceEndUndoLevel');
	},

	getInfo: function()
	{
		return {
			longname:  'Swat Plugin',
			author:    'silverorange Inc.',
			authorurl: 'http://www.silverorange.com/',
			version:   '1.0'
		};
	}

	});

	// register plugin
	tinymce.PluginManager.add('swat', tinymce.plugins.SwatPlugin);

})();
