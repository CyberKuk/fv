<?php

namespace fv\Parser;

use fv\Parser\Exception\ParseException;

abstract class AbstractParser {

    private $file;

    /**
     * @param null|string $file
     */
    public function __construct( $file = null ){
        if( $file )
            $this->setFile($file);
    }

    public function setFile( $file ){
        $this->file = (string)$file;
    }

    public function getFile(){
        return $this->file;
    }

    protected function getContent(){
        if( $this->getFile() )
            return file_get_contents( $this->getFile() );

        throw new ParseException("No file to parse");
    }

    abstract public function parse();
}
