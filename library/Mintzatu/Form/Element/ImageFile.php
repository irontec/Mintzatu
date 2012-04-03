<?php
class Mintzatu_Form_Element_ImageFile extends Zend_Form_Element_File
{
    protected $path = '';
    protected $imgName = '';

    public function __construct($imageName, $attributes = array(), $imagePath = null)
    {
        if (! isset($attributes["size"]) ) {
        
            $attributes["size"] = 11;
        }

        parent::__construct($imageName, $attributes);

        $this->clearDecorators();
        $this->addDecorator('Description', array('tag' => 'div', 'class' => 'description image-preview', 'escape' => false))
             ->addDecorator('File')
             ->addDecorator('Errors')
             ->addDecorator('HtmlTag', array('tag' => 'dd'))
             ->addDecorator('Label', array('tag' => 'dt'));

        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();

        $this->path = $attributes['publicPath'];
        $description = '<img style="border:1px solid black; max-width:150px;" src="'.$this->path.'" />';
        $description = '<a target="_blank" class="fancybox" href="'.$this->path.'">'.$description.'</a>';
        
        
        $this->setDescription($description);
    }

//    public function setValue($value)
//    {
//        if (empty($value)) {
//            return;
//        }
//
//        $this->imgName = $value;
//        $layout = Zend_Layout::getMvcInstance();
//        $view = $layout->getView();
//
//        $img = $this->path;
//
//        parent::setValue($value);
//
//        $description = '<img style="border:1px solid black; max-width:1s50px;" src="'.$img.'" />';
//        $description = '<a target="_blank" class="fancybox" href="'.$img.'">'.$description.'</a>';
//        
//        
//        $this->setDescription($description);
//        return $this;
//    }
//
//    public function getValue()
//    {
//        //return $this->getDescription();
//        return $this->imgName;
//    }
}
