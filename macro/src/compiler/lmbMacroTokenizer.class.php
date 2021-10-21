<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\macro\src\compiler;

use limb\macro\src\compiler\lmbMacroSourceLocation;

/**
 * class lmbMacroTokenizer.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTokenizer
{
  protected $publicId;
  protected $observer;
  protected $rawtext;
  protected $position;
  protected $length;

  function __construct($observer)
  {
    $this->observer = $observer;
  }

  function getLineNumber()
  {
    return 1 + mb_substr_count(mb_substr($this->rawtext, 0, $this->position), "\n");
  }

  function getCurrentLocation()
  {
    return new lmbMacroSourceLocation($this->getPublicId(), $this->getLineNumber());
  }

  function getPublicId()
  {
    return $this->publicId;
  }

  /**
  * Moves the position forward past any whitespace characters
  */
  function ignoreWhitespace()
  {
    while($this->position < $this->length &&
        mb_strpos(" \n\r\t", mb_substr($this->rawtext, $this->position, 1)) !== false)
      $this->position++;
  }

  protected function _parseUntilTagStart($start)
  {
    do
    {
      $php_start = mb_strpos($this->rawtext, '<?php', $start);
      $tag_start = mb_strpos($this->rawtext, '{{', $start);

      //no php found
      if($php_start === false)
      {
        //tag candidate found
        if($tag_start !== false)
        {
          //add preceding characters
          if($tag_start > $start)
            $this->observer->characters(mb_substr($this->rawtext, $start, $tag_start - $start));
          return $tag_start;
        }
        //no tags at all
        else
        {
          //add preceding characters
          if($start != $this->length)
            $this->observer->characters(mb_substr($this->rawtext, $start, $this->length - $start));
          return null;
        }
      }
      //php found
      else
      {
        $php_end = mb_strpos($this->rawtext, '?>', $php_start);
        //php end found
        if($php_end !== false)
        {
          //at the same time tag found and it's not inside php
          if($tag_start !== false && $tag_start < $php_start)
          {
            //add preceding characters
            if($start < $tag_start)
              $this->observer->characters(mb_substr($this->rawtext, $start, $tag_start - $start));
            return $tag_start;
          }
          //extract php block
          else
          {
            //add preceding characters
            if($start < $php_start)
              $this->observer->characters(mb_substr($this->rawtext, $start, $php_start - $start));
            $this->observer->php(mb_substr($this->rawtext, $php_start, $php_end - $php_start + 2));
            $start = $php_end + 2;
          }
        }
        //no php end found, everything is php then
        else
        {
          $this->observer->php(mb_substr($this->rawtext, $php_start));
          return null;
        }

      }
    }while($start < $this->length);
    return null;
  }

  /**
  * Begins the parsing operation, setting up any decorators, depending on
  * parse options invoking _parse() to execute parsing
  */
  function parse($data, $publicId = null)
  {
    $this->rawtext = $data;
    $this->length = mb_strlen($data);
    $this->position = 0;
    $this->publicId = $publicId;

    do
    {
      $start = $this->position;

      $this->position = $this->_parseUntilTagStart($start);
      if($this->position === null)
        return;

      $this->position += 2;   // ignore '{{' string
      if($this->position >= $this->length)
      {
        $this->observer->unexpectedEOF('{{');
        return;
      }

      /*if( strrpos($data, "{{list") !== false ) {
        exit();
      }*/

      $element_pos = $this->position;
      $this->position += 1;

      switch( mb_substr($this->rawtext, $element_pos, 1) )
      {
        case '/':
          $start = $this->position;
          while($this->position < $this->length &&
                !(mb_substr($this->rawtext, $this->position, 1) == '}' &&
                  mb_substr($this->rawtext, $this->position+1, 1) == '}'))
            $this->position++;

          if($this->position >= $this->length)
          {
            $this->observer->unexpectedEOF(mb_substr($this->rawtext, $element_pos - 1));
            return;
          }

          $tag = mb_substr($this->rawtext, $start, $this->position - $start);

          $this->observer->endElement($tag);
          $this->position += 2;   // ignore '}}' string
          break;

      default:
          while($this->position < $this->length && mb_strpos("}/ \n\r\t", mb_substr($this->rawtext, $this->position, 1)) === false)
            $this->position++;

          if($this->position >= $this->length)
          {
            $this->observer->unexpectedEOF(mb_substr($this->rawtext, $element_pos - 1));
            return;
          }

          $tag = mb_substr($this->rawtext, $element_pos, $this->position - $element_pos);
          $attributes = array();

          $this->ignoreWhitespace();

          //tag attributes
          while($this->position < $this->length &&
                mb_substr($this->rawtext, $this->position, 1) != '}' &&
                mb_substr($this->rawtext, $this->position, 1) != '/')
          {
            $start = $this->position;
            while($this->position < $this->length && mb_strpos("}= \n\r\t", mb_substr($this->rawtext, $this->position, 1)) === false)
              $this->position++;

            if($this->position >= $this->length)
            {
              $this->observer->unexpectedEOF(mb_substr($this->rawtext, $element_pos - 1));
              return;
            }

            $attribute_name = mb_substr($this->rawtext, $start, $this->position - $start);
            $attribute_value = null;

            $this->ignoreWhitespace();
            if($this->position >= $this->length)
            {
              $this->observer->unexpectedEOF(mb_substr($this->rawtext, $element_pos - 1));
              return;
            }

            if(mb_substr($this->rawtext, $this->position, 1) == '=')
            {
              $attribute_value = "";

              $this->position++;
              $this->ignoreWhitespace();
              if($this->position >= $this->length)
              {
                $this->observer->unexpectedEOF(mb_substr($this->rawtext, $element_pos - 1));
                return;
              }

              $quote = mb_substr($this->rawtext, $this->position, 1);
              if($quote == '"' || $quote == "'")
              {
                $start = $this->position + 1;
                $this->position = mb_strpos($this->rawtext, $quote, $start);
                if($this->position === false)
                {
                  $this->observer->unexpectedEOF(mb_substr($this->rawtext, $element_pos - 1));
                  return;
                }

                $attribute_value = mb_substr($this->rawtext, $start, $this->position - $start);

                $this->position++;
                if($this->position >= $this->length)
                {
                  $this->observer->unexpectedEOF(mb_substr($this->rawtext, $element_pos - 1));
                  return;
                }

                if(mb_strpos("/} \n\r\t", mb_substr($this->rawtext, $this->position, 1)) === false)
                {
                  $this->observer->invalidAttributeSyntax(mb_substr($this->rawtext, $this->position));
                }
              }
              else
              {
                $start = $this->position;
                while($this->position < $this->length && mb_strpos("} \n\r\t", mb_substr($this->rawtext, $this->position, 1)) === false)
                  $this->position++;

                if($this->position >= $this->length)
                {
                  $this->observer->unexpectedEOF(mb_substr($this->rawtext, $element_pos - 1));
                  return;
                }
                $attribute_value = mb_substr($this->rawtext, $start, $this->position - $start);
              }
            }

            $attributes[$attribute_name] = $attribute_value;

            $this->ignoreWhitespace();
          }

          if($this->position >= $this->length)
          {
            $this->observer->unexpectedEOF(mb_substr($this->rawtext, $element_pos - 1));
            return;
          }

          //self closing tag check
          if(mb_substr($this->rawtext, $this->position, 1) == '/' && mb_substr($this->rawtext, $this->position+1, 1) == '}')
          {
            $this->position += 2;
            if($this->position >= $this->length)
            {
              $this->observer->unexpectedEOF(mb_substr($this->rawtext, $element_pos - 1));
              return;
            }

            if( mb_substr($this->rawtext, $this->position, 1) != '}')
            {
              $start = $this->position;
              while($this->position < $this->length && mb_substr($this->rawtext, $this->position, 1) != '}')
                $this->position++;

              if($this->position >= $this->length)
              {
                $this->observer->invalidEntitySyntax(mb_substr($this->rawtext, $element_pos - 2));
                break;
              }

              $this->observer->invalidEntitySyntax(mb_substr($this->rawtext, $element_pos - 2,
                                                          $this->position - $element_pos + 2));
              $this->position += 1;
              break;
            }
            $this->observer->emptyElement($tag, $attributes);
          }
          else
          {
            $this->observer->startElement($tag, $attributes);
            //skipping }
            $this->position += 1;
          }

          $this->position += 1;
          break;
        }
    }
    while($this->position < $this->length);
  }
}

