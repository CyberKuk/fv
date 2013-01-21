<?php

namespace OrmBundle;

use OrmBundle\Exception\RelationLoaderException as Exception;
use OrmBundle\Field\Foreign as FieldForeign;
use OrmBundle\Field\References as FieldReferences;
use OrmBundle\Field\Constraint as FieldConstraint;

class RelationLoader {

    /** @var RelationLoader[] $dependentRelationLoaders */
    private $dependentRelationLoaders = array();

    /** @var Relation[] $relations */
    private $relations = array();

    /**
     * Загружает добавленные через addRelation связи в массив Root'ов
     * @param $entities
     */
    function load( $entities ){
        foreach( $this->relations as $relation ){
            $this->getRelatedObjects( $entities, $relation );
        }
    }

    /**
     * Загружает добавленные через addIndirectRelation связи в массив Root'ов
     * @param string $alias
     * @param array $relations
     */
    private function loadDependents( $alias, array $relations ){
        if( !isset( $this->dependentRelationLoaders[$alias] ) )
            return;

        if( empty($relations) )
            return;

        $this->dependentRelationLoaders[$alias]->load( $relations );
    }


    /**
     * Добавить прямую связь к загрузке
     * @param $fieldName
     * @param null $alias
     * @param null $condition
     * @throws Exception
     */
    function addRelation( $fieldName, $alias = null, $condition = null, $conditionParams = null ){
        if( $alias && $this->hasRelation($alias) )
            throw new Exception( "Duplicate alias!" );

        $this->relations[] = new Relation( $fieldName, $alias, $condition, $conditionParams );
    }

    /**
     * Добавить не прямую связь к загрузке (Загрузка сущностей в загружаемых сущностях)
     * @param $fromAlias
     * @param $fieldName
     * @param null $alias
     * @param null $condition
     */
    function addIndirectRelation( $fromAlias, $fieldName, $alias = null, $condition = null, $conditionParams = null ){
        $this
            ->getRelationLoaderByAlias( $fromAlias, $fieldName )
            ->addRelation( $fieldName, $alias, $condition, $conditionParams );
    }

    /**
     * Возвращает существует ли прямая связь по заданному $alias
     * @param $alias
     * @return bool
     */
    function hasRelation( $alias ){
        foreach( $this->relations as $relation ){
            /** @var $relation Relation */
            if( $relation->getAlias() == $alias )
                return true;
        }

        return false;
    }

    /**
     * @param Root[] $entities
     */
    private function getRelatedObjects( array $entities, Relation $relation ){
        if( empty($entities) )
            return array();

        /** @var $entity Root */
        $entity = $this->getFirstEntityFromArray( $entities );

        if( ! $entity instanceof Root )
            throw new Exception( "Can't load Related Objects to not Root array");

        $field = $entity->getField( $relation->getFieldName() );

        if( $field instanceof FieldConstraint ){
            /** @var $field FieldConstraint */
            $manager = ManagersPool::get( $field->getForeignEntity() );
            $key = $field->getForeignEntityKey();

            $ids = $this->getPks( $entities );
            if (!($alias = $relation->getAlias())) {
                $alias = 'root';
            }

            $query = $manager->query( $alias )->whereIn($key, $ids)->aggregateBy($key);

            $relation->useWhere($query);

            $relations = $query->execute();
            $this->loadDependents( $relation->getAlias(), $relations );
            $this->fillRelatedObjectsToConstraint( $entities, $relations, $relation );
            return true;
        }

        if( $field instanceof FieldReferences ){
            /** @var $field FieldReferences */
            $manager = ManagersPool::get( $field->getForeignEntity() );
            $currentKey = $field->getCurrentEntityKey();
            $foreignKey = $field->getForeignEntityKey();
            $refTableName = $field->getReferenceTableName();

            $ids = $this->getPks( $entities );

            if( !empty($ids) ){
                $ids = implode( ",", $ids );
                $relations = Query::getDriver()->query("SELECT {$currentKey}, {$foreignKey} FROM {$refTableName} WHERE {$currentKey} IN ({$ids})")->fetchAll( \PDO::FETCH_GROUP | \PDO::FETCH_COLUMN );

                $foreignKeys = array();
                foreach( $relations as $foreignArray ){
                    foreach( $foreignArray as $foreignKey ){
                        $foreignKeys[$foreignKey] = null;
                    }
                }
                $foreignObjs = $manager->select()
                    ->whereIn( 'root.'.$manager->getRootObj()->getPkName(), array_keys($foreignKeys) )
                    ->aggregateBy($manager->getRootObj()->getPkName() )
                    ->execute();

                foreach( $relations as &$foreignArray ){
                    foreach( $foreignArray as &$foreign ){
                        $foreign = $foreignObjs[$foreign];
                    }
                }

                $this->loadDependents( $relation->getAlias(), $relations );
                $this->fillRelatedObjectsToConstraint( $entities, $relations, $relation );
            }

            return true;
        }

        if( $field instanceof \OrmBundle\Field\Foreign ){
            $ids = $this->getValues( $entities, $relation->getFieldName() );

            /** @var $field FieldForeign */
            $manager = ManagersPool::get( $field->getForeignEntityName() );
            $query = $manager->select()
                ->whereIn('root.'.$manager->getRootObj()->getPkName(), $ids )
                ->aggregateBy($manager->getRootObj()->getPkName());

            $relations = $query->execute();
            $this->loadDependents( $relation->getAlias(), $relations );
            $this->fillRelatedObjectsToForeign( $entity, $relations, $relation );
            return true;
        }

        throw new Exception( "Can't load related objects by unknown field type " . get_class($field) );
    }

    private function getPks( array $entites ){
        $ids = array();

        foreach( $entites as $entity ){
            if( is_array($entity) )
                $ids = array_merge( $ids, $this->getPks( $entity ) );

            if( $entity instanceof Root ){
                /** @var $entity Root */
                $ids[] = $entity->getPk();
            }
        }

        return $ids;
    }

    private function getValues( array $entites, $fieldName ){
        $ids = array();

        foreach( $entites as $entity ){
            if( is_array($entity) )
                $ids = array_merge( $ids, $this->getValues( $entity, $fieldName ) );

            if( $entity instanceof Root ){
                /** @var $entity Root */
                $ids[] = $entity->getField($fieldName)->get();
            }
        }

        return $ids;
    }

    private function getFirstEntityFromArray( $entites ){
        if( is_array($entites) ){
            return $this->getFirstEntityFromArray( current($entites) );
        }

        if( $entites instanceof Root )
            return $entites;

        return null;
    }

    private function fillRelatedObjectsToConstraint( array $entities, array $relations, Relation $relation ){
        if( empty($entities) )
            return;

        if( is_array(current($entities)) ){
            foreach( $entities as $entitiesArray ){
                $this->fillRelatedObjectsToConstraint( $entitiesArray, $relations, $relation );
            }
            return;
        }

        /** @var $entity Root */
        foreach( $entities as $entity ){
            /** @var $constraint FieldConstraint */
            $constraint = $entity->getField($relation->getFieldName());
            if (!empty($relations[$entity->getPk()])){
                $currentRelation = $relations[$entity->getPk()];
            } else {
                $currentRelation = array();
            }

            $constraint->loadCache( md5(''), $currentRelation ); // @todo: Дописать мазанный CACHEKEY
        }
    }

    private function fillRelatedObjectsToForeign( Root $entity, array $relations, Relation $relation ){
        /** @var $field FieldForeign */
        $field = $entity->getField($relation->getFieldName());
        $field->preloadCache( $field->getForeignEntityName(), $relations );
    }

    /**
     * @param $alias
     * @return RelationLoader
     */
    private function getRelationLoaderByAlias( $alias, $throwException = true ){
        if( $this->hasRelation( $alias ) ){
            if( !isset($this->dependentRelationLoaders[$alias]) )
                $this->dependentRelationLoaders[$alias] = new RelationLoader;

            return $this->dependentRelationLoaders[$alias];
        }

        foreach( $this->dependentRelationLoaders as $relationLoader ){
            $loader = $relationLoader->getRelationLoaderByAlias( $alias, false );
            if( $loader instanceof RelationLoader ){
                return $loader;
            }
        }

        if( $throwException )
            throw new Exception("Unknown relation alias {$alias}");

        return false;
    }

}
