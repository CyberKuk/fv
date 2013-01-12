<?php

namespace fv\Yaml;

class YamlParser {

    static function parse( $data ) {
        $data = explode( "\n", $data );
        $config = array();
        $currentConfig = &$config;
        $path = array();

        foreach( $data as $configLine ){
            $configLine = preg_replace("/#.*$/", "", $configLine);

            if( strlen( trim( $configLine ) ) == 0 )
                continue;

            $indent = strlen( $configLine ) - strlen( ltrim( $configLine ) );
            $configLine = trim( $configLine );

            foreach( $path as $key => $value ){
                if( $key > $indent )
                    unset( $path[$key] );
            }

            if( empty( $path[$indent] ) ){
                $path[$indent] = &$currentConfig;
            } else{
                $currentConfig = &$path[$indent];
            }

            $key = substr( $configLine, 0, strpos( $configLine, ":" ) );
            $value = substr( $configLine, strpos( $configLine, ":" ) + 1 );

            if( strlen( $value = trim( $value ) ) > 0 ){
                $currentConfig[$key] = self::parseValue( $value );
            } else{
                if( !isset( $currentConfig[$key] ) )
                    $currentConfig[$key] = array();
                $currentConfig = &$currentConfig[$key];
            }
        }

        return $config;
    }

    private static function parseValue( $value ){
        $value = trim( $value );

        if( $value == "~" ){
            return "";
        }

        if( $value == "true" ){
            return true;
        }

        if( $value == "false" ){
            return false;
        }

        if( $value ){
            if( ( $value{0} == "[" ) && ( $value{strlen( $value ) - 1} == "]" ) ){
                $value = explode( ",", substr( $value, 1, strlen( $value ) - 2 ) );
                foreach( $value as &$oneValue ){
                    $oneValue = self::parseValue( trim( $oneValue ) );
                }

                return $value;
            }

            if( ( $value{0} == "{" ) && ( $value{strlen( $value ) - 1} == "}" ) ){
                $value = explode( ",", substr( $value, 1, strlen( $value ) - 2 ) );

                $result = array();

                foreach( $value as $oneValue ){
                    $a_key = substr( $oneValue, 0, strpos( $oneValue, ":" ) );
                    $a_value = substr( $oneValue, strpos( $oneValue, ":" ) + 1 );

                    $result[trim( $a_key )] = self::parseValue( trim( $a_value ) );
                }

                return $result;
            }

            /**
            if( preg_match_all( "/%([a-z_0-9\.]+)%/i", $value, $matches, PREG_SET_ORDER ) ){
                foreach( $matches as $match ){
                    $value = str_replace( $match[0], $this->get( $match[1], ( defined( $match[1] ) ? constant( $match[1] ) : null ) ), $value );
                }
            }
             **/
        }

        return $value;
    }

    public function get( $string ){

    }


}
