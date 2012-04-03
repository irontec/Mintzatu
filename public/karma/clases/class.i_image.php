<?php

class i_image {
	private $erStr;
	private $srcPath;
	var $srcCaract = array();
	var $srcCoords = array();
	var $srcDims = array();
	var $destCaract = array();
	var $srcIm;
	
	function __construct($src) {
		
		if (!file_exists($src)) {
			$this->erStr = "$src not found";
			return;
		}

		$this->srcPath = $src;
		if (!$this->srcCaract = @getimagesize($src)) {
			$this->erStr = "$src is not a valid image!";
			return;
		}
	}

	public function prepare() {
		
		switch ($this->srcCaract[2]) {
        	case IMAGETYPE_GIF:
            	$this->srcIm = imagecreatefromgif($this->srcPath);
       		break;
			case IMAGETYPE_JPEG:
            	$this->srcIm = imagecreatefromjpeg($this->srcPath);
			break;
           	case IMAGETYPE_PNG:
            	$this->srcIm = imagecreatefrompng($this->srcPath);
			break;
			default: 
            	$this->erStr = "Tipo de imagen no soportado";
				return;
		}
		return $this;

		
			
	}
	
	public function error() {
		return  $this->erStr;		
	}
	
	public function setNewDim($w,$h,$falseOnBigger = true) {
			
		$w = (int)$w;
		$h = (int)$h;
		
		if (($falseOnBigger) && (($h>$this->srcCaract[1]) || ($w>$this->srcCaract[0])) ) return false;
		// Comprobamos que los dos a la vez no sean cero... pero que al menos uno lo sea
		if (($w == 0) xor ($h == 0)) {

			if ((int)$w == 0) {
					$this->destCaract[1] = $h;
					$this->destCaract[0] = round($h*($this->srcCaract[0]/$this->srcCaract[1]));
			} else {

					$this->destCaract[0] = $w;
					$this->destCaract[1] = round($w*($this->srcCaract[1]/$this->srcCaract[0]));
			}
			$this->srcDims = array($this->srcCaract[0],$this->srcCaract[1]);
			$this->srcCoords = array(0,0);
			return true;

		} else {
			// Imagen fija => Cortando sobrantes....
			// La imagen destino tendra esas dimensiones exactamente, habra que modificar que zonas de la imagen origen se ven				
			$this->destCaract = array($w,$h);

			// Si son 0 => consideramos tamaño real => link simbólico

			if (($w==0)&&($h==0)) return true;

			$this->destCaract[0] = $w;
			$this->destCaract[1] = $h;
			$pD = $w/$h;
			
			$pO = $this->srcCaract[0]/$this->srcCaract[1];
			
			if ($pO<$pD) {
				$this->srcDims[0] = $this->srcCaract[0];
				$this->srcDims[1] = ($this->srcDims[0]*$h)/$w;
				$this->srcCoords[0] = 0;
				$this->srcCoords[1] = ($this->srcCaract[1]-(($this->srcCaract[0]*$h)/$w))/2;

			} else {
				$this->srcDims[1] = $this->srcCaract[1];
				$this->srcDims[0] = round(($this->srcDims[1]*$w)/$h);

				$this->srcCoords[1] = 0;
				$this->srcCoords[0] = ($this->srcCaract[0]-round(($this->srcCaract[1]*$w)/$h))/2;
			}	
		}
		return true;
	}


	
		
	function imResize($dest) {
		if (($this->destCaract[0]==0)&&($this->destCaract[1]==0)) {
			symlink($this->srcPath,$dest);
			return true;
		}
		
		$imDest = imageCreateTrueColor($this->destCaract[0],$this->destCaract[1]);
		switch ($this->srcCaract[2]) {
        	case 1://GIF
        		$transparencyIndex = imagecolortransparent($this->srcIm);
				$transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);
				if ($transparencyIndex >= 0) $transparencyColor = imagecolorsforindex($this->srcIm, $transparencyIndex);
				$transparencyIndex = imagecolorallocate($imDest, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
				imagefill($imDest, 0, 0, $transparencyIndex); 
				imagecolortransparent($imDest,$transparencyIndex);
        		imagecopyresized($imDest,$this->srcIm,0,0,$this->srcCoords[0],$this->srcCoords[1],$this->destCaract[0],$this->destCaract[1],$this->srcDims[0],$this->srcDims[1]);
				
        	break;
			case 3: //PNG
				imageSaveAlpha($imDest, true);
				imageAlphaBlending($imDest, false);
			default:
				imagecopyresampled($imDest,$this->srcIm,0,0,$this->srcCoords[0],$this->srcCoords[1],$this->destCaract[0],$this->destCaract[1],$this->srcDims[0],$this->srcDims[1]);               
		}
		
		$r = $this->returnResource($imDest,$dest);

		imagedestroy($imDest);			
		imagedestroy($this->srcIm);
		return $r;               
	}
	
	function imResizeDump() {
		if (($this->destCaract[0]==0)&&($this->destCaract[1]==0)) {
			readfile($this->srcPath);
			return true;
		}
		$imDest = imageCreateTrueColor($this->destCaract[0],$this->destCaract[1]);
		imageSaveAlpha($imDest, true);
		imageAlphaBlending($imDest, false);
		imagecopyresampled($imDest,$this->srcIm,0,0,$this->srcCoords[0],$this->srcCoords[1],$this->destCaract[0],$this->destCaract[1],$this->srcDims[0],$this->srcDims[1]);
		
		$r = $this->returnResource($imDest);
		imagedestroy($imDest);			
		imagedestroy($this->srcIm);
        return $r;               
	}
	
	
	
	function imResizeReturn(&$imDest) {
		if (($this->destCaract[0]==0)&&($this->destCaract[1]==0)) {
			$imDest = $this->srcIm;
			return true;
		}
		$imDest = imageCreateTrueColor($this->destCaract[0],$this->destCaract[1]);
		imageSaveAlpha($imDest, true);
		imageAlphaBlending($imDest, false);
		imagecopyresampled($imDest,$this->srcIm,0,0,$this->srcCoords[0],$this->srcCoords[1],$this->destCaract[0],$this->destCaract[1],$this->srcDims[0],$this->srcDims[1]);
        return true;
	}
	
	
	
	protected function rotateX($x, $y, $theta){
		return $x * cos($theta) - $y * sin($theta);
    }
    
   	protected function rotateY($x, $y, $theta){
		return $x * sin($theta) + $y * cos($theta);
    }
	/*
	 * 
	 * 
	 */
	public function rotate($dest,$angle,$bgcolor = "transparent") {
    	

    	$srcw = $this->srcCaract[0];
	    $srch = $this->srcCaract[1];
	
	    //Normalize angle
	    $angle %= 360;
	    //Set rotate to clockwise
	    //$angle = -$angle;
	
	    if($angle == 0) {
	    	return false;    
	    }
	
	    // Convert the angle to radians
	    $theta = deg2rad ($angle);
	
	    //Standart case of rotate
	    if ( (abs($angle) == 90) || (abs($angle) == 270) ) {
	        $width = $srch;
	        $height = $srcw;
	        
	        if ( ($angle == 90) || ($angle == -270) ) {
	            $minX = 0;
	            $maxX = $width;
	            $minY = -$height+1;
	            $maxY = 1;
	            
	        } else if ( ($angle == -90) || ($angle == 270) ) {
	        	
	            $minX = -$width+1;
	            $maxX = 1;
	            $minY = 0;
	            $maxY = $height;
	        }
	    } else if (abs($angle) === 180) {
	        $width = $srcw;
	        $height = $srch;
	        $minX = -$width+1;
	        $maxX = 1;
	        $minY = -$height+1;
	        $maxY = 1;
	    } else {
	        // Calculate the width of the destination image.
	        $temp = array ($this->rotateX(0, 0, 0-$theta),
	        $this->rotateX($srcw, 0, 0-$theta),
	        $this->rotateX(0, $srch, 0-$theta),
	        $this->rotateX($srcw, $srch, 0-$theta)
	        );
	        $minX = floor(min($temp));
	        $maxX = ceil(max($temp));
	        $width = $maxX - $minX;
	
	        // Calculate the height of the destination image.
	        $temp = array ($this->rotateY(0, 0, 0-$theta),
	        $this->rotateY($srcw, 0, 0-$theta),
	        $this->rotateY(0, $srch, 0-$theta),
	        $this->rotateY($srcw, $srch, 0-$theta)
	        );
	        $minY = floor(min($temp));
	        $maxY = ceil(max($temp));
	        $height = $maxY - $minY;
	        
	    }

	    
	    
	    $imDest = imagecreatetruecolor($width, $height);
	    
		
	    switch ($this->srcCaract[2]) {
        	case 1://GIF
        		$transparencyIndex = imagecolortransparent($this->srcIm);
				if ($transparencyIndex >= 0) {
						$transparencyColor = imagecolorsforindex($this->srcIm, $transparencyIndex);
				} else {
						$transparencyColor = imagecolorclosestalpha($this->srcIm,0,0,0,127);
				}
				
				$transparencyIndex = imagecolorallocate($imDest, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
				
				$tmpIm = imagecreatetruecolor($this->srcCaract[0],$this->srcCaract[1]);
				
				imagecolortransparent($tmpIm,$transparencyIndex);
				imagecolortransparent($imDest,$transparencyIndex);
				
				imagefilledrectangle($tmpIm ,0,0,$this->srcCaract[0],$this->srcCaract[1],$transparencyIndex);
				imagefilledrectangle($imDest ,0,0,$this->srcCaract[0],$this->srcCaract[1],$transparencyIndex);
				
				imagecopy ($tmpIm,$this->srcIm,0,0,0,0,$this->srcCaract[0],$this->srcCaract[1]);
				
				$this->srcIm = $tmpIm;
				
				
				if ($bgcolor == "transparent") {
						$bgcolor = $transparencyIndex;
				}
							
				
        	break;
			case 3: //PNG
				imageSaveAlpha($imDest, true);
				imageAlphaBlending($imDest, false);
	    		if ($bgcolor == "transparent") {
	    			$bgcolor = imagecolorclosestalpha($this->srcIm,255,255,255,127);
	    		}
	    	break;
			default:
	    		if ($bgcolor == "transparent") {
	    			$bgcolor = imagecolorclosestalpha($this->srcIm,255,255,255,127);
	    		}
			break;
		}
	    
	    if ($this->srcCaract[2] == IMAGETYPE_GIF) {
	    
	    	
	    	
	    }
	    for($x=$minX; $x<$maxX; $x++) {
	        for($y=$minY; $y<$maxY; $y++) {
	            // fetch corresponding pixel from the source image
	            $srcX = round($this->rotateX($x, $y, $theta));
	            $srcY = round($this->rotateY($x, $y, $theta));
	            if($srcX >= 0 && $srcX < $srcw && $srcY >= 0 && $srcY < $srch) {
	                $color = imagecolorat($this->srcIm, $srcX, $srcY );
	            } else {
	                $color = $bgcolor;
	            }
	            imagesetpixel($imDest, $x-$minX, $y-$minY, $color);
	        }
	    }
	    
	    $r = $this->returnResource($imDest,$dest);
		imagedestroy($imDest);			
		imagedestroy($this->srcIm);
		return $r;               
	}
	
	
	protected function returnResource($resource,$dest = NULL) {
		switch ($this->srcCaract[2]) {
        	case IMAGETYPE_GIF:
        		return imagegif($resource,$dest);
        		break;
         	case IMAGETYPE_JPEG:
				return imagejpeg($resource,$dest,100);
				break;
			case IMAGETYPE_PNG: //PNG
				return imagepng($resource,$dest,9);
		}		
	}
	
}

?>
