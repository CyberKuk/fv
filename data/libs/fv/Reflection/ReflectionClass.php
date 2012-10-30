<?php

namespace fv\Reflection;

use fv\Collection;

class ReflectionClass extends \ReflectionClass {

    /**
     * @param int|null $filter
     *
     * @return ReflectionProperty[]
     */
    function getProperties( $filter = null ){
        if( !is_null($filter) )
            $properties = parent::getProperties( $filter );
        else
            $properties = parent::getProperties();

        foreach( $properties as $key => $property ){
            $properties[$key] = new ReflectionProperty( $this->getName(), $property->getName() );
        }

        return $properties;
    }

    public function getSchema(){
        $docs = trim( $this->getDocComment() );

        $docs = preg_replace( '/^\/\*\*/', "", $docs );
        $docs = preg_replace( '/\*\//', "", $docs );

        $docs = explode("*", trim($docs));

        $schema = new Collection();
        foreach( $docs as $doc ){
            $doc = trim($doc);

            if( substr($doc, 0, 1) == "@" ){
                $firstSpacePosition = strpos( $doc, " " );
                $propertyName = trim( substr( $doc, 1, $firstSpacePosition - 1 ) );
                $propertyValue = trim( substr( $doc, $firstSpacePosition ) );

                switch( $propertyName ){
                    case 'method':
                        if( $method = $this->parseMethodDoc( $propertyValue ) ){
                            $methodName = $method['name'];
                            unset( $method['name'] );
                            if( ! $schema->methods )
                                $schema->methods = array();
                            $schema->methods->$methodName = $method;
                        }
                        break;
                    default:
                        $schema->$propertyName = $propertyValue;
                }
            }
        }

        return $schema;
    }

    private function parseMethodDoc( $doc ){
        if( preg_match( '/ (\w+) \(  ([^)]*) \)/x', $doc, $methodMatch ) == 0 ){
            return null;
        }

        $methodName = $methodMatch[1];
        $modifiers = array();
        $return = "void";
        $params = array();

        foreach( array("static", "public", "protected", "final", "private") as $modifier ){
            if( strpos( $doc, $modifier ) !== false ){
                $modifiers[] = $modifier;
                $doc = str_replace( $modifier, '', $doc );
            }
        }

        $doc = trim($doc);
        if( preg_match( '/^([\w_\\\]+)\s' . preg_quote($methodMatch[0], '/') . '/', $doc, $match ) > 0 )
            $return = $match[1];

        foreach( explode( ",", $methodMatch[2] ) as $param ){
            $param = trim($param);
            if( !empty($param) )
                $params[] = $param;
        }

        return array(
            "name" => $methodName,
            "modifiers" => $modifiers,
            "params" => $params,
            "return" => $return,
        );
    }
}
