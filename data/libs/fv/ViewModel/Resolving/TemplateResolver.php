<?php

namespace fv\Resolving;

use fv\Resolving\Exception\ResolvingException as Exception;

class TemplateResolver {
    private $className;
    private $extensions = array( "twig", "tpl" );
    private $namespacesPaths = array();
    public $isSorted = true;

    public static function resolveByClassName( $className ){
        return ( new static )->setClassName( $className )->resolve();
    }

    public static function resolveByClass( $object ){
        return self::resolveByClassName( get_class( $object ) );
    }

    public function setClassName( $className ){
        $this->className = $className;
        return $this;
    }

    public function resolve(){
        return $this->resolveClassName( $this->getClassName() );
    }

    private function resolveClassName( $className ){
        $this->sortNamespaces();
        $classNamespace = $this->getNamespace( $className );

        foreach( $this->namespacesPaths as $namespace => $namespacePath ){
            $pattern = "/^" . preg_quote( $namespace, "/" ) . "/";
            if( preg_match( $pattern, $classNamespace ) > 0 ){
                $tail = trim( preg_replace( $pattern, "", $className ), "\\" );

                if( ( $path = $this->findTemplate( $namespacePath, $tail ) ) !== false )
                    return $path;
            }
        }

        if( ( $path = $this->findTemplate( ".", $className ) ) !== false )
            return $path;

        foreach( $this->extensions as $ext ){
            if( file_exists( "{$path}.{$ext}" ) )
                return "{$path}.{$ext}";
        }

        $parent = get_parent_class( $className );
        if( $parent )
            return $this->resolveClassName( $parent );
        else
            throw new Exception( "Template not found" );
    }

    private function findTemplate( $dir, $tailNamespace ){
        $tail = preg_replace_callback( "/(\\\|^)(\w)/", function( $match ){
            return DIRECTORY_SEPARATOR . strtolower( $match[2] );
        }, $tailNamespace );
        $tail = preg_replace_callback( "/([A-Z])/", function( $match ){
            return "-" . strtolower( $match[1] );
        }, $tail );
        $path = trim( $dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . trim( $tail, DIRECTORY_SEPARATOR );

        var_dump( $path );
        foreach( $this->extensions as $ext ){
            if( file_exists( "{$path}.{$ext}" ) )
                return "{$path}.{$ext}";
        }

        return false;
    }

    private function getNamespace( $path ){
        $namespace = substr( $path, 0, strrpos( $path, "\\" ) );
        $namespace = trim( $namespace, "\\" );
        return $namespace;
    }

    public function getClassName(){
        return $this->className;
    }

    public function addExtension( $extension ){
        if( is_string( $extension ) && !in_array( $extension, $this->extensions ) )
            $this->extensions[] = $extension;

        return $this;
    }

    public function addExtensions( array $extensions ){
        foreach( $extensions as $extension ){
            $this->addExtension( $extension );
        }
        return $this;
    }

    public function removeExtension( $extension ){
        $extension = (string)$extension;
        $key = array_search( $extension, $this->extensions );
        if( $key !== false ){
            unset( $this->extensions[$key] );
        }
        return $this;
    }

    public function clearExtensions(){
        $this->extensions =  array( "twig", "tpl" );
        return $this;
    }

    public function addNamespacePath( $namespace, $path ){
        $this->namespacesPaths[(string)$namespace] = (string)$path;
        $this->isSorted = false;
        return $this;
    }

    public function removeNamespacePathByNamespace( $namespace ){
        if( array_key_exists( $namespace, $this->namespacesPaths ) ){
            unset( $this->extensions[$namespace] );
        }
        return $this;
    }

    public function clearNamespacesPaths(){
        $this->namespacesPaths = array();
        $this->isSorted = true;
        return $this;
    }

    private function sortNamespaces(){
        if( $this->isSorted )
            return;

        uksort( $this->namespacesPaths, function( $a, $b ){
            return mb_strlen( $a ) > mb_strlen( $b );
        } );

        $this->isSorted = true;
    }

    public function setClass( $class ){
        $this->setClassName( get_class( $class ) );
        return $this;
    }
}