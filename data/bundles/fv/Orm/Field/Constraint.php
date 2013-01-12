<?php

    namespace Bundle\fv\Orm\Field;

    class Constraint extends References{

        protected $foreignEntity;
        protected $currentEntity;
        protected $cache = array();
        protected $refTableName;
        protected $currentEntityKey;
        protected $foreignEntityKey;
        protected $pk;
        protected $toSet;
        protected $foreignKeys;
        protected $autocomplete = false;
        protected $fromJSON = true;

        function __construct( array $fieldSchema, $key ){
            parent::__construct( $fieldSchema, $key );

            $this->foreignEntity = $fieldSchema['entity'];

            $this->currentEntity = $fieldSchema['currentEntity'];

            $ent = array( $this->foreignEntity, $this->currentEntity );
            sort( $ent );

            if( $fieldSchema['local_key'] )
                $this->currentEntityKey = $fieldSchema['local_key'];
            else
                $this->currentEntityKey = mb_strtolower( $this->currentEntity, "utf-8" ) . 'Id';

            if( $fieldSchema['foreign_key'] )
                $this->foreignEntityKey = $fieldSchema['foreign_key'];
            else
                $this->foreignEntityKey = mb_strtolower( $this->foreignEntity, "utf-8" ) . 'Id';


            $this->foreignKeys = array();
        }

        private function getTableName(){
            $this->refTableName = fvManagersPool::get( $this->foreignEntity )->getTableName();

            return $this->refTableName;
        }

        function get(){
            $args = func_get_args();
            $cacheKey = md5( implode( ",", $args ) );

            if( !$this->pk )
                return array();

            if( !isset( $this->cache[$cacheKey] ) ){
                $where = "{$this->foreignEntityKey} = {$this->pk}";
                $args[0] = ( $args[0] ? "({$where}) AND ({$args[ 0 ]})" : $where );

                $this->cache[$cacheKey] = call_user_func_array( array( fvManagersPool::get( $this->foreignEntity ),
                                                                       'getAll' ),
                                                                $args );
            }

            return $this->cache[$cacheKey];
        }

        /** @return fvQuery */
        function select(){
            return fvManagersPool::get( $this->foreignEntity )->select()
                ->where( "{$this->foreignEntityKey} = {$this->pk}" );
        }

        function setRootPk( $value ){
            $this->pk = $value;
        }

        function getEditMethod(){
            return self::EDIT_METHOD_ENTITIES_LIST;
        }

        function asAdorned(){
            $response = "";
            foreach( $this->get() as $entity ){
                if( method_exists( $entity, "asAdorned" ) )
                    $response .= $entity->asAdorned();
                else
                    $response .= (string)$entity . "<br>";
            }
            return $response;
        }

        function getForeigns(){
            return fvManagersPool::get( $this->foreignEntity )
                ->select()
                ->where( Array( $this->foreignEntityKey => $this->pk ) )
                ->fetchAll();
        }

    }