<?php

namespace fv\Entity\Query\Expand;

trait Where {

    private $where = array();

    private $whereParams = array();

    final public function resetWhere(){
        return $this;
    }

    final public function where( $mixed, $params = null ){
        return $this;
    }

    final public function whereIn( $field, $array ){
        return $this;
    }

    final public function andWhere(){
        return $this;
    }

    final public function clearWhere(){
        return $this;
    }

    final public function getWhere(){
        return $this->where;
    }

    final public function getWhereParams(){
        return $this->whereParams;
    }
}
