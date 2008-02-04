function SwatTextareaEditor(id, width, height)
{
	this.id = id;
	this.height = height;
	this.width = width;

	YAHOO.util.Event.onDOMReady(this.init, this, true);
}

SwatTextareaEditor.prototype.init = function()
{
	var config = {
		height: this.height,
		width: this.width,
		dompath: true,
		focusAtStart: true,
		titlebar: 'Text'
	};

	this.editor = new YAHOO.widget.Editor(this.id, config);

	this.editor._defaultToolbar.buttonType = 'basic';
	this.editor._defaultToolbar.titlebar = '';
	this.editor._defaultToolbar.collapse = false;
	this.editor._defaultToolbar.buttons = [
		{ group: 'textstyle', label: 'Font Style',
			buttons: [
				{ type: 'push', label: 'Bold CTRL + SHIFT + B', value: 'bold' },
				{ type: 'push', label: 'Italic CTRL + SHIFT + I', value: 'italic' },
				{ type: 'push', label: 'Underline CTRL + SHIFT + U', value: 'underline' },
				{ type: 'separator' },
				{ type: 'push', label: 'Subscript', value: 'subscript', disabled: true },
				{ type: 'push', label: 'Superscript', value: 'superscript', disabled: true },
				{ type: 'separator' },
				{ type: 'push', label: 'Remove Formatting', value: 'removeformat', disabled: true },
				{ type: 'push', label: 'Show/Hide Hidden Elements', value: 'hiddenelements' }
			]
		},
		{ type: 'separator' },
		{ group: 'alignment', label: 'Alignment',
			buttons: [
				{ type: 'push', label: 'Align Left CTRL + SHIFT + [', value: 'justifyleft' },
				{ type: 'push', label: 'Align Center CTRL + SHIFT + |', value: 'justifycenter' },
				{ type: 'push', label: 'Align Right CTRL + SHIFT + ]', value: 'justifyright' },
				{ type: 'push', label: 'Justify', value: 'justifyfull' }
			]
		},
		{ type: 'separator' },
		{ group: 'parastyle', label: 'Paragraph Style',
			buttons: [
			{ type: 'select', label: 'Normal', value: 'heading', disabled: true,
				menu: [
					{ text: 'Normal', value: 'none', checked: true },
					{ text: 'Header 1', value: 'h1' },
					{ text: 'Header 2', value: 'h2' },
					{ text: 'Header 3', value: 'h3' },
					{ text: 'Header 4', value: 'h4' },
					{ text: 'Header 5', value: 'h5' },
					{ text: 'Header 6', value: 'h6' }
				]
			}
			]
		},
		{ type: 'separator' },
		{ group: 'indentlist', label: 'Indenting and Lists',
			buttons: [
				{ type: 'push', label: 'Indent', value: 'indent', disabled: true },
				{ type: 'push', label: 'Outdent', value: 'outdent', disabled: true },
				{ type: 'push', label: 'Create an Unordered List', value: 'insertunorderedlist' },
				{ type: 'push', label: 'Create an Ordered List', value: 'insertorderedlist' }
			]
		},
		{ type: 'separator' },
		{ group: 'insertitem', label: 'Insert Item',
			buttons: [
				{ type: 'push', label: 'HTML Link CTRL + SHIFT + L', value: 'createlink', disabled: true },
				{ type: 'push', label: 'Insert Image', value: 'insertimage' }
			]
		}
	];

	this.editor.render();
}
