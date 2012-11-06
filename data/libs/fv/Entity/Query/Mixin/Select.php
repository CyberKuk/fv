<?php

namespace fv\Entity\Query\Mixin;

trait Select {

    private $select = array();

    /**
     * @param string|array|\Iterator $statement
     *
     * @return Select
     */
    final public function select( $statement ){
        return $this->clearSelect()->addSelect( $statement );
    }

    /**
     * @param string|array|\Iterator $statement
     *
     * @return $this
     * @throws \fv\Entity\Exception\QueryException
     */
    final public function addSelect( $statement ){
        if( is_string( $statement ) ){
            $statement = explode( $statement, "," );
        }
        elseif( ! is_array( $statement ) && ! $statement instanceof \Iterator ){
            throw new \fv\Entity\Exception\QueryException( "Unknown type " . gettype($statement) . " for query select statement. Expect string, array, or iterator interface class." );
        }

        foreach( $statement as $item ){
            $this->select[] = (string)$item;
        }

        return $this;
    }

    final public function clearSelect(){
        $this->select = array();
        return $this;
    }

    final protected function getSelect(){
        return $this->select;
    }

}
