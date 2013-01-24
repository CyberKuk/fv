<?php

namespace Bundle\fv\ModelBundle\Query\Mixin;

trait Having {

    private $having = null;
    private $havingParams = array();

    final public function having( $statement = null, array $params = array() ) {
        $this->having = $statement;
        $this->havingParams = $params;

        return $this;
    }

    final public function clearHaving(){
        return $this->having();
    }

    final protected function getHaving() {
        return $this->having;
    }

    final protected function getHavingParams() {
        return $this->havingParams;
    }


}
