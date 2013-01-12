<?php

namespace fv\Entity\Query\Mixin;

trait Where {

    private $where = array();

    private $whereParams = array();

    /**
     * @param string $statement
     * @param array $params
     * @return static
     * @throws \fv\Entity\Exception\QueryException
     */
    final public function andWhere( $statement, array $params = null ){
        if( is_string($statement) ){
            list( $statement, $params ) = $this->createNamedParams( $statement, $params, ":w".count($this->where)."_" );
            $this->where[] = $statement;
            if( !empty($params) )
                $this->whereParams[] = $params;
        } elseif( is_array($statement) ){
            $where = "";
            foreach( $statement as $key => $value ){
                $where = ( empty($where) ? "" : " and " ) . "{$key} = :{$key}";
            }
            $this->where[] = $where;
            $this->whereParams += $statement;
        } else {
            throw new \fv\Entity\Exception\QueryException( "Unknown where statement type " . gettype($statement) );
        }

        return $this;
    }

    /**
     * @param string $field field name to search in
     * @param array $array array of expected values
     * @param bool $positive search in or search not in
     *
     * @return static
     *
     * @throws \fv\Entity\Exception\QueryException
     */
    final public function andWhereIn( $field, array $array, $positive = true ){
        if( empty($field) )
            throw new \fv\Entity\Exception\QueryException("Where field can not be empty!");

        $operator = $positive ? "IN" : "NOT IN";
        $keys = "";
        $values = array();

        foreach( $array as $key => $value ){
            if( is_numeric($key) )
                $key = ":" . $field . $key;

            $values[ $key ] = $value;
            $keys .= (empty($keys) ? ":" : ",:") . $key;
        }

        return $this->andWhere("{$field} {$operator} ({$keys})", $values);
    }

    /**
     * @param string $statement
     * @param array $params
     *
     * @return static
     */
    final public function where( $statement, array $params = null ){
        return $this->clearWhere()->andWhere( $statement, $params );
    }

    /**
     * clear where scope and add "in" filter
     *
     * @param string $field field name to search in
     * @param array $array array of expected values
     * @param bool $positive
     *
     * @return static
     */
    final public function whereIn( $field, array $array, $positive = true ){
        return $this->clearWhere()->andWhereIn( $field, $array, $positive );
    }

    /**
     * clear where scope and add "not in" filter
     *
     * @param string $field field name to search in
     * @param array $array array of not expected values
     *
     * @return static
     */
    final public function whereNotIn( $field, array $array ){
        return $this->clearWhere()->andWhereIn( $field, $array, false );
    }

    /**
     * @param string $field
     * @param array $array
     *
     * @return static
     */
    final public function andWhereNotIn( $field, array $array ){
        return $this->andWhereIn( $field, $array, false );
    }

    /**
     * @return static
     */
    final public function clearWhere(){
        $this->where = array();
        $this->whereParams = array();
        return $this;
    }

    /**
     * @return array where scopes
     */
    final protected function getWhere(){
        return $this->where;
    }

    /**
     * @return array where params
     */
    final protected function getWhereParams(){
        $return = [];
        foreach( $this->whereParams as $params ){
            foreach( $params as $key => $value ){
                $return[$key] = $value;
            }
        }
        return $return;
    }

    private function createNamedParams( $statement, $params, $keyBase ){
        $key = 0;
        while( strpos($statement, "?") !== false ){
            if( !isset( $params[$key] ) )
                throw new \fv\Entity\Exception\QueryException("Number of params not agreed with given statement");

            $paramKey = $keyBase . $key;

            // Only one replacement!
            $statement = preg_replace( "|\?|", $paramKey, $statement, 1 );

            // Store as named param
            $params[$keyBase] = $params[$key];
            unset( $params[$key] );

            $key ++;
        }

        return array( $statement, $params );
    }
}
