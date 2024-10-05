<?php

namespace limb\twig\Wysiwyg;

class TokenParser extends \Twig\TokenParser\AbstractTokenParser
{
    public function parse(\Twig\Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $params = [];
        if (!$stream->test(\Twig\Token::BLOCK_END_TYPE)) {
            $params = $this->parser->getExpressionParser()->parseExpression();
        }

        /*if ($stream->nextIf(\Twig\Token::NAME_TYPE, 'with')) {
          $datasource = $this->parser->getExpressionParser()->parseExpression();
        }*/

        if ($stream->nextIf(\Twig\Token::NAME_TYPE, 'form_id')) {
            $form_id = $this->parser->getExpressionParser()->parseExpression();
        }

        //var_dump($params);
        //var_dump($datasource);
        //exit();

        $stream->expect(\Twig\Token::BLOCK_END_TYPE);

        return new Node($form_id, $params, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'wysiwyg';
    }
}
