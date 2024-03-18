<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net\src;

use Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\StreamInterface;

/**
 * class lmbHttpResponse.
 *
 * @package net
 * @version $Id: lmbHttpResponse.php 7486 2009-01-26 19:13:20Z
 */
class lmbHttpResponse implements ResponseInterface
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

    /**
     * Status codes translation table.
     *
     * The list of codes is complete according to the
     * {@link https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml Hypertext Transfer Protocol (HTTP) Status Code Registry}
     * (last updated 2021-10-01).
     *
     * Unless otherwise noted, the status code is defined in RFC2616.
     *
     * @var array
     */
    public static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Content Too Large',                                           // RFC-ietf-httpbis-semantics
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Content',                                       // RFC-ietf-httpbis-semantics
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Too Early',                                                   // RFC-ietf-httpbis-replay-04
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',                                     // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    ];

    //protected $response_string = '';
    protected $response_file_path = '';
    protected $headers = array();
    protected $cookies = array();
    protected $is_redirected = false;
    protected $redirected_path = false;
    protected $redirect_strategy = null;
    protected $transaction_started = false;
    /** @var StreamInterface|null */
    private $stream;

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
    protected $statusText = null;

    public function __construct($content = '', $status = 200, $headers = [])
    {
        $this->headers = $headers;

        $this->version = '1.0';
        $this->statusCode = $status;
        $this->write($content);
    }

    function setRedirectStrategy($strategy)
    {
        $this->redirect_strategy = $strategy;
    }

    function redirect($path): self
    {
        if ($this->is_redirected)
            return $this;

        if ($this->redirect_strategy === null)
            $strategy = $this->_getDefaultRedirectStrategy();
        else
            $strategy = $this->redirect_strategy;

        $strategy->redirect($this, $path);

        $this->is_redirected = true;
        $this->redirected_path = $path;

        return $this;
    }

    function getRedirectedPath()
    {
        if ($this->is_redirected)
            return $this->redirected_path;
        else
            return '';
    }

    function getRedirectedUrl()
    {
        return $this->getRedirectedPath();
    }

    protected function _getDefaultRedirectStrategy(): lmbHttpRedirectStrategy
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
        //$this->response_string = '';
        $this->stream = null;
        $this->response_file_path = '';
        $this->headers = array();
        $this->is_redirected = false;
        $this->transaction_started = false;

        return $this;
    }

    protected function _checkStatusInHeader($header): bool
    {
        if (preg_match('/^HTTP\/1.\d[^\d]+(\d+)[^\d]*/i', $header, $matches)) {
            if (isset($matches[1])) {
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

    function getStatusCode(): int
    {
        return $this->getStatus();
    }

    function setStatusCode($code, $text = null): self
    {
        $this->statusCode = $code;

        $this->statusText = $text;

        return $this;
    }

    function getDirective($directive_name)
    {
        $directive = null;

        $regex = '/^' . preg_quote($directive_name) . "\s*/i";
        foreach ($this->headers as $header => $value) {
            if (preg_match($regex, $header, $matches))
                $directive = $value;
        }

        return $directive ?? false;
    }

    function getContentType(): string
    {
        if ($directive = $this->getDirective('content-type')) {
            list($type,) = explode(';', $directive);
            return trim($type);
        } else
            return 'text/html';
    }

    function getMimeType(): string
    {
        return $this->getContentType();
    }

    /** @deprecated */
    function getResponseString()
    {
        //return $this->response_string;
    }

    function isStarted()
    {
        return $this->transaction_started;
    }

    /** @deprecated */
    function isEmpty(): bool
    {
        $status = $this->getStatus();

        $res = (
            !$this->is_redirected &&
            //empty($this->response_string) &&
            !$this->stream->getSize() &&
            empty($this->response_file_path) &&
            ($status != 304 && $status != 412));//???

        return $res;
    }

    function isHeadersSent(): bool
    {
        return sizeof($this->headers) > 0;
    }

    function isFileSent(): bool
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
        $isStatus = $this->_checkStatusInHeader($header);
        if ($isStatus)
            return $this;

        if ($value === null && !is_array($header)) {
            @list($header, $value) = explode(':', $header);
        }

        $trimmed = trim($value, " \t");

        $this->headers[$header] = !empty($value) ? $trimmed : null;

        return $this;
    }

    function setCookie($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false)
    {
        $this->cookies[$name] = array(
            'name' => $name,
            'value' => $value,
            'expire' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure
        );

        return $this;
    }

    function getCookies()
    {
        return $this->cookies;
    }

    function removeCookie($name, $path = '/', $domain = '', $secure = false)
    {
        if (isset($this->cookies[$name])) {
            $path = $this->cookies[$name]['path'];
            $domain = $this->cookies[$name]['domain'];
            $secure = $this->cookies[$name]['secure'];

            unset($this->cookies[$name]);
        }

        $this->setCookie($name, '', 1, $path, $domain, $secure);
    }

    public function readFile($file_path)
    {
        $this->response_file_path = $file_path;

        return $this;
    }

    public function write($string)
    {
        //$this->response_string = $string;
        $this->getBody()->write($string);

        return $this;
    }

    /** @deprecated */
    public function append($string)
    {
        //$this->response_string .= $string;
        $this->getBody()->write($this->getBody()->getContents() . $string);

        return $this;
    }

    public function commit()
    {
        $this->sendHeaders();

        $this->sendContent();

        $this->transaction_started = true;

        return $this;
    }

    public function send()
    {
        return $this->commit();
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
        foreach ($this->headers as $header => $value) {
            $this->_sendHeader($header, $value);
        }

        // cookies
        foreach ($this->cookies as $cookie) {
            $this->_sendCookie($cookie);
        }

        return $this;
    }

    public function json($data)
    {
        return new lmbJsonHttpResponse($data, $this->statusCode, $this->headers);
    }

    protected function _sendHeader($header, $value)
    {
        header($header . ': ' . $value);
    }

    protected function _sendCookie($cookie)
    {
        setcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure']);
    }

    public function sendContent()
    {
        if (!empty($this->response_file_path))
            $this->_sendFile($this->response_file_path);
        //else if (!empty($this->response_string))
            //echo $this->response_string;
        else if ($this->stream->getSize())
            echo $this->stream;

        return $this;
    }

    protected function _sendFile($file_path)
    {
        readfile($file_path);
    }

    public function getProtocolVersion(): string
    {
        return $this->version;
    }

    public function withProtocolVersion($version): self
    {
        if ($this->version === $version) {
            return $this;
        }

        $new = clone($this);
        $new->version = $version;
        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader($name): array
    {
        $name = strtolower($name);

        return $this->headers[$name] ?? [];
    }

    public function getHeaderLine($name): string
    {
        return implode(': ', $this->getHeader($name));
    }

    public function withHeader($name, $value): self
    {
        $normalized = strtolower($name);

        $new = clone($this);
        $new->headers[$normalized] = $value;

        return $new;
    }

    public function withAddedHeader($name, $value): self
    {
        $normalized = strtolower($name);

        $new = clone($this);
        if(!isset($new->headers[$normalized]))
            $new->headers[$normalized] = [];

        $new->headers[$normalized][] = $value;

        return $new;
    }

    public function withoutHeader($name): self
    {
        $normalized = strtolower($name);

        $new = clone($this);
        unset($new->headers[$normalized]);

        return $new;
    }

    public function getBody(): StreamInterface
    {
        if (!$this->stream) {
            $this->stream = new lmbHttpStream();
        }

        return $this->stream;
    }

    public function withBody($body): self
    {
        //if ($body === $this->response_string) {
        if ($body === $this->stream) {
            return $this;
        }

        $new = clone($this);
        $new->stream = new lmbHttpStream($body);

        return $new;
    }

    public function withStatus($code, $reasonPhrase = ''): self
    {
        if ($code === $this->statusCode) {
            return $this;
        }

        $new = clone($this);
        $new->statusCode = $code;
        $new->statusText = $reasonPhrase;

        return $new;
    }

    public function getReasonPhrase(): string
    {
        return $this->statusText;
    }
}
