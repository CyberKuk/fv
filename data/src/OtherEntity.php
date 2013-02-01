<?php

use Bundle\fv\ModelBundle\Mixin\Record;
use Bundle\fv\ModelBundle\AbstractModel;

/**
 * @connection default
 *
 * @primaryIndex ( id )
 * @keyIndex ( id )
 */
class OtherEntity extends AbstractModel {

    use Record;

    /**
     * @field ( autoincrement=true )
     * @var Bundle\fv\ModelBundle\Field\Int
     */
    protected $id;

    /**
     * @field ( modelName=SomeEntity, key=foreign )
     * @var Bundle\fv\ModelBundle\Field\Relation\Constraint
     */
    public $somes;

}
