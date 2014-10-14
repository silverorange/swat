function SwatImageCropper(id, config)
{
	this.id = id;

	this.cropper = new YAHOO.widget.ImageCropper(this.id + '_image', config);
	this.cropper.on('moveEvent', this.handleChange, this, true);

	this.crop_box_width = document.getElementById(this.id + '_width');
	this.crop_box_height = document.getElementById(this.id + '_height');
	this.crop_box_x = document.getElementById(this.id + '_x');
	this.crop_box_y = document.getElementById(this.id + '_y');
}

SwatImageCropper.prototype.handleChange = function()
{
	var coords = this.cropper.getCropCoords();
	this.crop_box_width.value  = coords.width;
	this.crop_box_height.value = coords.height;
	this.crop_box_x.value      = coords.left;
	this.crop_box_y.value      = coords.top;
};
