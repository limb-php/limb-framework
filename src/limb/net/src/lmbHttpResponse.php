<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\net\src;

use limb\core\src\exception\lmbException;

/**
 * class lmbHttpResponse.
 *
 * @package net
 * @version $Id: lmbHttpResponse.php 7486 2009-01-26 19:13:20Z
 */
class lmbHttpResponse
{
    public const HTTP_CONTINUE = 100;
    public const HTTP_SWITCHING_PROTOCOLS = 101;
    public const HTTP_PROCESSING = 102;            // RFC2518
    public const HTTP_EARLY_HINTS = 103;           // RFC8297
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_RESET_CONTENT = 205;
    public const HTTP_PARTIAL_CONTENT = 206;
    public const HTTP_MULTI_STATUS = 207;          // RFC4918
    public const HTTP_ALREADY_REPORTED = 208;      // RFC5842
    public const HTTP_IM_USED = 226;               // RFC3229
    public const HTTP_MULTIPLE_CHOICES = 300;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_SEE_OTHER = 303;
    public const HTTP_NOT_MODIFIED = 304;
    public const HTTP_USE_PROXY = 305;
    public const HTTP_RESERVED = 306;
    public const HTTP_TEMPORARY_REDIRECT = 307;
    public const HTTP_PERMANENTLY_REDIRECT = 308;  // RFC7238
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_PAYMENT_REQUIRED = 402;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_NOT_ACCEPTABLE = 406;
    public const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const HTTP_REQUEST_TIMEOUT = 408;
    public const HTTP_CONFLICT = 409;
    public const HTTP_GONE = 410;
    public const HTTP_LENGTH_REQUIRED = 411;
    public const HTTP_PRECONDITION_FAILED = 412;
    public const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    public const HTTP_REQUEST_URI_TOO_LONG = 414;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const HTTP_EXPECTATION_FAILED = 417;
    public const HTTP_I_AM_A_TEAPOT = 418;                                               // RFC2324
    public const HTTP_MISDIRECTED_REQUEST = 421;                                         // RFC7540
    public const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    public const HTTP_LOCKED = 423;                                                      // RFC4918
    public const HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
    public const HTTP_TOO_EARLY = 425;                                                   // RFC-ietf-httpbis-replay-04
    public const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    public const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    public const HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    public const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    public const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;                               // RFC7725
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED = 501;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_GATEWAY_TIMEOUT = 504;
    public const HTTP_VERSION_NOT_SUPPORTED = 505;
    public const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    public const HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    public const HTTP_LOOP_DETECTED = 508;                                               // RFC5842
    public const HTTP_NOT_EXTENDED = 510;                                                // RFC2774
    public const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585

    protected $response_string = '';
    protected $response_file_path = '';
    protected $headers = array();
    protected $cookies = array();
    protected $is_redirected = false;
    protected $redirected_path = false;
    protected $redirect_strategy = null;
    protected $transaction_started = false;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $statusText = '';

    public function __construct($content = '', $status = 200, $headers = [])
    {
        $this->headers = $headers;

        $this->response_string = $content;
        $this->statusCode = $status;
        $this->version = '1.0';
    }

  function setRedirectStrategy($strategy)
  {
    $this->redirect_strategy = $strategy;
  }

  function redirect($path)
  {
    $this->_ensureTransactionStarted();

    if ($this->is_redirected)
      return;

    if($this->redirect_strategy === null)
      $strategy = $this->_getDefaultRedirectStrategy();
    else
      $strategy = $this->redirect_strategy;

    $strategy->redirect($this, $path);

    $this->is_redirected = true;
    $this->redirected_path = $path;
  }

  function getRedirectedPath()
  {
    if($this->is_redirected)
      return $this->redirected_path;
    else
      return '';
  }

  function getRedirectedUrl()
  {
    return $this->getRedirectedPath();
  }

  protected function _getDefaultRedirectStrategy()
  {
    return new lmbHttpRedirectStrategy();
  }

  function isRedirected()
  {
      return $this->is_redirected;
  }

  /* */
  function reset()
  {
    $this->response_string = '';
    $this->response_file_path = '';
    $this->headers = array();
    $this->is_redirected = false;
    $this->transaction_started = false;
  }

  function start()
  {
    $this->reset();
    $this->transaction_started = true;
  }

  protected function _checkStatusInHeader($header)
  {
      if(preg_match('/^HTTP\/1.\d[^\d]+(\d+)[^\d]*/i', $header, $matches)) {
          if( isset($matches[1]) ) {
              $status = (int)$matches[1];
              $this->statusCode = $status;

              return true;
          }
      }

      return false;
  }

  function getStatus()
  {
    return $this->statusCode;
  }
  function getStatusCode()
  {
      return $this->getStatus();
  }

  function getDirective($directive_name)
  {
    $directive = null;

    $regex = '/^' . preg_quote($directive_name). "\s*/i";
    foreach($this->headers as $header => $value)
    {
      if(preg_match($regex, $header, $matches))
        $directive = $value;
    }

    return $directive ?? false;
  }

  function getContentType()
  {
    if($directive = $this->getDirective('content-type'))
    {
      list($type, ) = explode(';', $directive);
      return trim($type);
    }
    else
      return 'text/html';
  }

  function getMimeType()
  {
    return $this->getContentType();
  }

  function getResponseString()
  {
    return $this->response_string;
  }

  function isStarted()
  {
    return $this->transaction_started;
  }

  function isEmpty()
  {
    $status = $this->getStatus();

    $res = (
      !$this->is_redirected &&
      empty($this->response_string) &&
      empty($this->response_file_path) &&
      ($status != 304 &&  $status != 412));//???

    return $res;
  }

  function isHeadersSent()
  {
    return sizeof($this->headers) > 0;
  }

  function isFileSent()
  {
    return !empty($this->response_file_path);
  }

  function reload()
  {
    $this->redirect($_SERVER['PHP_SELF']);
  }

  /**
   * Add header
   * @param string $header
   * @param string|null $value
   */
  function addHeader($header, $value = null)
  {
      $this->_ensureTransactionStarted();

      $isStatus = $this->_checkStatusInHeader($header);
      if($isStatus)
          return;

      if($value === null && !is_array($header)) {
          @list($header, $value) = explode(':', $header);
      }

      $this->headers[$header] = !empty($value) ? trim($value) : null;
  }

  function setCookie($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false)
  {
    $this->_ensureTransactionStarted();

    $this->cookies[$name] = array(
        'name' => $name,
        'value' => $value,
        'expire' => $expire,
        'path' => $path,
        'domain' => $domain,
        'secure' => $secure
    );
  }

  function getCookies()
  {
    return $this->cookies;
  }

  function removeCookie($name, $path = '/', $domain = '', $secure = false)
  {
    if(isset($this->cookies[$name]))
    {
      $path  = $this->cookies[$name]['path'];
      $domain  = $this->cookies[$name]['domain'];
      $secure  = $this->cookies[$name]['secure'];

      unset($this->cookies[$name]);
    }

    $this->setCookie($name, '', 1, $path, $domain, $secure);
  }

  public function readFile($file_path)
  {
    $this->_ensureTransactionStarted();

    $this->response_file_path = $file_path;
  }

  public function write($string)
  {
    $this->_ensureTransactionStarted();

    $this->response_string = $string;
  }

  public function append($string)
  {
    $this->_ensureTransactionStarted();

    $this->response_string .= $string;
  }

  public function commit()
  {
    $this->_ensureTransactionStarted();

    $this->sendHeaders();

    if(!empty($this->response_file_path))
      $this->_sendFile($this->response_file_path);
    else if(!empty($this->response_string))
      $this->_sendString($this->response_string);

    $this->transaction_started = false;
  }
  public function send()
  {
      $this->commit();
  }

  /**
  * Sends HTTP headers.
  *
  * @return $this
  */
  public function sendHeaders()
  {
      // headers have already been sent by the developer
      if (headers_sent()) {
          return $this;
      }

      // status
      header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText), true, $this->statusCode);

      // headers
      foreach($this->headers as $header => $value) {
          $this->_sendHeader($header, $value);
      }

      // cookies
      foreach($this->cookies as $cookie) {
          $this->_sendCookie($cookie);
      }

      return $this;
  }

  public function json($data)
  {
      $json = \json_encode($data);
      if($json === false) {
          $error = \json_last_error_msg();
          throw new lmbException('JSON encode error: ' . $error);
      }

      $this->addHeader('Content-type', 'application/json');

      $this->write($json);
  }

  protected function _sendHeader($header, $value)
  {
      header($header . ': ' . $value);
  }

  protected function _sendCookie($cookie)
  {
    setcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure']);
  }

  protected function _sendString($string)
  {
    echo $string;
  }

  protected function _sendFile($file_path)
  {
    readfile($file_path);
  }

  protected function _ensureTransactionStarted()
  {
    if(!$this->transaction_started)
      $this->start();
  }
}
