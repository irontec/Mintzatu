<?php
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
	define("SERVER_NAME",$_SERVER['HTTP_X_FORWARDED_HOST']);
} else {
	define("SERVER_NAME",$_SERVER['HTTP_HOST']);
	//.(($_SERVER['SERVER_PORT']!="80")? ":".$_SERVER['SERVER_PORT']:""));
}

	require_once(dirname(__FILE__)."/clases/class.i.php");


	
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Traductor de Contenidos</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script type="text/javascript" src="../../utils/mctabs.js"></script>
	<script type="text/javascript" src="../../utils/form_utils.js"></script>
	<script type="text/javascript" src="../../utils/validate.js"></script>
	<script type="text/javascript" src="../../utils/editable_selects.js"></script>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery.translate-1.2.6.min.js"></script>
	<script type="text/javascript">


function doChange(){
	value = document.getElementById('langsCombo').value
	
	
	tmp = value.split("|");
	$('#contentsToTrans').translate(tmp[0], tmp[1]);
}
	
	
	</script>

	<link href="css/advimage.css" rel="stylesheet" type="text/css" />
	<base target="_self" />
</head>
<body id="advimage" style="display: none">

<style>
img.preimg , img.previmg{
border:1px solid #7F93A3;
max-height:70px;
margin:2px;
}
#gcontent{
border:1px solid #7F93A3;
height:150px;
margin:10px 0 0;
overflow:auto;
width:615px;
z-index:5000;
}
#gcontent2{
height:auto;
width:auto;
z-index:2000;
}

.panel_wrapper div.current {
height:428px;
}
#prev {
border:1px solid #000000;
height:96px;
margin:0;
overflow:auto;
width:99%;
}
.medio{
float:left;
height:170px;
overflow:auto;
position:relative;
width:314px;
}
.medio li{
	list-style-image:none;
	list-style-position:outside;
	list-style-type:none;
	margin:3px 0 0;
}

.catlink{
	text-decoration:none;
	
}
.delcatlink{
	text-decoration:none;
	font-size:8px;
}
.inmedio{
	margin:15px;
}
.inmedio a,.inmedio a:hover,.inmedio a:visited{
	text-decoration:none;
	font-wight:bold;
}

#contentsToTrans{
margin: 10px 0 0 0 ;
}
</style>
<select id="langsCombo" onChange="doChange();" >
<option value="false">Selecciona la dirección de traducción</option>
<option value="es|en">es - en</option>
<option value="en|es">en - es</option>
</select>
<br />
<div id="contentsToTrans">
<?php echo 	$_GET['o'];?>
</div>


</body> 
</html> 

