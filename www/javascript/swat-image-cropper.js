function SwatImageCropper(id, config)
{
	this.id = id;
	this.cropper = new YAHOO.widget.ImageCropper(this.id + '_image', config);
	this.cropper.onChangeEvent.subscribe(this.handleChange, this, true);

	this.crop_box_width = document.getElementById(this.id + '_width');
	this.crop_box_height = document.getElementById(this.id + '_height');
	this.crop_box_x = document.getElementById(this.id + '_x');
	this.crop_box_y = document.getElementById(this.id + '_y');
}

SwatImageCropper.prototype.handleChange = function()
{
	var region = this.cropper.getCropRegion();

	this.crop_box_width.value = region.w;
	this.crop_box_height.value = region.h;
	this.crop_box_x.value = region.x;
	this.crop_box_y.value = region.y;
}
