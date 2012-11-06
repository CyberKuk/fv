<?php

namespace fv\Entity\Query\Mixin;

trait Limit {

    private $limitCount;
    private $limitOffset;

    final public function limit( $count, $offset = null ){
        $this->clearLimit();

        if( $offset && $count ){
            $this->limitCount = (int)$count;
            $this->limitOffset = (int)$offset;
        }
        elseif( !is_null( $count ) ){
            $this->limitCount = (int)$count;
        }

        return $this;
    }

    final public function clearLimit(){
        $this->limitCount = null;
        $this->limitOffset = null;

        return $this;
    }

    final protected function getLimitCount() {
        return $this->limitCount;
    }

    final protected function getLimitOffset() {
        return $this->limitOffset;
    }

}
