class SwatZIndexNode {
	constructor(element) {
		if (element) {
			this.element = element;
			this.element._swat_z_index_node = this;
		} else {
			this.element = null;
		}

		this.parent = null;
		this.nodes = [];
	}

	add(node) {
		for (var i = 0; i < this.nodes.length; i++) {
			if (this.nodes[i] === node || this.nodes[i].id === node) {
				return;
			}
		}

		this.nodes.push(node);
		node.parent = this;
	}

	remove(node) {
		var found = false;

		for (var i = 0; i < this.nodes.length; i++) {
			if (this.nodes[i] === node) {
				found = this.nodes[i];
				found.parent = null;
				this.nodes.splice(i, 1);
				break;
			}
		}

		return found;
	}
}

module.exports = SwatZIndexNode;
