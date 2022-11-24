<?php
set_include_path(get_include_path() . PATH_SEPARATOR .
                 dirname(__FILE__) . '/lib/minify/lib/src/' . PATH_SEPARATOR);

lmb_require(dirname(__FILE__) . '/toolkit.inc.php');

/**
* Returns GZIP compressed content string with header
*
**/
function gz_compress($content)
{
  if(!empty($_SERVER['HTTP_ACCEPT_ENCODING']) && strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
  {
    return create_gz_compress($content);
  }
  else
  {
    return false;
  }
}

function create_gz_compress($content)
{
  if( function_exists('gzcompress') )
  {
    $Size = strlen( $content );
    $Crc = crc32( $content );

    $content = gzcompress( $content, 2 );
    $content = substr( $content, 0, strlen($content) - 4 );

    $gz_content = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
    $gz_content .= ( $content );
    $gz_content .= ( pack( 'V', $Crc ) );
    $gz_content .= ( pack( 'V', $Size ) );

    return $gz_content;
  }
  else
  {
    return false;
  }
}

/**
* Sets the correct gzip header
*
**/
function set_gzip_header($response = null) {
  if (!empty($_SERVER["HTTP_ACCEPT_ENCODING"]))
  {
    if(strpos(" " . $_SERVER["HTTP_ACCEPT_ENCODING"], "x-gzip")) {
      $encoding = "x-gzip";
    }
    if(strpos(" " . $_SERVER["HTTP_ACCEPT_ENCODING"], "gzip")) {
      $encoding = "gzip";
    }
    if(!empty($encoding)) {
      if($response)
        $response->addHeader("Content-Encoding: " . $encoding);
      else
        header("Content-Encoding: " . $encoding);
    }
  }
}

/* */
function processUrlPath(&$path, $route = null)
{
  $common_conf = lmbToolkit :: instance()->getCommonSettings();
  $suffix = $common_conf['url_suffix'];

  if( $suffix )
  {
    $uri = new lmbUri($path);
    $old_path = rtrim($uri->getPath(), '/');
    if( (strpos($old_path, ".") === false) && ($old_path != "/" && $old_path != "") )
      $uri->setPath( $old_path . $suffix );

    $path = $uri->toString();
  }
}
function processPath($path)
{
  processUrlPath($path);
  return $path;
}

function filterPath($path)
{
  $common_conf = lmbToolkit :: instance()->getCommonSettings();
  $suffix = $common_conf['url_suffix'];

  if($suffix)
  {
    $pos = strrpos($path, $suffix);
    if($pos !== false)
    {
      $path = substr($path, 0, -strlen($suffix));
    }
  }

  return $path;
}

