const loaderUtils = require('loader-utils');
const SourceNode = require('source-map').SourceNode;
const SourceMapConsumer = require('source-map').SourceMapConsumer;

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
const ElementLoader = function(content, sourceMap) {
	if (this.cacheable) {
		this.cacheable();
	}

	if (sourceMap) {
		const currentRequest = loaderUtils.getCurrentRequest(this);
		const node = SourceNode.fromStringWithSourceMap(
			content.replace(/Y\.Element/g, 'eval(\'Y.Element\')'),
			new SourceMapConsumer(sourceMap)
		);
		const result = node.toStringWithSourceMap({
			file: currentRequest
		});
		this.callback(null, result.code, result.map.toJSON());
		return;
	}

	return content.replace(/Y\.Element/g, 'eval(\'Y.Element\')');
};

module.exports = ElementLoader;
