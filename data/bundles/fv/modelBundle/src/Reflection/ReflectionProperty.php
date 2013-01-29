<?php

namespace Bundle\fv\ModelBundle\Reflection;

use fv\Collection\Collection;

class ReflectionProperty extends \ReflectionProperty {

    /**
     * @return \fv\Collection\Collection
     */
    public function getSchema(){
        $docs = trim( $this->getDocComment() );

        $docs = preg_replace( '/^\/\*\*/', "", $docs );
        $docs = preg_replace( '/\*\//', "", $docs );

        $docs = explode("*", trim($docs));

        $propertySchema = new Collection;
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
                    case 'field':
                        if( $var = $this->parseFieldDoc( $propertyValue ) ){
                            $propertySchema->field = $var;
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

    private function parseFieldDoc( $propertyValue ) {
        $result = array();
        if( preg_match('/\((.*)\)/', $propertyValue, $match) > 0 ){
            $settings = explode( ",", $match[1] );
            foreach( $settings as $setting ){
                if( preg_match('/(.*)=(.*)/', $setting, $match) ){
                    $key = trim($match[1]);
                    $value = trim($match[2]);
                    if( strtolower( $value ) == "true" )
                        $value = true;

                    if( strtolower( $value ) == "false" )
                        $value = false;

                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

}
