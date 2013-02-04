<?php
namespace Bundle\fv\RoutingBundle\Layout;

class Meta{
    const ATTRIBUTE_CHARSET = "charset";
    const ATTRIBUTE_CONTENT = "content";
    const ATTRIBUTE_HTTP_EQUIV = "http-equiv";
    const ATTRIBUTE_NAME = "name";
    const ATTRIBUTE_SCHEME = "scheme";

    private $content = Array();

    public function setAttribute( $attribute, $value ){
        $this->content[$attribute] = $value;

        return $this;
    }

    /**
     * The most readable method ever!
     * @return string
     */
    public function __toString(){
        $metaContent =
            array_reduce(
                array_map(
                    function ( $attr, $value ){
                        return sprintf( "'%s'='%s' ",
                                        $attr,
                                        $value );
                    },
                    array_keys( $this->content ),
                    $this->content
                ),

                function ( &$metaString, $part ){
                    $metaString .= $part;

                    return $metaString;
                }
            );

        return sprintf( "<meta %s/>",
                        $metaContent );
    }
}
