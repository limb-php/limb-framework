<?php
namespace limb\twig\src;

use Wysiwyg_Node;

class Wysiwyg_TokenParser extends \Twig\TokenParser\AbstractTokenParser
{
    public function parse(\Twig\Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $params = [];
        if (!$stream->test(\Twig\Token::BLOCK_END_TYPE)) {
            $params = $this->parser->getExpressionParser()->parseExpression();
        }

        if ($stream->nextIf(/* Token::NAME_TYPE */ 5, 'with')) {
          $datasource = $this->parser->getExpressionParser()->parseExpression();
        }

        //var_dump($params);
        //var_dump($datasource);
        //exit();

        $stream->expect(\Twig\Token::BLOCK_END_TYPE);

        return new Wysiwyg_Node($datasource, $params, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'wysiwyg';
    }
}
