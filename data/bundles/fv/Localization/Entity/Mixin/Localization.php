<?php

namespace Bundle\fv\Localization\Entity\Mixin;

use Bundle\fv\Localization\Exception\LocalizationException;
use Bundle\fv\Localization\Entity\Language;
use fv\Entity\AbstractEntity;

trait Localization {

    /**
     * @field
     * @var \fv\Entity\Field\Relation\Constraint
     */
    protected $localizations;

    public function getLocalization( Language $language ){
        $method = "fetch";
        $class = $this->getLocalizationClassName();

        /** @var $this AbstractEntity */
        $fields = $this->getPrimaryFields();

        if( empty($fields) )
            throw new LocalizationException("Entity must have primary field to use localization");

        foreach( $fields as &$field ){
            $field = $field->get();
        }
        $fields["languageId"] = $language->getId();

        return $class::$method($fields);
    }

    private function getLocalizationClassName(){
        /** @var $this AbstractEntity */
        if( $this->getSchema()->localization )
            $localizationClassName = $this->getSchema()->method;
        else
            $localizationClassName = get_class($this) . "Localization";

        if( !class_exists( $localizationClassName ) ){
            throw new LocalizationException("Localization class '{$localizationClassName}' not found");
        }

        return $localizationClassName;
    }

}
