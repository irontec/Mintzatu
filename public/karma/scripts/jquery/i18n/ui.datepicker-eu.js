/* Inicialización en español para la extensión 'UI date picker' para jQuery. */
/* Traducido por Vester (xvester@gmail.com). */
jQuery(function($){
	$.datepicker.regional['eu'] = {clearText: 'Garbitu', clearStatus: '',
		closeText: 'Itxi', closeStatus: '',
		prevText: '&lt;Aur', prevStatus: '',
		nextText: 'Hur&gt;', nextStatus: '',
		currentText: 'Gaur', currentStatus: '',
		monthNames: ['Urtarrila','Otsaila','Martxoa','Apirila','Maiatza','Ekaina',
		'Uztaila','Abuztua','Iraila','Urria','Azaroa','Abendua'],
		monthNamesShort: ['Urt','Ots','Mar','Api','Mai','Eka',
		'Uzt','Abu','Ira','Urr','Aza','Abe'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'As', weekStatus: '',
		dayNames: ['Igandea','Astelehena','Asteartea','Asteazkena','Osteguna','Ostirala','Larunbata'],
		dayNamesShort: ['Iga','Astl','Astr','Astz','Ostg','Ostr','Lar'],
		dayNamesMin: ['Ig','Al','Ar','Az','Og','Or','Lr'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'yy/mm/dd', firstDay: 1, 
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['eu']);
});
