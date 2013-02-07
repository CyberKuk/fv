<?php

namespace fv\ViewModel;

use fv\ViewModel\Exception\ViewModelException;

/** @noinspection PhpUndefinedClassInspection */
class ViewModelCollection extends ViewModel implements \Iterator {

    private $viewModels;

    /**
     * @param ViewModel[] $viewModels
     * @throws Exception\ViewModelException
     */
    function __construct( array $viewModels ){
        $this->viewModels = $viewModels;
        foreach( $this->viewModels as $viewModel ){
            if( ! $viewModel instanceof ViewModel )
                throw new ViewModelException("Array must be array of ViewModels");
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current() {
        return current($this->viewModels);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next() {
        next( $this->viewModels );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key() {
        return key($this->viewModels);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid() {
        return isset($this->viewModels[ $this->key() ]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind() {
        reset($this->viewModels);
    }


}
