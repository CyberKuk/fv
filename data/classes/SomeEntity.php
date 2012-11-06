<?php

use fv\Entity\Mixin;

/**
 * User: cah4a
 * Date: 23.10.12
 * Time: 12:06
 * @table someEntity
 * @connection default
 * @method getSomething()
 */
class SomeEntity extends \fv\Entity\AbstractEntity {

    use Mixin\Record;

    /**
     * @field
     * @var \fv\Entity\Field\Primary
     */
    protected $id;

    /**
     * @field
     * @nullable false
     * @default 1231
     * @var \fv\Entity\Field\Int
     */
    protected $counter;

    public function setCounter( $counter ) {
        $this->counter->set( $counter );
        return $this;
    }

    /**
     * @return \fv\Entity\Field\Int
     */
    public function getCounter() {
        return $this->counter->get();
    }

    public function getId() {
        return $this->id->get();
    }
}
