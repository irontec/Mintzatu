;<?php exit(); ?>
; =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
; =- 	Módulos para nivel 0 (Zona Pública)
; =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=


[0]
;Configuraciones de la página:
page_title = "visita costa del sol noticias agenda"
meta_titulo = "visita costa del sol noticias agenda"
meta_language = "es"

meta_autor = "Irontec"
meta_copy = "Irontec"

meta_pag_inicio = "inicio.html"

title = "visita costa del sol noticias agenda";
cabecera="cabecera.php";
pie="pie.php";
;css="estilo.css";
css="estilo2.css,ui.datepicker.css";
js = "jquery/jquery.js,ui.datepicker.js"

[0::index]
f="inicio.php"
js = "main.js"

[0::inicio]
f="inicio.php"
js = "main.js"

[0::agenda]
f="inicio.php"
js = "main.js"

[0::general]
f="inicio.php"
js = "main.js"

[0::noticias]
f="inicio.php"

[0::search]
f="search.php"
js = "main2.js"
[0::old]
f="search.php"
js = "main.js"

[0::calendario]
f="calendar.php"
nocheck=1
