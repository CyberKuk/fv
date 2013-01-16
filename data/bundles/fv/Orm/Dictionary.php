<?php

namespace Bundle\fv\Orm;

use Bundle\fv\Orm\Root\Language;
use Bundle\fv\Orm\Root\LanguageManager;
use Bundle\fv\Storage\Storage;
use Bundle\fv\Orm\Root\Dictionary as RootObj;

/**
 * Словарь
 */
class Dictionary {
    
    const NO_TRANSLATION = "<i>Нет перевода</i>";

    /** @var RootObj[] */
    private $dictionary;
    static private $instance;

    /** @var Storage */
    private $storage;

    /**
     * @returns Dictionary
     */
    private function __construct() {
        if( $this->storage )
            $dict = $this->storage->get("__dictionary_" . LanguageManager::$languageCode);

        if( empty($dict) ){
            /** @var $list RootObj[] */
            $list = RootObj::getManager()->getAll();

            $dict = array();
            foreach ($list as $e) {
                $dict[(string) $e->keyword] = $e;
            }

            $this->saveCache();
        }

        $this->dictionary = $dict;

        return $this;
    }

    /**
     * @return self
     */
    public static function getInstance() {
        static $instance;

        if ( ! $instance instanceof self )
            $instance = new self;

        return $instance;
    }

    public function __get($key) {
        return $this->get($key);
    }

    public function get($key, $default = null) {
        if (isset($this->dictionary[$key])) {
            /** @var $translationField \Bundle\fv\Orm\Field\String */
            $translationField = $this->dictionary[$key]->translation;
            if (strlen($translationField->get()))
                return $translationField->get();

            return $default ? $default : $this->noTranslation($key);
        }

        $dbCheck = RootObj::getManager()->select()->where( array( "keyword" => $key ) )->fetchOne();
        if( $dbCheck instanceof Dictionary ){
            $this->dictionary[$key] = $dbCheck;
            $this->saveCache();
            return $this->dictionary[$key]->translation;
        }

        $this->createElement($key, $default);

        return $default ? $default : $this->noTranslation($key);
    }

    private function noTranslation($key) {
        return $key;
    }

    /**
     * save new macros
     * @param string $key macros key
     * @return bool
     */
    private function createElement($key, $default = null) {
        $iDictionary = new RootObj;
        $iDictionary->keyword = $key;
        $this->dictionary[$key] = $iDictionary;
        if ($default) {
            $iDictionary->save();
            $iDictionary->setLanguage(LanguageManager::$languageCode);
            $iDictionary->translation->set($default);
        }
        $this->saveCache();
        $iDictionary->save();
    }

    public function dropCache(){
        if( ! $this->storage )
            return;

        $langs = Language::getManager()->getAll();

        foreach( $langs as $l )
            $this->storage->set("__dictionary_" . $l->code, null);
    }

    public function saveCache(){
        if( ! $this->storage )
            return;

        $this->storage->set( "__dictionary_" . LanguageManager::$languageCode, $this->dictionary );
    }
}
