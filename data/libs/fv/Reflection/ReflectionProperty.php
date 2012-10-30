<?php

namespace fv\Reflection;

/**
 * User: cah4a
 * Date: 26.10.12
 * Time: 11:48
 */
class ReflectionProperty extends \ReflectionProperty {

    public function getSchema(){
        $docs = trim( $this->getDocComment() );

        $docs = preg_replace( '/^\/\*\*/', "", $docs );
        $docs = preg_replace( '/\*\//', "", $docs );

        $docs = explode("*", trim($docs));

        $propertySchema = new Schema;
        foreach( $docs as $doc ){
            $doc = trim($doc);

            if( substr($doc, 0, 1) == "@" ){
                $firstSpacePosition = strpos( $doc, " " );
                if( $firstSpacePosition !== false ){
                    $propertyName = trim( substr( $doc, 1, $firstSpacePosition - 1 ) );
                    $propertyValue = trim( substr( $doc, $firstSpacePosition ) );
                } else {
                    $propertyValue = true;
                    $propertyName = substr($doc, 1);
                }

                switch( $propertyName ){
                    case 'var':
                        if( $var = $this->parseVarDoc( $propertyValue ) ){
                            $propertySchema->var = $var;
                        }
                        break;
                    default:
                        $propertySchema->$propertyName = $propertyValue;
                }
            }
        }

        return $propertySchema;
    }

    private function parseVarDoc( $propertyValue ){
        if( preg_match('/^([\|\w_\\\]+)\s?/', $propertyValue, $match) > 0 ){
            return array(
                "type" => $match[1],
                "comment" => trim( preg_replace( "|" . preg_quote($match[0], "|") . "|", "", $propertyValue ) )
            );
        }

        return false;
    }

}
