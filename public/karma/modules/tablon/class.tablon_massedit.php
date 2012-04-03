<?php
/**
 * Fichero principal de la clase tablon_edit,
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_massedit extends tablon
{
	protected $aIds = array();
	protected $massPlt;
	protected $relDataPlt=false;
	protected $plantillaMass;
	protected $plantillaRelData=false;
	protected $relcon;

	function __construct(&$conf)
	{
		$this->l = new k_literal(KarmaRegistry::getInstance()->get('lang'));
		$this->conf = $conf;
		$this->fixOps();
		$this->setCurrentSection();
		$this->massPlt = $this->selectedConf['massplt'];
		$this->plantillaMass = $this->parsemassplt($this->rutaPlantillas . $this->massPlt);

		if (isset($this->selectedConf['reldataplt']) && !empty($this->selectedConf['reldataplt'])) {
			$this->relDataPlt = $this->selectedConf['reldataplt'];
		}

		$this->aJs[] = "../modules/tablon/scripts/jeditable.js";
		$this->aJs[] = "../modules/tablon/scripts/tablon_mass.js";
	}

	protected function getPltRelData()
	{
		if (isset($this->relDataPlt) && $this->relDataPlt != false) {
			if (!file_exists($this->rutaPlantillas.$this->relDataPlt)) {
			    return false;
			}
			return $this->rutaPlantillas . $this->relDataPlt;
		} else {
			return false;
		}
	}

	protected function parsemassplt($f, $noFields= false)
	{
		if (!file_exists($f)) {
		    return false;
		}
		$aFields = parse_ini_file($f, true);
		return $aFields;
	}

	protected function getmassSQL()
	{
		$pl = $this->plantillaMass;
		$sql = " select " . $pl['::main']['id'] . " as __ID,";
		$asql=array();

		foreach ($pl as $id=>$v) {
			if ($id=="::main") {
			    continue;
			}
			if (in_array($id, $this->getNoShownFields())) {
			    continue;
			}
			if ($v['type'] == "GHOST") {
				$asql[]=  " " . $v['sql'] . " ";
				continue;
			}
			$asql[]=  $id." as '" . $this->getMLval($v['alias'], $v, 'alias') . "'";
		}
		$sql .= implode(',', $asql);
		$sql .= " from " . $pl['::main']['tab'] . "  ";
		if (isset($pl['::main']['lefttabs'])) {
			$leftTabs = explode("|", $pl['::main']['lefttabs']);
			$leftConds = explode("|", $pl['::main']['leftconds']);
			$leftWhere = explode("|", $pl['::main']['leftwhere']);
			if (sizeof($leftTabs)>0 && sizeof($leftConds)>0
			   && sizeof($leftTabs) == sizeof($leftConds)) {
				for ($i=0;$i<sizeof($leftTabs);$i++) {
					if ($i!=0) {
						$sql .= " ";
					}
					$sql .= " left join " . $leftTabs[$i] . " on(" . $leftConds[$i] . ") ";
				}
			}
		}
		$sqlCond = "";
		if (isset($pl['::main']['deleted'])) {
			$sqlCond .= " where " . $pl['::main']['deleted'] . "='0'";

		}

		if (isset($this->selectedConf['hideifnotroladmin'])
		   && !in_array($this->selectedConf['hideifnotroladmin'], $_SESSION["__IDROL"])) {
			if ($sqlCond != "") {
				$sqlCond .= ' and ';
			} else {
				$sqlCond .= ' where ';
			}
			$sqlCond .= $pl['::main']['id'] . '!=\'' . $this->selectedConf['hideifnotroladmin'] . '\'';
		}

		if ($this->plantillaPrincipal->getrelExcludeCurrentID() !== false) {
			if ($sqlCond != "") {
				$sqlCond .= " and ";
			} else {
				$sqlCond .= " where ";
			}
			$sqlCond .= $pl['::main']['id']."!='".$this->currentValue."'";
		}
		if (isset($pl['::main']['pltCond']) && !empty($pl['::main']['pltCond'])) {
			if ($sqlCond != "") {
				$sqlCond .= " and ";
			} else {
				$sqlCond .= " where ";
			}
			$sqlCond .= $pl['::main']['pltCond']." ";
		}
		if (isset($this->selectedConf['pltCond']) && !empty($this->selectedConf['pltCond'])) {
			if ($sqlCond != "") {
				$sqlCond .= " and ";
			} else {
				$sqlCond .= " where ";
			}
			$sqlCond .= $this->selectedConf['pltCond']." ";
		}
		if (isset($leftWhere) && is_array($leftWhere)) {
			if ($sqlCond != "") {
				$sqlCond .= ' and ';
			} else {
				$sqlCond .= ' where ';
			}
			$sqlCond .= implode("and ", $leftWhere) . " ";
		}
       if (isset($this->selectedConf['search'])) {
            list($aConds2, $aHaving2) = $this->buildMassConds();

            if (sizeof($aConds2)>0) {
                if ($sqlCond != "") {
                    $sqlCond .= ' and ';
                } else {
                    $sqlCond .= ' where ';
                }
                $sqlCond .= implode("and ", $aConds2 ) . "  ";
            }
        }
		$sql .= $sqlCond;

		if (isset($pl['::main']['order']) && !empty($pl['::main']['order'])) {
			$sql .= ' order by ' . $pl['::main']['order'] . ' ';
		}

		if (isset($_GET['DEBUG'])) {
			iError::warn("<textarea>" . $sql . "</textarea>");
		}



		return $sql;
	}

	protected function getrelSQL()
	{
		$pl = $this->plantillaMass;

		$sql = " select " . $this->plantillaPrincipal->getrelID() . " as __IDREL, ";
		if ($this->plantillaPrincipal->getrelIDmain() !== false) {
			$sql .= $this->plantillaPrincipal->getrelIDmain() . " as __ID, ";
		} else {
			$sql .= $this->plantillaPrincipal->getID() . " as __ID, ";
		}
		if ($this->plantillaPrincipal->getrelIDmass() !== false) {
			$sql .= $this->plantillaPrincipal->getrelIDmass() . " as __IDMASS ";
		} else {
			$sql .= $pl['::main']['id'] . " as __IDMASS ";
		}
		if ($this->relDataPlt != false) {
			for ($i=0; $i<$this->plantillaRelData->getNumFields(); $i++) {
				if (in_array($this->plantillaRelData->fields[$i]->getIndex(), $this->getNoShownFields())) {
				    continue;
				}
				$sql .= ", " . $this->plantillaRelData->fields[$i]->getSQL($this->plantillaRelData->getTab());
				if ($this->plantillaRelData->fields[$i]->hasSubFields()) {
					for ($j=0; $j<$this->plantillaRelData->fields[$i]->sizeofsubFields; $j++) {
					    $theTab = $this->plantillaRelData->getTab();
						$sql .= ",";
						$sql .= $this->plantillaRelData->fields[$i]->subFields[$j]->getSQL($theTab);
					}
				}
			}
		}

		$sql .= " from " . $this->plantillaPrincipal->getrelTAB();
		if ($this->plantillaPrincipal->getrelIDmain() !== false) {
			$sql .= " where " . $this->plantillaPrincipal->getrelIDmain() . " = '" . $this->currentValue . "' ";
		} else {
			$sql .= " where " . $this->plantillaPrincipal->getID() . " = '" . $this->currentValue . "' ";
		}

		if (isset($this->selectedConf['hideifnotroladmin'])
		   && !in_array($this->selectedConf['hideifnotroladmin'], $_SESSION["__IDROL"])) {
			if ($this->plantillaPrincipal->getrelIDmass() !== false) {
			    $relidMass = $this->plantillaPrincipal->getrelIDmass();
			    $vlrMass = $this->selectedConf['hideifnotroladmin'];
				$sql .= ' and ';
				$sql .= $relidMass . '!=\''. $vlrMass . '\'';
			} else {
				$sql .= ' and ' . $pl['::main']['id'] . '!=\'' . $this->selectedConf['hideifnotroladmin'] . '\'';
			}
		}
		if (isset($_GET['DEBUG'])) {
		    iError::warn("<textarea>" . $sql . "</textarea>");
		}

		return $sql;
	}

	protected function dorelSQL()
	{
		$this->relcon = new con($this->getrelSQL());
		return !$this->relcon->error();
	}

	protected function drawrelErrors()
	{
		iError::error($this->relcon->getError());
		switch ($this->relcon->getErrorNumber()) {
			case 1146:
			    $strError = "
                <textarea rows='20' cols='80' class='warn'>" .$this->getCreateRelSQL() ."</textarea>";
				iError::warn($strError);
                break;
		}
	}
	protected function getCreateRelSQL()
	{
		$pl = $this->plantillaMass;
		$cSQL = "CREATE TABLE " . $this->plantillaPrincipal->getrelTAB() . "(\n";
		$cSQL .= $this->plantillaPrincipal->getrelID() . " mediumint(8) unsigned not null auto_increment,\n";
		$cSQL .= $this->plantillaPrincipal->getID() . " mediumint(8) unsigned not null,\n";
		$cSQL .= "index (" . $this->plantillaPrincipal->getID() . "),\n";
		$cSQL .= "foreign key(" . $this->plantillaPrincipal->getID() . ") ";
        $cSQL .= "references " . $this->plantillaPrincipal->getTab() . " ";
        $cSQL .= "(" . $this->plantillaPrincipal->getID() . ") ";
        $cSQL .= "on delete cascade on update cascade,\n";
        $cSQL .= $pl['::main']['id'] . " mediumint(8) unsigned not null,\n";
        $cSQL .= "index(" . $pl['::main']['id'] . ") ,\n";
        $cSQL .= "foreign key(" . $pl['::main']['id'] . ") ";
        $cSQL .= "references " . $pl['::main']['tab']." ";
        $cSQL .= "(" . $pl['::main']['id'] . ") ";
        $cSQL .= "on delete cascade on update cascade,\n";
        $cSQL .= "primary key(" . $this->plantillaPrincipal->getrelID() . ")\n";
        $cSQL .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		return $cSQL;
	}

    protected function buildConds()
    {
        $aConds = array();
        $pl = &$this->plantillaPrincipal;
        if ($del = $pl->getDeletedFLD()) {
            $aConds[] = ' '.$del.'=\'0\'';
        }

        $aIds = explode(",", $this->currentValue);

        $aMiniConds = array();
        for ($i=0; $i<sizeof($aIds); $i++) {
            $this->aIds[] = $aIds[$i];
            $aMiniConds[] = ' ' . $pl->getTab() . '.' . $this->plantillaPrincipal->getID() . '=\'' . $aIds[$i] . '\'';
        }
        $aConds[] = '(' . implode(" or ", $aMiniConds) . ')';
        if (sizeof($aConds)>0) {
            return " where " . implode(' and ', $aConds);
        } else {
            return "";
        }
    }

    protected function buildMassConds()
    {

        if (isset($this->selectedConf['search'])) {

        $aConds = array();
        $aHaving = array();
        $aCondSearch = array();
        $aHaveSearch = array();

        if (!isset($_SESSION['searchMass'][$this->currentSection])) {
            $_SESSION['searchMass'][$this->currentSection] = array();
        }
        if (!isset($_SESSION['searchMass'][$this->currentSection][$this->currentFather])) {
            $_SESSION['searchMass'][$this->currentSection][$this->currentFather] = array();
        }
        if (!isset($_SESSION['searchMass'][$this->currentSection][$this->currentFather][$this->currentValue])) {
            $_SESSION['searchMass'][$this->currentSection][$this->currentFather][$this->currentValue] = array();
        }

        $sess = &$_SESSION['searchMass'][$this->currentSection][$this->currentFather][$this->currentValue];
        if ((isset($_POST['submit'])) && ($_POST["submit"] == 'cancel') ) {
            $sess = array();
            return(array($aConds, $aHaving));
        }


        $pl = new tablon_plantilla($this->rutaPlantillas.$this->massPlt);
        //$pl = $this->plantillaPrincipal;
        $searchFields = explode(",", $this->selectedConf['search']);

        for ($i=0; $i < $pl->getNumFields(); $i++) {
            if (!in_array($pl->fields[$i]->getIndex(), $searchFields)) {
                continue;
            }
            $currentFldSearch = $pl->fields[$i]->getSQLFLDSearch();

            $var = '';
            if (isset($_REQUEST[$currentFldSearch])) {
                $var = $_REQUEST[$currentFldSearch];
            } else if (isset($sess[$currentFldSearch])) {
                $var = $sess[$currentFldSearch];
            }

            if ( ($var != "") && ($var != NULL)  && ($var != "NULL") ) {
                $pl->fields[$i]->setSearchValue($var);
                $sess[$currentFldSearch] = $pl->fields[$i]->getSearchValue();
                $searchField = $pl->fields[$i]->getSearchOp();
                if (is_array($searchField)) {
                    $currentCond = "(".$pl->fields[$i]->getSQLFLDSearch(true). ' '.$searchField['op'].' '
                                   . implode(
                                       " or " . $pl->fields[$i]->getSQLFLDSearch(true) . ' '.$searchField['op'].' ',
                                       $searchField['vals']
                                   )
                                   .")";
                } else {
                    $currentCond = $pl->fields[$i]->getSQLFLDSearch(true) . $searchField;
                }
                switch ($pl->fields[$i]->getSearchVarType()) {
                    case 'aCondSearch':
                        $aCondSearch[] = $currentCond;
                        break;
                    case 'aHaveSearch':
                        $aHaveSearch[] = $currentCond;
                        break;
                }
            } else {
                if (isset($sess[$currentFldSearch])) {
                    unset($sess[$currentFldSearch]);
                }
            }
        }

        if (sizeof($aCondSearch)>0) {
            $aConds[] = ' ('.implode(' and ', $aCondSearch).')';
        }
        if (sizeof($aHaveSearch) > 0) {
            $aHaving[] = ' ('.implode(' and ', $aHaveSearch).')';
        }
        //var_dump($aConds, $aHaving);
        return array($aConds, $aHaving);
        }

    }

	protected function doSQL()
	{
		$this->conPrinc = new con($this->getSQL());
		return !$this->conPrinc->error();
	}

	/* Aunque los parámetros no se usan, como esta clase extiende de tablón,
	 * y en ésta la función los lleva, pues hay que ponerlas */
	protected function drawTableContents($checkBox = false,$csv = false)
	{
		$pl = &$this->plantillaPrincipal;
		$flagEntrada = true;
		$rAux = false;
		$this->getrelSQL();
		$plr = new con($this->getmassSQL());

		if ($plr->getNumRows()<=0) {
			iError::warn("La tabla a relacionar no tiene contenidos.");
			return false;
		}
		$aRel=array();
		$aRelD = array();
		while ($r = $this->relcon->getResult()) {
			$aRel[$r['__IDMASS']] = $r['__IDREL'];
			foreach ($r as $id=>$v) {
				if ($id !="__IDMASS" && $id!="__IDREL" && $id!= "__ID") {
					$aRelD[$r['__IDMASS']][$id] = $v;
				}
			}
		}

		while ($r = $this->conPrinc->getResult()) {
			$a = $pl->getPreForm();
			$param6  = '0';
			foreach ($r as $al=>$vlr) {
			    $a = str_replace($al, $vlr, $a);
			}
			echo '<tr class="tablon_tr_header">';
			echo '<th class="cabecera">' . $pl->getPreForm() . '</th>';
			$cont = 1;
			echo '
                <th class="multiselect" id="mlMASTER">
                    <input type="checkbox" id="chk_msMASTERMASS"/>
                </th>';
			$htmlCodeRel = "";
			if ($this->relDataPlt != false && $this->plantillaRelData != false) {
				$param6 = $this->relDataPlt;
				for ($i=0; $i<$this->plantillaRelData->getNumFields(); $i++) {
					if ($this->isNoShown($this->plantillaRelData->fields[$i]->getIndex())) {
					    continue;
					}
					$htmlCodeRel .= '<th class="tablon_edit" >';
					$htmlCodeRel .= $this->plantillaRelData->fields[$i]->getAlias();
					$htmlCodeRel .= '</th>';
				}
				if ($this->selectedConf["reldatadraworder"] == "first") {
				    echo $htmlCodeRel;
				}
			}
			foreach ($this->plantillaMass as $id=>$v) {
				if ($id=="::main") {
				    continue;
				}
				if ($this->isNoShown($id)) {
				    continue;
				}
				echo '<th class="tablon_edit" >';
				echo $this->getMLval($v['alias'], $v, 'alias');
				echo '</th>';
				$cont++;
			}
			if ($htmlCodeRel != "" && ($this->selectedConf["reldatadraworder"] == "last")) {
				echo $htmlCodeRel;
			}
			if ($this->hasOptions() && (isset($this->selectedConf['opsintable'])
			   && $this->selectedConf['opsintable'] != "0")) {
				echo '<th class="tablon_edit" >Opciones</th>';
			}
			echo '</ tr>';

			$aNoEdit = $this->getNoEditFields();
			if (!empty($aNoEdit)) {
				$param7 = implode(",", $this->getNoEditFields());
			} else {
				$param7 = '0';
			}
			while ($r=$plr->getResult()) {
			    $flagEntrada = false;
			    $appendear = "";
                if (isset($aRel[$r['__ID']])) {
                    $appendear = ' checked="checked" ';
                }
                $idParaTd = basename($pl->getFile()) . '::';
                $idParaTd .= $this->massPlt . '::';
                $idParaTd .= $r['__ID'] . '::';
                if (isset($aRel[$r['__ID']])) {
                    $idParaTd .= $aRel[$r['__ID']] . '::';
                } else {
                    $idParaTd .= 'nulo::';
                }
                $idParaTd .= $this->currentValue . '::';
                $idParaTd .= $param6 . '::';
                $idParaTd .= $param7;
				echo '
                <tr>
                    <td class="cabecera">' . (($flagEntrada)? $a : "") . '</td>
                    <td class="multiselect" newid="0" id="' . $idParaTd . '">
                        <input type="checkbox"' . $appendear .' />
                    </td>';

				$htmlCodeRel = "";
				if ($this->relDataPlt != false && $this->plantillaRelData != false) {
					for ($i=0; $i<$this->plantillaRelData->getNumFields(); $i++) {
						if ($this->isNoShown($this->plantillaRelData->fields[$i]->getIndex())) {
						    continue;
						}
						$atributos = 'relAttr = "true"';
						$clase = "tablon_edit";
						if ($this->plantillaRelData->fields[$i]->isRequired()) {
						    $clase .= ' required ';
						}
						if (isset($this->selectedConf['reldataplt_selfeditable'])) {
								$atributos .=' eseditable="true"';
						}
						if (isset($aRelD[$r['__ID']]) && !empty($aRelD[$r['__ID']])) {
							$clase .= ' editable ';
							$clase = 'class="' . $clase . '"';

							$tipoParaTd = 'type = "';
							$tipoParaTd .= $this->plantillaRelData->fields[$i]->getType();
							$tipoParaTd .= '"';

							$idParaTd = 'id="';
							$idParaTd .= basename($this->plantillaRelData->getFile()) . '::';
							$idParaTd .= $this->plantillaRelData->fields[$i]->getIndex() . '::';
							if (isset($aRel[$r['__ID']])) {
							    $idParaTd .=  $aRel[$r['__ID']];
							} else {
							    $idParaTd .=  '::nulo';
							}
							$idParaTd .= '"';

							$aliasFld = $this->plantillaRelData->fields[$i]->getAlias();

							$htmlCodeRel .= '
                        <td ' . $clase . ' ' . $atributos . ' ' . $tipoParaTd . ' ' . $idParaTd . '>' .
                            $this->plantillaRelData->fields[$i]->drawTableValue($aRelD[$r['__ID']][$aliasFld]);
						} else {
						    $claseTd = 'class="tablon_edit"';

                            $relTd = 'relAttr = "true"';

						    $tipoParaTd = 'type="';
						    $tipoParaTd .= $this->plantillaRelData->fields[$i]->getType();
						    $tipoParaTd .= '"';

						    $idParaTd = 'id="';
						    $idParaTd .= basename($this->plantillaRelData->getFile()) . '::';
						    $idParaTd .= $this->plantillaRelData->fields[$i]->getIndex() . '::';
						    if (isset($aRel[$r['__ID']])) {
						        $idParaTd .= $aRel[$r['__ID']];
						    } else {
						        $idParaTd .= 'nulo';
						    }
							$htmlCodeRel .= '
						<td ' . $claseTd . ' ' . $atributos . ' ' . $relTd . ' ' . $tipoParaTd . ' ' . $idParaTd . '">';
						}
						$htmlCodeRel .= '</td>';
					}
					if ($this->selectedConf["reldatadraworder"] == "first") {
					    echo $htmlCodeRel;
					}
				}

				foreach ($r as $id=>$v) {
					if ($id=="__ID") {
					    continue;
					}
					if ($this->isNoShown($id)) {
					    continue;
					}
					echo '<td class="tablon_edit" >';
					echo $v;
					echo '</td>';
				}

				if ($htmlCodeRel != "" && ($this->selectedConf["reldatadraworder"] == "last")) {
					echo $htmlCodeRel;
				}

				if ($this->hasOptions() && (isset($this->selectedConf['opsintable'])
				   && $this->selectedConf['opsintable'] != "0")) {
					if (isset($aRel[$r['__ID']])) {
						if ($opts = $this->drawOptions($aRel[$r['__ID']])) {
						    echo '<td>' . $opts . '</td>';
						}
					} else {
						echo '<td></td>';
					}
				}
				echo '</ tr>';
			}

			$rAux = $r;
		}

		if ($rAux!==false) {
		    echo '<tr id="' . basename($pl->getFile()) . '::' . $rAux['__ID'] . '" >';
		}
		else echo '<tr>';
		echo '<td class="tablon_edit" colspan="' . ($cont+1) . '">';

		echo '</td></tr>';

	}
	public function getNoShownFields()
	{
		if (isset($this->selectedConf['noshow'])) {
		    $this->noShowFields = explode(",", $this->selectedConf['noshow']);
		} else {
		    $this->noShowFields = array();
		}
		return $this->noShowFields;
	}

	public function drawGeneralOps()
	{
		echo '<ul id="optsTablon" class="tablon_edit_opts">';
		echo '<li class="optsIni">&nbsp;</li>';
		echo '<li class="optsEnd">&nbsp;</li>';
		echo '<li class="opts backButton">' . $this->drawBackLink() . '</li>';
		echo '</ul>';
	}

    protected function drawSearcher()
    {
        if (isset($_GET['DEBUG'])) {
            echo '<form action="'.krm_menu::getURL().$this->getHistoryURL().'&DEBUG='.$_GET['DEBUG'].'" method="post">';
        } else {
            echo '<form action="'.krm_menu::getURL().$this->getHistoryURL().'" method="post">';
        }
        echo '<input type="hidden" name="do" value="search" />';
        echo '<input type="hidden" name="manualLimit" value="0" />';
        echo '<div id="tablonsearchnew" >';
        echo '<table id="tablonsearchnewtable" class="tablon">';
        echo '<tr class="tablon_tr_header"><th class="colspaned" colspan="2">';

        echo '<a id="__hide_filtrado" class="actionOpenClose"><img src="./icons/viewmag-.png" />'
             . $this->l->l('Ocultar Filtrado').'</a>';
        echo '</th></tr>';

        $searchFields = explode(",", $this->selectedConf['search']);



        $pl = new tablon_plantilla($this->rutaPlantillas.$this->massPlt);

        for ($i=0;$i<$pl->getNumFields();$i++) {
            if (!in_array($pl->fields[$i]->getIndex(), $searchFields)) continue;
            $val = $pl->fields[$i]->getSearchValue();

            echo '<tr><td class="">'.$pl->fields[$i]->getAlias().'</td>';
            echo '<td';
            if ($pl->fields[$i]->searchValue!="") echo ' class="used"';
            echo ' class="box"';
            echo '>';

            if ($pl->fields[$i] instanceof tablon_FLDenum) {
                $pl->fields[$i]->setNullable($this->l->l("Seleccionar"));
                $pl->fields[$i]->setDefault(false);
            }
            if (method_exists($pl->fields[$i], "setEditForSearch")) {
                $pl->fields[$i]->setEditForSearch(true);
            }
            echo $pl->fields[$i]->drawTableValueEdit($val);
            if ( ($helpSearchStr = $pl->fields[$i]->getSearchHelp()) !== false) {
                echo '<img class="helpsearch" src="./icons/info.png" title="'
                     . str_replace("\"", "'", $helpSearchStr).'" />';

            }
            if (method_exists($pl->fields[$i], "setEditForSearch")) $pl->fields[$i]->setEditForSearch(false);
            echo '</td></tr>';
        }
        echo '<tr><td colspan="2" class="colspaned act centrar">'
             . '<p class="nopadding left"><button name="submit" value="search">'
             . '<img src="./icons/viewmag.png" />&nbsp;'
             . $this->l->l("Buscar")
             . '</button><button name="submit" value="cancel">'
             . '<img src="./icons/cancel.png" />&nbsp;'
             . $this->l->l('Cancelar Filtrado')
             . '</button></p></td></tr>';
        echo '</table>';
        echo '</div>';
        if (method_exists($this, "showOnlySelected")) {
            echo '<input type="hidden" name="mrs_only_selected" value="'.(($this->showOnlySelected())? ' 1':'0').'" />';
        }
        echo '</form>';
    }
	public function draw()
	{
		echo $this->drawTitle();
		if (!$plantilla = $this->getPlt()) {
			iError::error("Plantilla no encontrada");
			return false;
		}

		$this->plantillaPrincipal = new tablon_plantilla($plantilla);
		if ($this->relDataPlt != false) {
			$this->plantillaRelData = new tablon_plantilla($this->getPltRelData());
		}

		$pl = &$this->plantillaPrincipal;






       if (isset($this->selectedConf['search'])) {
            echo '<p id="__user_filtrado"><img src="./icons/viewmag.png" alt="'
                 . $this->l->l('ut_filtrado').'" />'
                 . $this->l->l('act_filtrado').'</p>';
            $this->drawSearcher();
       }



		echo '
		<div id="tablonContainer">
		  <table id="tablon_edit" class="tablon" genero="' . $pl->getGenero() . '" entidad="' . $pl->getEntidad() . '">';

		if (!$this->doSQL()) {
			$this->drawErrors();
		} else {
			if (!$this->dorelSQL()) {
			    $this->drawrelErrors();
			}
			$this->drawTableContents();
		}
		echo '</table>';
		$this->drawGeneralOps();
		if (isset($this->selectedConf['ops']) && !empty($this->selectedConf['ops'])) {
			if (sizeof($this->selectedConf['ops']) == 1) {
				$k = array_keys($this->selectedConf['ops']);
				$opM = $k[0];
				$url = krm_menu::getURL();
				$url .= $this->getHistoryURL();
				$url .= 'tSec[' . $this->currentSection . '::' . $opM . ']=%id%';
				$class = "opts";
				if ($this->conf[$opM]['class'] == "tablon_edit") {
				    $titulo = $this->conf[$opM]['tit'];
					$li =  '
                        <li class="opts multiopsh hidden">
                            <a href="' . $url . '"  class="' . $class . '" title="' . $titulo . '" id="masseditar">';
					if (isset($this->conf[$opM]['img'])) {
						$li .= '<img src="./icons/_edit.png" alt="' . $this->conf[$opM]['tit'] . '" />';
					}
					$li .= $this->conf[$opM]['tit'];
					$li .= '</a>
					   </li>';
				}
				$lista = '
				<div style="display:none;">
				    <input type="hidden" id="almacen" value="" />
				    <ul id="optsTablon">
				        ' . $li . '
			        </ul>
		        </div>';
				echo $lista;
			}
		}
		echo '</div>';

	}

	public function hola()
	{
	    echo "hola";
    }

	static function foo()
	{
	}
}
