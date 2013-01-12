<?php

    namespace Bundle\fv\Orm;

    use Bundle\fv\Orm\Exception\OrmException as Exception;

    /**
     * Base model class
     * @version 2.0
     */
    abstract class Root extends FieldCollection {

        protected $_foreign = Array();
        protected $key;
        protected $keyName;
        protected $subclassKeyName;
        protected $tableName;
        protected $valid;
        protected $loadedLanguages = Array();
        protected $implements = Array();

        const UPDATE = 'Update';
        const INSERT = 'Insert';

        protected static $serializeTypes = array( 'array', 'object' );

        function __construct( array $map = array() ){
            $className = call_user_func( array( get_class( $this ), "getEntity" ), array() );
            $this->implement( $className );

            if( !empty( $map ) )
                $this->hydrate( $map );
        }

        public function isImplements( $name ){
            return ( array_search( trim( $name ), $this->implements ) !== false );
        }

        private function implement( $name ){
            $className = call_user_func( array( get_class( $this ), "getEntity" ), array() );

            if( $this->isImplements( $name ) )
                throw new Exception( "Scheme '{$name}' already implemented '{$className}' Entity" );

            $this->implements[] = $name;

            $schema = fvSite::$fvConfig->get( "entities.{$name}" );

            if( !$schema )
                $schema = fvSite::$fvConfig->get( "abstract.{$name}" );

            if( !$schema )
                throw new Exception( "Can't find implementation '{$name}' of '" . $className . "' Entity" );

            if( is_array( $schema['implements'] ) )
                foreach( $schema['implements'] as $implementSchemaName ){
                    $this->implement( $implementSchemaName );
                }

            if( is_array( $schema['foreigns'] ) )
                foreach( $schema['foreigns'] as $key => $foreign ){
                    $foreign['type'] = "foreign" . ( $foreign['type'] ? '_' . ucfirst( $foreign['type'] ) : '' );
                    $this->updateFields( array( $foreign['key'] => $foreign ) );
                    $this->_foreign[$key] = $foreign['key'];
                }

            if( is_array( $schema['constraints'] ) ){
                foreach( $schema['constraints'] as $key => $constraint ){
                    $constraint['type'] = "constraint" . ( $constraint['type'] ? '_' . ucfirst( $constraint['type'] )
                        : '' );
                    $constraint['currentEntity'] = $className;
                    $this->updateFields( array( $key => $constraint ) );
                }
            }
            if( is_array( $schema['references'] ) ){
                foreach( $schema['references'] as $key => $reference ){
                    $reference['type'] = "references" . ( $reference['type'] ? '_' . ucfirst( $reference['type'] )
                        : '' );
                    $reference['currentEntity'] = $className;
                    $this->updateFields( array( $key => $reference ) );
                }
            }
            if( is_array( $schema['fields'] ) )
                $this->updateFields( $schema['fields'] );

            if( $schema['table_name'] )
                $this->tableName = $schema['table_name'];

            if( $schema['primary_key'] )
                $this->keyName = $schema['primary_key'];

            if( $schema['subclass_key'] )
                $this->subclassKeyName = $schema['subclass_key'];
        }

        public function __get( $name ){
            if( $field = $this->getForeign( $name ) )
                return $field->asEntity();

            return parent::__get( $name );
        }

        /** @return \Bundle\fv\Orm\Field\Foreign */
        public function getForeign( $name ){
            if( !isset( $this->_foreign[$name] ) )
                return false;

            return parent::__get( $this->_foreign[$name] );
        }

        public function hasForeign( $name ){
            return isset( $this->_foreign[$name] );
        }

        /** @return \Bundle\fv\Orm\Field\AbstractField */
        public function getField( $name ){
            if( isset( $this->_foreign[$name] ) )
                return parent::__get( $this->_foreign[$name] );

            return parent::__get( $name );
        }

        /**
         * Используется для ебучего парса и наебулек, писичек и прочих красот в админке.
         *
         * @param string $name ключ филды
         * @return string
         */
        public function getFieldAdorned( $name ){
            return $this->getField( $name )->asAdorned();
        }

        function getTableName(){
            return $this->tableName;
        }

        function isNew(){
            return empty( $this->key );
        }

        /**
         * Return current entity name.
         * Could'n represent class without this function
         * @static
         * @return string Entity Name
         */
        abstract static function getEntity();

        /**
         * Это красотень ребятульки! Здесь мы получаем язык, указываем всем полям,
         * какой язык использовать, и записываем значения этих языков
         * @param type $lang
         */
        function setLanguage( $lang ){
            if( !$this->isLanguaged() )
                return;

            if( $lang instanceof Language )
                $lang_code = (string)$lang->code;
            else{
                $lang_code = $lang;
                $lang = Language::getManager()->getOneByCode( $lang_code );
            }

            if( !$lang instanceof Language ){
                throw new Exception( "Language {$lang_code} not found" );
            }

            foreach( $this->_fields as $fieldName => $field ){
                if( $field->isLanguaged() )
                    $field->setLanguage( $lang_code );
            }

            if( !in_array( $lang_code, $this->loadedLanguages ) && !$this->isNew() ){
                $sql = "select *
                    from {$this->getLanguageTableName()}
                    where
                    {$this->getPkName()} = {$this->getPk()} and
                    languageId = {$lang->getPk()}
                    limit 1";

                $result = fvSite::$pdo->query( $sql )->fetchAll( PDO::FETCH_ASSOC );
                ;
                if( count( $result ) == 1 ){
                    $result = current( $result );

                    foreach( $this->_fields as $fieldName => $field ){
                        if( $field->isLanguaged() ){
                            $field->setLanguage( $lang_code );
                            $map[$fieldName] = $result[$fieldName];
                        }
                    }
                    if( !empty($map) ){
                        $this->hydrate( $map );
                    }
                }
                else{
                    $insertList = array( "id" => $this->getPk(),
                                         "languageId" => $lang->getPk(), );
                    fvSite::$pdo->insert( $this->getLanguageTableName(), $insertList );

                    foreach( $this->_fields as $fieldName => $field ){
                        if( $field->isLanguaged() ){
                            $field->setDefaultValue();
                        }
                    }
                }

                $this->loadedLanguages[] = $lang_code;
            }
            else{
                foreach( $this->_fields as $fieldName => $field ){
                    if( $field->isLanguaged() ){
                        $field->setDefaultValue();
                    }
                }
            }
        }

        function isLanguaged(){
            foreach( $this->_fields as $fieldName => $field ){
                if( $field->isLanguaged() )
                    return true;
            }

            return false;
        }

        function getLanguageTableName(){
            return $this->getTableName() . fvSite::$fvConfig->get( "languages.databasePostfix" );
        }

        /**
         * Static method that return manager from pool.
         * If manager is missing create EntityManager if exist or create RootManager if not exist
         * @return RootManager
         */
        public static function getManager(){
            $subclass = get_called_class();
            $class = $subclass;
            $parentClass = get_parent_class( $class );

            $className = call_user_func( array( $subclass, "getEntity" ), array() );

            while( $parentClass != __CLASS__ && $className != $class ){
                $parentClass = get_parent_class( $class );
            }

            return ManagersPool::get( $class )->setSubclass( $subclass );
        }

        function hydrate( $map, $languaged = false ){
            if( $languaged ){
                if( is_array( $map['main'] ) ){
                    $this->hydrate( $map['main'] );
                }
                if( $this->isLanguaged() ){
                    $languages = Language::getManager()->getAll();
                    foreach( $languages as $lang ){
                        if( is_array( $map[(string)$lang->code] ) ){
                            $this->setLanguage( $lang );
                            $this->hydrate( $map[(string)$lang->code] );
                        }
                    }
                }

                return true;
            }

            if( isset( $map[$this->keyName] ) ){
                $this->setPk( $map[$this->keyName] );
                unset( $map[$this->keyName] );
            }
            return parent::hydrate( $map );
        }

        function save( $logging = true ){
            if( $this->isNew() ){
                $saveType = self::INSERT;
            }
            else{
                $saveType = self::UPDATE;
            }

            $isTransactionOpen = fvSite::$pdo->isTransactionOpen();
            try{
                if( !$isTransactionOpen )
                    fvSite::$pdo->beginTransaction();

                $insertList = array();
                foreach( $this->getFields() as $key => $field ){
                    if( $field instanceof Field_String_File )
                        $field->upload();

                    if( !$field->isLanguaged() && $field->isChanged() )
                        $insertList[$key] = $field->asMysql();
                }

                if( $this->getSubclassKeyName() && $saveType == self::INSERT ){
                    $insertList[$this->getSubclassKeyName()] = get_class( $this );
                }

                if( count( $insertList ) > 0 ){

                    if( $saveType == self::INSERT ){
                        ManagersPool::get( get_class( $this ) )
                            ->insert()
                            ->set( $insertList )
                            ->execute();
                    }
                    else{
                        $where = "{$this->getPkName()} = :{$this->getPkName()}";
                        $whereParams = array( $this->getPkName() => $this->getPk() );

                        ManagersPool::get( get_class( $this ) )
                            ->update()
                            ->set( $insertList )
                            ->where( $where, $whereParams )
                            ->execute();
                    }
                }

                if( $saveType == self::INSERT )
                    $this->setPk( fvSite::$pdo->lastInsertId() );


                /** Привет, ребята я – охуенный костыль. Давайте дружить, сучечьки
                 *  Хотя, нет. Я — не костыль. Идите нахуй.
                 */
                foreach( $this->getFields() as $key => $field ){
                    if( method_exists( $field, "save" ) ){
                        $field->save();
                    }
                }

                if( $this->isLanguaged() ){
                    $languages = Language::getManager()->getAll();
                    foreach( $languages as $lang ){
                        $insertList = array();
                        foreach( $this->getFields() as $key => $field ){
                            if( $field->isLanguaged() && $field->isChanged() )
                                $insertList[$key] = $field->asMysql();
                        }
                        if( count( $insertList ) > 0 ){
                            if( $saveType == self::INSERT ){
                                $tInsertList = $insertList;
                                $tInsertList['id'] = $this->getPk();
                                $tInsertList['languageId'] = $lang->getPk();
                                fvSite::$pdo->insert( $this->getLanguageTableName(), $tInsertList );
                            }
                            if( $saveType == self::UPDATE ){
                                $whereParams = array( $this->getPkName() => $this->getPk(),
                                                      "languageId"       => $lang->getPk() );
                                $where = array( "{$this->getPkName()} = :{$this->getPkName()} AND languageId = :languageId" );
                                fvSite::$pdo->update( $this->getLanguageTableName(),
                                                      $insertList,
                                                      $where,
                                                      $whereParams );
                            }
                        }
                    }
                }

                /*
                if( $logging && $this instanceof iLogger ){
                    $this->putToLog( ( $saveType == self::INSERT ) ? Log::OPERATION_INSERT : Log::OPERATION_UPDATE );
                }
                */

                $this->setChanged( false );

                if( !$isTransactionOpen )
                    fvSite::$pdo->commit();

                /*
                if( fvMemCache::getInstance()->checkMemCache() ){
                    fvMemCache::getInstance()->setCache( $this->getTableName() . $this->getPk(), $this );
                }
                */

                return true;
            }
            catch( Exception $e ){
                if( !$isTransactionOpen )
                    fvSite::$pdo->rollBack();

                if( $logging && $this instanceof iLogger ){
                    $this->putToLog( Log::OPERATION_ERROR );
                }

                throw $e;
            }
        }

        function delete(){
            if( $this->isNew() )
                return false;

            foreach( $this->getFields() as $key => $field ){
                if( $field instanceof Field_String_File )
                    $field->delete();
            }

            $where = array( "{$this->getPkName()} = :{$this->getPkName()}" );
            $whereParams = array( $this->getPkName() => $this->getPk() );
            fvSite::$pdo->delete( $this->getTableName(), $where, $whereParams );

            if( $this instanceof iLogger ){
                $this->putToLog( Log::OPERATION_DELETE );
            }

            return true;
        }

        function isValid(){
            $className = call_user_func( array( get_class( $this ), "getEntity" ), array() );
            if( !parent::isValid() )
                return false;

            foreach( $this->getFields() as $key => $field ){
                if( $field->isUnique() ){
                    $value = $field->asMysql();

                    if( is_null( $value ) )
                        continue;

                    if( $this->isNew() )
                        $count = ManagersPool::get( $className )->getCount( "{$key} = :k",
                                                                              array( 'k' => $value ) );
                    else
                        $count = ManagersPool::get( $className )
                            ->getCount( "{$key} = :k and {$this->getPkName()} <> :pk",
                                        array( 'k' => $value, 'pk' => $this->getPk() ) );

                    if( $count > 0 )
                        return false;
                }
            }

            return true;
        }

        function getPk( $keyName = null ){
            if( is_null( $keyName ) )
                return $this->key;
            else
                return $this->key[$keyName];
        }

        function setPk( $key, $keyName = null ){
            if( is_null( $keyName ) && !is_array( $this->key ) )
                $this->key = $key;
            else{
                $this->key[$keyName] = $key;
            }

            foreach( $this->getFields() as $field ){
                if( method_exists( $field, "setRootPk" ) )
                    $field->setRootPk( $key );
            }

            return true;
        }

        function getPkName(){
            return $this->keyName;
        }

        function getSubclassKeyName(){
            return $this->subclassKeyName;
        }

        function unQuote( $mVal ){
            $mVal = @is_array( $mVal ) ? array_map( "UnQuote", $mVal )
                : ( isset( $mVal ) ? stripslashes( $mVal ) : null );
            return $mVal;
        }

        /**
         * is $string empty
         * @param string $string
         * @return bool
         */
        public function isEmpty( $string ){
            return strlen( $string ) > 0;
        }

        /**
         * is given string email
         * @param string $string
         * @returns bool
         */
        public function isEmail( $string ){
            return ( preg_match( "/^[a-z0-9_\-\.]+@[a-z_\-\.]+\.[a-z]{2,3}$/i", $string ) > 0 );
        }

        /**
         * is given key of existance entity related to give manager
         * @param string $key
         * @param RootManager $manager
         * @return bool
         */
        public function isEntity( $key, RootManager $manager ){
            return ( $manager->getByPk( intval( $key ) ) instanceof Root );
        }

        /**
         * Привет, я костыль!
         * @return string ровным счётом ничего.
         */
        public function getLangVersion(){
            return "";
        }

    }
