<?php

    namespace Bundle\fv\Orm;

    use Bundle\fv\Orm\Exception\QueryException as Exception;
    use \PDO as PDO;

    /**
     * @property fvRelationLoader $_relationLoader
     */
    class Query{

        const STATEMENT_SELECT = 1;
        const STATEMENT_INSERT = 2;
        const STATEMENT_UPDATE = 3;
        const STATEMENT_DELETE = 4;
        const STATEMENT_UPDATE_All = 5;

        const OPERATION_EQUAL = "=";
        const OPERATION_NOT_EQUAL = "!=";
        const OPERATION_LIKE = "like";
        const OPERATION_MORE = ">";
        const OPERATION_MORE_OR_EQUAL = ">=";
        const OPERATION_LESS = "<";
        const OPERATION_LESS_OR_EQUAL = "<=";

        protected
            $_type = self::STATEMENT_SELECT,
            $_where = array(),
            $_whereScope = 0,
            $_having,
            $_group = '',
            $_join,
            $_joins = array(),
            $_select,
            $_heap,
            $_rootAlias,
            $_foundRows,
            $_limit,
            $_useQModifiers = true,
            $_qModifiers = array(),
            $_fetchMode,
            $_aggregateBy,
            $_relationLoader,
            $_order = array(),
            $_params = array(),
            $_managers = array(),
            $_set = array(),
            $_rootManager;

        private $_inCounter = 0;

        /**
         * @param RootManager|Root|string $from
         * @param null $rootAlias
         * @param int $fetchMode
         */
        function __construct( $from, $rootAlias = null, $fetchMode = PDO::FETCH_CLASS ){
            if( $from instanceof RootManager ){
                $rootManager = $from;
            }
            elseif( $from instanceof Root ){
                $rootManager = $from->getManager();
            }
            elseif( is_string( $from ) ){
                $rootManager = ManagersPool::get( $from );
            }
            elseif( is_object( $from ) ){
                throw new Exception( "Can't create fvQuery from class " . get_class( $from ) );
            }
            else
                throw new Exception( "Can't create fvQuery from type " . gettype( $from ) );

            if( is_null( $rootAlias ) )
                $rootAlias = 'root';

            if( empty( $rootAlias ) )
                throw new Exception( "Alias couldn't be empty!" );

            $this->_rootManager = $rootManager;
            $this->_managers[$rootAlias] = $rootManager;
            $this->_select = "{$rootAlias}.*";
            $this->_rootAlias = $rootAlias;

            $this->setFetchMode( $fetchMode );

            if( $rootManager->getRootObj()->isLanguaged() ){
                $lang = Language::getManager()->getOneByCode( fvSite::$fvConfig->getCurrentLang(), true );
                if( !$lang instanceof Language ){
                    if( FV_DEBUG_MODE )
                        throw new Exception( "Language absent" );
                    else
                        fvAction::redirect404();
                }
                $this->_join = " LEFT JOIN {$rootManager->getLanguageTableName()} as {$rootAlias}_lang ON {$rootAlias}_lang.id = {$rootAlias}.id and languageId = " . $lang->getPk() . " ";
                $this->_select = "{$rootAlias}_lang.*, " . $this->_select;
            }

            $this->groupBy( $rootAlias . "." . $rootManager->getRootObj()->getPkName() );
        }

        /**
         * @return \fv\Connection\Driver\PdoMysql
         */
        public static function getDriver(){
            static $driver;

            if( empty($driver) ){
                $connectionFactory = new \fv\Connection\ConnectionFactory();
                $connection = $connectionFactory->getConnection();
                $driver = $connection->getDriver();
            }

            return $driver;
        }

        public function getClone(){
            return clone $this;
        }

        public function set( array $set ){
            $this->_set = $set;
            return $this;
        }

        public function getSet(){
            return $this->_set;
        }

        public function join( $constraint, $toAlias = null, $type = "INNER", $additionalExpression = null ){
            if( is_null( $toAlias ) ){
                $temp = explode( ' ', $constraint );
                if( count( $temp ) == 2 ){
                    $constraint = array_shift( $temp );
                    $toAlias = array_shift( $temp );
                }
                else
                    $toAlias = $constraint;
                //throw new Exception("Alias {$constraint} syntax error! Expect: '[&lt;fromAlias&gt;.]&lt;relationField&gt;[ &lt;alias&gt;]'. Got: '{$constraint}'.");
            }

            if( isset( $this->_managers[$toAlias] ) )
                throw new Exception( "Alias {$toAlias} already defined!" );

            $tmp = explode( ".", $constraint );

            if( count( $tmp ) == 1 ){
                $fieldName = array_shift( $tmp );
                $fromEntity = $this->_rootManager;
                $fromAlias = $this->_rootAlias;
            }
            elseif( count( $tmp ) == 2 ){
                $fromAlias = array_shift( $tmp );
                $fieldName = array_shift( $tmp );
                if( !isset( $this->_managers[$fromAlias] ) )
                    throw new Exception( "Table alias {$fromAlias} not found in current query." );
                $fromEntity = $this->_managers[$fromAlias];
            }
            else
                throw new Exception( "Error constraint syntax: '{$constraint}'" );

            if( !( $field = $fromEntity->getRootObj()->getForeign( $fieldName ) ) )
                $field = $fromEntity->getRootObj()->$fieldName;

            if( $field instanceof Field_Foreign ){
                /** @var $field Field_Foreign */
                $toEntity = fvManagersPool::get( $field->getForeignEntityName() );

                $table = $toEntity->getTableName();
                $constraint = "{$fromAlias}.{$field->getKey()} = {$toAlias}.{$toEntity->getRootObj()->getPkName()}";
            }
            elseif( $field instanceof Field_Constraint ){
                /** @var $field Field_Constraint */
                $toEntity = fvManagersPool::get( $field->getForeignEntityName() );
                $table = $toEntity->getTableName();

                $constraint = "{$toAlias}.{$field->getForeignEntityKey()} = {$fromAlias}.{$fromEntity->getRootObj()->getPkName()}";
            }
            elseif( $field instanceof Field_References ){
                /** @var $field Field_References */
                $toEntity = fvManagersPool::get( $field->getForeignEntityName() );
                $table = $toEntity->getTableName();
                $referenceTable = $field->getReferenceTableName();
                $referenceAlias = $toAlias . "2" . $fromAlias;

                $this->_join .= " {$type} JOIN {$referenceTable} AS {$referenceAlias} ON ({$fromAlias}.{$fromEntity->getRootObj()->getPkName()} = {$referenceAlias}.{$field->getCurrentEntityKey()})";

                $constraint = "{$toAlias}.{$toEntity->getRootObj()->getPkName()} = {$referenceAlias}.{$field->getForeignEntityKey()}";
            }
            else
                throw new Exception( "Unknown relation column '{$fieldName}' with type '" . get_class( $field ) . "'" );

            $this->_managers[$toAlias] = $toEntity;

            $this->_joins[$fromAlias][$fieldName] = $toAlias;

            if( $additionalExpression )
                $this->_join .= " {$type} JOIN {$table} AS {$toAlias} ON ({$constraint} AND {$additionalExpression}) ";
            else
                $this->_join .= " {$type} JOIN {$table} AS {$toAlias} ON ({$constraint}) ";

            return $this;
        }

        public function leftJoin( $constraint, $alias = null, $additionalExpression = null ){
            return $this->join( $constraint, $alias, "LEFT", $additionalExpression );
        }

        public function limit( $rowCount, $offset = null ){
            if( $offset && $rowCount )
                $this->_limit = "{$offset}, {$rowCount}";
            elseif( $rowCount )
                $this->_limit = $rowCount;
            else
                $this->_limit = "";
            return $this;
        }

        public function resetWhere(){
            $this->_params = array();
            $this->_where = array();
            $this->_whereScope = 0;
            return $this;
        }

        public function where( $where, $params = null ){
            return $this->resetWhere()->andWhere( $where, $params );
        }

        public function andWhere( $where, $params = null, $operation = Query::OPERATION_EQUAL ){
            if( is_array( $where ) ){
                $fields = $where;
                $params = array();
                $where = array();
                foreach( $fields as $field => $value ){
                    $key = str_replace( '.', '_', $field );
                    if( !strstr( $field, "." ) ){
                        $field = $this->getRootAlias() . "." . $field;
                    }
                    $where[] = "{$field} {$operation} :{$key}";
                    $params[$key] = $value;
                }
                $where = implode( " and ", $where );
                return $this->andWhere( $where, $params );
            }

            if( is_array( $params ) )
                $this->_params = array_merge( $this->_params, $params );
            elseif( !is_null( $params ) )
                $this->_params[] = $params;

            if( $where )
                $this->_where[$this->_whereScope][] = "({$where})";

            return $this;
        }

        public function whereIn( $field, $values = null, $positive = true ){
            return $this->resetWhere()->andWhereIn( $field, $values, $positive );
        }

        public function whereNotIn( $field, $values = null ){
            $this->_params = array();
            $this->_where = array();

            return $this->andWhereIn( $field, $values, false );
        }

        public function andWhereNotIn( $field, $values = null ){
            return $this->andWhereIn( $field, $values, false );
        }

        public function andWhereIn( $field, $values = null, $positive = true ){
            if( empty( $values ) ){
                if( $positive )
                    return $this->andWhere( "{$field} IS NULL" );
                else
                    return $this;
            }
            elseif( is_array( $values ) ){
                $seed = "a" . ( ++$this->_inCounter ) . "_";
                $i = 0;
                $seeds = array();
                $params = array();
                foreach( $values as $value ){
                    $i++;
                    $seeds[$seed . $i] = ":" . $seed . $i;
                    $params[$seed . $i] = $value;
                }

                $seeds = implode( ",", $seeds );

                if( $positive )
                    return $this->andWhere( "{$field} IN ({$seeds})", $params );
                else
                    return $this->andWhere( "{$field} NOT IN ({$seeds})", $params );
            }
            elseif( is_string( $values ) ){
                if( $positive )
                    return $this->andWhere( "{$field} IN ({$values})" );
                else
                    return $this->andWhere( "{$field} NOT IN ({$values})" );
            }
            elseif( $values instanceof Query ){
                if( $positive )
                    return $this->andWhere( "{$field} IN ({$values->getSQL()})" );
                else
                    return $this->andWhere( "{$field} NOT IN ({$values->getSQL()})" );
            }
            else{
                throw new Exception( 'Invalid values in "WHERE IN"' );
            }

        }

        public function orderBy( $keys, $ascending = true ){
            $this->_order = array();

            foreach( explode( ",", $keys ) as $key ){
                if( $key ){
                    $this->andOrderBy( trim( $key ), $ascending );
                }
            }
            return $this;
        }

        public function andOrderBy( $key, $ascending = true ){
            if( !empty( $key ) ){
                if( preg_match( '/(.+)\s(ASC|DESC)/i', $key, $arr ) ){
                    return $this->andOrderBy( trim( $arr[1] ), mb_strtoupper( trim( $arr[2] ) ) == 'ASC' );
                }

                $this->_order[] = ( $ascending ) ? "{$key} ASC" : "{$key} DESC";
            }


            return $this;
        }

        public function groupBy( $key ){
            $this->_group = $key;
            return $this;
        }

        public function addOr(){
            $this->_whereScope++;
            return $this;
        }

        public function getSql(){
            if( $this->_useQModifiers ){
                $modifiers = $this->_getModifiers();
            }

            if( empty( $this->_heap ) )
                $sql = "SELECT {$this->_select} FROM {$this->_rootManager->getTableName()} {$this->_rootAlias} ";
            else{
                if( $this->getFetchMode() & PDO::FETCH_CLASS )
                    $sql = "SELECT {$this->_select}, {$this->_heap} FROM {$this->_rootManager->getTableName()} {$this->_rootAlias}";
                else
                    $sql = "SELECT {$this->_heap} FROM {$this->_rootManager->getTableName()} {$this->_rootAlias} ";
            }

            if( $this->_join ){
                $sql .= $this->_join;
            }

            $where = $this->getWhere();
            if( !empty( $modifiers['where'] ) )
                $where = ( $where ? "($where) AND " : "" ) . "(" . implode( " AND ", $modifiers['where'] ) . ")";
            if( !empty( $where ) )
                $sql .= " WHERE " . $where;

            if( $this->_group ){
                $sql .= " GROUP BY {$this->_group}";
            }

            if( $this->_having ){
                $sql .= " HAVING {$this->_having}";
            }

            $order = $this->_order;
            if( !empty( $modifiers['order'] ) )
                $order = array_merge( $order, $modifiers['order'] );
            if( !empty( $order ) )
                $sql .= " ORDER BY " . implode( ",", $order );

            if( $this->_limit ){
                $sql .= " LIMIT {$this->_limit}";
            }

            return $sql;
        }

        public function getWhere(){
            $where = array();
            foreach( $this->_where as $whereScope ){
                $where[] = "(" . implode( " AND ", $whereScope ) . ")";
            }
            return implode( " OR ", $where );
        }

        public function fetchAll( $fetchMode = null ){
            if( $fetchMode )
                $this->setFetchMode( $fetchMode );

            $statement = $this->getStatement();
            if( $this->getFetchMode() & PDO::FETCH_CLASS ){
                $result = array();
                foreach( $statement->fetchAll( $this->getFetchMode() & ~PDO::FETCH_CLASS | PDO::FETCH_ASSOC ) as $row ){
                    $rowObj = $this->_rootManager->instantiate( $row );

                    if( $this->_aggregateBy ){
                        if( $this->_aggregateBy != $rowObj->getPkName() ){
                            $fieldName = $this->_aggregateBy;
                            $field = $rowObj->$fieldName;
                            $result[$field->get()][] = $rowObj;
                        }
                        else
                            $result[$rowObj->getPk()] = $rowObj;
                    }
                    else
                        $result[$rowObj->getPk()] = $rowObj;
                }
            }
            else
                $result = $statement->fetchAll( $this->getFetchMode() );

            if( $this->_relationLoader )
                $this->relationLoader()->load( $result );

            //Field_Foreign::preloadCache( $this->getRootManager()->getRootObj()->getEntity(), $result );

            return $result;
        }

        public function fetchOne( $fetchMode = null ){
            if( $fetchMode )
                $this->setFetchMode( $fetchMode );

            $statement = $this->getStatement();
            if( $this->getFetchMode() & PDO::FETCH_CLASS ){
                if( $row = $statement->fetch( $this->getFetchMode() & ~PDO::FETCH_CLASS | PDO::FETCH_ASSOC ) ){
                    return $this->_rootManager->instantiate( $row );
                }
                return false;
            }
            else
                return $this->getStatement()->fetch( $this->getFetchMode() );
        }

        public function execute(){
            switch( $this->_type ){
                case self::STATEMENT_SELECT:
                    return $this->fetchAll();
                    break;
                case self::STATEMENT_UPDATE_All:
                    return $this->updateAll();
                    break;
                case self::STATEMENT_UPDATE:
                    self::getDriver()->update( $this->_rootManager->getTableName(),
                                          $this->getSet(),
                                          $this->getWhere(),
                                          $this->_params );
                    break;
                case self::STATEMENT_INSERT:
                    self::getDriver()->insert( $this->_rootManager->getTableName(), $this->getSet() );
                    break;
                default :
                    throw new Exception( 'Unknown operation type' );
            }
            return true;
        }

        public function updateAll(){
            $sql = "UPDATE {$this->_rootManager->getTableName()} SET ";

            $sql .= $this->getSetSection();

            if( !empty( $this->_where ) ){
                $sql .= " WHERE " . $this->getWhere();
            }
            if( !empty( $this->_limit ) ){
                $sql .= " LIMIT {$this->_limit}";
            }
            return self::getDriver()->prepare( $sql )->execute( $this->getSetParams() );
        }

        private function getSetSection(){
            $result = array();
            foreach( $this->_set as $field => $value ){
                $key = ":" . preg_replace( "/[^\d\w]/", "", $field );
                $result[] = "$field = {$key}";
            }
            return implode( ",", $result );
        }

        private function getSetParams(){
            return self::getDriver()->prepareSetParams( $this->getSet() );
        }

        public function getStatement(){
            $microtime = microtime( true );

            $sth = self::getDriver()->prepare( $sql = $this->getSql() );

            $sth->execute( $this->_params );

            if( $this->_params )
                $sql .= "<br/>Params:" . print_r( $this->_params, true );

            /*
            if( FV_PROFILE && defined( 'FV_PROFILE' ) )
                Profile::addQuery( $sql, microtime( true ) - $microtime, "pQ" );
            */

            return $sth;
        }

        public function getCount(){
            $microtime = microtime( true );

            $limit = $this->_limit;
            $select = $this->_select;
            $this->_limit = null;
            $this->_select = $this->_rootAlias . "." . $this->_rootManager->getRootObj()->getPkName() . " as roid";

            $sql = "SELECT count(roid) FROM ({$this->getSql()}) a";
            $this->_limit = $limit;
            $this->_select = $select;

            $sth = self::getDriver()->prepare( $sql );
            $sth->execute( $this->_params );

            $result = $sth->fetchColumn();

            /*
            if( FV_PROFILE && defined( 'FV_PROFILE' ) )
                Profile::addQuery( $sql, microtime( true ) - $microtime, "pQ" );
            */

            return $result;
        }

        public function useFilters( $array ){
            foreach( $array as $filter ){
                $this->useFilter( $filter );
            }

            return $this;
        }

        /** @return $this */
        public function useFilter( Component_Filter $filter ){
            $filter->where( $this );
            return $this;
        }

        /*public function updateAll() {
            $this->_type = self::STATEMENT_SELECT;
            return $this;
        }*/

        public function select( $expression = null ){
            $this->_type = self::STATEMENT_SELECT;
            $this->_heap = $expression;
            return $this;
        }

        public function insert(){
            $this->_type = self::STATEMENT_INSERT;
            return $this;
        }

        public function update(){
            $this->_type = self::STATEMENT_UPDATE;
            return $this;
        }

        public function delete(){
            $this->_type = self::STATEMENT_DELETE;
            return $this;
        }

        /**
         * Она только для внутренних нужд)
         */
        protected function setParams( $params ){
            $this->_params = $params;
            return $this;
        }

        public function getParams(){
            return $this->_params;
        }

        /**
         * @param $relation
         * @param null $condition
         * @return Query
         * @throws Exception
         */
        public function loadRelation( $relation, $condition = null, $conditionParams = null ){
            $tmp = explode( ".", $relation );

            if( count( $tmp ) == 1 ){
                $fieldName = array_shift( $tmp );
                $fromAlias = $this->_rootAlias;
            }
            elseif( count( $tmp ) == 2 ){
                $fromAlias = array_shift( $tmp );
                $fieldName = array_shift( $tmp );
            }
            else
                throw new Exception( "Error relation syntax: '{$relation}'" );

            $tmp = explode( " ", $fieldName );
            if( count( $tmp ) == 1 ){
                $fieldName = array_shift( $tmp );
                $asAlias = $this->_joins[$fromAlias][$fieldName];
            }
            elseif( count( $tmp ) == 2 ){
                $fieldName = array_shift( $tmp );
                $asAlias = array_shift( $tmp );
            }
            else
                throw new Exception( "Error relation syntax: '{$relation}'" );

            if( $fromAlias == $this->_rootAlias )
                $this->relationLoader()->addRelation( $fieldName, $asAlias, $condition, $conditionParams );
            else
                $this->relationLoader()
                    ->addIndirectRelation( $fromAlias, $fieldName, $asAlias, $condition, $conditionParams );

            return $this;
        }


        /**
         * @return fvRelationLoader
         */
        public function relationLoader(){
            if( !isset( $this->_relationLoader ) )
                $this->_relationLoader = new fvRelationLoader;

            return $this->_relationLoader;
        }

        public function aggregateBy( $key ){
            $this->_aggregateBy = $key;
            return $this;
        }

        public function useQModifiers( $useQModifiers ){
            $this->_useQModifiers = $useQModifiers;
            return $this;
        }

        /**
         * O(n^3)?
         * И чё?
         */
        private function _getModifiers(){
            return array();

            $resultModifiers = $this->_qModifiers;


            if( !( $modifiers = fvSite::$fvConfig->get( "qModifier" ) ) )
                return $resultModifiers;

            foreach( $this->_managers as $alias => $manger ){
                /** @var $manger RootManager */
                foreach( $modifiers as $iEntity => $modificators ){
                    if( !$manger->getRootObj()->isImplements( $iEntity ) )
                        continue;

                    foreach( $modificators as $type => $modifier ){
                        $resultModifiers[$type][] = str_replace( '<alias>', $alias, $modifier );
                    }
                }
            }

            return $resultModifiers;
        }

        public function addQModifier( $type, $modifier ){
            $this->_qModifiers[$type][] = $modifier;
            return $this;
        }

        public function setFetchMode( $fetchMode ){
            $this->_fetchMode = $fetchMode;
            return $this;
        }

        public function setParam( $key, $value ){
            $this->_params[$key] = $value;
            return $this;
        }

        public function getFetchMode(){
            return $this->_fetchMode;
        }

        public function having( $having ){
            $this->_having = $having;
            return $this;
        }

        public function getHaving(){
            return $this->_having;
        }

        /** @return RootManager */
        public function getRootManager(){
            return $this->_rootManager;
        }

        /** @return string */
        public function getRootAlias(){
            return $this->_rootAlias;
        }

        public function setSelect( $select ){
            $this->_select = $select;
            return $this;
        }

    }
