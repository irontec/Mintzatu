<?php
/**
 * Fichero de clase para campo tipo CP (Código Postal)
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */
class tablon_FLDmappoint_lat extends tablon_FLDsafetext {

        public $parent;
        public function getIsReal()
        {
            if($this->parent) {
                if (isset($this->parent->conf['intab']) && $this->parent->conf['intab']=="true") {
                    return true;
                }
            }
            return false;
        }
        public function getMysqlValue($value) {
            if (isset($this->parent->conf['intab']) && $this->parent->conf['intab']=="true") {
                $aRef = explode("||",$value);
                $results = array();
                foreach ($aRef as $r) {
                    list($f, $vv) = explode("=", $r);
                    $results[$f] = $vv;

                }
                $v =$results[$this->getIndex()];
                $value  =$v;
                $this->setValue($value);

                return '\''.$value.'\'';
            }
            $this->setValue($value);

            return false;
        }
        public function loadParentObj( $obj, $value ){
            $this->parent = $obj;
        }
        public function drawTableValue($v, $v2) {
            if (isset($this->parent->conf['intab']) && $this->parent->conf['intab']=="true") {

                return $v;

            }
            $this->setValue($v2);
            $mapPoint = new $this->parent->conf['class']($this);

             return $mapPoint->draw();
        }
        public function getSQL($tab,$alias = true)
        {

            if (isset($this->parent->conf['intab']) && $this->parent->conf['intab']=="true") {
                $ret = "";
                $ret = $this->getSQLFLDRequest();
                if ($alias) $ret .= " as '".$this->getAlias()."'";

                return $ret;
            }
            return false;
        }

        public function getCl() {
        //var_dump($this->conf);
            return "submappoint";
        }


}

class tablon_FLDmappoint_lon extends tablon_FLDmappoint_lat {

}

class tablon_FLDmappoint_aux extends tablon_FLDmappoint_lat {

}

class tablon_FLDmappoint extends tablon_FLDsafetext {

	public function getSQLType() {
		return "varchar(100)".$this->getSQLRequired();
	}

	public function getType() {

		return "mappoint";
	}

    public function loadJS() {

        $key = $this->conf['googleMapsApiKey'];
        $js = 'ABSOLUTEhttp://maps.google.com/maps/api/js?sensor=false&language=en&key='.$key;


        return array(
            "../modules/tablon/scripts/mappoint.js",
            $js
        );
    }

    public function getCl() {
        //var_dump($this->conf);
        $cl="";
        if ($this->conf['intab']=="true") $cl = " intab";
        return "mappoint".$cl;
    }

    public function registerCustomSubField($type,$idSubField) {
        if ($this->conf['intab']=="true") {


            $type = strtolower($type);
            $this->$type = $idSubField;

        }
    }

    /*public function getSQLFLDRequest() {
        var_dump($this->subFields[$this->mappoint_aux]->getSQLFLD());
        return $this->subFields[$this->mappoint_aux]->getSQLFLD();
    }*/
    public function drawEditJSON() {
        //$this->setValue($value);
        //$aRet['datef'] = $this->getdateformat(1);
        $aRet['name'] = $this->getSQLFLD();
        $aRet['theclass'] = "mapInput";
        if(isset($this->conf['req']) && $this->conf['req'] == "1"){
            $aRet['req']=true;
        }
        return json_encode($aRet);
    }

    public function drawTableValueEdit($value,$clone=false,$disabled=false) {
        $this->setValue($value);
        $cl = "";
        if ($this->conf['intab']=="true") {
            $cl = "intab";
        }
        $html = '<input type="hidden'
              . '" id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
              . '" name="'.$this->getSQLFLD().(($clone)? '_clone':'')
              . '" class="mapInput '
              . $cl . '" value="';

         if($this->getValue() !== false){
            $html .= $this->getValue();
        }

        //$this->subFields[0]->getIndex());
//        var_dump($this->subFields[0]->getIndex());
  //      var_dump($this->subFields[0]->getValue());
        $html .= '" />';


        foreach ($this->subFields as $sub) {
            $html .= '<input type="hidden'
                  . '" id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
                  . '" name="'.$sub->getSQLFLD() . (($clone)? '_clone':'')
                  . '" class="mapInput '
                  .$cl.'" value="';

            if ($sub->getValue() !== false) {
                $html .= $sub->getValue();
            }

            //$this->subFields[0]->getIndex());
    //        var_dump($this->subFields[0]->getIndex());
      //      var_dump($this->subFields[0]->getValue());
            $html .= '" />';

        }
        $ret = array();
                $ret[$this->getIndex()] = $this->getValue();
                foreach ($this->subFields as $sub) {
                   $ret[$sub->getIndex()] = $sub->getValue();
                }
        return $html.'<img class="mapButton"  latlng = "'.implode(',',$ret).'" src="./icons/network.png"/>';
    }


    public function drawTableValue($value)
    {   $exValue ="";



        if ($this->conf['intab']=="true") {

                $this->setValue($value);

                $aRef = explode("||",$value);

                if (sizeof($aRef)>1) {

                    $results = array();
                    foreach ($aRef as $r) {
                        list($f, $vv) = explode("=", $r);
                        $results[$f] = $vv;

                    }

                    //var_dump($this->getIndex());
                    $v =$results[$this->getIndex()];

                    $value  =$v;


                }
                $ret = array();
                $ret[$this->getIndex()] = $this->getValue();
                foreach ($this->subFields as $sub) {
                   $ret[$sub->getIndex()] = $sub->getValue();
                }

                $exValue = $value;

                return '<img class="mapButtonStart"  latlng = "'.implode(',',$ret).'" value="'.$value.'"/>'. ' ' .$exValue ;
        } else {

            $mapPoint = new $this->conf['class']($this);
            $ret = $mapPoint->getData($value);

        }
        //var_dump($ret);
        return '<img class="mapButtonStart"  src="./icons/network.png" latlng = "'.$ret['latitude'].','.$ret['longitude'].'" value="'.$value.'"/>'. ' ' .$exValue ;
    }

    public function getMysqlValue($value)
    {
        $c = new con("select 1;");
        $v=tablon_FLD::cleanMySQLValue($value);
//var_dump($this->getIndex()." => ".$v);
        if ($this->conf['intab']=="true") {


            $aRef = explode("||",$v);

            $results = array();
            foreach ($aRef as $r) {
                list($f, $vv) = explode("=", $r);
                $results[$f] = $vv;

            }

            //var_dump($this->getIndex());
            $v =$results[$this->getIndex()];
        }



        $this->setValue($v);
        return '\''.$this->getValue().'\'';
        //return '\''.tablon_FLD::cleanMySQLValue($v).'\'';
    }


    public function storeData()
    {


        $mapPoint = new $this->conf['class']($this);

        $ret = $mapPoint->saveData();
        //$ret
        list($plantilla, $req_field, $id) = explode("::", $_GET['id'],3);
        $ret['locate'] = $plantilla."::%s::".$id;

        return $ret;
    }

}

class defaultMapPointManager
{
    public $fld;

    public function __construct($fld)
    {
        $this->fld = $fld;



    }

    public function prepare($reqMapPoint)
    {
        foreach ($reqMapPoint as $key => $mapPointGoogleData) {
            $this->$key = new stdClass;
            foreach ($mapPointGoogleData as $k=>$v) $this->$key->$k = $v;
        }
    }

    public function saveData()
    {
        $reqMapPoint = $_GET['mappoint'];

        $this->prepare($reqMapPoint);

        return $this->save();
    }

    public function save()
    {

        $point = array(
            'iso' => con::escape($this->country->short_name),
            'name' => con::escape($this->request->short_name),
            'printable_name' => con::escape($this->request->long_name),
            'clean_name' => i::clean(con::escape($this->request->long_name)),
            'longitude' => con::escape($this->request->lng),
            'latitude' => con::escape($this->request->lat),
            'type' => con::escape($this->request->types[0])
        );

        $ret = array();

        $conf = $this->fld->getTagConf();
        $ret[$this->fld->getIndex()] = $point[$conf['realIndex']];

        if ($this->fld->hasSubFields()) {
            for ($j=0;$j<$this->fld->sizeofsubFields;$j++) {

                $conf = $this->fld->subFields[$j]->getTagConf();

                $ret[$this->fld->subFields[$j]->getIndex()] = $point[$conf['realIndex']];
            }


        }
        $point = $ret;

        $ret = array('success'=>'success', 'point'=>$point);
        return $ret;

    }

    public function draw()
    {

    }

    public function getData()
    {


    }

}

?>