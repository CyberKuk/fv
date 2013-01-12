<?php

namespace Bundle\fv\Localization\Entity;

use fv\Entity\AbstractEntity;

class Language extends AbstractEntity
{

    /**
     * @field
     * @autoincrement true
     * @var \fv\Entity\Field\Primary
     */
    protected $id;

    /**
     * @field
     * @var \fv\Entity\Field\String
     */
    protected $name;

    /**
     * @field
     * @var \fv\Entity\Field\String
     */
    protected $code;

    /**
     * @param string $code
     */
    public function setCode($code) {
        $this->code->set($code);
        return $this;
    }

    /**
     * @return string
     */
    public function getCode() {
        return $this->code->get();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id->get();
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name->set($name);
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name->get();
    }


}
