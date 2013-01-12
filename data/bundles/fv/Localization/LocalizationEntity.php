<?php

namespace Bundle\fv\Localization;

use fv\Entity\AbstractEntity as Entity;

class LocalizationEntity extends Entity {

    /**
     * @field
     * @autoincrement false
     * @var \fv\Entity\Field\Primary
     */
    protected $baseId;

    /**
     * @field
     * @autoincrement false
     * @var \fv\Entity\Field\Primary
     */
    protected $languageId;

    private static $baseClassName;

    public function getEntity(){
        $class = $this->getBaseClassName();
        $method = 'fetch';

        return $class::$method( $this->getBaseId() );
    }

    private function getBaseClassName(){
        if( !isset( self::$baseClassName ) ){
            if( $this->getSchema()->localization )
                $baseClassName = $this->getSchema()->localization;
            else
                $baseClassName = get_class($this) . "Localization";

            if( !class_exists( $baseClassName ) ){
                throw new Exception\LocalizationException("Localization class '{$baseClassName}' not found");
            }

            $class = new $baseClassName;

            if( ! $class instanceof LocalizationEntity ){
                throw new Exception\LocalizationException("Localization class '{$baseClassName}' must be instance of \\Bundle\\fv\\AbstractEntity");
            }

            self::$baseClassName = $baseClassName;
        }

        return self::$baseClassName;
    }

    /**
     * @param int $externalId
     */
    public function setBaseId($externalId)
    {
        $this->baseId->set($externalId);
        return $this;
    }

    /**
     * @return int
     */
    public function getBaseId()
    {
        return $this->baseId->get();
    }

    /**
     * @param int $languageId
     */
    public function setLanguageId($languageId)
    {
        $this->languageId->set($languageId);
        return $this;
    }

    /**
     * @return int
     */
    public function getLanguageId()
    {
        return $this->languageId->get();
    }


}
