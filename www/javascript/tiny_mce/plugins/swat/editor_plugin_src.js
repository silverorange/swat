/**
 * Swat TinyMCE Plugin
 * Copyright (C) 2009 silverorange Inc.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301
 * USA
 *
 * Portions of this plugin are based on the Yahoo User-Interface Library, which
 * is released under the following license:
 *
 *   Copyright (c) 2009, Yahoo! Inc.
 *   All rights reserved.
 *
 *   Redistribution and use of this software in source and binary forms, with
 *   or without modification, are permitted provided that the following
 *   conditions are met:
 *
 *   - Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   - Redistributions in binary form must reproduce the above copyright,
 *     notice, this list of conditions and the following disclaimer in the
 *     documentation and/or other materials provided with the distribution.
 *   - Neither the name of Yahoo! Inc. nor the names of its contributors may be
 *     used to endorse or promote products derived from this software without
 *     specific prior written permission of Yahoo! Inc.
 *
 *   THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 *   "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
 *   TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 *   PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 *   CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *   EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 *   PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 *   PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 *   LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *   NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 *   SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * Portions of this plugin are based on the 'avdlink' plugin distributed with
 * TinyMCE, which is released under the following license:
 *
 *   TinyMCE
 *   Copyright (C) 2003-2009 Moxicode Systems AB.
 *
 *   This library is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Lesser General Public
 *   License as published by the Free Software Foundation; either
 *   version 2.1 of the License, or (at your option) any later version.
 *
 *   This library is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *   Lesser General Public License for more details.
 *
 *   You should have received a copy of the GNU Lesser General Public
 *   License along with this library; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301
 *   USA
 */
(function() {

	var DOM        = tinymce.DOM;
	var Event      = tinymce.dom.Event;
	var Dispatcher = tinymce.util.Dispatcher;

	var _getRect = function(el)
	{
		if (   typeof YAHOO == 'undefined'
			|| typeof YAHOO.util == 'undefined'
			|| typeof YAHOO.util.Dom == 'undefined'
		) {
			var rect = DOM.getRect(el);
		} else {
			// Use YUI if available for more accurate measurements
			var rect = YAHOO.util.Dom.getRegion(el);
			rect.x = rect.left;
			rect.y = rect.top;
			rect.w = rect.right  - rect.left;
			rect.h = rect.bottom - rect.top;
		}

		return rect;
	};

	var _getDocumentHeight = function()
	{
		var viewPort = DOM.getViewPort();

		var scrollHeight = (typeof document.scrollHeight == 'undefined') ?
			document.body.scrollHeight : document.scrollHeight;

		var h = Math.max(scrollHeight, viewPort.h);

		return h;
	};

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

	Swat.MODE_VISUAL = 1;
	Swat.MODE_SOURCE = 2;

	// }}}
	// {{{ Swat.Dialog

	Swat.Dialog = function(ed)
	{
		this.editor    = ed;
		this.selection = null;
		this.frame     = null;
		this.container = null;

		this.drawOverlay();
		this.drawDialog();

		this.onConfirm = new Dispatcher(this);
		this.onCancel  = new Dispatcher(this);
	};

	Swat.Dialog.prototype.reset = function()
	{
	};

	Swat.Dialog.prototype.focus = function()
	{
	};

	Swat.Dialog.prototype.getData = function()
	{
		return {};
	};

	Swat.Dialog.prototype.setData = function(data)
	{
	};

	Swat.Dialog.prototype.close = function(confirmed)
	{
		var ed = this.editor;

		this.overlay.style.display = 'none';
		this.container.style.display = 'none';

		// hide select elements in IE6
		if (tinymce.isIE6) {
			selectElements = DOM.doc.getElementsByTagName('select');
			for (var i = 0; i < selectElements.length; i++) {
				if (typeof selectElements[i].style._visibility != 'undefined') {
					selectElements[i].style.visibility =
						selectElements[i].style._visibility;
				}
			}
		}

		// remove editor focus handler
		var obj = (ed.settings.content_editable) ?
			ed.getBody() :
			(!tinymce.isGecko) ?
				ed.getWin() :
				ed.getDoc();

		Event.remove(obj, 'focus', this.handleEditorFocus);

		ed.focus();

		// restore selection (for IE6)
		if (this.selection) {
			ed.selection.moveToBookmark(this.selection);
		}

		if (confirmed) {
			this.onConfirm.dispatch(this, this.getData());
		} else {
			this.onCancel.dispatch(this);
		}

		this.reset();

		Event.remove(DOM.doc, 'keydown', this.handleKeyPress);
	};

	Swat.Dialog.prototype.open = function(data)
	{
		var ed = this.editor;

		// save selection (for IE6)
		this.selection = ed.selection.getBookmark();

		// set up focus handler to prevent keyboard navigation back to the
		// editor while this dialog is open
		var obj = (ed.settings.content_editable) ?
			ed.getBody() :
			(!tinymce.isGecko) ?
				ed.getWin() :
				ed.getDoc();

		Event.add(obj, 'focus', this.handleEditorFocus, this);

		this.setData(data);

		// show select elements in IE6
		if (tinymce.isIE6) {
			selectElements = DOM.doc.getElementsByTagName('select');
			for (var i = 0; i < selectElements.length; i++) {
				selectElements[i].style._visibility =
					selectElements[i].style.visibility;

				selectElements[i].style.visibility = 'hidden';
			}
		}

		// display offsecreen to get dimensions
		this.container.style.left    = '-10000px';
		this.container.style.top     = '-10000px';
		this.container.style.display = 'block';

		var rect = _getRect(this.container);

		// in WebKit, the container element has no width for some reason so
		// get the width of the table it contains.
		var el = ed.getContainer().firstChild;
		var editorRect = _getRect(el);

		var x = editorRect.x + ((editorRect.w  - rect.w) / 2);
		var y = editorRect.y + 30;

		this.overlay.style.height = _getDocumentHeight() + 'px';
		this.overlay.style.display = 'block';

		this.container.style.left = x + 'px';
		this.container.style.top = y + 'px';
		this.container.style.display = 'block';
		this.focus();

		Event.add(DOM.doc, 'keydown', this.handleKeyPress, this);
	};

	Swat.Dialog.prototype.drawDialog = function()
	{
		this.frame = DOM.create('div');
		this.frame.className = 'swat-frame';

		this.container = DOM.create('div');
		this.container.className = 'swat-textarea-editor-dialog';
		this.container.appendChild(this.frame);

		DOM.doc.body.appendChild(this.container);
	};

	Swat.Dialog.prototype.drawOverlay = function()
	{
		this.overlay = DOM.create('div');
		this.overlay.className = 'swat-textarea-editor-overlay';
		DOM.doc.body.appendChild(this.overlay);
	};

	Swat.Dialog.prototype.handleKeyPress = function(e)
	{
		var which = (e.which) ?
			e.which :
			((e.keyCode) ? e.keyCode : e.charCode);

		// handle escape key
		if (which == 27) {
			this.close(false);
		}
	};

	Swat.Dialog.prototype.handleEditorFocus = function(e)
	{
		// move focus our of iframe for WebKit browsers
		window.focus();
		this.focus();
	};

	// }}}
	// {{{ Swat.LinkDialog

	Swat.LinkDialog = function(ed)
	{
		Swat.LinkDialog.superclass.constructor.call(this, ed);
	};

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
		var data = Swat.LinkDialog.superclass.getData.call(this);
		data.link_uri = this.uriEntry.value;
		return data;
	},

	setData: function(data)
	{
		Swat.LinkDialog.superclass.setData.call(this, data);
		if (data.link_uri) {
			this.uriEntry.value = data.link_uri;
		}
	},

	open: function(data)
	{
		Swat.LinkDialog.superclass.open.call(this, data);

		// set confirm button title
		if (this.uriEntry.value.length) {
			this.insertButton.value = this.editor.getLang('swat.link_update');
			this.entryNote.style.display = 'block';
		} else {
			this.insertButton.value = this.editor.getLang('swat.link_insert');
			this.entryNote.style.display = 'none';
		}
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

		this.entryNote = DOM.create('div');
		this.entryNote.className = 'swat-note';
		this.entryNote.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.link_uri_field_note')
			)
		);

		var entryFormField = DOM.create('div');
		entryFormField.className = 'swat-form-field';
		entryFormField.appendChild(entryLabel);
		entryFormField.appendChild(entryFormFieldContents);
		entryFormField.appendChild(this.entryNote);

		this.insertButton = DOM.create('input', { type: 'button' });
		this.insertButton.className = 'swat-button swat-primary';
		this.insertButton.value = this.editor.getLang('swat.link_insert');
		Event.add(this.insertButton, 'click', function(e)
		{
			this.close(true);
		}, this);

		var cancel = DOM.create('input', { type: 'button' });
		cancel.className = 'swat-button';
		cancel.value = this.editor.getLang('swat.link_cancel');
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

		this.frame.appendChild(form);
	}

	});

	// }}}
	// {{{ Swat.ImageDialog

	Swat.ImageDialog = function(ed)
	{
		Swat.ImageDialog.superclass.constructor.call(this, ed);
	};

	Swat.extend(Swat.ImageDialog, Swat.Dialog, {

	focus: function()
	{
		this.srcEntry.focus();
	},

	reset: function()
	{
		this.srcEntry.value = '';
		this.altEntry.value = '';
	},

	getData: function()
	{
		var data = Swat.ImageDialog.superclass.getData.call(this);
		data.image_src = this.srcEntry.value;
		data.image_alt = this.altEntry.value;
		return data;
	},

	setData: function(data)
	{
		Swat.ImageDialog.superclass.setData.call(this, data);

		if (data.image_src) {
			this.srcEntry.value = data.image_src;
		}

		if (data.image_alt) {
			this.altEntry.value = data.image_alt;
		}
	},

	open: function(data)
	{
		Swat.ImageDialog.superclass.open.call(this, data);

		// set confirm button title
		if (this.srcEntry.value.length) {
			this.insertButton.value = this.editor.getLang('swat.image_update');
		} else {
			this.insertButton.value = this.editor.getLang('swat.image_insert');
		}
	},

	drawDialog: function()
	{
		Swat.ImageDialog.superclass.drawDialog.call(this);

		var srcEntryId = this.editor.id + '_image_src_entry';

		this.srcEntry = DOM.create('input', { id: srcEntryId, type: 'text' });
		this.srcEntry.className = 'swat-entry';

		// select all on focus
		Event.add(this.srcEntry, 'focus', function(e)
		{
			this.select();
		}, this.srcEntry);

		var srcEntryLabel = DOM.create('label');
		srcEntryLabel.htmlFor = srcEntryId;
		srcEntryLabel.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.image_src_field')
			)
		);

		var srcEntryFormFieldContents = DOM.create('div');
		srcEntryFormFieldContents.className = 'swat-form-field-contents';
		srcEntryFormFieldContents.appendChild(this.srcEntry);

		var srcEntryFormField = DOM.create('div');
		srcEntryFormField.className = 'swat-form-field';
		srcEntryFormField.appendChild(srcEntryLabel);
		srcEntryFormField.appendChild(srcEntryFormFieldContents);

		var altEntryId = this.editor.id + '_image_alt_entry';

		this.altEntry = DOM.create(
			'input',
			{ id: altEntryId, type: 'text' }
		);
		this.altEntry.className = 'swat-entry';

		// select all on focus
		Event.add(this.altEntry, 'focus', function(e)
		{
			this.select();
		}, this.altEntry);

		var altEntryLabelSpan = DOM.create('span');
		altEntryLabelSpan.className = 'swat-note';
		altEntryLabelSpan.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.image_optional')
			)
		);

		var altEntryLabel = DOM.create('label');
		altEntryLabel.htmlFor = altEntryId;
		altEntryLabel.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.image_alt_field')
			)
		);
		altEntryLabel.appendChild(DOM.doc.createTextNode(' '));
		altEntryLabel.appendChild(altEntryLabelSpan);

		var altEntryFormFieldContents = DOM.create('div');
		altEntryFormFieldContents.className = 'swat-form-field-contents';
		altEntryFormFieldContents.appendChild(this.altEntry);

		var altEntryFormField = DOM.create('div');
		altEntryFormField.className = 'swat-form-field';
		altEntryFormField.appendChild(altEntryLabel);
		altEntryFormField.appendChild(altEntryFormFieldContents);

		this.insertButton = DOM.create('input', { type: 'button' });
		this.insertButton.className = 'swat-button swat-primary';
		this.insertButton.value = this.editor.getLang('swat.image_insert');
		Event.add(this.insertButton, 'click', function(e)
		{
			this.close(true);
		}, this);

		var cancel = DOM.create('input', { type: 'button' });
		cancel.className = 'swat-button';
		cancel.value = this.editor.getLang('swat.image_cancel');
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
		form.appendChild(srcEntryFormField);
		form.appendChild(altEntryFormField);
		form.appendChild(footerFormField);
		Event.add(form, 'submit', function(e)
		{
			Event.cancel(e);
			this.close(true);
		}, this);

		this.frame.appendChild(form);
	}

	});

	// }}}
	// {{{ Swat.SnippetDialog

	Swat.SnippetDialog = function(ed)
	{
		Swat.SnippetDialog.superclass.constructor.call(this, ed);
	};

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
		var data = Swat.SnippetDialog.superclass.getData.call(this);
		data.snippet = this.snippetEntry.value;
		return data;
	},

	drawDialog: function()
	{
		Swat.SnippetDialog.superclass.drawDialog.call(this);

		var entryId = this.editor.id + '_snippet_entry';

		this.snippetEntry = DOM.create(
			'textarea',
			{ id: entryId, rows: 4, cols: 50 }
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
		cancel.value = this.editor.getLang('swat.snippet_cancel');
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

		this.frame.appendChild(form);
	}

	});

	// }}}

	// define plugin
	tinymce.create('tinymce.plugins.SwatPlugin', {

	// {{{ tinymce.plugins.SwatPlugin.init()

	init: function(ed, url)
	{
		this.editor = ed;

		var that = this;

		// after rendering UI, draw source mode and tabs for visual editor
		ed.onLoadContent.add(this.initMode, this);
		if (ed.getParam('swat_modes_enabled', 'yes').toLowerCase() != 'no') {
			ed.onPostRender.add(this.drawSourceMode, this);
		}

		// double up linebreaks around blocklevel elements
		ed.onGetContent.add(function(ed, o)
		{
			if (this.mode != Swat.MODE_SOURCE) {
				var block = '(?:p|pre|dl|div|blockquote|form|h[1-6]|table'
					+ '|fieldset|address|ul|ol)';

				// match one or mode ending block-level tags, optionally
				// separated by whitespace
				var exp = new RegExp(
					  '('
					+ '<\/'+ block + '[^<>]*?>'
					+ '(?:[\n\r\t ]<\/' + block + '[^<>]*?>)*'
					+ ')[\n\r\t ]*',
					'g'
				);

				o.content = o.content.replace(exp, '$1\n\n');

				// trim trailing whitespace
				o.content = o.content.replace(/[\r\n\t ]*$/, '');
			}
		}, this);

		// if the form is submitted in source mode, push changes back to
		// visual editor before the regular form submit hook runs
		ed.onSubmit.addToTop(function(ed, e)
		{
			if (this.mode == Swat.MODE_SOURCE) {
				this.setVisualMode();
			}
		}, this);

		// load plugin CSS
		ed.onBeforeRenderUI.add(function()
		{
			DOM.loadCSS(url + '/css/swat.css');
		});

		this.dialogs = {
			'link':    new Swat.LinkDialog(ed),
			'snippet': new Swat.SnippetDialog(ed),
			'image':   new Swat.ImageDialog(ed)
		};

		// dialog close handler for link dialog
		this.dialogs.link.onConfirm.add(function(dialog, data)
		{
			var uri = data.link_uri;
			this.insertLink(uri);
		}, this);

		// dialog close handler for image dialog
		this.dialogs.image.onConfirm.add(function(dialog, data)
		{
			var src = data.image_src;
			var alt = data.image_alt;
			this.insertImage(src, alt);
		}, this);

		// dialog close handler for snippet dialog
		this.dialogs.snippet.onConfirm.add(function(dialog, data)
		{
			var content = data.snippet;
			this.insertSnippet(content);
		}, this);

		// link button
		ed.addCommand('mceSwatLink', function()
		{
			var sel = ed.selection;

			// if there is no selection or not on a link, do nothing
			if (sel.isCollapsed() && !ed.dom.getParent(sel.getNode(), 'A')) {
				return;
			}

			// get existing href
			var uri = ed.dom.getAttrib(sel.getNode(), 'href');

			var data = { 'link_uri':  uri };
			that.dialogs.link.open(data);
		});

		// image button
		ed.addCommand('mceSwatImage', function()
		{
			var data, sel = ed.selection;

			// if an image is selected, get its attributes
			var el = ed.dom.getParent(sel.getNode(), 'IMG');
			if (el) {
				// get existing image data
				data = {
					'image_src': ed.dom.getAttrib(el, 'src'),
					'image_alt': ed.dom.getAttrib(el, 'alt')
				};
			} else {
				// new image
				data = {};
			}

			that.dialogs.image.open(data);
		});

		// snippet button
		ed.addCommand('mceSwatSnippet', function()
		{
			var data = {};
			that.dialogs.snippet.open(data);
		});

		// register link button
		ed.addButton('link', {
			'title': 'swat.link_desc',
			'cmd':   'mceSwatLink',
			'class': 'mce_link'
		});

		// register link keyboard shortcut
		ed.addShortcut('crtl+k', 'swat.link_desc', 'mceSwatLink');

		// register image button
		ed.addButton('image', {
			'title': 'swat.image_desc',
			'cmd':   'mceSwatImage',
			'class': 'mce_image'
		});

		// register snippet button
		ed.addButton('snippet', {
			'title': 'swat.snippet_desc',
			'cmd':   'mceSwatSnippet',
			'class': 'mce_snippet'
		});

		// register enable/disable event handler for link button
		var t = this;
		ed.onInit.add(function() {
			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled(
					'link',
					(co && n.nodeName != 'A') || t.mode == Swat.MODE_SOURCE
				);
				cm.setActive(
					'link',
					n.nodeName == 'A' && !n.name && t.mode != Swat.MODE_SOURCE
				);
			});
		});
	},

	// }}}
	// {{{ tinymce.plugins.SwatPlugin.insertLink()

	insertLink: function(href)
	{
		//checkPrefix(href);

		var ed  = this.editor;
		var dom = ed.dom;

		// get selected link
		var el = ed.selection.getNode();
		el = dom.getParent(el, 'A');

		// remove element if there is no href
		if (!href) {
			ed.execCommand('mceBeginUndoLevel');
			var i = ed.selection.getBookmark();
			dom.remove(el, true);
			ed.selection.moveToBookmark(i);
			ed.execCommand('mceEndUndoLevel');
			return;
		}

		ed.execCommand('mceBeginUndoLevel');

		// create new anchor elements
		if (el === null) {

			ed.getDoc().execCommand('unlink', false, null);

			ed.execCommand(
				'CreateLink',
				false,
				'#mce_temp_url#',
				{ skip_undo: 1 }
			);

			var elements = tinymce.grep(
				dom.select('a'),
				function (n)
				{
					return (dom.getAttrib(n, 'href') == '#mce_temp_url#');
				}
			);

			for (var i = 0; i < elements.length; i++) {
				dom.setAttrib(el = elements[i], 'href', href);
			}

		} else {
			dom.setAttrib(el, 'href', href);
		}

		// don't move caret if selection was image
		if (el.childNodes.length != 1 || el.firstChild.nodeName != 'IMG') {
			ed.focus();
			ed.selection.select(el);
			ed.selection.collapse(false);
		}

		ed.execCommand('mceEndUndoLevel');
	},

	// }}}
	// {{{ tinymce.plugins.SwatPlugin.insertImage()

	insertImage: function(src, alt)
	{
		var ed  = this.editor;
		var dom = ed.dom;

		// get selected image
		var el = ed.selection.getNode();
		el = dom.getParent(el, 'IMG');

		ed.execCommand('mceBeginUndoLevel');

		// create new img elements
		if (el === null) {

			ed.execCommand(
				'InsertImage',
				false,
				'#mce_temp_src#',
				{ skip_undo: 1 }
			);

			var elements = tinymce.grep(
				dom.select('img'),
				function (n)
				{
					return (dom.getAttrib(n, 'src') == '#mce_temp_src#');
				}
			);

			for (var i = 0; i < elements.length; i++) {
				el = elements[i];
				dom.setAttrib(el, 'src', src);
				if (alt) {
					dom.setAttrib(el, 'alt', alt);
				}
			}

		} else {
			dom.setAttrib(el, 'src', src);
			if (alt) {
				dom.setAttrib(el, 'alt', alt);
			}
		}

		ed.execCommand('mceEndUndoLevel');
	},

	// }}}
	// {{{ tinymce.plugins.SwatPlugin.insertSnippet()

	insertSnippet: function(content)
	{
		var ed = this.editor;

		ed.execCommand('mceBeginUndoLevel');

		ed.selection.setContent('#mce_temp_content##mce_temp_cursor#');

		// insert content
		ed.setContent(
			ed.getContent().replace(
				/#mce_temp_content#/g,
				content
			)
		);

		// find cursor position
		var nodes = this.editor.dom.select('body *');
		var cursorNode = null;
		var cursorPosition = -1;
		for (var i = 0; i < nodes.length; i++) {
			for (var j = 0; j < nodes[i].childNodes.length; j++) {
				var childNode = nodes[i].childNodes[j];
				if (childNode.nodeType == 3) {
					cursorPosition = childNode.nodeValue.indexOf(
						'#mce_temp_cursor#'
					);

					if (cursorPosition !== -1) {
						cursorNode = childNode;
						break;
					}
				}
			}

			if (cursorNode !== null) {
				break;
			}
		}

		// focus and position cursor
		ed.focus();
		if (cursorNode) {
			var split = cursorNode.nodeValue.split('#mce_temp_cursor#', 2);
			if (split[0] === '' && split[1] === '') {
				var node = ed.getDoc().createTextNode('');
				cursorNode.parentNode.replaceChild(node, cursorNode);
				ed.selection.select(node);
				ed.selection.collapse(false);
			} else if (split[0] === '') {
				var node = ed.getDoc().createTextNode(split[1]);
				cursorNode.parentNode.replaceChild(node, cursorNode);
				ed.selection.select(node);
				ed.selection.collapse(true);
			} else if (split[1] === '') {
				var node = ed.getDoc().createTextNode(split[0]);
				cursorNode.parentNode.replaceChild(node, cursorNode);
				ed.selection.select(node);
				ed.selection.collapse(false);
			} else {
				var leftNode = ed.getDoc().createTextNode(split[0]);
				var rightNode = ed.getDoc().createTextNode(split[1]);
				cursorNode.parentNode.replaceChild(rightNode, cursorNode);
				rightNode.parentNode.insertBefore(leftNode, rightNode);
				ed.selection.select(leftNode);
				ed.selection.collapse(false);
			}
		}

		ed.execCommand('mceEndUndoLevel');
	},

	// }}}
	// {{{ tinymce.plugins.SwatPlugin.drawSourceMode()

	drawSourceMode: function(ed)
	{
		var ed = this.editor;

		// get textarea
		var el = ed.getElement();

		el.className         = 'swat-textarea';
		el.style.overflowX   = 'hidden';
		el.style.overflowY   = 'scroll';
		el.style.borderStyle = 'none';
		el.style.borderWidth = '0';    // For IE
		el.style.resize      = 'none'; // For WebKit
		el.style.outline     = 'none'; // For WebKit
		el.style.padding     = '1px';
		el.style.width       = '99.5%';
		el.style.maxWidth    = '99.5%';

		this.sourceContainer = document.createElement('div');

		this.sourceContainer.className      = 'swat-textarea-container';
		this.sourceContainer.style.display  = 'none';
		this.sourceContainer.style.zIndex   = '3';
		this.sourceContainer.style.position = 'absolute';

		// add container to document
		el.parentNode.replaceChild(this.sourceContainer, el);
		this.sourceContainer.appendChild(el);

		// set textarea as block again since it is in a display:none container
		el.style.display = 'block';

		this.drawModeSwitcher(ed);
	},

	// }}}
	// {{{ tinymce.plugins.SwatPlugin.drawModeSwitcher()

	drawModeSwitcher: function(ed)
	{
		var ed = this.editor;

		this.modeLink = [];

		visualSpan = document.createElement('span');
		visualSpan.appendChild(
			document.createTextNode(
				ed.getLang('swat.mode_visual')
			)
		);

		var visualLink = document.createElement('a');
		visualLink.href = '#';
		if (this.mode != Swat.MODE_SOURCE) {
			visualLink.className = 'selected';
		}
		visualLink.appendChild(visualSpan);
		Event.add(visualLink, 'click', function(e)
		{
			Event.prevent(e);
			var focus = (this.mode != Swat.MODE_VISUAL);

			this.setVisualMode();

			// focus visual editor
			if (focus) {
				this.editor.focus();
			}
		}, this);

		var visualListItem = document.createElement('li');
		visualListItem.appendChild(visualLink);

		sourceSpan = document.createElement('span');
		sourceSpan.appendChild(
			document.createTextNode(
				ed.getLang('swat.mode_source')
			)
		);

		var sourceLink = document.createElement('a');
		sourceLink.href = '#';
		if (this.mode == Swat.MODE_SOURCE) {
			sourceLink.className = 'selected';
		}
		sourceLink.appendChild(sourceSpan);
		Event.add(sourceLink, 'click', function(e)
		{
			Event.prevent(e);

			var focus = (this.mode != Swat.MODE_SOURCE);

			this.setSourceMode();

			// focus source editor
			if (focus) {
				var el = this.editor.getElement();
				el.focus();
				if (el.setSelectionRange) {
					el.setSelectionRange(0, 0);
				} else {
					var r = el.createTextRange();
					r.collapse(true);
					r.moveToPoint(0);
				}
			}
		}, this);

		var sourceListItem = document.createElement('li');
		sourceListItem.appendChild(sourceLink);

		var ul = document.createElement('ul');
		ul.className = 'swat-textarea-editor-mode-switcher';
		ul.appendChild(sourceListItem);
		ul.appendChild(visualListItem);

		// get editor width
		var el = this.editor.getContainer().firstChild;
		var rect = _getRect(el);
		ul.style.width = (rect.w + 2) + 'px';

		// Hack to set width on a 50ms timeout because the table has not yet
		// been resized to fit the toolbar in older versions of WebKit.
		setTimeout(function() {
			var rect = _getRect(el);
			ul.style.width = (rect.w + 2) + 'px';
		}, 50);

		var clear = document.createElement('div');
		clear.style.clear = 'right';

		ed.getContainer().appendChild(ul);
		ed.getContainer().appendChild(clear);

		this.modeLink[Swat.MODE_VISUAL] = visualLink;
		this.modeLink[Swat.MODE_SOURCE] = sourceLink;
	},

	// }}}
	// {{{ tinymce.plugins.SwatPlugin.initMode()

	initMode: function()
	{
		var ed = this.editor;

		// initialize mode from form data
		var modeStateEl = document.getElementById(ed.id + '_mode');
		if (modeStateEl && modeStateEl.value == Swat.MODE_SOURCE) {
			this.setSourceMode();
		} else {
			this.setVisualMode();
		}

	},

	// }}}
	// {{{ tinymce.plugins.SwatPlugin.setVisualMode()

	setVisualMode: function()
	{
		if (this.mode == Swat.MODE_VISUAL) {
			return;
		}

		var ed = this.editor;

		if (this.modeLink) {
			DOM.removeClass(
				this.modeLink[Swat.MODE_SOURCE],
				'selected'
			);

			DOM.addClass(
				this.modeLink[Swat.MODE_VISUAL],
				'selected'
			);
		}

		// enable toolbar, do this before set content so note update
		// dispatcher updates toolbar state appropriately
		var cm = ed.controlManager;
		for (var id in cm.controls) {
			if (   id != ed.id + '_undo'
				&& id != ed.id + '_redo'
			) {
				cm.setDisabled(id, false);
			}
		}

		// hide textarea
		if (this.sourceContainer) {
			this.sourceContainer.style.display  = 'none';
		}

		// set content
		var el = ed.getElement();
		ed.setContent(el.value);

		this.mode = Swat.MODE_VISUAL;
		var modeStateEl = document.getElementById(ed.id + '_mode');
		if (modeStateEl) {
			modeStateEl.value = this.mode;
		}
	},

	// }}}
	// {{{ tinymce.plugins.SwatPlugin.setSourceMode()

	setSourceMode: function()
	{
		if (this.mode == Swat.MODE_SOURCE) {
			return;
		}

		var ed = this.editor;

		if (this.modeLink) {
			DOM.removeClass(
				this.modeLink[Swat.MODE_VISUAL],
				'selected'
			);

			DOM.addClass(
				this.modeLink[Swat.MODE_SOURCE],
				'selected'
			);
		}

		// disable toolbar
		var cm = ed.controlManager;
		for (var id in cm.controls) {
			if (   id != ed.id + '_undo'
				&& id != ed.id + '_redo'
			) {
				cm.setDisabled(id, true);
			}
		}

		var bgElement = ed.getBody().firstChild || ed.getBody();


		// get editor iframe for geometry
		var iframe = ed.getWin().frameElement;

		// get style of iframe to compute bg color
		var style = (tinymce.isIE) ?
			iframe.currentStyle :
			getComputedStyle(iframe, null);

		var bgColor = style.backgroundColor;

		// get width and height
		var w = iframe.clientWidth;
		var h = iframe.clientHeight;

		// get position
		var rect = _getRect(ed.getContentAreaContainer());

		// get textarea
		var el = ed.getElement();

		el.value         = this.editor.getContent();
		el.style.height  = (h - 4) + 'px'; // account for textarea padding

		// position and size textarea container
		this.sourceContainer.style.width    = w + 'px';
		this.sourceContainer.style.height   = h + 'px';
		this.sourceContainer.style.left     = '1px'; // TODO: get correct position
		this.sourceContainer.style.top      = '30px'; // TODO: get correct position

		// set background color
		this.sourceContainer.style.backgroundColor = bgColor;

		// display textarea
		this.sourceContainer.style.display  = 'block';

		this.mode = Swat.MODE_SOURCE;
		var modeStateEl = document.getElementById(ed.id + '_mode');
		if (modeStateEl) {
			modeStateEl.value = this.mode;
		}
	},

	// }}}
	// {{{ tinymce.plugins.SwatPlugin.toggleMode()

	toggleMode: function()
	{
		if (this.mode == Swat.MODE_VISUAL) {
			this.setSourceMode(true);
		} else {
			this.setVisualMode(true);
		}
	},

	// }}}
	// {{{ tinymce.plugins.SwatPlugin.getInfo()

	getInfo: function()
	{
		return {
			longname:  'Swat Plugin',
			author:    'silverorange Inc.',
			authorurl: 'http://www.silverorange.com/',
			version:   '1.0'
		};
	}

	// }}}

	});

	// register plugin
	tinymce.PluginManager.add('swat', tinymce.plugins.SwatPlugin);

	// }}}

})();
