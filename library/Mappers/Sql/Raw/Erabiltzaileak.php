<?php

/**
 * Application Model Mappers
 *
 * @package Mappers\Sql
 * @subpackage Raw
 * @author <Lander Ontoria Gardeazabal>
 * @copyright Irontec - Internet y Sistemas sobre GNU/Linux
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Data Mapper implementation for Mintzatu_Model_Erabiltzaileak
 *
 * @package Mappers\Sql
 * @subpackage Raw
 * @author <Lander Ontoria Gardeazabal>
 */
namespace Mappers\Sql\Raw;
class Erabiltzaileak extends MapperAbstract
{
    /**
     * Returns an array, keys are the field names.
     *
     * @param Mintzatu_Model_Erabiltzaileak $model
     * @return array
     */
    public function toArray($model)
    {
        if (! $model instanceof \Mintzatu_Model_Erabiltzaileak) {
            throw new \Exception('Unable to create array: invalid model passed to mapper', 2000);
        }

        $result = array(
            'id_erabiltzaile' => $model->getIdErabiltzaile(),
            'erabiltzailea' => $model->getErabiltzailea(),
            'pasahitza' => $model->getPasahitza(),
            'rekuperatu' => $model->getRekuperatu(),
            'izena' => $model->getIzena(),
            'abizenak' => $model->getAbizenak(),
            'herria' => $model->getHerria(),
            'irudi_izena' => $model->getIrudiIzena(),
            'irudi_tamaina' => $model->getIrudiTamaina(),
            'irudi_mota' => $model->getIrudiMota(),
            'deskribapena' => $model->getDeskribapena(),
            'posta' => $model->getPosta(),
            'facebook' => $model->getFacebook(),
            'twitter' => $model->getTwitter(),
            'jaiotze_data' => $model->getJaiotzeData(),
            'url' => $model->getUrl(),
            'alta' => $model->getAlta(),
            'aldaketa' => $model->getAldaketa(),
            'giltza' => $model->getGiltza(),
            'aktibatua' => $model->getAktibatua(),
        );

        return $result;
    }

    /**
     * Returns the DbTable class associated with this mapper
     *
     * @return Mappers\\Sql\\DbTable\\Erabiltzaileak
     */
    public function getDbTable()
    {
        if ($this->_dbTable === null) {
            $this->setDbTable('Mappers\\Sql\\DbTable\\Erabiltzaileak');
        }

        return $this->_dbTable;
    }

    /**
     * Deletes the current model
     *
     * @param Mintzatu_Model_Erabiltzaileak $model The model to delete
     * @see Mintzatu_Model_DbTable_TableAbstract::delete()
     * @return int
     */
    public function delete($model)
    {
        if (! $model instanceof \Mintzatu_Model_Erabiltzaileak) {
            throw new \Exception('Unable to delete: invalid model passed to mapper', 2000);
        }

        $useTransaction = true;

		try {

			$this->getDbTable()->getAdapter()->beginTransaction();

		} catch (\Exception $e) {

			//Transaction already started
			$useTransaction = false;
		}

        try {

            //onDeleteCascades emulation
            if ($this->_simulateReferencialActions and count($deleteCascade = $model->getOnDeleteCascadeRelationships()) > 0) {

                $depList = $model->getDependentList();

                foreach ($deleteCascade as $fk) {

					$capitzalizedFk = '';
					foreach (explode("_", $fk) as $part) {

						$capitzalizedFk .= ucfirst($part);
					}

                    if (! isset($depList[$capitzalizedFk])) {

                        continue;

                    } else {

                        $relDbAdapName = 'Mappers\\Sql\\DbTable\\' . $depList[$capitzalizedFk]["table_name"];
                        $depMapperName = 'Mappers\\Sql\\' . $depList[$capitzalizedFk]["table_name"];
                        $depModelName = 'Mintzatu_Model_' . $depList[$capitzalizedFk]["table_name"];

                        if ( class_exists($relDbAdapName) and class_exists($depModelName) ) {

                            $relDbAdapter = new $relDbAdapName;
                            $references = $relDbAdapter->getReference('Mappers\\Sql\\DbTable\\Erabiltzaileak', $capitzalizedFk);

                            $targetColumn = array_shift($references["columns"]);
                            $where = $relDbAdapter->getAdapter()->quoteInto( $targetColumn . ' = ?', $model->getPrimaryKey() );

							$depMapper = new $depMapperName;
							$depObjects = $depMapper->fetchList($where);

							if (count($depObjects) === 0) {

								continue;
							}

							foreach ($depObjects as $item) {

								$item->delete();
							}
                        }
                    }
                }
            }

            //onDeleteSetNull emulation
            if ($this->_simulateReferencialActions and count($deleteSetNull = $model->getOnDeleteSetNullRelationships()) > 0) {

                $depList = $model->getDependentList();

                foreach ($deleteSetNull as $fk) {

					$capitzalizedFk = '';
					foreach (explode("_", $fk) as $part) {

						$capitzalizedFk .= ucfirst($part);
					}

                    if (! isset($depList[$capitzalizedFk])) {

                        continue;

                    } else {

                        $relDbAdapName = 'Mappers\\Sql\\DbTable\\' . $depList[$capitzalizedFk]["table_name"];
						$depMapperName = 'Mappers\\Sql\\' . $depList[$capitzalizedFk]["table_name"];
                        $depModelName = 'Mintzatu_Model_' . $depList[$capitzalizedFk]["table_name"];

                        if ( class_exists($relDbAdapName) and class_exists($depModelName) ) {

                            $relDbAdapter = new $relDbAdapName;
                            $references = $relDbAdapter->getReference('Mappers\\Sql\\DbTable\\Erabiltzaileak', $capitzalizedFk);

                            $targetColumn = array_shift($references["columns"]);
                            $where = $relDbAdapter->getAdapter()->quoteInto( $targetColumn . ' = ?', $model->getPrimaryKey() );

							$depMapper = new $depMapperName;
							$depObjects = $depMapper->fetchList($where);

							if (count($depObjects) === 0) {

								continue;
							}

							foreach ($depObjects as $item) {

								$setterName = 'set' . ucfirst($targetColumn);							
								$item->$setterName(null);
								$item->save();
							} //end foreach 

                        } //end if
                    } //end else

                }//end foreach ($deleteSetNull as $fk)
            } //end if

            $where = $this->getDbTable()->getAdapter()->quoteInto('id_erabiltzaile = ?', $model->getIdErabiltzaile());
            $result = $this->getDbTable()->delete($where);

            if ($this->_cache) {

                $this->_cache->remove(get_class($model)."_".$model->getPrimarykey());
            }

			$fileObjects = array();
			$availableObjects = method_exists($model, 'getFileObjects') ? $model->getFileObjects() : array();

			foreach ($availableObjects as $fso) {

				$removeMethod = 'remove' . $fso;
				$model->$removeMethod();
			}
			
			if ($useTransaction) {
            	$this->getDbTable()->getAdapter()->commit();
            }

        } catch (\Exception $e) {
			
			if ($useTransaction) {
			
				$this->getDbTable()->getAdapter()->rollback();
			}
            	
            $result = false;
        }

        return $result;
    }

    /**
     * Saves current row, and optionally dependent rows
     *
     * @param \Mintzatu_Model_Erabiltzaileak $model
     * @param boolean $ignoreEmptyValues Should empty values saved
     * @param boolean $recursive Should the object graph be walked for all related elements
     * @param boolean $useTransaction Flag to indicate if save should be done inside a database transaction
     * @return boolean If the save action was successful
     */
    public function save(\Mintzatu_Model_Erabiltzaileak $model,
        $ignoreEmptyValues = false, $recursive = false, $useTransaction = true, $transactionTag = null
    ) {

		$fileObjects = array();

		$availableObjects = method_exists($model, 'getFileObjects') ? $model->getFileObjects() : array();
		$fileSpects = array();

		foreach ($availableObjects as $item) {

			$objectMethod = 'fetch' . $item;
			$fso = $model->$objectMethod(false);

			if ( !is_null($fso) and $fso->mustFlush() ) {

				$fileObjects[$item] = $fso;
				$specMethod = 'get'.$item.'Specs';
				$fileSpects[$item] = $model->$specMethod();

				$fileSizeSetter = 'set' . $fileSpects[$item]['sizeName'];
				$baseNameSetter = 'set' . $fileSpects[$item]['baseNameName'];
				$mimeTypeSetter = 'set' . $fileSpects[$item]['mimeName'];

   				$model->$fileSizeSetter($fso->getSize())
				      ->$baseNameSetter($fso->getBaseName())
				      ->$mimeTypeSetter($fso->getMimeType());					
			}
		}

        $data = $model->toArray();
        if ($ignoreEmptyValues) {
            foreach ($data as $key => $value) {
                if ($value === null or $value === '') {
                    unset($data[$key]);
                }
            }
        } 

        $mainPrimaryKey = $primary_key = $model->getIdErabiltzaile();
        $success = true;

        if ($useTransaction) {

			try {

				if ($recursive and is_null($transactionTag)) {

					$this->getDbTable()->getAdapter()->query('SET transaction_allow_batching = 1');
				}

				$this->getDbTable()->getAdapter()->beginTransaction();

			} catch (\Exception $e) {
			
				//transaction already started
			}


            $transactionTag = 't_' . rand(1, 999) . str_replace(array('.', ' '), '', microtime());
        }

        unset($data['id_erabiltzaile']);

        try {
            if ($primary_key === null or empty($primary_key)) {
                $mainPrimaryKey = $primary_key = $this->getDbTable()->insert($data);
                if ($primary_key) {
                    $model->setIdErabiltzaile($primary_key);
                } else {
                    Throw new \Exception("Insert sentence did not return a valid primary key", 9000);
                }

				if ($this->_cache) {

					$parentList = $model->getParentList();

					foreach ($parentList as $constraint => $values) {

			            $refTable = $this->getDbTable();

						$ref = $refTable->getReference('Mappers\\Sql\\DbTable\\' . $values["table_name"], $constraint);				
						$column = array_shift($ref["columns"]);

						$cacheHash = 'Mintzatu_Model_' . $values["table_name"]. '_'. $data[$column] .'_' . $constraint;

						if ($this->_cache->test($cacheHash)) {

							$cachedRelations = $this->_cache->load($cacheHash);
							$cachedRelations->results[] = $primary_key;

				            if ($useTransaction) {

								$this->_cache->save($cachedRelations, $cacheHash, array($transactionTag));

				            } else {

				                $this->_cache->save($cachedRelations, $cacheHash);
				            }
						}
					}
				}
            } else {
                $this->getDbTable()
                     ->update($data,
                              array(
                                 'id_erabiltzaile = ?' => $primary_key
                              )
                );
            }

			if (is_numeric($primary_key) and !empty($fileObjects)) {

				foreach ($fileObjects as $key => $fso) {

					$baseName = $fso->getBaseName();

					if (! empty($baseName)) {

						$fso->flush($primary_key);
					}
				}
			}


            if ($recursive) {
                if ($model->getChecks(false) !== null) {
                    $Checks = $model->getChecks();

                    if (!is_array($Checks)) {
                        
                        $Checks = array($Checks);
                    }
                    
                    foreach ($Checks as $value) {
                        $value->setIdErabiltzaile($primary_key)
                              ->save($ignoreEmptyValues, $recursive, false, $transactionTag);
                    }
                }

                if ($model->getLekuak(false) !== null) {
                    $Lekuak = $model->getLekuak();

                    if (!is_array($Lekuak)) {
                        
                        $Lekuak = array($Lekuak);
                    }
                    
                    foreach ($Lekuak as $value) {
                        $value->setIdErabiltzaile($primary_key)
                              ->save($ignoreEmptyValues, $recursive, false, $transactionTag);
                    }
                }

                if ($model->getRelErabiltzaileakByIdErabiltzaile1(false) !== null) {
                    $RelErabiltzaileak = $model->getRelErabiltzaileakByIdErabiltzaile1();

                    if (!is_array($RelErabiltzaileak)) {
                        
                        $RelErabiltzaileak = array($RelErabiltzaileak);
                    }
                    
                    foreach ($RelErabiltzaileak as $value) {
                        $value->setIdErabiltzaile1($primary_key)
                              ->save($ignoreEmptyValues, $recursive, false, $transactionTag);
                    }
                }

                if ($model->getRelErabiltzaileakByIdErabiltzaile2(false) !== null) {
                    $RelErabiltzaileak = $model->getRelErabiltzaileakByIdErabiltzaile2();

                    if (!is_array($RelErabiltzaileak)) {
                        
                        $RelErabiltzaileak = array($RelErabiltzaileak);
                    }
                    
                    foreach ($RelErabiltzaileak as $value) {
                        $value->setIdErabiltzaile2($primary_key)
                              ->save($ignoreEmptyValues, $recursive, false, $transactionTag);
                    }
                }

            }

			if ($success === true) {
				
				foreach ($model->getOrphans() as $itemToDelete) {

					$itemToDelete->delete();
				}
				
				$model->resetOrphans();				
			}

            if ($useTransaction && $success) {

                $this->getDbTable()->getAdapter()->commit();

            } elseif ($useTransaction) {

                $this->getDbTable()->getAdapter()->rollback();

                if ($this->_cache) {

                    $this->_cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($transactionTag));
                }
            }

        } catch (\Exception $e) {
            if ($useTransaction) {
                $this->getDbTable()->getAdapter()->rollback();

                if ($this->_cache) {

                    if ($transactionTag) {

                        $this->_cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($transactionTag));

                    } else {

                        $this->_cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG);
                    }
                }
            }

            Throw $e;
        }

        if ($success and $this->_cache) {

            if ($useTransaction) {

                $this->_cache->save($model->toArray(), get_class($model)."_".$model->getPrimaryKey(), array($transactionTag));

            } else {

                $this->_cache->save($model->toArray(), get_class($model)."_".$model->getPrimaryKey());
            }
        }

		if ($success === true) {

			return $mainPrimaryKey;
		}

        return $success;
    }

    /**
     * Finds row by primary key
     *
     * @param int $primary_key
     * @param Mintzatu_Model_Erabiltzaileak|null $model
     * @return Mintzatu_Model_Erabiltzaileak|null The object provided or null if not found
     */
    public function find($primary_key, $model = null)
    {
        if (!($this->_cache and $this->_cache->test("Mintzatu_Model_Erabiltzaileak_".$primary_key))) {

            $result = $this->getRowset($primary_key);

            if (is_null($result)) {
                return null;
            }

            $row = $result->current();
            $model = $this->loadModel($row, $model);

            if ($this->_cache) {

                $this->_cache->save($model->toArray(), get_class($model)."_".$primary_key);
            }

        } else {

            $tmp = $this->_cache->load("Mintzatu_Model_Erabiltzaileak_".$primary_key);
            $model = $this->loadModel($tmp, $model);
        }

        return $model;
    }

    /**
     * Loads the model specific data into the model object
     *
     * @param \Zend_Db_Table_Row_Abstract|array $data The data as returned from a \Zend_Db query
     * @param Mintzatu_Model_Erabiltzaileak|null $entry The object to load the data into, or null to have one created
     * @return Mintzatu_Model_Erabiltzaileak The model with the data provided
     */
    public function loadModel($data, $entry = null)
    {
        if (!$entry) {
            $entry = new \Mintzatu_Model_Erabiltzaileak();
        }

        if (is_array($data)) {
            $entry->setIdErabiltzaile($data['id_erabiltzaile'])
                  ->setErabiltzailea($data['erabiltzailea'])
                  ->setPasahitza($data['pasahitza'])
                  ->setRekuperatu($data['rekuperatu'])
                  ->setIzena($data['izena'])
                  ->setAbizenak($data['abizenak'])
                  ->setHerria($data['herria'])
                  ->setIrudiIzena($data['irudi_izena'])
                  ->setIrudiTamaina($data['irudi_tamaina'])
                  ->setIrudiMota($data['irudi_mota'])
                  ->setDeskribapena($data['deskribapena'])
                  ->setPosta($data['posta'])
                  ->setFacebook($data['facebook'])
                  ->setTwitter($data['twitter'])
                  ->setJaiotzeData($data['jaiotze_data'])
                  ->setUrl($data['url'])
                  ->setAlta($data['alta'])
                  ->setAldaketa($data['aldaketa'])
                  ->setGiltza($data['giltza'])
                  ->setAktibatua($data['aktibatua']);
        } elseif ($data instanceof \Zend_Db_Table_Row_Abstract || $data instanceof \stdClass) {
            $entry->setIdErabiltzaile($data->{'id_erabiltzaile'})
                  ->setErabiltzailea($data->{'erabiltzailea'})
                  ->setPasahitza($data->{'pasahitza'})
                  ->setRekuperatu($data->{'rekuperatu'})
                  ->setIzena($data->{'izena'})
                  ->setAbizenak($data->{'abizenak'})
                  ->setHerria($data->{'herria'})
                  ->setIrudiIzena($data->{'irudi_izena'})
                  ->setIrudiTamaina($data->{'irudi_tamaina'})
                  ->setIrudiMota($data->{'irudi_mota'})
                  ->setDeskribapena($data->{'deskribapena'})
                  ->setPosta($data->{'posta'})
                  ->setFacebook($data->{'facebook'})
                  ->setTwitter($data->{'twitter'})
                  ->setJaiotzeData($data->{'jaiotze_data'})
                  ->setUrl($data->{'url'})
                  ->setAlta($data->{'alta'})
                  ->setAldaketa($data->{'aldaketa'})
                  ->setGiltza($data->{'giltza'})
                  ->setAktibatua($data->{'aktibatua'});
        }

        $entry->setMapper($this);

        return $entry;
    }
}
