const ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
	entry: './vendor/silverorange/swat/www/javascript/index.js',
	output: {
		filename: 'www/bundle.js'
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
			}
		]
	},
	resolve: {
		symlinks: false
	},
	plugins: [
		new ExtractTextPlugin('www/bundle.css')
	]
};
