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
					},
				},
			},
			{
				test: /event\/event.js$/,
				use: {
					loader: 'exports-loader',
					options: {
						Event: 'YAHOO.util.Event',
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
			'YAHOO.util.Event': ['../../../yui/www/event/event', 'Event'],
			'YAHOO.util.CustomEvent': ['../../../yui/www/event/event', 'CustomEvent'],
			'YAHOO.util.Subscriber': ['../../../yui/www/event/event', 'Subscriber'],
			'YAHOO.util.Anim': ['../../../yui/www/animation/animation', 'Anim'],
			'YAHOO.util.AnimMgr': ['../../../yui/www/animation/animation', 'AnimMgr'],
			'YAHOO.util.Easing': ['../../../yui/www/animation/animation', 'Easing'],
			'YAHOO.util.Config': ['../../../yui/www/container/container_core', 'Config'],
			'YAHOO.widget.Module': ['../../../yui/www/container/container_core', 'Module'],
			'YAHOO.widget.Overlay': ['../../../yui/www/container/container_core', 'Overlay'],
			'YAHOO.widget.OverlayManager': ['../../../yui/www/container/container_core', 'OverlayManager'],
			'YAHOO.widget.ContainerEffect': ['../../../yui/www/container/container_core', 'ContainerEffect'],
		}),
	],
};
