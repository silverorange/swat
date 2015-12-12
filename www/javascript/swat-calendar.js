$(function() {
	$('input.swat-calendar').each(function() {
		var $calendar = $(this);

		var min_date = $calendar.data('min-date');
		var max_date = $calendar.data('max-date');
		var format = $calendar.data('format');

		min_date = (min_date === undefined) ? null : min_date;
		max_date = (max_date === undefined) ? null : max_date;

		$calendar.datepicker({
			showOn: 'button',
			buttonText: 'Open',
			showButtonPanel: true,
			showOtherMonths: true,
			hideOnSelect: false,
			selectOnToday: true,
			minDate: min_date,
			maxDate: max_date,
			dateFormat: format
		});
	});
});
