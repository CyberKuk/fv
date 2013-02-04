<?php
namespace fv\View\Twig\TokenParser;

class Extend extends \Twig_TokenParser_Extends{
    public function parse( \Twig_Token $token ){
        $fileName = $this->parser->getStream()->getCurrent()->getValue();

        var_dump( $fileName );
        return parent::parse( $token );
    }
}
