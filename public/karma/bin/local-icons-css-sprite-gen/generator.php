<?php

$dir = isset($argv[1])? $argv[1]:false;
$size = isset($argv[2])? $argv[2]:false;

echo "\n";
echo "\t====LOCAL ICON CSS SPRITE GEN====\n";
{
	if (!is_dir($dir)) {
		echo "[*!] Directory not found\n";
		exit();
	}
	if ($size == false || !is_numeric($size)) {
		echo "[*!] size not found\n";
		exit();
	}
	$files = array();
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
			if (in_array($file, array('.', '..'))) continue;
			$fileType = filetype($dir . $file);
			$fileKey = $file;
			if ($fileType == 'link') {
				
			} else {
				$info = pathinfo($dir . $file);
				$files[$fileKey] = array(basename($file, '.' . $info['extension']));
			}
        }
        closedir($dh);
    }
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
			if (in_array($file, array('.', '..'))) continue;
			$fileType = filetype($dir . $file);
			$fileKey = $file;
			if ($fileType == 'link') {
				$linkFile = $file;
				$route = array();
				do {
					$info = pathinfo($dir . $linkFile);
					$route[] = basename($linkFile, '.' . $info['extension']);
					$linkFile = readlink($dir . $linkFile);
				} while (filetype($dir . $linkFile) == 'link');

				if (!isset($files[$linkFile])) {
					$info = pathinfo($dir . $linkFile);
					$files[$linkFile] = array(basename($linkFile, '.' . $info['extension']));
				}		
				foreach ($route as $f) {
					array_push($files[$linkFile], $f);
				}
				$files[$linkFile] = array_unique($files[$linkFile]);
			}
        }
        closedir($dh);
    }

	$tmp = dirname(__FILE__) . '/tmp/';

	foreach ($files as $pic => $data) {
		if (file_exists($dir . $pic)) {
			$info = pathinfo($dir . $pic);
			$exec =  'convert +antialias -background transparent  "' .$dir . $pic . '" -resize '.$size.'x'.$size.' '. $tmp . $info['filename'].'.png';
			exec($exec);
		}
	}


	$imagePadding = 6;
	$x = $y = ceil(sqrt(sizeof($files)));
	$xWidth = ($size+($imagePadding*2)) * $x;
	$yHeight = ($size+($imagePadding*2)) * $y;
	
	$pngpath = dirname(__FILE__) . "/output/icon-css-stripe-".$size.".png";


//	$base = imagecreate(  $xWidth, $yHeight );
//	$black = imagecolorallocate($base, 0, 0, 0);
//	imagecolortransparent($base, $black);

	$base = new Imagick();
	$base->newImage($xWidth, $yHeight , new ImagickPixel('transparent'));
	$base->setImageFormat('png32'); 

	$col = 0;
	$row = 0;

	$xPos = $imagePadding;
	$yPos = $imagePadding;


	$htmlBuffer = "";
	$cssBuffer = "";

	foreach ($files as $pic => $data) {

		if (file_exists($dir . $pic)) {
			if ($col == $x) {
				$col = 0;
				$xPos = $imagePadding;
				$row++;
				$yPos = $row * ($imagePadding*2+$size);
			}

			$info = pathinfo($dir . $pic);
			$f = $tmp . $info['filename'].'.png';
			
			$xPos = ($col + 1) + $xPos;

			$im = new Imagick($f);

			$base->setImageColorspace($im->getImageColorspace() );
			$base->compositeImage($im, $im->getImageCompose(), $xPos, $yPos); 


			$im->clear();
			$im->destroy();

			//$im = imagecreatefrompng($f);
			//imagealphablending($im, true);
			//imagesavealpha($im, true);
			//imagecopy( $base , $im , $xPos, $yPos, 0, 0, $size, $size);
			//imagedestroy($im);


				
			$tmpArr = array();
			foreach ($data as $mimeElement) {
				$tmpArr[] = $mimeElement = str_replace(array("+", "."), '-', $mimeElement);
				$htmlBuffer.= "\t".'<div class="mime '.$mimeElement.'" ></div>' . "\n";
			}

			$cssBuffer.="\n.mime.". implode(", .mime.", $tmpArr) . "\n{\n";

			$cssBuffer.="\tbackground-position: -".$xPos."px -".$yPos."px;\n";

			$cssBuffer.="}\n";

			$xPos+=$size+$imagePadding;

			$col++;

		}
	}







//imagepng($base, $pngpath);
//imagedestroy($base);

$base->writeImage($pngpath);
$base->clear();
$base->destroy();
	

$cssBuff = "\n\n";
$cssBuff.= "/*LOCAL ICON CSS SPRITE GEN*/\n";
$cssBuff.= "
.mime {
    background-image: url(./icon-css-stripe-".$size.".png);
    background-color: transparent;
    background-position: 0 0;
    background-repeat: no-repeat;
    border: 1px solid #EEEEEE;
    float: left;
    height: ".$size."px;
    margin: 6px;
    padding: 0px;
    width: ".$size."px;
}
\n";
$cssBuff.= "/*LOCAL ICON CSS SPRITE GEN*/\n" . $cssBuffer;


$htmlBuff = "";
$htmlBuff.= "<html>\n";
$htmlBuff.= "<head>\n";
$htmlBuff.= '<link rel="Stylesheet" type="text/css" href="./icon-css-stripe-'.$size.'.css" />' . "\n";
$htmlBuff.= "</head>\n";
$htmlBuff.= "<body>\n";
$htmlBuff.= "<h1>LOCAL ICON CSS SPRITE GEN</h1>\n" . $htmlBuffer;
$htmlBuff.= "</body>\n";
$htmlBuff.= "</html>\n";

$html = fopen(dirname(__FILE__) . "/output/icon-css-stripe-".$size.".html", 'w');
$css = fopen(dirname(__FILE__) . "/output/icon-css-stripe-".$size.".css", 'w');
fwrite($html, $htmlBuff);
fclose($html);
fwrite($css, $cssBuff);
fclose($css);
}

exec('rm ' . $tmp . '*');
echo "\n";
