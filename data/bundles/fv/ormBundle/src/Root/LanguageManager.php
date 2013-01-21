<?php

namespace OrmBundle\Root;

use fv\Storage\Exception\StorageException;
use fv\Storage\StorageFactory;
use OrmBundle\Root;
use OrmBundle\RootManager;
use OrmBundle\Exception\ManagerException as Exception;

/**
 * @method null|Root getOneByIsDefault()
 * @method null|Root getOneByCode()
 */
class LanguageManager extends RootManager
{
    /** @var Language[] */
    private $languages;
    private $default;

    private $storage = null;

    static $languageCode;

    function __construct($entity){
        parent::__construct($entity);

        try{
            $factory = new StorageFactory;
            $this->storage = $factory->get('cache');
        } catch( StorageException $e ){
            $this->storage = null;
        }

        $this->languages = $this->getAll();

        $this->default = $this->getOneByIsDefault(TRUE);
        if( !$this->default ){
            $this->default = $this->getOneByCode(self::$languageCode);
        }

        if( empty($this->languages) ){
            throw new Exception("No languages found!");
        }
    }

    function getAll(){
        $args = func_get_args();
        if( empty($args) ) {
            if( empty($this->languages) ){
                if( $this->storage )
                    $this->languages = $this->storage->get('_languages');

                if( empty($this->languages) ){
                    $this->reloadCache();
                }
            }

            return $this->languages;
        }
        return call_user_func_array(array('parent', 'getAll'), $args);
    }

    function getByPk($pk, $createNonExist = false) {
        foreach( $this->languages as $lang ) {
            if( $lang->getPk() == $pk )
                return $lang;
        }

        return parent::getByPk($pk, $createNonExist);
    }

    function getDefault(){
        return $this->default;
    }

    protected function getAllByFieldName($fieldName, $value, $condition, $limit = null, $case_sensitive = true){
        $result = array();

        foreach( $this->languages as $language ) {
            if( $language->$fieldName->get() == $value ){
                $result[] = $language;
            }
        }

        return $result;
    }

    function reloadCache(){
        $this->languages = parent::getAll();
        if( $this->storage )
            $this->storage->setCache('_languages', $this->languages);
    }

    public function getCurrentLanguage() {
        return $this->getOneByCode(self::$languageCode);
    }
}
