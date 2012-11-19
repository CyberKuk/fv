<?php

namespace Bundle\fv\Localization;

use fv\Entity\AbstractEntity as Entity;

class AbstractEntity extends Entity {

    /**
     * @field
     * @autoincrement true
     * @var \fv\Entity\Field\Primary
     */
    protected $id;

    static private $localizationClassName;

    /**
     * @param LanguageEntity $language
     * @return LocalizationEntity
     */
    public function getLocalization( LanguageEntity $language ){
        $method = "fetch";
        $class = $this->getLocalizationClassName();

        return $class::$method([
            "baseId" => $this->getId(),
            "languageId" => $language->getId()
        ]);
    }

    private function getLocalizationClassName(){
        if( !isset( self::$localizationClassName ) ){
            if( $this->getSchema()->localization )
                $localizationClassName = $this->getSchema()->localization;
            else
                $localizationClassName = get_class($this) . "Localization";

            if( !class_exists( $localizationClassName ) ){
                throw new Exception\LocalizationClassException("Localization class '{$localizationClassName}' not found");
            }

            $class = new $localizationClassName;

            if( ! $class instanceof LocalizationEntity ){
                throw new Exception\LocalizationClassException("Localization class '{$localizationClassName}' must be instance of \\Bundle\\fv\\LocalizationEntity");
            }

            self::$localizationClassName = $localizationClassName;
        }

        return self::$localizationClassName;
    }

    /**
     * @return mixed
     */
    function getId(){
        return $this->id->get();
    }

}
