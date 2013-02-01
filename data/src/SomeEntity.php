<?php

use Bundle\fv\ModelBundle\Mixin\Record;
use Bundle\fv\ModelBundle\AbstractModel;

/**
 * @connection default
 *
 * @primaryIndex ( id )
 * @keyIndex ( id )
 */
class SomeEntity extends AbstractModel {

    use Record;

    /**
     * @field ( autoincrement=true )
     * @var Bundle\fv\ModelBundle\Field\Int
     */
    protected $id;

    /**
     * @field ( nullable=false, default=3 )
     * @var Bundle\fv\ModelBundle\Field\Int
     */
    protected $counter;

    /**
     * @field ( modelName=OtherEntity )
     * @var Bundle\fv\ModelBundle\Field\Relation\Foreign
     */
    public $foreign;

    public function setCounter( $counter ) {
        $this->counter->set( $counter );
        return $this;
    }

    public function getCounter() {
        return $this->counter->get();
    }

    public function getId() {
        return $this->id->get();
    }


}
