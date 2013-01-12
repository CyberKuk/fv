<?php

    namespace Bundle\fv\Orm\Field;

    use Bundle\fv\Orm\Exception\FieldException;

    /**
     * Абстрактный клас поля в обьекте fvFieldCollection
     *
     * @author Sancha
     * @version 1.0
     * @since 2011/18/10
     */
    abstract class AbstractField {

        /**
         * Текущее значение поля
         * @var mixed $value
         */
        protected $value;

        /**
         * Значение по умолчнию. Присваевается при создании новой сущности
         * @var mixed $defaultValue
         */
        protected $defaultValue = null;

        /**
         * Возможность быть ничтожеством
         * @var boolean $nullable
         */
        protected $nullable = true;

        /**
         * Возможность быть первосортным и второсортным
         * @var boolean $sortable
         */
        protected $sortable = false;

        /**
         * Возможность быть мультиязычным полем
         */
        protected $languaged = false;

        /**
         * Значения языковых версий поля
         */
        protected $langValues = array( );

        /**
         * Language is used for get and set functions
         * @var string $lang
         */
        protected $lang = null;

        /**
         * Field description
         * @var string $name
         */
        protected $name;

        /**
         * Is this field is changed
         * @var bool $changed
         */
        protected $changed = false;

        /**
         * Возможность быть редактируемой в административной панели
         */
        protected $editable = true;

        /**
         * Ключ поля
         * @var string[a-zA-Z]
         */
        protected $key = null;

        /**
         * Выводить в таблице
         * @var string[a-zA-Z]
         */
        protected $listable = true;

        /** таб при эдите */
        protected $tab = false;

        /** Поле уникальное */
        protected $unique = false;

        /** Поле только для чтения */
        protected $readonly = false;

        /**
         * Методы вывода редакторирования поля
         */

        const EDIT_METHOD_INPUT = "input";
        const EDIT_METHOD_PASSWORD = "password";
        const EDIT_METHOD_DATE = "date";
        const EDIT_METHOD_DATETIME = "datetime";
        const EDIT_METHOD_TEXTAREA = "textarea";
        const EDIT_METHOD_RICH = "rich";
        const EDIT_METHOD_UPLOAD = "upload";
        const EDIT_METHOD_UPLOAD_IMAGE = "image";
        const EDIT_METHOD_LIST = "list";
        const EDIT_METHOD_MULTILIST = "multilist";
        const EDIT_METHOD_CHECKBOX = "checkbox";
        const EDIT_METHOD_ENTITIES_LIST = "entitylist";
        const EDIT_METHOD_ENTITIES_LIST_AUTOCREATE = "entitylistautocreate";
        const EDIT_METHOD_ENTITIES_LIST_AUTOCOMPLETE = "entitylistautocomplete";
        const EDIT_METHOD_SLIDE = "slider";
        const EDIT_METHOD_ENTITIES_LIST_READONLY = "entitylistreadonly";

        function __construct( $fieldSchema, $name ) {
            //var_dump($name);
            $this->key = $name;
            $this->updateSchema( $fieldSchema );
            $this->setDefaultValue();
        }

        function updateSchema( $fieldSchema ) {
            if ( isset( $fieldSchema[ 'default' ] ) )
                $this->defaultValue = $fieldSchema[ 'default' ];

            if ( isset( $fieldSchema[ 'nullable' ] ) )
                $this->nullable = ( bool ) $fieldSchema[ 'nullable' ];

            if ( isset( $fieldSchema[ 'language' ] ) )
                $this->languaged = ( bool ) $fieldSchema[ 'language' ];

            if ( isset( $fieldSchema[ 'editable' ] ) )
                $this->editable = $fieldSchema[ 'editable' ];

            if ( isset( $fieldSchema[ 'listable' ] ) )
                $this->listable = $fieldSchema[ 'listable' ];

            if ( isset( $fieldSchema[ 'sortable' ] ) )
                $this->sortable = ( bool ) $fieldSchema[ 'sortable' ];

            if ( isset( $fieldSchema[ 'name' ] ) )
                $this->name = ( string ) $fieldSchema[ 'name' ];

            if ( isset( $fieldSchema[ 'unique' ] ) )
                $this->unique = ( bool ) $fieldSchema[ 'unique' ];

            if ( isset( $fieldSchema[ 'tab' ] ) )
                $this->name = ( string ) $fieldSchema[ 'tab' ];

            if ( isset( $fieldSchema[ 'readonly' ] ) )
                $this->readonly = ( bool ) $fieldSchema[ 'readonly' ];
        }

        function __toString() {
            return $this->asString();
        }

        /**
         * Присваеваем новое значение поля, если конечно сказка завершается счастливым концом
         * @param mixed $value
         */
        public function get() {
            if ( $this->isLanguaged() ) {
                if ( $this->lang ) {
                    if ( isset( $this->langValues[ $this->lang ] ) )
                        return $this->langValues[ $this->lang ];
                }

                return $this->defaultValue;
            }
            return $this->value;
        }

        function isUnique(){
            return $this->unique;
        }

        /**
         * Присваеваем новое значение поля
         * @param mixed $value
         */
        function set( $value ) {
            if ( $this->get() === $value )
                return;

            if ( $this->isLanguaged() ) {
                if ( !$this->lang )
                    throw new FieldException( "Please define language before set value of field" );

                $this->langValues[ $this->lang ] = $value;
            } else {
                $this->value = $value;
            }

            $this->changed = true;
        }

        /**
         * расказывает нам сказку о том, может ли такое значение быть таки правильным
         * с точки зрения логики данного поля
         *
         * @param mixed $newValue
         * @return bool
         */
        public function isValid() {
            if ( is_null( $this->get() ) && !$this->nullable ) {
                return false;
            }
            return true;
        }

        public function setDefaultValue() {
            $this->set( $this->defaultValue );
        }

        public function getDefaultValue() {
            return $this->defaultValue;
        }

        public function isLanguaged() {
            return $this->languaged;
        }

        public function isEditable() {
            return $this->editable;
        }

        public function isSortable() {
            return $this->editable;
        }

        public function isListable() {
            return $this->listable;
        }

        public function setLanguage( $lang ) {
            $this->lang = $lang;
        }

        public function getLanguage() {
            return $this->lang;
        }

        public function getName() {
            if ( $this->name )
                return $this->name;

            return get_class( $this );
        }

        public function getKey() {
            if ( $this->key )
                return $this->key;

            return get_class( $this );
        }

        abstract function getEditMethod();

        function asString() {
            return ( string ) $this->get();
        }

        function asMysql() {
            return $this->get();
        }

        function asAdorned() {
            return $this->asString();
        }

        function isChanged() {
            return $this->changed;
        }

        function setChanged( $value ) {
            $this->changed = ( bool ) $value;
        }

        public function setErrorType( $val ) {
            $this->errorType = $val;
        }

        public function getErrorType() {
            return $this->errorType;
        }

        public function checkProperty( $property ) {
            if ( !isset( $this->$property ) )
                return false;
            return $this->$property;
        }

        public function getValidationMessage( $entityName ) {
            return $entityName;
        }

        public function isReadonly(){
            return $this->readonly;
        }

        public function setReadonly( $readonly ){
            $this->readonly = (bool)$readonly;
        }

        public function isNullable(){
            return $this->nullable;
        }
    }