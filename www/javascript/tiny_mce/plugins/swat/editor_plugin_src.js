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
	var JSON       = tinymce.util.JSON;
	var XHR        = tinymce.util.XHR;

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

	var _getEditorEl = function(editor)
	{
		var container = editor.getContainer();
		var el;
		for (var i = 0; i < container.childNodes.length; i++) {
			if (DOM.hasClass(container.childNodes[i], 'mceLayout')) {
				el = container.childNodes[i];
				break;
			}
		}
		return el;
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

		SwatZIndexManager.raiseElement(this.overlay);
		SwatZIndexManager.raiseElement(this.container);

		// display offsecreen to get dimensions
		this.container.style.left    = '-10000px';
		this.container.style.top     = '-10000px';
		this.container.style.display = 'block';

		var rect = _getRect(this.container);

		// in WebKit, the container element has no width for some reason so
		// get the width of the table it contains.
		var el = _getEditorEl(ed);
		if (el) {
			var editorRect = _getRect(el);

			var x = editorRect.x + ((editorRect.w  - rect.w) / 2);
			var y = editorRect.y + 30;

			this.overlay.style.height = _getDocumentHeight() + 'px';
			this.overlay.style.display = 'block';

			this.container.style.left = x + 'px';
			this.container.style.top = y + 'px';
			this.container.style.display = 'block';
		}
		this.focus();

		Event.add(DOM.doc, 'keydown', this.handleKeyPress, this);
	};

	Swat.Dialog.prototype.drawDialog = function()
	{
		this.frame = DOM.create('div');
		this.frame.className = 'swat-frame';

		this.container = DOM.create('div');
		this.container.style.display = 'none';
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

	Swat.ImageDialog = function(ed, imageServer)
	{
		this.imageServer = imageServer;
		this.uploadImage = 0;
		this.notebookPage = 0;
		Swat.ImageDialog.superclass.constructor.call(this, ed);
	};

	Swat.extend(Swat.ImageDialog, Swat.Dialog, {

	focus: function()
	{
		if (DOM.hasClass(
			this.notebookPages[1],
			'swat-textarea-editor-notebook-page-selected'
		)) {
			this.srcEntry.focus();
		} else {
			this.uploadAltEntry.focus();
		}
	},

	reset: function()
	{
		this.srcEntry.value = '';
		this.uriAltEntry.value = '';
		this.uploadAltEntry.value = '';
		this.uploadCaptionEntry.value = '';

		if (typeof this.imageData != 'undefined' && this.imageData.length) {
			this.selectUploadImage(0);
			this.selectNotebookPage(0);
		}
	},

	getData: function()
	{
		var data = Swat.ImageDialog.superclass.getData.call(this);

		data.image_src = this.srcEntry.value;
		data.image_alt = this.uriAltEntry.value;

		if (this.notebookPage == 0) {
			var imageData = this.imageData[this.uploadImage];
			var radios = this.uploadImageDimensionRadios[this.uploadImage];
			var shortname;

			// get selected dimension
			for (shortname in radios) {
				if (radios[shortname].checked) {
					break;
				}
			}

			var imageDimension = imageData.images[shortname];
			data.image_upload = {
				'src':       imageDimension.uri,
				'width':     imageDimension.width,
				'height':    imageDimension.height,
				'alt':       this.uploadAltEntry.value,
				'caption':   this.uploadCaptionEntry.value
			};
		} else {
			data.image_upload = null;
		}

		return data;
	},

	setData: function(data)
	{
		Swat.ImageDialog.superclass.setData.call(this, data);

		var found = false;

		// search for image in image data
		if (this.imageData) {
			var i, shortname, imageDimension;
			for (i = 0; i < this.imageData.length; i++) {
				for (shortname in this.imageData[i].images) {
					imageDimension = this.imageData[i].images[shortname];
					if (data.image_src == imageDimension.uri) {
						this.selectNotebookPage(0);
						this.selectUploadImage(i);
						this.uploadImageDimensionRadios[i][shortname].checked =
							true;

						if (data.image_alt) {
							this.uploadAltEntry.value = data.image_alt;
						}

						found = true;
						break;
					}
				}
				if (found) {
					break;
				}
			}
		}

		if (!found && (data.image_src || data.image_alt)) {
			this.selectNotebookPage(1);
			if (data.image_src) {
				this.srcEntry.value = data.image_src;
			}
			if (data.image_alt) {
				this.uriAltEntry.value = data.image_alt;
			}
		}

		// set confirm button title
		if (found || this.srcEntry.value.length) {
			this.insertButton.value = this.editor.getLang('swat.image_update');
			this.hideCaption();
		} else {
			this.insertButton.value = this.editor.getLang('swat.image_insert');
			this.showCaption();
		}
	},

	open: function(data)
	{
		Swat.ImageDialog.superclass.open.call(this, data);
	},

	drawDialog: function()
	{
		Swat.ImageDialog.superclass.drawDialog.call(this);

		// Notebook

		this.notebookTabs  = [];
		this.notebookPages = [];

		var notebookTabs = DOM.create('div', {
			'id':    this.editor.id + '_image_notebook_tabs',
			'class': 'swat-textarea-editor-notebook-tabs'
		});

		var notebookPages = DOM.create('div', {
			'id':    this.editor.id + '_image_notebook_pages',
			'class': 'swat-textarea-editor-notebook-pages'
		});

		// From Upload

		this.notebookTabs[0] = DOM.create('a', {
			'href':  '#',
			'class': 'swat-textarea-editor-notebook-tab swat-textarea-editor-notebook-tab-selected'
		});
		this.notebookTabs[0].appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.image_uploads')
			)
		);
		Event.add(this.notebookTabs[0], 'click', function(e)
		{
			Event.prevent(e);
			this.selectNotebookPage(0);
		}, this);

		this.notebookPages[0] = DOM.create('div', {
			'class': 'swat-textarea-editor-notebook-page swat-textarea-editor-notebook-page-selected'
		});

		this.uploadImageList = DOM.create('div', {
			'class': 'swat-textarea-editor-upload-left'
		});

		this.uploadImageDetails = DOM.create('div', {
			'class': 'swat-textarea-editor-upload-right'
		});

		var uploadAltEntryId = this.editor.id + '_image_upload_alt_entry';
		this.uploadAltEntry = DOM.create('input', {
			'id':    uploadAltEntryId,
			'type':  'text',
			'class': 'swat-entry'
		});
		Event.add(this.uploadAltEntry, 'focus', function(e)
		{
			this.select();
		}, this.uploadAltEntry);

		var uploadAltEntryLabelSpan = DOM.create('span');
		uploadAltEntryLabelSpan.className = 'swat-note';
		uploadAltEntryLabelSpan.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.image_optional')
			)
		);

		var uploadAltEntryLabel = DOM.create('label');
		uploadAltEntryLabel.htmlFor = uploadAltEntryId;
		uploadAltEntryLabel.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.image_alt_field')
			)
		);
		uploadAltEntryLabel.appendChild(DOM.doc.createTextNode(' '));
		uploadAltEntryLabel.appendChild(uploadAltEntryLabelSpan);

		var uploadAltEntryFormFieldContents = DOM.create('div');
		uploadAltEntryFormFieldContents.className = 'swat-form-field-contents';
		uploadAltEntryFormFieldContents.appendChild(this.uploadAltEntry);

		var uploadAltEntryFormField = DOM.create('div');
		uploadAltEntryFormField.className = 'swat-form-field';
		uploadAltEntryFormField.appendChild(uploadAltEntryLabel);
		uploadAltEntryFormField.appendChild(uploadAltEntryFormFieldContents);

		var uploadCaptionEntryId = this.editor.id + '_image_upload_alt_entry';
		this.uploadCaptionEntry = DOM.create('input', {
			'id':    uploadCaptionEntryId,
			'type':  'text',
			'class': 'swat-entry'
		});
		Event.add(this.uploadCaptionEntry, 'focus', function(e)
		{
			this.select();
		}, this.uploadCaptionEntry);

		var uploadCaptionEntryLabelSpan = DOM.create('span');
		uploadCaptionEntryLabelSpan.className = 'swat-note';
		uploadCaptionEntryLabelSpan.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.image_optional')
			)
		);

		var uploadCaptionEntryLabel = DOM.create('label');
		uploadCaptionEntryLabel.htmlFor = uploadCaptionEntryId;
		uploadCaptionEntryLabel.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.image_caption_field')
			)
		);
		uploadCaptionEntryLabel.appendChild(DOM.doc.createTextNode(' '));
		uploadCaptionEntryLabel.appendChild(uploadCaptionEntryLabelSpan);

		var uploadCaptionEntryFormFieldContents = DOM.create('div', {
			'class': 'swat-form-field-contents'
		});
		uploadCaptionEntryFormFieldContents.appendChild(this.uploadCaptionEntry);

		this.uploadCaptionEntryFormField = DOM.create('div', {
			'class': 'swat-form-field'
		});
		this.uploadCaptionEntryFormField.appendChild(uploadCaptionEntryLabel);
		this.uploadCaptionEntryFormField.appendChild(
			uploadCaptionEntryFormFieldContents
		);

		this.uploadImageDetails.appendChild(uploadAltEntryFormField);
		this.uploadImageDetails.appendChild(this.uploadCaptionEntryFormField);

		this.notebookPages[0].appendChild(this.uploadImageList);
		this.notebookPages[0].appendChild(this.uploadImageDetails);
		notebookTabs.appendChild(this.notebookTabs[0]);
		notebookPages.appendChild(this.notebookPages[0]);

		// From URI

		this.notebookTabs[1] = DOM.create('a', {
			'href':  '#',
			'class': 'swat-textarea-editor-notebook-tab'
		});
		this.notebookTabs[1].appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.image_from_uri')
			)
		);
		Event.add(this.notebookTabs[1], 'click', function(e)
		{
			Event.prevent(e);
			this.selectNotebookPage(1);
		}, this);

		this.notebookPages[1] = DOM.create('div', {
			'class': 'swat-textarea-editor-notebook-page'
		});

		var srcEntryId = this.editor.id + '_image_src_entry';
		this.srcEntry = DOM.create('input', { id: srcEntryId, type: 'text' });
		this.srcEntry.className = 'swat-entry';
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

		var uriAltEntryId = this.editor.id + '_image_uri_alt_entry';

		this.uriAltEntry = DOM.create(
			'input',
			{ id: uriAltEntryId, type: 'text' }
		);
		this.uriAltEntry.className = 'swat-entry';
		Event.add(this.uriAltEntry, 'focus', function(e)
		{
			this.select();
		}, this.uriAltEntry);

		var uriAltEntryLabelSpan = DOM.create('span');
		uriAltEntryLabelSpan.className = 'swat-note';
		uriAltEntryLabelSpan.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.image_optional')
			)
		);

		var uriAltEntryLabel = DOM.create('label');
		uriAltEntryLabel.htmlFor = uriAltEntryId;
		uriAltEntryLabel.appendChild(
			DOM.doc.createTextNode(
				this.editor.getLang('swat.image_alt_field')
			)
		);
		uriAltEntryLabel.appendChild(DOM.doc.createTextNode(' '));
		uriAltEntryLabel.appendChild(uriAltEntryLabelSpan);

		var uriAltEntryFormFieldContents = DOM.create('div');
		uriAltEntryFormFieldContents.className = 'swat-form-field-contents';
		uriAltEntryFormFieldContents.appendChild(this.uriAltEntry);

		var uriAltEntryFormField = DOM.create('div');
		uriAltEntryFormField.className = 'swat-form-field';
		uriAltEntryFormField.appendChild(uriAltEntryLabel);
		uriAltEntryFormField.appendChild(uriAltEntryFormFieldContents);

		this.notebookPages[1].appendChild(srcEntryFormField);
		this.notebookPages[1].appendChild(uriAltEntryFormField);
		notebookTabs.appendChild(this.notebookTabs[1]);
		notebookPages.appendChild(this.notebookPages[1]);

		// Footer

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

		notebookTabs.appendChild(DOM.create('div', { style: 'clear: left' }));

		var form = DOM.create('form');
		form.className = 'swat-form';
		form.appendChild(notebookTabs);
		form.appendChild(notebookPages);
		form.appendChild(footerFormField);
		Event.add(form, 'submit', function(e)
		{
			Event.cancel(e);
			this.close(true);
		}, this);

		this.frame.appendChild(form);

		if (this.imageServer) {
			this.loadUploadImages();
		} else {
			this.selectNotebookPage(1, false);
			this.hideNotebookPage(0);
		}
	},

	hideCaption: function()
	{
		this.uploadCaptionEntryFormField.style.display = 'none';
	},

	showCaption: function()
	{
		this.uploadCaptionEntryFormField.style.display = 'block';
	},

	hideNotebookPage: function(page)
	{
		if (this.notebookTabs[page]) {
			this.notebookTabs[page].style.display = 'none';
		}
		if (this.notebookPages[page]) {
			this.notebookPages[page].style.display = 'none';
		}
	},

	showNotebookPage: function(page)
	{
		if (this.notebookTabs[page]) {
			this.notebookTabs[page].style.display = 'auto';
		}
		if (this.notebookPages[page]) {
			this.notebookPages[page].style.display = 'auto';
		}
	},

	selectNotebookPage: function(page, focus)
	{
		if (page != this.noteBookPage) {
			for (var i = 0; i < this.notebookTabs.length; i++) {
				if (i == page) {
					DOM.addClass(
						this.notebookTabs[i],
						'swat-textarea-editor-notebook-tab-selected'
					);
					DOM.addClass(
						this.notebookPages[i],
						'swat-textarea-editor-notebook-page-selected'
					);

					this.notebookPage = page;
				} else {
					DOM.removeClass(
						this.notebookTabs[i],
						'swat-textarea-editor-notebook-tab-selected'
					);
					DOM.removeClass(
						this.notebookPages[i],
						'swat-textarea-editor-notebook-page-selected'
					);
				}
			}

			if (typeof focus === 'undefined' || focus) {
				this.focus();
			}
		}
	},

	selectUploadImage: function(image)
	{
		if (image != this.uploadImage) {
			for (var i = 0; i < this.uploadImages.length; i++) {
				if (i == image) {
					DOM.addClass(
						this.uploadImages[i],
						'swat-textarea-editor-upload-image-selected'
					);
					DOM.addClass(
						this.uploadImageDimensions[i],
						'swat-textarea-editor-upload-image-dimension-list-selected'
					);

					this.uploadImage = image;
				} else {
					DOM.removeClass(
						this.uploadImages[i],
						'swat-textarea-editor-upload-image-selected'
					);
					DOM.removeClass(
						this.uploadImageDimensions[i],
						'swat-textarea-editor-upload-image-dimension-list-selected'
					);
				}
			}
			this.focus();
		}
	},

	loadUploadImages: function()
	{
		var that = this;

		function failCallback(data, req, o)
		{
			that.imageData = null;
			that.selectNotebookPage(1);
			that.hideNotebookPage(0);
		}

		function successCallback(data, req, o)
		{
			that.imageData = JSON.parse(data);

			if (typeof that.imageData == 'undefined') {
				failCallback(data, req, o);
				return;
			}

			that.uploadImages               = [];
			that.uploadImageDimensions      = [];
			that.uploadImageDimensionRadios = [];

			var image, radio, radioName, label, shortname, span;
			var first = true;
			for (var i = 0; i < that.imageData.length; i++) {

				image = that.imageData[i].images;

				if (first) {
					that.uploadImages[i] = DOM.create('a', {
						'href':  '#',
						'class': 'swat-textarea-editor-upload-image swat-textarea-editor-upload-image-selected'
					});
					that.uploadImageDimensions[i] = DOM.create('div', {
						'class': 'swat-textarea-editor-upload-image-dimension-list swat-textarea-editor-upload-image-dimension-list-selected'
					});
					first = false;
				} else {
					that.uploadImages[i] = DOM.create('a', {
						'href':  '#',
						'class': 'swat-textarea-editor-upload-image'
					});
					that.uploadImageDimensions[i] = DOM.create('div', {
						'class': 'swat-textarea-editor-upload-image-dimension-list'
					});
				}

				(function() {
					var index = i;
					Event.add(that.uploadImages[i], 'click', function(e)
					{
						Event.prevent(e);
						that.selectUploadImage(index);
					}, that);
				})();

				that.uploadImages[i].appendChild(
					DOM.create('img', {
						'src':    image.pinky.uri,
						'width':  image.pinky.width,
						'height': image.pinky.height
					})
				);

				that.uploadImageDimensionRadios[i] = {};
				radioName = that.editor.id + '_upload_image_' +
					that.imageData[i].id;

				for (shortname in image) {

					that.uploadImageDimensionRadios[i][shortname] = DOM.create(
						'input',
						{
							'type':  'radio',
							'name':  radioName,
							'value': shortname
						}
					);

					label = DOM.create('label');
					label.appendChild(
						that.uploadImageDimensionRadios[i][shortname]
					);
					label.appendChild(document.createTextNode(
						image[shortname].title + ' '
					));
					span = DOM.create('span');
					span.appendChild(document.createTextNode(
						'(' + image[shortname].width + ' × '
						+ image[shortname].height + ')'
					));
					label.appendChild(span);

					// IE 6/7 cannot set checked until button is added to parent
					if (shortname == 'small') {
						that.uploadImageDimensionRadios[i][shortname].checked =
							true;
					}

					that.uploadImageDimensions[i].appendChild(label);
				}

				that.uploadImageList.appendChild(that.uploadImages[i]);
				that.uploadImageDetails.insertBefore(
					that.uploadImageDimensions[i],
					that.uploadImageDetails.firstChild
				);
			}
		}

		XHR.send({
			url:     this.imageServer,
			type:    'GET',
			error:   failCallback,
			success: successCallback
		});
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
		var modes = ed.getParam('swat_modes_enabled', 'yes');
		if (   modes === true
			|| (typeof modes === 'string' && modes.toLowerCase() != 'no')
		) {
			ed.onPostRender.add(this.drawSourceMode, this);
		}

		this.imageServer = ed.getParam('swat_image_server', null);

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
			'image':   new Swat.ImageDialog(ed, this.imageServer)
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
			var src, alt, width, height, caption;

			if (data.image_upload) {
				src       = data.image_upload.src;
				alt       = data.image_upload.alt;
				width     = data.image_upload.width;
				height    = data.image_upload.height;
				caption   = data.image_upload.caption;
			} else {
				src = data.image_src;
				alt = data.image_alt;
			}

			if (src) {
				this.insertImage(
					src, alt, width, height, caption
				);
			}
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

	insertImage: function(src, alt, width, height, caption)
	{
		var ed  = this.editor;
		var dom = ed.dom;

		// get selected image
		var el = ed.selection.getNode();
		el = dom.getParent(el, 'IMG');

		// if caption, insert captioned image block
		if (caption) {
			caption = caption
				.replace(/&/, '&amp;')
				.replace(/</, '&lt;')
				.replace(/>/, '&gt;')
				.replace(/"/, '&quot;');

			var styleWidth = (width) ? 'style="width: '  + width  + 'px;"' : '';

			alt    = (alt)    ? ' alt="'    + alt    + '"' : '';
			width  = (width)  ? ' width="'  + width  + '"' : '';
			height = (height) ? ' height="' + height + '"' : '';

			var snippet = '<div class="image-captioned"' + styleWidth + '>'
				+ '<div class="image-captioned-image">'
				+ '<img src="' + src + '"' + alt + width + height + ' />'
				+ '</div><div class="image-captioned-caption">'
				+ caption
				+ '</div></div>';

			this.insertSnippet(snippet);
			return;
		}

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
				if (width) {
					dom.setAttrib(el, 'width', width);
				}
				if (height) {
					dom.setAttrib(el, 'height', height);
				}
			}

		} else {
			dom.setAttrib(el, 'src', src);
			if (alt) {
				dom.setAttrib(el, 'alt', alt);
			}
			if (width) {
				dom.setAttrib(el, 'width', width);
			}
			if (height) {
				dom.setAttrib(el, 'height', height);
			}

			// update captioned image width
			if (   dom.hasClass(el.parentNode.parentNode, 'image-captioned')
				&& width
			) {
				dom.setAttrib(
					el.parentNode.parentNode,
					'style',
					'width: ' + width + 'px'
				);
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

	drawSourceMode: function()
	{
		var ed = this.editor;

		// get textarea
		var el = ed.getElement();

		el.className = 'swat-textarea swat-textarea-editor-source-mode';

		this.sourceContainer = document.createElement('div');

		this.sourceContainer.className      = 'swat-textarea-container';
		this.sourceContainer.style.display  = 'none';
		this.sourceContainer.style.zIndex   = '3';
		this.sourceContainer.style.position = 'absolute';

		// add container to document
		el.parentNode.replaceChild(this.sourceContainer, el);
		this.sourceContainer.appendChild(el);

		// reset style properties set by default plugins
		el.style.display = null;
		el.style.width = null;

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
		var el = _getEditorEl(this.editor);
		if (el) {
			var rect = _getRect(el);
			ul.style.width = (rect.w + 2) + 'px';

			// Hack to set width on a 50ms timeout because the table has not yet
			// been resized to fit the toolbar in older versions of WebKit.
			setTimeout(function() {
				var rect = _getRect(el);
				ul.style.width = (rect.w + 2) + 'px';
			}, 500);
		}

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
			this.sourceContainer.style.display = 'none';
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

		// get width and height
		var w = iframe.clientWidth;
		var h = iframe.clientHeight;

		// get position
		var rect = _getRect(ed.getContentAreaContainer());

		// get textarea
		var el = ed.getElement();

		// get padding and box sizing for height offset
		var style = (tinymce.isIE)
			? el.currentStyle
			: getComputedStyle(el, null);

		var boxSizing = style.boxSizing;
		var leftPadding = 0;

		// If using content-box sizing, subtract the vertical padding from
		// the textarea height and set a fixed height. Otherwise use default
		// 100% height.
		if (boxSizing === 'content-box' || !boxSizing) {
			var verticalPadding = parseInt(style.paddingTop, 10) +
				parseInt(style.paddingBottom, 10);

			var leftPadding = parseInt(style.paddingLeft, 10);
			el.style.height  = (h - verticalPadding) + 'px';
		}

		el.value = this.editor.getContent();

		// position and size textarea container
		this.sourceContainer.style.width  = w + 'px';
		this.sourceContainer.style.height = h + 'px';
		this.sourceContainer.style.left   = leftPadding + 'px';

		// Use YUI if available to get toolbar height
		 if (YAHOO && YAHOO.util && YAHOO.util.Dom) {
			var toolbar = YAHOO.util.Dom.getElementsByClassName(
				'mceToolbar',
				'td',
				el.parentNode.parentNode
			).pop();
			if (toolbar) {
				// set container position based on toolbar height
				var region = YAHOO.util.Dom.getRegion(toolbar);
				this.sourceContainer.style.top = region.height + 'px';
			} else {
				// no toolbar found, use default height
				this.sourceContainer.style.top = '29px';
			}
		 } else {
			 // if no YUI is available, just use a default height
			this.sourceContainer.style.top = '29px';
		 }

		// display textarea
		this.sourceContainer.style.display = 'block';

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

})();
