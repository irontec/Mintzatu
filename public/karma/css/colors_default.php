<?php

	//*************************Configuraciones de colores por defecto de la web:
	/*Color Base para tipografía. Es la que llevará el body por defecto*/
	$color_base = (isset($color_base))?$color_base:"#333333";
	/*Barra de título principal y color de base*/	
	$color_principal = (isset($color_principal))?$color_principal:"#5263ab";
	/*Barra de título auxiliar y color secundario*/
	$color_secundario = (isset($color_secundario))?$color_secundario:"#555";
    /*Color de las letras del título principal*/
	$color_titulos_principales = (isset($color_titulos_principales))?$color_titulos_principales:"#ffffff"; 
	/*Color de las letras del título auxiliar*/
	$color_titulos_auxiliares = (isset($color_titulos_auxiliares))?$color_titulos_auxiliares:"#ffffff";
	/*Fondo del cuerpo donde se incluyen los módulos*/ 
	$color_fondo_inner = (isset($color_fondo_inner))?$color_fondo_inner:"#F6F6F6"; 
	/*Fondo de los enlaces del menu al hacer un hover*/
	$color_fondo_menu_hover = (isset($color_fondo_menu_hover))?$color_fondo_menu_hover:"#c2ceff";
	/*Color de las letras de los enlaces del menu al hacer hover*/ 
	$color_menu_hover = (isset($color_menu_hover))?$color_menu_hover:"#000000"; 
	/*Color de los links*/ 
	$color_links = (isset($color_links))?$color_links:"#669"; 
	/*Color de los links visitados*/ 
	$color_links_visited = (isset($color_links_visited))?$color_links_visited:"#000080"; 
	/*Color de los links al hacer hover*/ 
	$color_links_hover = (isset($color_links_hover))?$color_links_hover:"#FF7F50"; 
	$color_th_tablon = (isset($color_th_tablon))?$color_th_tablon:$color_principal;
?>