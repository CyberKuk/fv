<?php

    namespace OrmBundle;

    use OrmBundle\Field\AbstractField as Field;
    use OrmBundle\Exception\FieldException as Exception;
    use OrmBundle\Field\Heap as FieldHeap;
    use OrmBundle\Root\Language;

    abstract class FieldCollection {

        /**
         *
         * @var Field[] $_fields
         */
        protected $_fields = Array( );

        protected function updateFields( $schema ) {
            $new_fields = array( );

            foreach ( $schema as $name => $fieldSchema ) {
                if ( isset( $this->_fields[ $name ] ) ) {
                    $this->_fields[ $name ]->updateSchema( $fieldSchema );
                }
                else {
                    $className = __NAMESPACE__ . "\\Field\\" . ucfirst($fieldSchema[ 'type' ]);
                    $new_fields[ $name ] = new $className( $fieldSchema, $name );
                }
            }

            $this->_fields = array_merge_recursive( $new_fields, $this->_fields );

    //        fvDebug::debug( $this->_fields );
        }

        public function __get( $name ) {
            if ( !isset( $this->_fields[ $name ] ) ) {
                throw new Exception( "Trying to get field '{$name}' wich does not implement in schema." );
            }

            return $this->_fields[ $name ];
        }

        public function __set( $name, $value ) {
            if ( !isset( $this->_fields[ $name ] ) ) {
                $this->_fields[ $name ] = new FieldHeap(array(), $name);
                //throw new EFieldError( "Trying to set field '{$name}' wich does not implement in schema." );
            }

            $this->_fields[ $name ]->set( $value );
        }

        function getFieldList() {
            return array_keys( $this->_fields );
        }

        /**
         * @param null $type
         * @param null $parameter
         * @return array|Field[]
         */
        public function getFields( $type = null, $parameter = null ) {
            $fieldCollection = $this->_fields;

            if ( $type ) {
                $result = Array( );

                foreach ( $fieldCollection as $keyName => $field ) {
                    if ( is_a( $field, $type ) )
                        $result[ $keyName ] = $field;
                }

                $fieldCollection = $result;
            }
            // Отобрать по параметру
            if ( !is_null( $parameter ) ) {
                $result = Array( );

                foreach ( $fieldCollection as $keyName => $field ) {
                    if ( $field->checkProperty( $parameter ) )
                        $result[ $keyName ] = $field;
                }
                $fieldCollection = $result;
            }


            return $fieldCollection;
        }

        function isValid() {
            foreach ( $this->getFields() as $field ) {
                if ( !$field->isValid() )
                    return false;
            }

            return true;
        }

        /**
         * Fill fields by array (fieldName => fieldValue)
         * @param string $map
         */
        function hydrate( $map, $languaged = false ){
            $field = key($map);
            try {
                if ( !is_array( $map ) )
                    throw new Exception( "Can't create object from non array" );
                foreach ( $map as $field => $value ) {
                    if ( !isset( $this->_fields[ $field ] ) ) {
                        $this->_fields[ $field ] = new FieldHeap(array(), $field);
                    }

                    $this->_fields[ $field ]->set( $value );
                }

                return true;
            }
            catch ( Exception $e ) {
                throw new Exception( "Field {$field} throw error: " . $e->getMessage() );
            }
        }

        function toHash() {
            $result = array( );
            foreach ( $this->_fields as $name => $field ) {
                $result[ $name ] = ( string ) $field;
            }

            return $result;
        }

        function hasField( $fieldName ) {
            return isset( $this->_fields[ $fieldName ] );
        }

        function __clone() {
            foreach ( $this->_fields as &$field ) {
                $field = clone $field;
            }
        }

        function setChanged( $value ) {
            foreach ( $this->_fields as $field )
                $field->setChanged( $value );
        }

        // todo write this fucken getValidationResult method!
        public function getValidationResult() {
            $valid = array( );

            foreach ( $this->_fields as $fieldName => $field ) {
                if ( $field->isLanguaged() ) {
                    $langs = Language::getManager()->getAll( " isActive = 1 " );
                    foreach ( $langs as $lang ) {
                        $field->setLanguage( $lang->code->get() );
                        if ( !$field->isValid() ) {
                            $valid[ $lang->code->get() . $fieldName ] = $field->getValidationMessage( $this->getEntity() );
                            $valid[ "l" . $lang->code->get() . $fieldName ] = $field->getValidationMessage( $this->getEntity() );
                        }
                    }
                }
                else
                if ( !$field->isValid() ) {
                    $valid[ $fieldName ] = $field->getValidationMessage( $this->getEntity() );
                    $valid[ "l" . $fieldName ] = $field->getValidationMessage( $this->getEntity() );
                }
            }

            return $valid;
        }

    }
