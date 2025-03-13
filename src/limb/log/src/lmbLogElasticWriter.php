<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

use Elastic\Elasticsearch\ClientBuilder;
use limb\core\src\exception\lmbException;
use limb\datetime\src\lmbDateTime;
use limb\net\src\lmbIp;

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
            'TIME' => $time,
            'IP' => lmbIp::getRealIp(),
            'METHOD' => $_SERVER['REQUEST_METHOD'],
            'URI' => $_SERVER['REQUEST_URI'],
            'REFERER' => $_SERVER['HTTP_REFERER'] ?? null,
            'MESSAGE' => $entry->asText(),
            'LEVEL' => $entry->getLevelForHuman(),
        ];

        return $log_message;
    }
}
