<?php


/*
 *
 * data:
 *
 */




class tablon_FLDglocation_data extends tablon_FLDglocation {

        public function getMysqlValue($value) {
            $result = json_decode($value);
            if (isset($this->conf['googleJsonKey'])) {
                $atmp = explode("/",$this->conf['googleJsonKey']);
                $tmp = $result;
                foreach ($atmp as $v) {
                    $tmp = $tmp->$v;
                }
                $value = $tmp;
            }
            if (isset($this->conf['googleJsonType'])) {
                $types = explode(",",$this->conf['googleJsonType']);
                $type = implode("-",$types);
                $resObj = false;
                foreach ($tmp as $component) {
                    $cType = implode("-",$component->types);
                    if ($cType == $type) {
                        $resObj = $component;
                        break;
                    }
                }
                $field = $this->conf['googleJsonField'];
                if ($res = $resObj->$field) {
                    $value = $res;
                }
            }
            if (isset($this->conf['findExternalIndex']) && $res) {
                list($id, $tab, $code) = explode("::",$this->conf['findExternalIndex']);
                $c = new con("select ".$id." as RES from ".$tab." where ".$code." = '".$res."' limit 1");
                if ($c->getNumRows()>0) {
                    $r = $c->getResult();
                    $res = $r['RES'];
                }
                $value = $res;
            }
            $this->setValue($value);
            return '\''.$value.'\'';
        }

        public function drawTableValue($v) {
            if (isset($this->conf['findExternalIndex']) && $v) {
                list($id, $tab, $code) = explode("::",$this->conf['findExternalIndex']);
                $c = new con("select ".$this->conf['showExternalIndex']." as RES from ".$tab." where ".$id." = '".$v."' limit 1");
                if ($c->getNumRows()>0) {
                    $r = $c->getResult();
                    $res = $r['RES'];
                }
                $v = $res;
            }
            return $v;
        }
}


class tablon_FLDglocation extends tablon_FLD
{
    public function loadJS() {
        $key = krm_menu::getinstance()->getMenuSection('main', 'googleMapsApiKey');
        $js = 'ABSOLUTEhttp://maps.google.com/maps/api/js?sensor=false&language=en&key='.$key;
        return array(
            "../modules/tablon/scripts/json2.js",
            "../modules/tablon/scripts/glocation.js",
            $js
        );
    }

    public function getConstantTypeAjaxUpload()
    {
        return "_POST";
    }

    public function getCl() {
        return "glocation";
    }

    public function drawTableValue($value)
    {
        if ($this->entitify) {
            $value = htmlentities($value, ENT_QUOTES, 'utf-8');
        }
        if (empty($value) && $value!="0" && $value!=0) return "";

        if ($_GET['op'] == 'tablon_edit' && $_GET['acc'] == 'save') {
            return ''.($value).'';
        }

        return '<input type="glocationinput" class="miniValue" value="'.htmlentities($value).'" />';
    }
    public function getValue()
    {
        return $this->value;
    }
    public function drawTableValueEdit($value, $clone=false, $disabled=false)
	{
		$this->setValue($value);
		$clase = 'glocationField '.(($clone)? 'clone':'');

		if (isset($this->conf['req']) && $this->conf['req'] == "1") {
		    $clase .= ((empty($clase))?"required":" required");
		}

		if (!empty($clase)) {
            $clase = " class = '".$clase."'";
		}

		$toappend = "";
        //$toappend = "<div class></div>";

        if ($this->hasSubFields()) {
            $toappend = "<br />";
            $toappend.= '<ul id="teResults">';
            for ($j=0;$j<$this->sizeofsubFields;$j++) {
                $alias = $this->subFields[$j]->getAlias();
                $value = $this->subFields[$j]->getValue();
                $value = $this->subFields[$j]->drawTableValue($value);
                $toappend.= '<li>';
                $toappend.= '<strong >'.$alias.':</strong> <span class="value" id="'.$this->getPlt().'::'.$this->subFields[$j]->getIndex().'::'.$this->currentID.'" >'.$value.'</span>';
                $toappend.= '</li>';
            }
            $toappend.= '</ul>';
        }


		return '<input type="'.$this->getType().'"  '
		      . ((isset($this->conf['max']))? 'maxlength="'.$this->conf['max'].'"':'')
		      . ' size="'.((isset($this->conf['size']))? $this->conf['size']:'37')
		      . '" name="'.$this->getSQLFLD().(($clone)? '_clone':'')
              . '" id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
		      . '" '.$clase
		      . ' value="'.htmlentities($this->getValue()).'" />'
		      . $toappend;
	}

	public function getType()
	{
		return "text";
	}


    public function processReturnJSONValue($v,$pl,$id,$edit=false) {

        $principal = '';
        if(isset($v))
        {
            $principal = $v;
        }
        $ret = array(
            0 => 0,
            "principal" => rawurlencode($this->drawTableValue($principal)),
            "subfields" => array()
        );
        for($i=0;$i<$this->sizeofsubFields;$i++) {
            $ret['subfields'][basename($pl->getFile()).'::'.$this->subFields[$i]->getSQLFLD().'::'.$id] = ($this->subFields[$i]->drawTableValue($this->subFields[$i]->getValue()));
        }
        return $ret;
    }
}
