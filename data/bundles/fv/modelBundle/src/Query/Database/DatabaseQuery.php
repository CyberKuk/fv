<?php

namespace Bundle\fv\ModelBundle\Query\Database;

use Bundle\fv\ModelBundle\Query\AbstractQuery;
use Bundle\fv\ModelBundle\AbstractModel as Model;
use Bundle\fv\ModelBundle\Field\AbstractField as Field;

use Bundle\fv\ModelBundle\Exception\QueryException;

abstract class DatabaseQuery extends AbstractQuery {

    use
        \Bundle\fv\ModelBundle\Query\Mixin\Select,
        \Bundle\fv\ModelBundle\Query\Mixin\Where,
        \Bundle\fv\ModelBundle\Query\Mixin\Group,
        \Bundle\fv\ModelBundle\Query\Mixin\Having,
        \Bundle\fv\ModelBundle\Query\Mixin\Aggregate,
        \Bundle\fv\ModelBundle\Query\Mixin\Set,
        \Bundle\fv\ModelBundle\Query\Mixin\Limit;

    final public function fetch( $key ) {
        $primaryFields = $this->getSchema()->getFields( '\\fv\\Model\\Field\\Primary' );

        if( count($primaryFields) == 0 )
            throw new \Bundle\fv\ModelBundle\Exception\QueryException("Can't fetch {$this->getModelClassName()} without any primary fields");

        $where = array();

        if( is_array( $key ) ){
            if( count($primaryFields) == 1 && count($key) != 1 )
                throw new \Bundle\fv\ModelBundle\Exception\QueryException("Key must be value or array with one value, {$this->getModelClassName()} not use composite key");

            if( count($primaryFields) != count($key) )
                throw new \Bundle\fv\ModelBundle\Exception\QueryException("Key must must include exact " . count($primaryFields) . " elements for {$this->getModelClassName()} composite key" );

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
                throw new \Bundle\fv\ModelBundle\Exception\QueryException("Key must be array, {$this->getModelClassName()} uses composite key");

            $where[current($primaryFields)] = $key;
        }

        return $this->where( $where )->fetchOne();
    }

    final public function persist( Model $Model ) {
        $primaryFields = $Model->getPrimaryFields();

        if( count($primaryFields) == 0 ){
            throw new QueryException( "Can't persist Model {$this->getModelClassName()} without any primary key used" );
        }

        if( count($primaryFields) > 1 ){
            foreach( $primaryFields as $key => $field ){
                if( ! $field->asMysql() )
                    throw new QueryException( "Can't persist Model {$this->getModelClassName()} with empty primary key {$key} while composite key used" );
            }

            // @todo: Adding functionality for support persisting composite keys
            throw new QueryException( "Can't persist Model {$this->getModelClassName()}. Not implemented!" );
        }

        $pkKey = key($primaryFields);
        $pkField = reset($primaryFields);
        $pk = $pkField->asMysql();

        if( empty( $pk ) ){
            foreach( $Model->getFields() as $fieldKey => $field ){
                $this->andSet( $fieldKey, $field->asMysql() );
            }

            $newPkKey = $this->insert();

            if( empty( $newPkKey ) )
                return false;

            $pkField->set( $newPkKey );
            $pkField->setIsChanged( false );

            return true;
        } else {
            foreach( $Model->getFields() as $fieldKey => $field ){
                if( $fieldKey == $pkKey )
                    continue;

                if( ! $field->isChanged() )
                    continue;

                $this->andSet( $fieldKey, $field->asMysql() );
            }

            return $this->where( array( $pkKey => $pk ) )->update();
        }
    }

    final public function remove( Model $Model ) {
        $where = array_map( function( Field $field ){
            return $field->asMysql();
        }, $Model->getPrimaryFields());

        $this->where( $where )->delete();
    }

    abstract public function insert();
    abstract public function delete();
    abstract public function update();
    abstract protected function extract();

    /**
     * @return Model|null
     */
    public function fetchOne(){
        $result = $this->fetchOneAssoc();

        if( $result )
            $result = $this->createModel( reset($result) );

        return $result;
    }

    /**
     * @return Model[]
     */
    public function fetchAll(){
        $results = $this->fetchAssoc();

        if( count( $results ) > 0 ){
            $entities = [];
            foreach( $results as $result ){
                $entities[] = $this->createModel( $result );
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
     * @return Model
     */
    private function createModel( $map ){
        $ModelName = $this->getModelClassName();
        /** @var $Model \Bundle\fv\ModelBundle\AbstractModel */
        $Model = new $ModelName;

        foreach( $Model->getFields() as $key => $field ){
            if( isset( $map[$key] ) ){
                $field->fromMysql($map[$key]);
                unset( $map[$key] );
            }
        }

        foreach( $map as $key => $value ){
            // @todo: save heap fields to Model
        }

        return $Model;
    }

    public function getTableName(){
        return $this->getSchema()->table;
    }
}
