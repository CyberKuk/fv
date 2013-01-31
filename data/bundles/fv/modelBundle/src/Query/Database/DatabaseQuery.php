<?php

namespace Bundle\fv\ModelBundle\Query\Database;

use Bundle\fv\ModelBundle\Query\AbstractQuery;
use Bundle\fv\ModelBundle\Field\Int as FieldInt;
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
        $primaryFieldKeys = $this->getSchema()->getPrimaryKeys();

        if( count($primaryFieldKeys) == 0 )
            throw new \Bundle\fv\ModelBundle\Exception\QueryException("Can't fetch {$this->getModelClassName()} without any primary fields");

        $where = array();

        if( is_array( $key ) ){
            if( count($primaryFieldKeys) == 1 && count($key) != 1 )
                throw new \Bundle\fv\ModelBundle\Exception\QueryException("Key must be value or array with one value, {$this->getModelClassName()} not use composite key");

            if( count($primaryFieldKeys) != count($key) )
                throw new \Bundle\fv\ModelBundle\Exception\QueryException("Key must must include exact " . count($primaryFieldKeys) . " elements for {$this->getModelClassName()} composite key" );

            foreach( $primaryFieldKeys as $fieldKey ){
                if( isset( $key[$fieldKey] ) ){
                    $where[$fieldKey] = $key[$fieldKey];
                    unset( $primaryFieldKeys[$fieldKey] );
                    unset( $key[$fieldKey] );
                }
            }

            foreach( $primaryFieldKeys as $fieldKey => $field ){
                $where[$fieldKey] = array_shift( $key );
            }

        } else {
            if( count($primaryFieldKeys) > 1 )
                throw new \Bundle\fv\ModelBundle\Exception\QueryException("Key must be array, {$this->getModelClassName()} uses composite key");

            $where[key($primaryFieldKeys)] = $key;
        }

        return $this->where( $where )->fetchOne();
    }

    final public function persist( Model $model ) {
        $primaryFields = $model->getPrimaryFields();

        if( count($primaryFields) == 0 ){
            throw new QueryException( "Can't persist Model {$this->getModelClassName()} without any primary key used" );
        }

        if( count($primaryFields) > 1 ){
            foreach( $primaryFields as $key => $field ){
                if( ! $field->asMysql() )
                    throw new QueryException( "Can't persist Model {$this->getModelClassName()} with empty primary key {$key} while composite key used" );
            }
        }

        if( count($primaryFields) == 1 ){
            $pkField = reset($primaryFields);

            if( ! $pkField->get() ){
                if( ! $pkField instanceof FieldInt ){
                    $pkKey = key($primaryFields);
                    throw new QueryException( "Can't persist Model {$this->getModelClassName()} with empty primary key {$pkKey} not autoincrement int field" );
                }

                if( ! $pkField->isAutoincrement() ){
                    $pkKey = key($primaryFields);
                    throw new QueryException( "Can't persist Model {$this->getModelClassName()} with empty primary key {$pkKey} not autoincrement int field" );
                }

                return $this->autoincrementPersist( $model, $pkField );
            }
        }

        return $this->directPersist($model, $primaryFields);
    }

    private function autoincrementPersist( Model $model, FieldInt $pkField ) {
        foreach( $model->getFields() as $fieldKey => $field ){
            if( $field != $pkField ){
                $where[$fieldKey] = $field->asMysql();
            } else {
                $this->andSet( $fieldKey, $field->asMysql() );
            }
        }

        $newPkKey = $this->insert();

        if( empty( $newPkKey ) )
            throw new QueryException("Couldn't obtain autoincrement primary key value");

        $pkField->set( $newPkKey );
        $pkField->setIsChanged( false );

        return true;
    }

    private function directPersist( Model $model, array $primaryFields ) {
        $where = array();
        foreach ($model->getFields() as $fieldKey => $field) {
            if (in_array($fieldKey, $primaryFields)) {
                $where[$fieldKey] = $field->asMysql();
            } else {
                if (!$field->isChanged()) continue;

                $this->andSet($fieldKey, $field->asMysql());
            }
        }

        return $this->where($where)->update();
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
    abstract public function updateAll();
    abstract protected function extract();

    /**
     * @return Model|null
     */
    public function fetchOne(){
        $result = $this->fetchOneAssoc();

        if( $result )
            $result = $this->createModel( $result );

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

    public function getTableName(){
        if( !$this->getSchema()->table )
            return $this->getModelClassName();

        return $this->getSchema()->table->get();
    }
}
