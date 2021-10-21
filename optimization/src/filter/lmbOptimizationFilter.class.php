<?php
namespace limb\optimization\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\toolkit\src\lmbToolkit;
use limb\macro\src\compiler\lmbMacroCompiler;
use limb\wact\src\compiler\WactCompiler;

class lmbOptimizationFilter implements lmbInterceptingFilterInterface
{
  public function run($filter_chain)
  {
    $conf = lmbToolkit :: instance()->getConf('optimization');

    // minify html template
    if( $conf->has('HTML_MINIFY') && ($conf->get('HTML_MINIFY') === true) )
    {
      lmbMacroCompiler :: registerOnCompileCallback(array($this, 'onCompileTemplate'));

      WactCompiler :: registerOnCompileCallback(array($this, 'onCompileTemplate'));
    }

    $filter_chain->next();

    $response = lmbToolkit :: instance()->getResponse();

    // gzip output html
    if( $conf->has('HTML_GZIP') && ($conf->get('HTML_GZIP') === true) && ($response->getContentType() == 'text/html') )
    {
      $content = gz_compress( $response->getResponseString() );
      if(false !== $content)
      {
        set_gzip_header($response);
        $response->write($content);
      }
    }

    $response->commit();
  }

  function onCompileTemplate($data)
  {
    require_once('JSMin.php');
    require_once('Minify/HTML.php');
    require_once('Minify/CSS.php');
    require_once('Minify/CommentPreserver.php');

    $conf = lmbToolkit :: instance()->getConf('optimization');

    $minifier_params = array(
     'xhtml' => true,
     'jsMinifier' => ($conf->has('HTML_JS_MINIFY_ENABLE') && $conf->get('HTML_JS_MINIFY_ENABLE') === true) ? array('JSMin', 'minify') : null,
     'cssMinifier' => ($conf->has('HTML_CSS_MINIFY_ENABLE') && $conf->get('HTML_CSS_MINIFY_ENABLE') === true) ? array('Minify_CSS', 'minify') : null
    );

    return Minify_HTML :: minify($data, $minifier_params);
  }
}

