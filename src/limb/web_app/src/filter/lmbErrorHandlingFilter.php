<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\web_app\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\core\src\lmbErrorGuard;
use limb\core\src\exception\lmbException;
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbErrorHandlingFilter.
 *
 * @package web_app
 * @version $Id: lmbErrorHandlingFilter.php 6019 2007-06-27 14:29:40Z
 */
class lmbErrorHandlingFilter implements lmbInterceptingFilterInterface
{
  const CONTEXT_RADIUS    = 3;
  const MODE_DEVEL        = 'devel';
  const MODE_PRODUCTION   = 'production';

  protected $toolkit;
  protected $error_page;

  function __construct($error500_page = '')
  {
    if(!$error500_page)
      $error500_page = dirname(__FILE__) . '/../../template/server_error.html';

    $this->error_page = $error500_page;
  }

  function run($filter_chain, $request = null, $callback = null)
  {
    $this->toolkit = lmbToolkit::instance();

    lmbErrorGuard::registerFatalErrorHandler($this, 'handleFatalError');
    lmbErrorGuard::registerExceptionHandler($this, 'handleException');

    return $filter_chain->next($request, $callback);
  }

  function handleFatalError($error)
  {
    $this->toolkit->getLog()->log(LOG_ERR, $error['message']);
    $this->toolkit->getResponse()->reset();

    header('HTTP/1.x 500 Server Error');

    if($this->toolkit->isWebAppDebugEnabled())
      $this->_echoErrorBacktrace($error);
    else
      $this->_echoErrorPage();

    exit(1);
  }

  function handleException($e)
  {
    if(function_exists('\debugBreak'))
      \debugBreak();

    $this->toolkit->getLog()->logException($e);
    $this->toolkit->getResponse()->reset();

    header('HTTP/1.x 500 Server Error');

    if($this->toolkit->isWebAppDebugEnabled())
      $this->_echoExceptionBacktrace($e);
    else
      $this->_echoErrorPage();

    exit(1);
  }

  function _echoErrorPage()
  {
    for($i=0; $i < ob_get_level(); $i++)
      ob_end_clean();

    echo file_get_contents($this->error_page);
  }

  protected function _echoErrorBacktrace($error)
  {
    $message = $error['message'];
    $trace = '';
    $file = $error['file'];
    $line = $error['line'];
    $context = htmlspecialchars($this->_getFileContext($file, $line));
    $request = htmlspecialchars($this->toolkit->getRequest()->dump());

    for($i=0; $i < ob_get_level(); $i++)
      ob_end_clean();

    $session = htmlspecialchars($this->toolkit->getSession()->dump());
    echo $this->_renderTemplate($message, '', $trace, $file, $line, $context, $request, $session);
  }

  protected function _echoExceptionBacktrace($e)
  {
    $params = '';
    if ($e instanceof lmbException)
    {
      $error = htmlspecialchars($e->getOriginalMessage());
      foreach($e->getParams() as $name => $value)
        $params .= $name . '  =>  ' . print_r($value, true) . PHP_EOL;

      $params = htmlspecialchars($params);
    }
    else
    {
      $error = htmlspecialchars($e->getMessage());
    }

    if($e instanceof lmbException)
      $trace = htmlspecialchars($e->getNiceTraceAsString());
    else
      $trace = htmlspecialchars($e->getTraceAsString());

    list($file, $line) = $this->_extractExceptionFileAndLine($e);
    $context = htmlspecialchars($this->_getFileContext($file, $line));
    $request = htmlspecialchars($this->toolkit->getRequest()->dump());
    $session = htmlspecialchars($this->toolkit->getSession()->dump());

    for($i=0; $i < ob_get_level(); $i++)
      ob_end_clean();

    echo $this->_renderTemplate($error, $params, $trace, $file, $line, $context, $request, $session);
  }

  protected function _renderTemplate($error, $params, $trace, $file, $line, $context, $request, $session)
  {
    $formatted_error = nl2br($error);
    $formatted_file = nl2br($file);

    $body = <<<EOD
<html>
<head>
  <title>{$error}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
    body { background-color: #fff; color: #333; }

    body, p, ol, ul, td {
      font-family: verdana, arial, helvetica, sans-serif;
      font-size:   13px;
      line-height: 25px;
    }

    pre {
      background-color: #eee;
      padding: 10px;
      font-size: 11px;
      line-height: 18px;
    }

    a { color: #000; }
    a:visited { color: #666; }
    a:hover { color: #fff; background-color:#000; }
  </style>

  <script>
  function TextDump() {
    w = window.open('', "Error text dump", "scrollbars=yes,resizable=yes,status=yes,width=1000px,height=800px,top=100px,left=100px");
    w.document.write('<html><body>');
    w.document.write('<h1>' + document.getElementById('Title').innerHTML + '</h1>');
    w.document.write(document.getElementById('Context').innerHTML);
    w.document.write(document.getElementById('Trace').innerHTML);
    w.document.write(document.getElementById('Request').innerHTML);
    w.document.write(document.getElementById('Session').innerHTML);
    w.document.write('</body></html>');
    w.document.close();
  }
  </script>
</head>
<body>
<h2 id='Title'>{$formatted_error}</h2>

<p>{$params}</p>

<a href="#" onclick="document.getElementById('Trace').style.display='none';document.getElementById('Context').style.display='block'; return false;">Context</a> |

<a href="#" onclick="document.getElementById('Trace').style.display='block';document.getElementById('Context').style.display='none'; return false;">Call stack</a> |

<a href="#" onclick="TextDump(); return false;">Raw dump</a>

<div id="Context" style="display: block;">
<h3>Error in '{$formatted_file}' around line {$line}:</h3>
<pre>{$context}</pre>
</div>

<div id="Trace" style="display: none;">
<h3>Call stack:</h3>
<pre>{$trace}</pre>
</div>

<div id="Request">
<h2>Request</h2>
<pre>{$request}</pre>
</div>

<div id="Session">
<h2>Session</h2>
<pre>{$session}</pre>
</div>

</body>
</html>
EOD;
    return $body;
  }

  protected function _extractExceptionFileAndLine($e)
  {
    if($e instanceof lmbException)
    {
      $params = $e->getParams();
      if(isset($params['file']))
        return array($params['file'], $params['line']);
    }
    return array($e->getFile(), $e->getLine());
  }

  protected function _getFileContext($file, $line_number)
  {
    $context = array();
    $i = 0;
    foreach(file($file) as $line)
    {
      $i++;
      if($i >= $line_number - self::CONTEXT_RADIUS && $i <= $line_number + self::CONTEXT_RADIUS)
        $context[] = $i . "\t" . $line;

      if($i > $line_number + self::CONTEXT_RADIUS)
        break;
    }

    return "\n" . implode("", $context);
  }
}
