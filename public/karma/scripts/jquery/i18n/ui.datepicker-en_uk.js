/* Inicialización en español para la extensión 'UI date picker' para jQuery. */
/* Traducido por Vester (xvester@gmail.com). */
jQuery(function($){

	$.datepicker.regional['en'] = {

	
			clearText: 'Clear', // Display text for clear link
			clearStatus: 'Erase the current date', // Status text for clear link
			closeText: 'Close', // Display text for close link
			closeStatus: 'Close without change', // Status text for close link
			prevText: '&#x3c;Prev', // Display text for previous month link
			prevStatus: 'Show the previous month', // Status text for previous month link
			prevBigText: '&#x3c;&#x3c;', // Display text for previous year link
			prevBigStatus: 'Show the previous year', // Status text for previous year link
			nextText: 'Next&#x3e;', // Display text for next month link
			nextStatus: 'Show the next month', // Status text for next month link
			nextBigText: '&#x3e;&#x3e;', // Display text for next year link
			nextBigStatus: 'Show the next year', // Status text for next year link
			currentText: 'Today', // Display text for current month link
			currentStatus: 'Show the current month', // Status text for current month link
			monthNames: ['January','February','March','April','May','June',
				'July','August','September','October','November','December'], // Names of months for drop-down and formatting
			monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], // For formatting
			monthStatus: 'Show a different month', // Status text for selecting a month
			yearStatus: 'Show a different year', // Status text for selecting a year
			weekHeader: 'Wk', // Header for the week of the year column
			weekStatus: 'Week of the year', // Status text for the week of the year column
			dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'], // For formatting
			dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'], // For formatting
			dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'], // Column headings for days starting at Sunday
			dayStatus: 'Set DD as first week day', // Status text for the day of the week selection
			dateStatus: 'Select DD, M d', // Status text for the date selection
			dateFormat: 'mm/dd/yy', // See format options on parseDate
			firstDay: 0, // The first day of the week, Sun = 0, Mon = 1, ...
			initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['en']);
});
	
	