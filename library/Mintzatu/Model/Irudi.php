<?php

class Mintzatu_Model_Irudi
{
    protected $_imagick;
    protected $_type = 'png';

    public function __construct()
    {
        $this->_imagick = new Imagick();
    }

    public function setPath($path)
    {
        $this->_imagick->readImage($path);
        return $this;
    }

    public function setData($data)
    {
        $this->_imagick->readImageBlob($data);
        $this->_imagick->setImageFormat($this->_type);
        return $this;
    }

    public function setType($type)
    {
        $this->_type = $type;
        $this->_imagick->setImageFormat($this->_type);
        return $this;
    }

    public function getLekuaSize()
    {
        $this->crop(90, 90);
        return $this->_imagick->getImageBlob();
    }
    
    public function getMapaSize()
    {
        $this->crop(22, 22);
        
        $img = new Imagick( __DIR__ . '/../../../public/img/txintxeta.png');
        
        $img->compositeImage( $this->_imagick, $this->_imagick->getImageCompose(), 3, 3 );
        
        $this->_imagick = $img;
        
        return $this->_imagick->getImageBlob();
    }
    
    public function getAktibitateaSize()
    {
        $this->crop(50, 50);
        return $this->_imagick->getImageBlob();
    }
    
    public function getDefaultSize()
    {
        $this->crop(120, 120);
        return $this->_imagick->getImageBlob();
    }
    
    public function getTxikiSize()
    {
        $this->crop(50, 50);
        return $this->_imagick->getImageBlob();
    }

    public function resize($width, $height)
    {
        $this->_imagick->resizeImage($width, $height, imagick::FILTER_LANCZOS, 1);
    }

    public function crop($width, $height)
    {
        $this->_imagick->cropThumbnailImage($width, $height);
    }

    public function getImage()
    {
        return $this->_imagick->getImageBlob();
    }

    public function getMimeType()
    {
        return $this->_imagick->getImageMimeType();
    }

    public function saveImage($path)
    {
        return $this->_imagick->writeImage($path);
    }
}
