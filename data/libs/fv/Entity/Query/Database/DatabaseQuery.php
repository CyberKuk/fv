<?php

namespace fv\Entity\Query\Database;

use fv\Entity\Query\AbstractQuery;
use fv\Entity\AbstractEntity as Entity;
use fv\Entity\Field\AbstractField as Field;

use fv\Entity\Exception\QueryException;

abstract class DatabaseQuery extends AbstractQuery {

    use
        \fv\Entity\Query\Expand\Select,
        \fv\Entity\Query\Expand\Where,
        \fv\Entity\Query\Expand\Group,
        \fv\Entity\Query\Expand\Having,
        \fv\Entity\Query\Expand\Aggregate,
        \fv\Entity\Query\Expand\Set,
        \fv\Entity\Query\Expand\Limit;

    final public function fetch( $key ) {
        $primaryFields = $this->getSchema()->getFields( '\\fv\\Entity\\Field\\Primary' );

        $where = array();

        if( is_array( $key ) ){
            if( count($primaryFields) == 1 && count($key) != 1 )
                throw new \fv\Entity\Exception\QueryException("Key must be value or array with one value, {$this->getEntity()} not use composite key");

            if( count($primaryFields) != count($key) )
                throw new \fv\Entity\Exception\QueryException("Key must must include exact " . count($primaryFields) . " elements for {$this->getEntity()} composite key" );

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
                throw new \fv\Entity\Exception\QueryException("Key must be array, {$this->getEntity()} uses composite key");

            $where[key($primaryFields)] = $key;
        }

        return $this->where( $where )->fetchOne();
    }

    final public function persist( Entity $entity ) {
        $primaryFields = $entity->getPrimaryFields();

        if( count($primaryFields) == 0 ){
            throw new QueryException( "Can't persist Entity {$this->getEntity()} without any primary key used" );
        }

        if( count($primaryFields) > 1 ){
            foreach( $primaryFields as $key => $field ){
                if( ! $field->asMysql() )
                    throw new QueryException( "Can't persist Entity {$this->getEntity()} with empty primary key {$key} while composite key used" );
            }

            // @todo: Adding functionality for support persisting composite keys
            throw new QueryException( "Can't persist Entity {$this->getEntity()}. Not implemented!" );
        }

        $pkKey = reset($primaryFields);

        $pkField = $entity->getField( $pkKey );
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
        $result = $this->limit(1)->extract();

        if( count( $result ) > 0 )
            return $this->createEntity( reset($result) );
        else
            return null;
    }

    /**
     * @return Entity[]
     */
    public function fetchAll(){

    }

    /**
     * @return array()
     */
    public function fetchAssoc(){

    }

    public function fetchOneAssoc(){

    }

    /**
     * @param $row
     * @return Entity
     */
    private function createEntity( $row ){
        $entityName = $this->getEntity();
        $entity = new $entityName;

        return $entity;
    }
}
