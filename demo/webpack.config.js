module.exports = {
	entry: './vendor/silverorange/swat/www/javascript/index.js',
	output: {
		filename: 'www/bundle.js'
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /(node_modules)/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: ['env']
					}
				}
			}
		]
	},
	resolve: {
		symlinks: false
	}
};
