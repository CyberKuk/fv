<?php

namespace fv;

class Collection implements \ArrayAccess, \Iterator, \Countable {

    private $params = array();

    function __construct( array $value = null ){
        if( !is_null( $value ) ){
            foreach( $value as $key => $var )
                $this->$key = $var;
        }
    }

    /**
     * @param $name
     *
     * @return Collection|string|integer|mixed
     */
    function __get( $name ) {
        if( !isset($this->params[$name]) )
            return null;

        return $this->params[$name];
    }

    function __set( $name, $value ) {
        if( is_array($value) )
            $this->params[$name] = new Collection( $value );
        else
            $this->params[$name] = $value;
    }

    public function get( $path ) {
        $current = &$this;

        foreach( explode(".", $path) as $key ){
            $current = $current->$key;

            if( is_null($current) )
                return null;
        }

        return $current;
    }

    public function merge( Collection $collection ){
        foreach( $collection as $key => $value ){
            if( isset( $this->params[$key] ) ){
                $old = $this->params[$key];

                if( $old instanceof Collection && $value instanceof Collection ){
                    $old->merge($value);
                    continue;
                }
            }

            $this->params[$key] = $value;
        }
    }

    function filter( callable $callback ){
        $collection = new Collection;
        foreach( $this as $key => $value ){
            if( $callback($value) )
                $collection->$key = $value;
        }
        return $collection;
    }

    function map( callable $callback ){
        $collection = new Collection;
        foreach( $this as $key => $value ){
            $collection->$key = $callback($value);
        }
        return $collection;
    }

    function keys(){
        return array_keys( $this->params );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current() {
        return current( $this->params );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next() {
        next( $this->params );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key() {
        return key( $this->params );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *       Returns true on success or false on failure.
     */
    public function valid() {
        return isset( $this->params[$this->key()] );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind() {
        reset( $this->params );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     * </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     *       The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists( $offset ) {
        return isset( $this->params[$offset] );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     * </p>
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet( $offset ) {
        return $this->params[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     * </p>
     * @param mixed $value  <p>
     *                      The value to set.
     * </p>
     *
     * @return void
     */
    public function offsetSet( $offset, $value ) {
        $this->params[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     * </p>
     *
     * @return void
     */
    public function offsetUnset( $offset ) {
        unset( $this->params[$offset] );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     *       The return value is cast to an integer.
     */
    public function count() {
        return count($this->params);
    }
}
