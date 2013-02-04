<?php
namespace fv\View\Twig;


class Environment extends \Twig_Environment{
    /**
     * @var \Twig_TokenParserInterface[]
     */
    protected $overrideParsers = array();

    public function overrideTokenParser( \Twig_TokenParserInterface $parser ){
        $this->overrideParsers[] = $parser;

        return $this;
    }

    public function getTokenParsers(){
        parent::getTokenParsers();

        foreach( $this->overrideParsers as $parser ){
            if( $parser instanceof \Twig_TokenParserInterface ){
                $this->parsers->addTokenParser( $parser );
            }
            elseif( $parser instanceof \Twig_TokenParserBrokerInterface ){
                $this->parsers->addTokenParserBroker( $parser );
            }
            else{
                throw new \Twig_Error_Runtime( 'getTokenParsers() must return an array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances' );
            }
        }

        return $this->parsers;
    }
}
