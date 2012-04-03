<?php
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
	define("SERVER_NAME",$_SERVER['HTTP_X_FORWARDED_HOST']);
} else {
	define("SERVER_NAME",$_SERVER['HTTP_HOST']);
	//.(($_SERVER['SERVER_PORT']!="80")? ":".$_SERVER['SERVER_PORT']:""));
}
	
	if(!isset($_GET['config']) || empty($_GET['config']) || !file_exists(dirname(__FILE__)."/".$_GET['config'])){
		die();
	}
	
	$confAdv = parse_ini_file(dirname(__FILE__)."/".$_GET['config'],true);
	if(!isset($confAdv['advlinkCustom'])){
		die();
	}else{
		$conf= $confAdv['advlinkCustom'];
		if(!isset($conf['gallery_tab']) || empty($conf['gallery_tab']) ||
			!isset($conf['gallery_fldid']) || empty($conf['gallery_fldid']) ||
			!isset($conf['gallery_fldname']) || empty($conf['gallery_fldname']) ||
			!isset($conf['gallery_fichtab']) || empty($conf['gallery_fichtab']) ||
			!isset($conf['fich_fldid']) || empty($conf['fich_fldid']) ||
			!isset($conf['fich_fldname']) || empty($conf['fich_fldname']) ||
			!isset($conf['fich_mime']) || empty($conf['fich_mime']) ||
			!isset($conf['fich_size']) || empty($conf['fich_size']) ||
			!isset($conf['fich_name']) || empty($conf['fich_name']))
			die("vivo por que no muero");
	}
	if(isset($conf['includefiles']) && !empty($conf['includefiles']) && file_exists(dirname(__FILE__)."/".dirname($_GET['config'])."/".($conf['includefiles']))){
		require_once dirname(__FILE__)."/".dirname($_GET['config'])."/".($conf['includefiles']);
	}
	if(isset($_GET['editor'])){
		$parentEditor = $_GET['editor'];
	}else{
		$parentEditor=false;
	}
	$actLang ="";
	$actLangTrick ="";
	if(isset($conf['data_langs']) && !empty($conf['data_langs']) && $parentEditor!==false && isset($conf['lang_match']) && !empty($conf['lang_match'])) {
		$alangs = explode(",",$conf['data_langs']);
		$alangsTrick = explode(",",$conf['fich_trickurl_langs']);
		for($i=0;$i<sizeof($alangs);$i++){
			$patron = str_replace('data_langs',$alangs[$i],$conf['lang_match']);
			if(preg_match($patron,$parentEditor)>0){
				$actLang = $alangs[$i];
				$actLangTrick = $alangsTrick[$i];
				break;
			}
		}
		 
		$gfieldsinlangs = array();
		$ifieldsinlangs = array();
		if(isset($conf['gfieldsinlangs']) && !empty($conf['gfieldsinlangs'])){
			$gfieldsinlangs = explode(",",$conf['gfieldsinlangs']);	
		}
		if(isset($conf['ffieldsinlangs']) && !empty($conf['ffieldsinlangs'])){
			$ifieldsinlangs = explode(",",$conf['ffieldsinlangs']);	
		}
	}
	$galleryTab = $conf['gallery_tab'];
	$galleryId = $conf['gallery_fldid'];
	$galleryName = $conf['gallery_fldname'];
	if($parentEditor!==false && $actLang!=""){
		if(in_array('gallery_fldname',$gfieldsinlangs)){
			$galleryName.=$actLang;
		}
	}
	if(isset($conf['gallery_showcond']) && !empty($conf['gallery_showcond'])){
		$galleryshowcond = " and ".$conf['gallery_showcond']; 
	}else{
		$galleryshowcond = "";
	}
	$imgsTab = $conf['gallery_fichtab'];
	$imgsId = $conf['fich_fldid'];
	$imgsName = $conf['fich_fldname'];
	if($parentEditor!==false && $actLang!=""){
		if(in_array('fich_fldname',$ifieldsinlangs)){
			$imgsName.=$actLang;
		}
	}
	if(isset($conf['fich_idgallery']) && !empty($conf['fich_idgallery'])){
		$imgsGalleryId = $conf['fich_idgallery']; 
	}else{
		$imgsGalleryId = $galleryId;
	}
	if(isset($conf['fich_showcond']) && !empty($conf['fich_showcond'])){
		$imgshowcond = " and ".$conf['fich_showcond']." "; 
	}else{
		$imgshowcond = "";
	}
	$imgsAlt = false;
	$imgsLongDesc = false;
	$extFldSelectImgs = "";
	if(isset($conf['fich_title']) && !empty($conf['fich_title'])){
		$imgsAlt = $conf['fich_title'];
		if($parentEditor!==false && $actLang!=""){
			if(in_array('fich_title',$ifieldsinlangs)){
				$imgsAlt.=$actLang;
			}
		}
		$extFldSelectImgs .= ", ".$imgsTab.".".$imgsAlt." as imgAlt";
	}

	if(isset($conf['fich_tricksource']) && !empty($conf['fich_tricksource'])){
		$imgtrick = dirname(__FILE__)."/".dirname($_GET['config'])."/".$conf['fich_tricksource'];
	}else{
		$imgtrick = false;
	}
	if(isset($conf['fich_trickurl']) && !empty($conf['fich_trickurl'])){
		//$imgurltrick = "http://".$_SERVER['HTTP_HOST'].$conf['img_trickurl'];
		$dd = dirname(__FILE__)."/".dirname($_GET['config'])."/".$conf['fich_trickurl'];
		$aP = explode('/',$dd);
		while(($aguja = array_search("..",$aP))!==false){
			unset($aP[$aguja-1]);
			unset($aP[$aguja]);
			$aP = array_values($aP);
		}
		$dd = implode("/",$aP);
		$imgurltrick = str_replace($_SERVER['DOCUMENT_ROOT'],"http://".$_SERVER['HTTP_HOST']."/", $dd);
	}else{
		$imgurltrick = false;
	}
	if($actLangTrick!="" && $imgurltrick!==false){
		$imgurltrick .= "/".$actLangTrick;
	}
	$imgbin = $conf['fich_mime'];
	if($parentEditor!==false && $actLang!=""){
		if(in_array('fich_mime',$ifieldsinlangs)){
			$imgbin.=$actLang;
		}
	}
	$imgsize = $conf['fich_size'];
	if($parentEditor!==false && $actLang!=""){
		if(in_array('fich_size',$ifieldsinlangs)){
			$imgsize.=$actLang;
		}
	}
	$imgname = $conf['fich_name'];
	if($parentEditor!==false && $actLang!=""){
		if(in_array('fich_name',$ifieldsinlangs)){
			$imgname.=$actLang;
		}
	}
	
	$ret = array();
	$ret['error'] = false;
	$cats = array();
	$imgs = array();
	$aPasa = array('.','..','.svn');
	
	if(!isset($_GET['w']) || empty($_GET['w'])){
		die("sigo viviendo");
	}
	$selection = "";
	if($_GET['w']!='all')
		$selection = "and ".$imgsTab.".".$imgsId." = '".$_GET['w']."'"; 
	$sql = "select ".
		$galleryTab.".".$galleryId." as gtabId,".
		$galleryTab.".".$galleryName." as gtabName,".
		$imgsTab.".".$imgsId." as idI,".
		$imgsTab.".".$imgsName." as nameI,".
		$imgsTab.".".$imgbin." as imgbin,".
		$imgsTab.".".$imgsize." as imgsize,".
		$imgsTab.".".$imgname." as imgname ".
		$extFldSelectImgs."
	from ".$imgsTab." 
	left join ".$galleryTab." on (".$galleryTab.".".$galleryId." = ".$imgsTab.".".$imgsGalleryId.") 
	where 
		".$galleryTab.".".$galleryId." is not null and
		".$imgsTab.".".$imgsId." is not null ".
		$galleryshowcond.
		$imgshowcond.
		$selection;
	$con = new con ($sql);
	if($con->error()){
		$ret['error'] = "No se pudieron obtener las galerías";
		echo json_encode($ret);
		exit();
	}
	if($con->getNumRows()<1){
		$ret['categorias']['idG'] = "-1";
		echo json_encode($ret);
		exit();
	}		
		
	switch($_GET['w']){
		case "all":
			$i = 0;
			while($r=$con->getResult()){
				$cats[$i]['idG'] = $r['idI']; 
				$cats[$i]['nameG'] = $r['nameI']." (".$r['imgname'].") - ".$r['gtabName'];
				$i++;
			}
			break;
	//	break; WTF?!?!?!??!?!?! no break needed?
		default:
			$r = $con->getResult();
			$cats[0]['idG'] = $r['idI']; 
			$cats[0]['nameG'] = $r['nameI']." (".$r['imgname'].") - ".$r['gtabName'];
			$cats[0]['titulo'] = $r['nameI']; 
			$cats[0]['href'] = $imgurltrick."/".$r['imgname'];
		break;
		
	}
	$p = $imgtrick;
	$ret['categories'] = $cats;
	echo json_encode($ret);
	exit();

?>
