<?php

namespace fv\Entity\Query\Mixin;

use fv\Entity\AbstractEntity;
use fv\Entity\Exception\QueryException;

trait Aggregate {

    private $aggregateKeys = [];
    private $aggregatePrimary = true;

    /**
     * @param $key
     * @return static
     */
    final public function aggregateBy( $key ){
        $this->resetAggregateBy();

        if( is_array($key) ){
            foreach($key as $fieldKey ){
                $this->aggregateBy($fieldKey);
            }
            return $this;
        }

        return $this->andAggregateBy($key);
    }

    /**
     * @param $key
     * @return static
     * @throws \fv\Entity\Exception\QueryException
     */
    final public function andAggregateBy( $key ){
        if( in_array( $key, $this->aggregateKeys ) ){
            throw new QueryException("Query already aggregated by {$key}");
        }

        $this->aggregateKeys[] = $key;
        return $this;
    }

    /**
     * @return array
     */
    final public function getAggregateBy(){
        return $this->aggregateKeys;
    }

    /**
     * @return static
     */
    final public function resetAggregateBy(){
        $this->aggregateKeys = [];
        return $this;
    }

    /**
     * @param bool $bool
     * @return static
     */
    final public function aggregateByPrimary( $bool ){
        $this->aggregatePrimary = (bool)$bool;
        return $this;
    }

    /**
     * @return bool
     */
    final public function isAggregateByPrimary(){
        return $this->aggregatePrimary;
    }

    /**
     * Used for finally reaggregate simple array of entities to use specified keys
     * @param AbstractEntity[] $entities
     */
    final protected function reaggregate( $entities ){
        if( empty( $entities ) ){
            return $entities;
        }

        $keys = $this->getAggregateBy();

        if( $this->isAggregateByPrimary() ){
            /** @var $entity AbstractEntity */
            $entity = reset($entities);
            $primareFields = $entity->getPrimaryFields();

            foreach( $primareFields as $key => $field ){
                if( !in_array( $key, $keys ) )
                    $keys[] = $key;
            }
        }

        if( empty($keys) )
            return $entities;

        $result = array();
        foreach( $entities as $entity ){
            $res = & $result;

            foreach( $keys as $key ){
                $field = $entity->getField($key);
                $value = $field->get();

                if( !isset($res[$value]) )
                    $res[$value] = array();

                $res = & $res[$value];
            }

            // If we aggregate by primary key we don't need additional array dimension
            if( $this->isAggregateByPrimary() ){
                $res = $entity;
            } else {
                $res[] = $entity;
            }
        }

        return $result;
    }
}
