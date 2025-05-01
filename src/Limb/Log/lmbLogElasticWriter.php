<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Log;

use Elastic\Elasticsearch\ClientBuilder;
use Limb\Core\Exception\lmbException;
use Limb\Datetime\lmbDateTime;
use Limb\Net\lmbIp;

/**
 * class lmbLogElasticWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogElasticWriter implements lmbLogWriterInterface
{
    private $client;
    private $config;

    function __construct(array $config)
    {
        $this->config = $config;

        $this->client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setBasicAuthentication($this->config['username'], $this->config['password'])
            ->setHosts($this->config['hosts'])
            ->build();
    }

    function write(lmbLogEntry $entry)
    {
        $formated = $this->formatEntry($entry);

        try {
            $this->client->index([
                'index' => $this->config['index'],
                'body' => $formated
            ]);
        } catch (\Exception $e) {
            if (!$this->config['ignore_error']) {
                throw new lmbException("Error sending messages to Elasticsearch: " . $e->getMessage());
            }
        }
    }

    protected function formatEntry(lmbLogEntry $entry): mixed
    {
        $time = (new lmbDateTime($entry->getTime()))->format("Y-m-d\Th:i:s");

        $log_message = [
            'timestamp' => $time,
            'ip' => lmbIp::getRealIp(),
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'port' => $_SERVER['REMOTE_PORT'],
            'request_uri' => $_SERVER['REQUEST_URI'],
            'referer' => $_SERVER['HTTP_REFERER'] ?? null,
            'message' => $entry->getMessage(),
            'debug_level' => $entry->getLevelForHuman(),
        ];

        return $log_message;
    }
}
