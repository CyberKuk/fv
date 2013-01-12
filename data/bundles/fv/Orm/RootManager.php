<?php

    namespace Bundle\fv\Orm;

    use Bundle\fv\Orm\Exception\ManagerException as Exception;

    class RootManager{

        /** @var Root */
        protected $rootObj = null;
        
        /** @var Root[] */
        protected $rootSubclassesObjs = array();
        protected $modifier = array();
        static $instace = null;
        private $useQmodifier = true;
        private $queryObj = false;

        private $subclass;

        const GET_EQUAL = '=';
        const GET_NOT_EQUAL = '<>';
        const GET_GT = '>';
        const GET_GTE = '>=';
        const GET_LT = '<';
        const GET_LTE = '<=';
        const GET_LIKE = 'LIKE';
        const GET_NOT_LIKE = 'NOT LIKE';

        const GET_CHECK_CASE = 'cc';
        const GET_UNCHECK_CASE = 'ucc';

        public function __construct( $entity ){
            if( $entity == "fvUser" ){
                debug_print_backtrace();
                die;
            }
            $this->rootObj = new $entity;
            /*
            if( fvSite::$fvConfig->get( "qModifier" ) ){
                foreach( fvSite::$fvConfig->get( "qModifier" ) as $iEntity => $modificators ){
                    if( $this->rootObj->isImplements( $iEntity ) )
                        foreach( $modificators as $type => $modifier ){
                            $this->modifier[$type][] = $modifier;
                        }
                }
            }*/
        }

        /**
         * return entity created from
         * @return Root
         */
        public function getEntity(){
            return $this->rootObj;
        }

        /** @return Query */
        public function select( $expression = null ){
            return $this->query()->select( $expression );
        }

        public function query( $alias = 'root' ){
            $query = new Query( $this, $alias );
            $query->useQModifiers( $this->useQmodifier );

            if( $this->getSubclass() && $this->getSubclass() != get_class( $this->rootObj ) && $this->rootObj->getSubclassKeyName()
            ){
                $str = "{$alias}.{$this->rootObj->getSubclassKeyName()} = '{$this->getSubclass()}'";
                $query->addQModifier( 'where', $str );
            }

            return $query;
        }

        /** @return Query */
        public function insert(){
            return $this->query()->insert();
        }

        /** @return Query */
        public function update(){
            return $this->query()->update();
        }

        /** @return Query */
        public function delete(){
            return $this->query()->delete();
        }

        /**
         * Получить по первичному ключу
         *
         * @param mixed $pk
         * @param mixed $createNonExist : необходимо ли создание, если не существует
         * @return  Root - $this->rootObj
         */
        public function getByPk( $pk, $createNonExist = false ){
            if( $object = $this->select()->where( "root.{$this->rootObj->getPkName()} = ?", (int)$pk )->fetchOne() )
                return $object;

            if( $createNonExist )
                return clone $this->rootObj;

            return false;
        }

        public function getAllByQuery( Query $query, $keyName = null ){
            $result = array();
            $query->setFetchMode( \PDO::FETCH_ASSOC );
            $rows = $query->fetchAll();
            //		echo '<pre>'; var_dump($rows); echo '</pre>'; die();
            foreach( $rows as $row ){
                $o = $this->instantiate( $row );

                if( $keyName == $o->getPkName() )
                    $result[$o->getpk()] = $o;
                else if( !empty( $keyName ) )
                    $result[$o->$keyName] = $o;
                else
                    $result[] = $o;
                //unset($o);
            }
            return $result;
        }

        /**
         * @param array $map
         * @return Root
         */
        public function instantiate( array $map = array() ){
            $subclassKey = $this->rootObj->getSubclassKeyName();
            if( $subclassKey ){
                $subclass = $map[$subclassKey];

                if( is_null( $subclass ) ){
                    $rootObj = clone $this->rootObj;
                }
                else{
                    if( !isset( $this->rootSubclassesObjs[$subclass] ) )
                        $this->rootSubclassesObjs[$subclass] = new $subclass;

                    $rootObj = clone $this->rootSubclassesObjs[$subclass];
                }
            }
            else
                $rootObj = clone $this->rootObj;

            if( $rootObj->isLanguaged() ){
                if( isset( $map['languageId'] ) ){
                    $rootObj->setLanguage( fvManagersPool::get( 'Language' )->getByPk( $map['languageId'] ) );
                    unset( $map['languageId'] );
                }
                else{
                    $rootObj->setLanguage( lang );
                }
            }

            $rootObj->hydrate( $map );
            $rootObj->setChanged( false );

            return $rootObj;
        }

        /**
         * Returns all rows by:
         * @parameter string $where
         * @parameter $order
         * @parameter $limit
         * @parameter $params
         * @parameter $keyName
         * @returns mixed
         */
        function getAll( $where = null, $order = null, $limit = null, $params = null, $keyName = null ){
            if( is_string( $order ) )
                $order = explode( ',', $order );

            if( !is_null( $params ) && !is_array( $params ) )
                $params = array( $params );


            $keyName = trim( $keyName );

            $query = $this->select();
            if( !is_null( $where ) ){
                $query->where( $where, $params );
            }

            if( !is_null( $limit ) ){
                $limit = explode( ',', $limit );

                if( count( $limit ) == 1 )
                    $query->limit( $limit[0] );

                if( count( $limit ) == 2 )
                    $query->limit( $limit[1], $limit[0] );
            }

            if( is_array( $order ) ){
                foreach( $order as $orderStatement ){
                    $query->orderBy( $orderStatement );
                }
            }

            if( !is_null( $keyName ) ){
                $query->aggregateBy( $keyName );
            }

            return $query->fetchAll();
        }

        /**
         * Returns all rows by:
         * @parameter string $where
         * @parameter $order
         * @parameter $limit
         * @parameter $params
         * @returns mixed
         */
        function getAllCached(){
            $args = func_get_args();

            $where = $order = $limit = $numbered = "";
            $params = array();

            if( !empty( $args[0] ) )
                $where = $args[0];
            if( !empty( $args[1] ) )
                $order = $args[1];
            if( !empty( $args[2] ) )
                $limit = explode( ',', $args[2] );
            if( isset( $args[3] ) )
                $params = ( is_array( @$args[3] ) ) ? @$args[3] : array( @$args[3] );
            if( isset( $args[4] ) )
                $numbered = ( bool )$args[4];

            if( defined( "FV_MEMCACHE_ENABLED" ) && FV_MEMCACHE_ENABLED ){
                $key = lang . $where . $order . $args[2] . md5( serialize( $params ) ) . $numbered;
                $result = fvMemCache::getInstance()->getCache( $key );
                if( !is_array( $result ) ){
                    $result = $this->getAll( $where, $order, $args[2], $params, $numbered );
                    fvMemCache::getInstance()->setCache( $key, $result, 180 );
                }
            }
            else{
                $result = $this->getAll( $where, $order, $args[2], $params, $numbered );
            }

            return $result;
        }

        /**
         * returns exactly one row or returns false
         * @param type $where
         * @param type $order
         * @param type $params
         * @return type
         */
        function getOne( $where = "", $order = "", $params = null ){
            $result = $this->getAll( $where, $order, "0,1", $params );
            if( is_array( $result ) ){
                return current( $result );
            }
            return false;
        }

        /**
         * returns exactly one instance of Root
         * @param string $where
         * @param string $order
         * @param Array $params
         * @return Root
         */
        function getOneInstance( $where = "", $order = "", $params = null ){
            $instance = $this->getOne( $where, $order, $params );
            if( $instance instanceof Root ){
                return $instance;
            }
            throw new Exception( "Instance '{$this->rootObj->getEntity()}' is not exists" );
        }

        /**
         * @param array $ids
         * @return array
         */
        function getByIds( array $ids ){
            if( count( $ids ) == 0 )
                return array();

            if( $ids[0] instanceof $this->rootObj )
                return $ids;
            else
                return $this->getAll( 'id in (' . implode( ",", $ids ) . ')' );
        }

        function recall( $ids, $functionName, array $params = array() ){
            if( is_array( $functionName ) )
                $functions = $functionName;
            else
                $functions = array( $functionName );

            foreach( $this->getByIds( $ids ) as $entity ){
                foreach( $functions as $functionName ){
                    if( !method_exists( $entity, $functionName ) )
                        throw new Exception( 'unknown action!' );

                    call_user_func_array( array( $entity, $functionName ), $params );
                }
                $entity->save();
            }
        }

        function qWhereModifier( $where ){
            if( empty( $this->modifier['where'] ) )
                return $where;

            if( empty( $where ) )
                return implode( ' AND ', $this->modifier['where'] );

            return $where . ' AND (' . implode( ' AND ', $this->modifier['where'] ) . ')';
        }

        function qOrderModifier( $order ){
            if( empty( $this->modifier['order'] ) )
                return $order;

            if( empty( $order ) )
                return implode( ' AND ', $this->modifier['order'] );

            return $order . ', ' . implode( ', ', $this->modifier['order'] );
        }

        function htmlSelect( $field, $empty = "", $where = null, $order = null, $limit = null, $args = array() ){
            $objs = $this->getAll( $where, $order, $limit, $args );

            $result = array();
            if( !empty( $empty ) ){
                $result['0'] = $empty;
            }

            foreach( $objs as $obj ){
                $result[$obj->getPk()] = $field ? $obj->$field : (string)$obj;
            }

            return $result;
        }

        /**
         * Возвращает количество сущностей удовлетворяющих условию либо false в случае неудачи
         * @param $where string условие
         * @param $params array|mixed параметры условия
         * @return bool|mixed
         * @throws Exception
         */
        public function getCount( $where = null, $params = null ){
            $query = new Query( $this );
            $query->useQModifiers( $this->useQmodifier );

            if( !empty( $where ) )
                $query->where( $where, $params );



            return $query->getCount();
        }

        public function __call( $name, $arguments ){
            if( strpos( $name, 'getBy' ) === 0 ){
                if( ( $fieldName = $this->checkName( substr( $name, 5 ) ) ) === false ){
                    throw new Exception( "Unrecognized field '" . substr( $name, 5 ) . "'" );
                }
            }
            elseif( strpos( $name, 'getOneBy' ) === 0 ){
                if( ( $fieldName = $this->checkName( substr( $name, 8 ) ) ) === false ){
                    throw new Exception( "Unrecognized field '" . substr( $name, 8 ) . "'" );
                }
            }
            else{
                throw new Exception( "Call to undefined function {$name}" );
            }

            $value = $arguments[0];
            $condition = ( !empty( $arguments[1] ) ) ? $arguments[1] : self::GET_EQUAL;
            $case_sensitive = ( !empty( $arguments[2] ) ) ? ( $arguments[2] == self::GET_UNCHECK_CASE ) : true;

            if( strpos( $name, 'getBy' ) === 0 ){
                return $this->getAllByFieldName( $fieldName, $value, $condition, null, $case_sensitive );
            }
            else{
                $object = $this->getAllByFieldName( $fieldName, $value, $condition, "1", $case_sensitive );
                return reset( $object );
            }
        }

        protected function getAllByFieldName( $fieldName, $value, $condition, $limit = null, $case_sensitive = true ){
            if( $case_sensitive ){
                $where = "{$fieldName} {$condition} :value";
            }
            else{
                $where = "UPPER({$fieldName}) {$condition} :value";
            }

            return $this->select()->andWhere( $where, array( 'value' => $value ) )->limit( $limit )->fetchAll();
        }

        protected function checkName( $name ){
            if( $this->rootObj->hasField( $name ) )
                return $name;

            $name = lcfirst( $name );
            if( $this->rootObj->hasField( $name ) )
                return $name;

            $name = from_camel_case( $name );
            if( $this->rootObj->hasField( $name ) )
                return $name;
            return false;
        }

        /**
         * @deprecated Please use update() Query syntax
         *
         * @param $where
         * @param $updateFields
         *
         * @return bool
         */
        public function massUpdate( $where, $updateFields ){
            $o = clone $this->rootObj;

            foreach( $updateFields as $field => $value ){
                $fieldObj = $o->$field;
                $fieldObj->set( $value );
            }

            $values = array();
            foreach( $o->getFields() as $fieldName => $field ){
                if( $field->isChanged() )
                    $values[$fieldName] = $field->asMysql();
            }

            $this->update()->where( $where )->set( $values )->execute();

            return true;
        }

        public function getObjectBySQL( $sql, $addField = array(), $single_object = false ){
            $data = fvSite::$pdo->getAssoc( $sql );
            $res = array();
            foreach( $data as $k => $v ){
                $ex = new $this->rootObj;
                if( count( $addField ) ){
                    foreach( $addField as $key => $val ){
                        $ex->addField( $key, "$val", "" );
                    }
                }
                $ex->hydrate( $v );
                $res[] = $ex;
            }
            if( $single_object ){
                if( isset( $res[0] ) )
                    return $res[0];
                else
                    return array();
            }
            else
                return $res;
        }

        function getTableName(){
            return $this->rootObj->getTableName();
        }

        function getLanguageTableName(){
            return $this->rootObj->getLanguageTableName();
        }

        /**
         * @return Root|null
         */
        public function getRootObj(){
            return $this->rootObj;
        }

        public function useQmodifiers( $bool = true ){
            $this->useQmodifier = (boolean)$bool;
            return $this;
        }

        public function setSubclass( $subclass ){
            $this->subclass = $subclass;
            return $this;
        }

        public function getSubclass(){
            return $this->subclass;
        }

    }

?>
