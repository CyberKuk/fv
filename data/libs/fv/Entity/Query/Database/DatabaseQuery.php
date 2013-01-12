<?php

namespace fv\Entity\Query\Database;

use fv\Entity\Query\AbstractQuery;
use fv\Entity\AbstractEntity as Entity;
use fv\Entity\Field\AbstractField as Field;

use fv\Entity\Exception\QueryException;

abstract class DatabaseQuery extends AbstractQuery {

    use
        \fv\Entity\Query\Mixin\Select,
        \fv\Entity\Query\Mixin\Where,
        \fv\Entity\Query\Mixin\Group,
        \fv\Entity\Query\Mixin\Having,
        \fv\Entity\Query\Mixin\Aggregate,
        \fv\Entity\Query\Mixin\Set,
        \fv\Entity\Query\Mixin\Limit;

    final public function fetch( $key ) {
        $primaryFields = $this->getSchema()->getFields( '\\fv\\Entity\\Field\\Primary' );

        if( count($primaryFields) == 0 )
            throw new \fv\Entity\Exception\QueryException("Can't fetch {$this->getEntityClassName()} without any primary fields");

        $where = array();

        if( is_array( $key ) ){
            if( count($primaryFields) == 1 && count($key) != 1 )
                throw new \fv\Entity\Exception\QueryException("Key must be value or array with one value, {$this->getEntityClassName()} not use composite key");

            if( count($primaryFields) != count($key) )
                throw new \fv\Entity\Exception\QueryException("Key must must include exact " . count($primaryFields) . " elements for {$this->getEntityClassName()} composite key" );

            foreach( $primaryFields as $fieldKey => $field ){
                if( isset( $key[$fieldKey] ) ){
                    $where[$fieldKey] = $key[$fieldKey];
                    unset( $primaryFields[$fieldKey] );
                    unset( $key[$fieldKey] );
                }
            }

            foreach( $primaryFields as $fieldKey => $field ){
                $where[$fieldKey] = array_shift( $key );
            }

        } else {
            if( count($primaryFields) > 1 )
                throw new \fv\Entity\Exception\QueryException("Key must be array, {$this->getEntityClassName()} uses composite key");

            $where[current($primaryFields)] = $key;
        }

        return $this->where( $where )->fetchOne();
    }

    final public function persist( Entity $entity ) {
        $primaryFields = $entity->getPrimaryFields();

        if( count($primaryFields) == 0 ){
            throw new QueryException( "Can't persist Entity {$this->getEntityClassName()} without any primary key used" );
        }

        if( count($primaryFields) > 1 ){
            foreach( $primaryFields as $key => $field ){
                if( ! $field->asMysql() )
                    throw new QueryException( "Can't persist Entity {$this->getEntityClassName()} with empty primary key {$key} while composite key used" );
            }

            // @todo: Adding functionality for support persisting composite keys
            throw new QueryException( "Can't persist Entity {$this->getEntityClassName()}. Not implemented!" );
        }

        $pkKey = key($primaryFields);
        $pkField = reset($primaryFields);
        $pk = $pkField->asMysql();

        if( empty( $pk ) ){
            foreach( $entity->getFields() as $fieldKey => $field ){
                $this->andSet( $fieldKey, $field->asMysql() );
            }

            $newPkKey = $this->insert();

            if( empty( $newPkKey ) )
                return false;

            $pkField->set( $newPkKey );
            $pkField->setIsChanged( false );

            return true;
        } else {
            foreach( $entity->getFields() as $fieldKey => $field ){
                if( $fieldKey == $pkKey )
                    continue;

                if( ! $field->isChanged() )
                    continue;

                $this->andSet( $fieldKey, $field->asMysql() );
            }

            return $this->where( array( $pkKey => $pk ) )->update();
        }
    }

    final public function remove( Entity $entity ) {
        $where = array_map( function( Field $field ){
            return $field->asMysql();
        }, $entity->getPrimaryFields());

        $this->where( $where )->delete();
    }

    abstract public function insert();
    abstract public function delete();
    abstract public function update();
    abstract protected function extract();

    /**
     * @return Entity|null
     */
    public function fetchOne(){
        $result = $this->fetchOneAssoc();

        if( $result )
            $result = $this->createEntity( reset($result) );

        return $result;
    }

    /**
     * @return Entity[]
     */
    public function fetchAll(){
        $results = $this->fetchAssoc();

        if( count( $results ) > 0 ){
            $entities = [];
            foreach( $results as $result ){
                $entities[] = $this->createEntity( $result );
            }
            return $this->reaggregate( $entities );
        } else
            return array();
    }

    /**
     * @return array()
     */
    public function fetchAssoc(){
        return $this->extract();
    }

    public function fetchOneAssoc(){
        $this->limit(1);
        $result = $this->extract();

        if( count( $result ) > 0 )
            return reset($result);
        else
            return null;
    }

    /**
     * @param $map
     * @return Entity
     */
    private function createEntity( $map ){
        $entityName = $this->getEntityClassName();
        /** @var $entity \fv\Entity\AbstractEntity */
        $entity = new $entityName;

        foreach( $entity->getFields() as $key => $field ){
            if( isset( $map[$key] ) ){
                $field->fromMysql($map[$key]);
                unset( $map[$key] );
            }
        }

        foreach( $map as $key => $value ){
            // @todo: save heap fields to entity
        }

        return $entity;
    }

    public function getTableName(){
        return (string)$this->getSchema()->table;
    }
}
