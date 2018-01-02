const ExtractTextPlugin = require('extract-text-webpack-plugin');
const ProvidePlugin = require('webpack').ProvidePlugin;

module.exports = {
	entry: {
		swat: './vendor/silverorange/swat/www/javascript/index.js',
	},
	output: {
		filename: 'www/[name].js'
	},
	module: {
		rules: [
			{
				test: /\.css$/,
				exclude: /(node_modules)/,
				use: ExtractTextPlugin.extract({
					fallback: 'style-loader',
					use: [ 'css-loader' ]
				})
			},
			{
				test: /\.js$/,
				exclude: /(node_modules)/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: ['env']
					}
				}
			},
			{
				test: /\.(gif|png|jpe?g|svg)$/i,
				exclude: /(node_modules)/,
				use: {
					loader: 'file-loader',
					options: {
						useRelativePath: true,
						publicPath: 'images/',
						outputPath: 'www/images/'
					}
				}
			},
			{
				test: /yahoo\/yahoo.js$/,
				use: {
					loader: 'exports-loader?YAHOO',
				}
			},
			{
				test: /dom\/dom.js$/,
				use: {
					loader: 'exports-loader',
					options: {
						Dom: 'YAHOO.util.Dom',
						Region: 'YAHOO.util.Region',
						Point: 'YAHOO.util.Point',
					},
				},
			},
			{
				test: /event\/event.js$/,
				use: {
					loader: 'exports-loader',
					options: {
						Event: 'YAHOO.util.Event',
						EventProvider: 'YAHOO.util.EventProvider',
						CustomEvent: 'YAHOO.util.CustomEvent',
						Subscriber: 'YAHOO.util.Subscriber',
					},
				},
			},
			{
				test: /animation\/animation.js$/,
				use: {
					loader: 'exports-loader',
					options: {
						Anim: 'YAHOO.util.Anim',
						AnimMgr: 'YAHOO.util.AnimMgr',
						Easing: 'YAHOO.util.Easing',
					},
				},
			},
			{
				test: /container\/container_core.js$/,
				use: {
					loader: 'exports-loader',
					options: {
						Config: 'YAHOO.util.Config',
						Module: 'YAHOO.widget.Module',
						Overlay: 'YAHOO.widget.Overlay',
						OverlayManager: 'YAHOO.widget.OverlayManager',
						ContainerEffect: 'YAHOO.widget.ContainerEffect',
					},
				},
			},
			{
				test: /element\/element.js$/,
				use: {
					loader: 'exports-loader',
					options: {
						Attribute: 'YAHOO.util.Attribute',
						AttributeProvider: 'YAHOO.util.AttributeProvider',
						Element: 'YAHOO.util.Element',
					},
				},
			},
			{
				test: /imagecropper\/imagecropper.js$/,
				use: {
					loader: 'exports-loader',
					options: {
						ImageCropper: 'YAHOO.widget.ImageCropper',
					},
				},
			},
			{
				test: /selector\/selector.js$/,
				use: {
					loader: 'exports-loader',
					options: {
						Selector: 'YAHOO.util.Selector',
					},
				},
			},
			{
				test: /tabview\/tabview.js$/,
				use: {
					loader: 'exports-loader',
					options: {
						TabView: 'YAHOO.widget.TabView',
					},
				},
			},
		],
	},
	resolve: {
		symlinks: false
	},
	plugins: [
		new ExtractTextPlugin('www/swat.css'),
		new ProvidePlugin({
			YAHOO: '../../../yui/www/yahoo/yahoo',
			'YAHOO.util.Dom': ['../../../yui/www/dom/dom', 'Dom'],
			'YAHOO.util.Region': ['../../../yui/www/dom/dom', 'Region'],
			'YAHOO.util.Point': ['../../../yui/www/dom/dom', 'Point'],
			'YAHOO.util.Event': ['../../../yui/www/event/event', 'Event'],
			'YAHOO.util.EventProvider': ['../../../yui/www/event/event', 'EventProvider'],
			'YAHOO.util.CustomEvent': ['../../../yui/www/event/event', 'CustomEvent'],
			'YAHOO.util.Subscriber': ['../../../yui/www/event/event', 'Subscriber'],
			'YAHOO.util.Anim': ['../../../yui/www/animation/animation', 'Anim'],
			'YAHOO.util.AnimMgr': ['../../../yui/www/animation/animation', 'AnimMgr'],
			'YAHOO.util.Easing': ['../../../yui/www/animation/animation', 'Easing'],
			'YAHOO.util.Selector': ['../../../yui/www/selector/selector', 'Selector'],
			'YAHOO.util.Attribute': ['../../../yui/www/element/element', 'Attribute'],
			'YAHOO.util.AttributeProvider': ['../../../yui/www/element/element', 'AttributeProvider'],
			'YAHOO.util.Element': ['../../../yui/www/element/element', 'Element'],
			'YAHOO.util.Config': ['../../../yui/www/container/container_core', 'Config'],
			'YAHOO.widget.Module': ['../../../yui/www/container/container_core', 'Module'],
			'YAHOO.widget.Overlay': ['../../../yui/www/container/container_core', 'Overlay'],
			'YAHOO.widget.OverlayManager': ['../../../yui/www/container/container_core', 'OverlayManager'],
			'YAHOO.widget.ContainerEffect': ['../../../yui/www/container/container_core', 'ContainerEffect'],
			'YAHOO.widget.ImageCropper': ['../../../yui/www/imagecropper/imagecropper', 'ImageCropper'],
			'YAHOO.widget.TabView': ['../../../yui/www/tabview/tabview', 'TabView'],
		}),
	],
};
