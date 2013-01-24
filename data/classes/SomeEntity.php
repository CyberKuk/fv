<?php

use Bundle\fv\ModelBundle\Mixin\Record;
use Bundle\fv\ModelBundle\AbstractModel;

/**
 * User: cah4a
 * Date: 23.10.12
 * Time: 12:06
 */
class SomeEntity extends AbstractModel {

    use Record;

    /**
     * @field
     * @var Bundle\fv\ModelBundle\Field\Int
     */
    protected $id;

    /**
     * @field
     * @nullable false
     * @default 1231
     * @var Bundle\fv\ModelBundle\Field\Int
     */
    protected $counter;

    public function setCounter( $counter ) {
        $this->counter->set( $counter );
        return $this;
    }

    /**
     * @return Bundle\fv\ModelBundle\Field\Int
     */
    public function getCounter() {
        return $this->counter->get();
    }

    public function getId() {
        return $this->id->get();
    }


}
