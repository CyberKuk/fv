<?php

use fv\Entity\Mixin\Record;

/**
 * User: cah4a
 * Date: 23.10.12
 * Time: 12:06
 */
class SomeEntity extends \fv\Entity\AbstractEntity {

    use Record, Bundle\fv\Localization\Entity\Mixin\Localization;

    /**
     * @field
     * @primary true
     * @var \fv\Entity\Field\Int
     */
    protected $id;

    /**
     * @field
     * @nullable false
     * @default 1231
     * @var \fv\Entity\Field\Int
     */
    protected $counter;

    /**
     * @index
     * @var \fv\Connection\Database\Index\Primary
     */
    protected $primaryIndex;

    /**
     * @index
     * @var \fv\Connection\Database\Index\Key
     */
    protected $counterIndex;

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
