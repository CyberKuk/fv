<?php

namespace fv\Entity\Query\Mixin;

trait Group {

    private $group = array();

    final public function groupBy( $field ) {
        return $this->clearGroup()->andGroupBy( $field );
    }

    final public function andGroupBy( $field ) {
        $this->group[] = $field;
        return $this;
    }

    final public function clearGroup(){
        $this->group = array();
        return $this;
    }

    final protected function getGroup() {
        return $this->group;
    }
}
