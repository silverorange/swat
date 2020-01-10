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
	const patch = `

	YAHOO.util.Element.prototype.addListener = function(type, fn, obj, scope) {
		scope = scope || this;

		var el = this.get('element') || this.get('id');
		var self = this;
		var specialTypes = {
			mouseenter: true,
			mouseleave: true
		};

		if (specialTypes[type] && !YAHOO.util.Event._createMouseDelegate) {
			return false;
		}

		if (!this._events[type]) { // create on the fly
			if (el && this.DOM_EVENTS[type]) {
				YAHOO.util.Event.on(el, type, function(e, matchedEl) {
					// Remove IE hacks that break in modern browsers running in
					// strict mode.

					//	Note: matchedEl el is passed back for delegated listeners
					self.fireEvent(type, e, matchedEl);

				}, obj, scope);
			}
			this.createEvent(type, {scope: this});
		}

		// notify via customEvent
		return YAHOO.util.EventProvider.prototype.subscribe.apply(this, arguments);
	};
	`;

	if (sourceMap) {
		const currentRequest = loaderUtils.getCurrentRequest(this);
		const node = SourceNode.fromStringWithSourceMap(
			content,
			new SourceMapConsumer(sourceMap)
		);
		node.add(patch);
		const result = node.toStringWithSourceMap({
			file: currentRequest
		});
		this.callback(null, result.code, result.map.toJSON());
		return;
	}

	return content + patch;
};

module.exports = ElementLoader;
