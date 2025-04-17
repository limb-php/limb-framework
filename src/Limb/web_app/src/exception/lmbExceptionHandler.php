<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\exception;

use limb\core\exception\lmbException;
use limb\toolkit\lmbToolkit;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * class lmbExceptionHandler.
 *
 * @package core
 * @version $Id$
 */
class lmbExceptionHandler
{
    const CONTEXT_RADIUS = 3;
    const MODE_DEVEL = 'devel';
    const MODE_PRODUCTION = 'production';

    protected string $error_page;
    protected LoggerInterface $logger;

    function __construct($error500_page, $logger = null)
    {
        $this->error_page = $error500_page;

        if($logger)
            $this->logger = $logger;
        else
            $this->logger = lmbToolkit::instance()->getLog('error');
    }

    public function handleFatalError($error, $request): ResponseInterface
    {
        $this->_obEndClean();

        $toolkit = lmbToolkit::instance();

        $this->logger->error($error['message']);

        if ($toolkit->isWebAppDebugEnabled())
            $body = $this->_echoErrorBacktrace($error, $request);
        else
            $body = $this->_echoErrorPage();

        $response = response()
            ->reset()
            ->setStatusCode(500, 'Server Error');

        if ($this->_isAcceptJson($request)) {
            $response->json([
                'error' => $body,
                'type' => 'error'
            ]);
        } else {
            $response
                ->write($body);
        }

        return $response;
    }

    public function handleException($e, $request): ResponseInterface
    {
        $this->_obEndClean();

        if (function_exists('\debugBreak'))
            \debugBreak();

        $this->logException($e);

        if (lmbToolkit::instance()->isWebAppDebugEnabled())
            $body = $this->_echoExceptionBacktrace($e, $request);
        else
            $body = $this->_echoErrorPage();

        $response = response()
            ->reset()
            ->setStatusCode(500, 'Server Error');

        if ($this->_isAcceptJson($request)) {
            $response->json([
                'error' => $body,
                'type' => 'exception'
            ]);
        } else {
            $response->write($body);
        }

        return $response;
    }


    protected function _isAcceptJson($request): bool
    {
        $accept = $request->getHeaderLine('Accept');
        return $accept && (strpos($accept, 'json') !== false);
    }

    protected function _echoErrorPage(): string
    {
        return file_get_contents($this->error_page);
    }

    protected function _echoErrorBacktrace($error, $request): string
    {
        $toolkit = lmbToolkit::instance();

        $message = $error['message'];
        $trace = '';
        $file = $error['file'];
        $line = $error['line'];
        $context = htmlspecialchars($this->_getFileContext($file, $line));
        $request_text = htmlspecialchars($request->dump());
        $session_text = htmlspecialchars($toolkit->getSession()->dump());

        return $this->_renderTemplate($message, '', $trace, $file, $line, $context, $request_text, $session_text);
    }

    protected function _echoExceptionBacktrace($e, $request): string
    {
        $toolkit = lmbToolkit::instance();

        $params = '';
        if ($e instanceof lmbException) {
            $error = htmlspecialchars($e->getOriginalMessage());
            foreach ($e->getParams() as $name => $value)
                $params .= $name . '  =>  ' . print_r($value, true) . PHP_EOL;

            $params = htmlspecialchars($params);
        } else {
            $error = htmlspecialchars($e->getMessage());
        }

        if ($e instanceof lmbException)
            $trace = htmlspecialchars($e->getNiceTraceAsString());
        else
            $trace = htmlspecialchars($e->getTraceAsString());

        list($file, $line) = $this->_extractExceptionFileAndLine($e);
        $context_text = htmlspecialchars($this->_getFileContext($file, $line));
        $request_text = htmlspecialchars($request->dump());
        $session_text = htmlspecialchars($toolkit->getSession()->dump());

        return $this->_renderTemplate($error, $params, $trace, $file, $line, $context_text, $request_text, $session_text);
    }

    protected function logException($exception): void
    {
        if ($exception instanceof lmbException)
            $this->logger->log(
                LogLevel::ERROR,
                $exception->getMessage(),
                [
                    'exception' => $exception,
                    'params' => $exception->getParams(),
                ],
                $exception->getBacktraceObject() // ??? no in PSR-3
            );
        else
            $this->logger->log(
                LogLevel::ERROR,
                $exception->getMessage(),
                [
                    'exception' => $exception,
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine()
                ]
            );
    }

    protected function _getFileContext($file, $line_number): string
    {
        $context = array();
        $i = 0;
        foreach (file($file) as $line) {
            $i++;
            if ($i >= $line_number - self::CONTEXT_RADIUS && $i <= $line_number + self::CONTEXT_RADIUS)
                $context[] = $i . "\t" . $line;

            if ($i > $line_number + self::CONTEXT_RADIUS)
                break;
        }

        return "\n" . implode("", $context);
    }

    protected function _renderTemplate($error, $params, $trace, $file, $line, $context, $request, $session): string
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
</head>
<body>
<h2 id='Title'>{$formatted_error}</h2>

<p>{$params}</p>

<div id="Context" style="display: block;">
<h3>Error in '{$formatted_file}' around line {$line}:</h3>
<pre>{$context}</pre>
</div>

<div id="Trace" style="display: block;">
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
        if ($e instanceof lmbException) {
            $params = $e->getParams();
            if (isset($params['file']))
                return array($params['file'], $params['line']);
        }
        return array($e->getFile(), $e->getLine());
    }

    protected function _obEndClean()
    {
        for ($i = 0; $i < ob_get_level(); $i++) {
            ob_end_clean();
        }
        ob_start();
    }
}
