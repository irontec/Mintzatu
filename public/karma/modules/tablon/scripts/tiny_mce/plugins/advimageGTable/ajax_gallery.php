<?php
if (!defined('SERVER_NAME')) {
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        define("SERVER_NAME", $_SERVER['HTTP_X_FORWARDED_HOST']);
    } else {
        define("SERVER_NAME", $_SERVER['HTTP_HOST']);
    }
}

require_once("config.php");
require_once(dirname(__FILE__)."/clases/class.i.php");

if (!isset($_GET['config']) || empty($_GET['config'])
    || !file_exists(dirname(__FILE__)."/".$_GET['config'])) {
	die();
}

$confAdv = parse_ini_file(dirname(__FILE__)."/".$_GET['config'], true);
if (!isset($confAdv['advimageGTable'])) {
	die();
} else {
	$conf= $confAdv['advimageGTable'];
	if (!isset($conf['gallery_tab']) || empty($conf['gallery_tab']) ||
		!isset($conf['gallery_fldid']) || empty($conf['gallery_fldid']) ||
		!isset($conf['gallery_fldname']) || empty($conf['gallery_fldname']) ||
		!isset($conf['gallery_imgtab']) || empty($conf['gallery_imgtab']) ||
		!isset($conf['img_fldid']) || empty($conf['img_fldid']) ||
		!isset($conf['img_fldname']) || empty($conf['img_fldname']) ||
		!isset($conf['img_img']) || empty($conf['img_img']) ||
		!isset($conf['img_imgsize']) || empty($conf['img_imgsize']) ||
		!isset($conf['img_imgname']) || empty($conf['img_imgname']))
		die("vivo por que no muero");
}
$filename = dirname(__FILE__)."/".dirname($_GET['config'])."/".$conf['includefiles'];
if (isset($conf['includefiles']) && !empty($conf['includefiles'])
    && file_exists($filename)) {
	require_once $filename;
}
if (isset($_GET['editor'])) {
	$parentEditor = $_GET['editor'];
} else {
	$parentEditor=false;
}
$actLang ="";
if (isset($conf['data_langs']) && !empty($conf['data_langs'])
    && $parentEditor!==false && isset($conf['lang_match'])
    && !empty($conf['lang_match'])) {
	$alangs = explode(",", $conf['data_langs']);
	for ($i=0;$i<sizeof($alangs);$i++) {
		$patron = str_replace('data_langs', $alangs[$i], $conf['lang_match']);
		if (preg_match($patron, $parentEditor)>0) {
			$actLang = $alangs[$i];
			break;
		}
	}

	$gfieldsinlangs = array();
	$ifieldsinlangs = array();
	if (isset($conf['gfieldsinlangs']) && !empty($conf['gfieldsinlangs'])) {
		$gfieldsinlangs = explode(",", $conf['gfieldsinlangs']);
	}
	if (isset($conf['ifieldsinlangs']) && !empty($conf['ifieldsinlangs'])) {
		$ifieldsinlangs = explode(",", $conf['ifieldsinlangs']);
	}
}
$galleryTab = $conf['gallery_tab'];
$galleryId = $conf['gallery_fldid'];
$galleryName = $conf['gallery_fldname'];
if ($parentEditor!==false && $actLang!="") {
	if (in_array('gallery_fldname', $gfieldsinlangs)) {
		$galleryName.=$actLang;
	}
}
if (isset($conf['gallery_showcond']) && !empty($conf['gallery_showcond'])) {
	$galleryshowcond = " where ".$conf['gallery_showcond'];
} else {
	$galleryshowcond = "";
}
$imgsTab = $conf['gallery_imgtab'];
$imgsId = $conf['img_fldid'];
$imgsName = $conf['img_fldname'];
if ($parentEditor!==false && $actLang!="") {
	if (in_array('img_fldname', $ifieldsinlangs)) {
		$imgsName.=$actLang;
	}
}
if (isset($conf['img_idgallery']) && !empty($conf['img_idgallery'])) {
	$imgsGalleryId = $conf['img_idgallery'];
} else {
	$imgsGalleryId = $galleryId;
}
if (isset($conf['img_showcond']) && !empty($conf['img_showcond'])) {
	$imgshowcond = " and ".$conf['img_showcond']." ";
} else {
	$imgshowcond = "";
}
$imgsAlt = false;
$imgsLongDesc = false;
$extFldSelectImgs = "";
if (isset($conf['img_alt']) && !empty($conf['img_alt'])) {
	$imgsAlt = $conf['img_alt'];
	if ($parentEditor!==false && $actLang!="") {
		if (in_array('img_alt', $ifieldsinlangs)) {
			$imgsAlt.=$actLang;
		}
	}
	$extFldSelectImgs .= ", ".$imgsAlt." as imgAlt";
}
if (isset($conf['img_longdesc']) && !empty($conf['img_longdesc'])) {
	$imgsLongDesc = $conf['img_longdesc'];
	if ($parentEditor!==false && $actLang!="") {
		if (in_array('img_longdesc', $ifieldsinlangs)) {
			$imgsLongDesc.=$actLang;
		}
	}
	$extFldSelectImgs .= ", ".$imgsLongDesc." as imgLongDesc";
}


if (isset($conf['img_tricksource']) && !empty($conf['img_tricksource'])) {
	$imgtrick = dirname(__FILE__)."/".dirname($_GET['config'])."/".$conf['img_tricksource'];
    $imgtrick = str_replace('%data_langs%', $actLang, $imgtrick);
} else {
	$imgtrick = false;
}
if (isset($conf['img_trickurl']) && !empty($conf['img_trickurl'])) {
	$dd = dirname(__FILE__)."/".dirname($_GET['config'])."/".$conf['img_trickurl'];
	$dd = str_replace('%data_langs%', $actLang, $dd);
	$aP = explode('/', $dd);
	while (($aguja = array_search("..", $aP))!==false) {
		unset($aP[$aguja-1]);
		unset($aP[$aguja]);
		$aP = array_values($aP);
	}
	$dd = implode("/", $aP);
	$imgurltrick = str_replace($_SERVER['DOCUMENT_ROOT'], "http://".$_SERVER['HTTP_HOST']."/", $dd);
} else {
	$imgurltrick = false;
}

$imgbin = $conf['img_img'];
$imgsize = $conf['img_imgsize'];
$imgname = $conf['img_imgname'];

if ($parentEditor!==false && $actLang!="") {
    if (in_array('img_imgsize', $ifieldsinlangs)) {
        $imgbin.=$actLang;
    }
    if (in_array('img_img', $ifieldsinlangs)) {
        $imgsize.=$actLang;
    }
    if (in_array('img_imgname', $ifieldsinlangs)) {
        $imgname.=$actLang;
    }
}
$ret = array();
$ret['error'] = false;
$cats = array();
$imgs = array();
$cache = dirname(__FILE__)."/".CACHE."";
$aPasa = array('.','..','.svn');
$imgok= array('image/jpeg','image/jpg','image/gif','image/png');

switch($_GET['w']){
	case "all":
		$sql = "select ".$galleryId." as gtabId,".$galleryName." as gtabName from ".$galleryTab.$galleryshowcond;
		$con = new con($sql);
		if ($con->error()) {
			$ret['error'] = "No se pudieron obtener las galerías";
			echo json_encode($ret);
			exit();
		}
		if ($con->getNumRows()>0) {
			$i=0;
			while ($r=$con->getResult()) {
				$cats[$i]['idG'] = $r['gtabId'];
				$cats[$i]['nameG'] = $r['gtabName'];
				$i++;
			}
		} else {
			$cats[0]['idG'] = "-1";
			$cats[0]['nameG'] = "No hay galerías disponibles";
		}
//	break; WTF?!?!?!??!?!?! no break needed?
	case "images":
		if (!isset($_GET['c'])) {
			if ($cats[0]['idG'] == "-1") {
				$imgs[0]["idI"] = "-1";
				$imgs[0]["NameI"] = "";
				$imgs[0]['alt'] = "";
				$imgs[0]['longDesc'] = "";
				break;
			} else {
				$c = $cats[0]["idG"];
				$galleryName = $cats[0]["nameG"];
			}
		} else {
			$c = $_GET['c'];
			if (empty($galleryshowcond)) {
				$galleryshowcond = " where ".$galleryId." = '".$c."'";
			} else {
				$galleryshowcond = " and ".$galleryId." = '".$c."'";
			}
			$sql = "select ".$galleryId." as gtabId,".$galleryName." as gtabName from ".$galleryTab.$galleryshowcond;
			$con = new con($sql);
			$g = $con->getResult();
			$galleryName = $g['gtabName'];
		}
		$sql = "
            select
                ".$imgsId." as idI,
                ".$imgsName." as nameI,
                ".$imgbin." as imgbin,
                ".$imgsize." as imgsize,
                ".$imgname." as imgname
                ".$extFldSelectImgs."
            from
                ".$imgsTab."
            where
                ".$imgsGalleryId." = '".$c."'
                ".$imgshowcond;
		$con = new con($sql);

		if ($con->error()) {
			$ret['error'] = "No se pudieron obtener las imágenes";
			echo json_encode($ret);
			exit();
		}
		$dstpathR = CACHE;
		if ($con->getNumRows()>0) {
			$j=0;
			while ($r=$con->getResult()) {
				$dst = $dstpathR."_".$galleryName."_".$r['imgname'];
				$fimgtrick = $imgtrick."_".$imgbin."_".$r['idI'];
				if ($imgtrick!==false && file_exists($fimgtrick)) {
					$a = $imgurltrick.$r['imgname'];
					$b = $fimgtrick;
					if (!in_array(i::mime_content_type($b), $imgok)) {
					    continue;
					}
					$imgs[$j]['idI'] = $r['idI'];
					$imgs[$j]['nameI'] = $r['nameI'];
					$imgs[$j]['alt'] = "";
					$imgs[$j]['longDesc'] = "";
					if (isset($r['imgAlt']) && !empty($r['imgAlt'])) {
						$imgs[$j]['alt'] = $r['imgAlt'];
					}
					if (isset($r['imgLongDesc']) && !empty($r['imgLongDesc'])) {
						$imgs[$j]['longDesc'] = $r['imgLongDesc'];
					}
        			$imgs[$j]['img']= $a;
				}
				$j++;
			}
		} else {
			$imgs[0]['idI'] = "-1";
			$imgs[0]['nameI'] = "";
			$imgs[0]['alt'] = "";
			$imgs[0]['longDesc'] = "";
		}
    	break;

}
$p = $imgtrick;
$ret['categories'] = $cats;
$ret['images'] = $imgs;

$aP = explode('/', $p);
while (($aguja = array_search("..", $aP))!==false) {
	unset($aP[$aguja-1]);
	unset($aP[$aguja]);
	$aP = array_values($aP);
}
$p = implode("/", $aP);
$ret['path'] = "";

$ret['svname'] = "";
echo json_encode($ret);
exit();
