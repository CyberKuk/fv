<?php

namespace fv\Connection\Database\Generator;

use fv\Entity\AbstractEntity;
use fv\Entity\Field;
use fv\Connection\Database\PdoMysql;
use fv\Connection\Database\Generator\Column\DefaultColumnDefinition as ColumnDefinition;

class MysqlGenerator extends EntityGenerator {

    function __construct( PdoMysql $connection ) {
        $this->setConnection( $connection );
    }

    public function generate() {
        foreach( $this->getEntities() as $entity ){
            /** @var $query \fv\Entity\Query\Database\MysqlQuery */
            $query = $this->getConnection()->createQuery()
                ->setEntityClassName( get_class($entity) );

            $fields = $entity->getFields();

            $sql = "CREATE TABLE " . $query->getTableName() . " (";

            foreach( $fields as $key => $field ){
                $columnDefinition = ColumnDefinition::build($field);
                $sql .= "{$key} {$columnDefinition},";
            }

            print $sql;

            die;
        }
    }

}
