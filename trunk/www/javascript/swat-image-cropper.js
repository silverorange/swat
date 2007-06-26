
/**
 * A widget for cropping images
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */

/**
 * Creates a new cropper object
 *
 * @param string id the unique identifier of this textarea object.
 */
function SwatImageCropper(id)
{
	this.id = id;

	YAHOO.util.Event.onContentReady(
		this.id, this.handleOnAvailable, this, true);
}

// }}}
// {{{ handleOnAvailable()

/**
 * Sets up the cropping widget when the cropper is available and loaded in the
 * DOM tree
 */
SwatImageCropper.prototype.handleOnAvailable = function()
{
	var box = $('#' + this.id + ' .swat-image-cropper-box');
	box.css('width', this.crop_box_width);
	box.css('height', this.crop_box_height);
	box.css('left', this.crop_box_left);
	box.css('top', this.crop_box_top);
	box.css('background', 'url(' + this.image_url + ')');
	box.css('background-position', '-' + this.crop_box_left + 'px -' + this.crop_box_top + 'px');

	var background = $('#' + this.id + ' .swat-image-cropper-background');
	background.css('width', this.image_width);
	background.css('height', this.image_height);
	background.css('background', 'url(' + this.image_url + ')');

	$('#' + this.id).css('width', this.image_width);
	$('#' + this.id).css('height', this.image_height);

	if (this.crop_box_ratio != null) {
		$('#' + this.id + 'e').hide();
		$('#' + this.id + 'w').hide();
		$('#' + this.id + 'n').hide();
		$('#' + this.id + 's').hide();
	}

	$('#' + this.id + ' .swat-image-cropper-box').Resizable(
	{
		minTop: 0,
		minLeft: 0,
		maxRight: this.image_width,
		maxBottom: this.image_height,
		ratio: this.crop_box_ratio,
		dragHandle: true,
		handlers: {
			se: '#' + this.id + 'se',
			e: '#' + this.id + 'e',
			ne: '#' + this.id + 'ne',
			n: '#' + this.id + 'n',
			nw: '#' + this.id + 'nw',
			w: '#' + this.id + 'w',
			sw: '#' + this.id + 'sw',
			s: '#' + this.id + 's'
		},
		onDrag: function(x, y)
		{
			this.style.backgroundPosition = '-' + (x) + 'px -' + (y) + 'px';
			$('#' + this.id + '_x').val(x);
			$('#' + this.id + '_y').val(y);
		},
		onResize : function(size, position) {
			this.style.backgroundPosition = '-' + (position.left) + 'px -' + (position.top) + 'px';
		},
		onStop : function() {
			var box = $('#' + this.id + ' .swat-image-cropper-box');
			$('input[@name=' + this.id + '_width]').val(box.width());
			$('input[@name=' + this.id + '_height]').val(box.height());
		},
		onDragStop : function() {
			var box = $('#' + this.id + ' .swat-image-cropper-box');
			$('input[@name=' + this.id + '_x]').val(parseInt(box.css('left')));
			$('input[@name=' + this.id + '_y]').val(parseInt(box.css('top')));
		}
	})

}

// }}}
// {{{ static properties

/**
 * Image URL 
 *
 * The URL can be either absolute or relative 
 *
 * @var string
 */
SwatImageCropper.image_url = null;

/**
 * Image width
 *
 * @var integer
 */
SwatImageCropper.image_width;

/**
 * Image height
 *
 * @var integer
 */
SwatImageCropper.image_height;

/**
 * Crop-box width
 *
 * @var integer
 */
SwatImageCropper.crop_box_width;

/**
 * Crop-box height
 *
 * @var integer
 */
SwatImageCropper.crop_box_height;

/**
 * Crop-box top
 *
 * @var integer
 */
SwatImageCropper.crop_box_top;

/**
 * Crop-box left
 *
 * @var integer
 */
SwatImageCropper.crop_box_left;

/**
 * Aspect ratio of the crop-box
 *
 * @var float
 */
SwatImageCropper.crop_ratio;

// }}}
