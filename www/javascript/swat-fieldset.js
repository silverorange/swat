$(function() {
	$('.swat-fieldset').each(function() {
		var $fieldset = $(this);
		if (   $fieldset.prop('currentStyle')
			&& typeof $fieldset.prop('currentStyle').hasLayout !== 'undefined'
		) {
			// This fix is needed for IE6/7 and fixes display of relative
			// positioned elements below this fieldset during and after
			// animations.
			var $peekaBoo= $('<div></div>');
			$peekaBoo.css({
				height: 0,
				margin: 0,
				padding: 0,
				border: 'none'
			}).append($('<div></div>'));

			$fieldset.after($peekaBoo);
		}
	});
});
