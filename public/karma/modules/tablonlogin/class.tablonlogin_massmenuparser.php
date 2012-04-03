<?php
/**
 * Fichero principal de la clase tablon_edit,
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablonlogin_massmenuparser extends tablon
{
    protected $_aIds = array();
    protected $_massPlt;
    protected $_plantillaMass;
    protected $_relcon;

    function __construct($conf)
    {
        $this->l = new k_literal(KarmaRegistry::getInstance()->get('lang'));
        $this->conf = $conf;
        $this->fixOps();
        $this->setCurrentSection();
        $this->_massPlt = $this->selectedConf['massplt'];

        $this->_plantillaMass = $this->parsemassplt($this->rutaPlantillas.$this->_massPlt);
        //var_dump($this->_plantillaMass);
        $this->aJs[] = "../modules/tablon/scripts/tablon_mass.js";
    }

    protected function parsemassplt($f,$noFields= false)
    {
        if (!file_exists($f)) return false;
        $aFields = parse_ini_file($f, true);
        return $aFields;
    }

    protected function getmassSQL()
    {
        $pl=$this->_plantillaMass;
        $sql=" select ".$pl['::main']['id']." as __ID,";
        $asql=array();
        foreach ($pl as $id=>$v) {
            if ($id=="::main") continue;
            $asql[]=  $id." as '".$v['alias']."'";
        }
        $sql.= implode(',', $asql);
        $sql.= " from ".$pl['::main']['tab'];
        return $sql;
    }
    protected function getrelSQL()
    {
        $pl=$this->_plantillaMass;

        $sql=" select ".$this->plantillaPrincipal->getrelID()." as __IDREL, ";
        $sql.= $this->plantillaPrincipal->getID()." as __ID, ";
        $sql.= $pl['::main']['id']." as __IDMASS ";
        $sql.=" from ".$this->plantillaPrincipal->getrelTAB();
        $sql.=" where ".$this->plantillaPrincipal->getID()." = '".$this->currentValue."' ";
        return $sql;
    }

    protected function doParse()
    {
        $pl=$this->_plantillaMass;

        $f = $this->rutaPlantillas.'../'.$pl['::main']['parsefile'];


        if (!file_exists($f)) return array();
        $aFields = parse_ini_file($f, true);
        $plr = array();

        foreach ($aFields as $k => $arr) {
            if($k == "main") continue;
            if (!isset($arr[$pl['::main']['parseid']])) continue;

            $p = array();

            $p['__ID'] = $arr[$pl['::main']['parseid']];



            foreach ($pl as $cam=>$v) {
                if ($cam == "::main") {
                    continue;
                }
                if ($cam == "menu") {
                    $p[$cam] = $k;
                    continue;
                }
                $p[$cam] = isset($arr[$v['campo']]) ? $arr[$v['campo']]: false;

            }
            $plr[] = $p;
        }
        return $plr;
    }

    protected function dorelSQL()
    {

        $this->_relcon = new con($this->getrelSQL());
        return !$this->_relcon->error();

    }
    protected function drawrelErrors()
    {
        iError::error($this->_relcon->getError());
        switch($this->_relcon->getErrorNumber()) {
            case 1146:
                iError::warn("<textarea rows='20' cols='80' class='warn'>" . $this->getCreateRelSQL() . "</textarea>");
                break;
        }
    }
    protected function getCreateRelSQL()
    {
        $pl=$this->_plantillaMass;
        $cSQL = "create table " . $this->plantillaPrincipal->getrelTAB()
              . "(\n" . $this->plantillaPrincipal->getrelID() . " mediumint(8) unsigned not null auto_increment"
              . ",\n" . $this->plantillaPrincipal->getID() . " mediumint(8) unsigned not null "
              .    ",\n index(" . $this->plantillaPrincipal->getID() . ") "
              .    ",\n foreign key(" .  $this->plantillaPrincipal->getID() .  ") references "
              . $this->plantillaPrincipal->getTab()
              . "(".$this->plantillaPrincipal->getID() . ") on delete cascade on update cascade\n"
              . ",\n" . $pl['::main']['id'] . " mediumint(8) unsigned not null "
              .    ",\n index(" . $pl['::main']['id'] . ") "
              .    ",\n foreign key(" . $pl['::main']['id']
              . ") references " . $pl['::main']['tab'] . "("
              . $pl['::main']['id'] . ") on delete cascade on update cascade\n"
              . ",\nprimary key(" . $this->plantillaPrincipal->getrelID()
              . ")\n) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        return $cSQL;

    }
    protected function buildConds()
    {
        $aConds = array();
        $pl = $this->plantillaPrincipal;
        if ($del = $pl->getDeletedFLD()) {
            $aConds[] = ' '.$del.'=\'0\'';
        }

        $aIds = explode(",", $this->currentValue);

        $aMiniConds = array();
        for ($i=0;$i<sizeof($aIds);$i++) {
            $this->_aIds[] = $aIds[$i];
            $aMiniConds[] = ' '.$pl->getTab().'.'.$this->plantillaPrincipal->getID().'=\''.$aIds[$i].'\'';
        }
        $aConds[] = '(' . implode(" or ", $aMiniConds).')';
        if (sizeof($aConds)>0) {
            return " where " . implode(' and ', $aConds);
        } else {
            return "";
        }
    }

    protected function doSQL()
    {
        $this->conPrinc = new con($this->getSQL());
        return !$this->conPrinc->error();
    }

    protected function drawTableContents($checkBox = false,$csv = false)
    {
        $pl = $this->plantillaPrincipal;
        $flagEntrada = true;
        $rAux = false;
        $this->getrelSQL();

        $plr = $this->doParse();

        $aRel=array();
        while ($r = $this->_relcon->getResult()) {

            $aRel[$r['__IDMASS']]=$r['__IDREL'];
        }

        while ($r = $this->conPrinc->getResult()) {
            $a = $pl->getPreForm();
            foreach ($r as $al => $vlr) {
                $a = str_replace($al, $vlr, $a);
            }

            echo '<tr class="tablon_tr_header" >';
            echo '<th class="cabecera">'.$pl->getPreForm().'</th>';

            $cont=1;
            echo '<th class="multiselect" id="mlMASTER"><input type="checkbox" id="chk_msMASTERMASS"/></th>';
            foreach ($this->_plantillaMass as $id=>$v) {
                if ($id=="::main") continue;
                echo '<th class="tablon_edit" >';
                echo $this->getMLval($v['alias'], $v, 'alias');
                echo '</th>';
                $cont++;
            }
            echo '</ tr>';

            foreach ($plr as $r) {
                echo '<tr>';
                echo '<td class="cabecera" >'.(($flagEntrada)? $a:"").'</td>';
                $flagEntrada=false;
                echo '<td class="multiselect" newid="0" id="'
                     . basename($pl->getFile()) . '::' . $this->_massPlt . '::' . $r['__ID']
                     . ((isset($aRel[$r['__ID']]))? '::' . $aRel[$r['__ID']] : '::nulo')
                     . '::' . $this->currentValue . '">';
                echo '<input type="checkbox"'
                     . ((isset($aRel[$r['__ID']]))? 'checked="checked"':'')
                     . ' />';
                echo '</td>';

                foreach ($r as $id=>$v) {
                    if ($id=="__ID") continue;
                    echo '<td class="tablon_edit" >';
                    echo $v;
                    echo '</td>';
                }
                echo '</ tr>';
            }

            $rAux = $r;
        }

        if ($rAux!==false) echo '<tr id="'.basename($pl->getFile()).'::'.$rAux['__ID'].'" >';
        else echo '<tr>';
        echo '<td class="tablon_edit" colspan="'.($cont+1).'">';

        echo '</td></tr>';

    }

    public function draw()
    {

        echo $this->drawTitle();
        if (!$plantilla = $this->getPlt()) {
            iError::error("Plantilla no encontrada");
            return false;
        }

        $this->plantillaPrincipal = new tablon_plantilla($plantilla);


        $pl = $this->plantillaPrincipal;
        echo '<div id="tablonContainer">';
        echo '<table id="tablon_edit" class="tablon" genero="'.$pl->getGenero().'" entidad="'.$pl->getEntidad().'">';

        if (!$this->doSQL()) {
            $this->drawErrors();
        } else {
            if (!$this->dorelSQL())     $this->drawrelErrors();

            $this->drawTableContents();
        }
        echo '</table>';
        $this->drawGeneralOps();
        echo '</div>';

    }
    public function drawGeneralOps()
    {


        echo '<ul id="optsTablon" class="tablon_edit_opts">';
        echo '<li class="optsIni">&nbsp;</li>';
        echo '<li class="optsEnd">&nbsp;</li>';
        echo '<li class="opts backButton">'.$this->drawBackLink().'</li>';
        echo '</ul>';


    }
    public function hola()
    {
        echo "hola";
    }

    static function foo()
    {
    }
}
