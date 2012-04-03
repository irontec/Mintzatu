<?php


class days {

	public $mes=array(
						/*"LANG"=>array('N_MES'=>array('ABREV','min','mayus', ... ),*/
						"es"=>array('1'=>array('ene','enero','Enero'),
										'2'=>array('feb','febrero','Febrero'),
										'3'=>array('mar','marzo','Marzo'),
										'4'=>array('abr','abril','Abril'),
										'5'=>array('may','mayo','Mayo'),
										'6'=>array('jun','junio','Junio'),
										'7'=>array('jul','julio','Julio'),
										'8'=>array('ago','agosto','Agosto'),
										'9'=>array('sep','septiembre','Septiembre'),
										'10'=>array('oct','octubre','Octubre'),
										'11'=>array('nov','noviembre','Noviembre'),
										'12'=>array('dic','diciembre','Diciembre'),
								),
						"eu"=>array('1'=>array('urt','urtarrila','Urtarrila','ren'),
										'2'=>array('ots','otsaila','Otsaila'),
										'3'=>array('mar','martxoa','Martxoa'),
										'4'=>array('apr','aprila','Apirila'),
										'5'=>array('mai','maiatza','Maiatza'),
										'6'=>array('eka','ekaina','Ekaina'),
										'7'=>array('uzt','uztaila','Uztaila'),
										'8'=>array('abu','abuztua','Abuztua'),
										'9'=>array('ira','iraila','Iraila'),
										'10'=>array('urr','urria','Urria'),
										'11'=>array('aza','azaroa','Azaroa'),
										'12'=>array('abe','abendua','Abendua'),
								),
						"en"=>array('1'=>array('Jan','january','January'),
										'2'=>array('Feb','february','February'),
										'3'=>array('Mar','march','March'),
										'4'=>array('Apr','april','April'),
										'5'=>array('May','may','May'),
										'6'=>array('Jun','june','June'),
										'7'=>array('Jul','july','July'),
										'8'=>array('Aug','august','August'),
										'9'=>array('Sep','septembre','Septembre'),
										'10'=>array('Oct','october','October'),
										'11'=>array('Nov','november','November'),
										'12'=>array('Dec','december','December'),
								),
						"de"=>array('1'=>array('Jan','Januar','Januar'),
										'2'=>array('Feb','Februar','Februar'),
										'3'=>array('Mar','MÃ¤rz','MÃ¤rz'),
										'4'=>array('Apr','April','April'),
										'5'=>array('May','Mai','Mai'),
										'6'=>array('Jun','Juni','Juni'),
										'7'=>array('Jul','Juli','Juli'),
										'8'=>array('Aug','August','August'),
										'9'=>array('Sep','September','September'),
										'10'=>array('Oct','Oktober','Oktober'),
										'11'=>array('Nov','November','November'),
										'12'=>array('Dec','Dezember','Dezember'),
								),

								"ch"=>array('1'=>array('Jan','january','January'),
										'2'=>array('Feb','february','February'),
										'3'=>array('Mar','march','March'),
										'4'=>array('Apr','april','April'),
										'5'=>array('May','may','May'),
										'6'=>array('Jun','june','June'),
										'7'=>array('Jul','july','July'),
										'8'=>array('Aug','august','August'),
										'9'=>array('Sep','septembre','Septembre'),
										'10'=>array('Oct','october','October'),
										'11'=>array('Nov','november','November'),
										'12'=>array('Dec','december','December'),
								),

						"fr"=>array('1'=>array('jan','janvier','Janvier'),
										'2'=>array('fév','février','Février'),
										'3'=>array('mar','mars','Mars'),
										'4'=>array('avr','avril','Avril'),
										'5'=>array('mai','mai','Mai'),
										'6'=>array('jui','juin','Juin'),
										'7'=>array('juil','juillet','Juillet'),
										'8'=>array('aou','aout','Aout'),
										'9'=>array('sep','septembre','Septembre'),
										'10'=>array('oct','octobre','Octobre'),
										'11'=>array('nov','novembre','Novembre'),
										'12'=>array('déc','décembre','Décembre'),
								),
						);

		public $dia=array(
						/*"LANG"=>array('N_MES'=>array('ABREV','min','mayus', ... ),*/
						"es"=>array('1'=>array('lun','lunes','lunes'),
										'2'=>array('mar','martes','martes'),
										'3'=>array('mie','miércoles','miércoles'),
										'4'=>array('jue','jueves','jueves'),
										'5'=>array('vie','viernes','viernes'),
										'6'=>array('sab','sábado','sábado'),
										'7'=>array('dom','domingo','domingo')
								),
								"eu"=>array('1'=>array('lun','astelehena','lunes'),
										'2'=>array('mar','astearte','martes'),
										'3'=>array('mie','asteazkena','miércoles'),
										'4'=>array('jue','osteguna','jueves'),
										'5'=>array('vie','ostirala','viernes'),
										'6'=>array('sab','larunbata','sábado'),
										'7'=>array('dom','igandea','domingo')
								)
								);

	public function mes($l,$k,$f){ return $this->mes[$l][$k][$f]; }
	public function dia($l,$k,$f){ return $this->dia[$l][$k][$f]; }
}

?>
