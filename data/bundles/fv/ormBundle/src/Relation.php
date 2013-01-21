<?php

namespace OrmBundle;

use OrmBundle\Exception\RelationLoaderException as Exception;

class Relation {
    private $condition, $fieldName, $alias, $conditionParams;

    function __construct( $fieldName, $alias = null, $condition = null, $conditionParams = null ){
        $this->setCondition($condition);
        $this->setConditionParams($conditionParams);
        $this->setFieldName($fieldName);
        $this->setAlias($alias);
    }

    public function useWhere( Query $query ){
        $condition = $this->getCondition();

        if( is_null($condition) )
            return;

        if( is_string($condition) ){
            $query->andWhere( $condition, $this->getConditionParams() );
            return;
        }

        if( is_callable($condition) ){
            /** @var $condition \Closure */
            $condition( $query );
            return;
        }

        throw new Exception( "Unknown where condition for relation with type " . gettype($condition) );
    }

    protected function setAlias( $alias ) {
        $this->alias = $alias;
        return $this;
    }

    public function getAlias() {
        return $this->alias;
    }

    protected function setCondition( $condition ) {
        $this->condition = $condition;
        return $this;
    }

    public function getCondition() {
        return $this->condition;
    }

    protected function setFieldName( $fieldName ) {
        $this->fieldName = $fieldName;
        return $this;
    }

    public function getFieldName() {
        return $this->fieldName;
    }

    protected function setConditionParams( $conditionParams ) {
        $this->conditionParams = $conditionParams;
        return $this;
    }

    public function getConditionParams() {
        return $this->conditionParams;
    }
}