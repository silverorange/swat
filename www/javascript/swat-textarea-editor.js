// Import TinyMCE
import tinymce from 'tinymce/tinymce';

// A theme is also required
import 'tinymce/themes/modern/theme';

// Any plugins you want to use has to be imported
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/code';
import 'tinymce/plugins/image';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/paste';

/*
require.context(
  'file?name=[path][name].[ext]&context=tinymce!tinymce/skins',
  true,
  /.* /
);*/

tinymce.init({
	selector: '.swat-textarea-editor',

	plugins: [
		'autolink',
		'code',
		'paste',
		'image',
		'link',
		'lists',
	],

	// appearance
	menubar: false,
	branding: false,
	elementpath: false,
	statusbar: false,
	toolbar: ' bold italic | formatselect | removeformat | outdent indent | bullist numlist | link image | code ',
	block_formats: 'Paragraph=p;Blockquote=blockquote;Preformatted=pre;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;',

	// Make removeformat button also clear inline alignments, styles, colors
	// and classes.
	formats: {
		removeformat: [
			{
				selector: 'b,strong,em,i,font,u,strike',
				remove: 'all',
				split: true,
				expand: false,
				block_expand: true,
				deep: true,
			},
			{
				selector: 'span',
				attributes: [
					'style',
					'class',
					'align',
					'color',
					'background',
				],
				remove: 'empty',
				split: true,
				expand: false,
				deep: true,
			},
			{
				selector: '*',
				attributes: [
					'style',
					'class',
					'align',
					'color',
					'background',
				],
				split: false,
				expand: false,
				deep: true,
			},
		],
	},

	// link plugin
	link_context_toolbar: true,
	link_title: false,
	target_list: false,

	// image plugin
	image_caption: false,
	image_description: false,
	image_dimensions: false,
	image_title: false,

	width: 640,
});
