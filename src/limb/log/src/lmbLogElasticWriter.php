<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

use Elastic\Elasticsearch\ClientBuilder;
use limb\core\src\exception\lmbException;
use limb\core\src\lmbSetInterface;
use limb\datetime\src\lmbDateTime;

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

    function __construct(lmbSetInterface $config)
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
                'body' => [
                    'log' => $formated
                ]
            ]);
        } catch (\Exception $e) {
            if (!$this->config['ignore_error']) {
                throw new lmbException("Error sending messages to Elasticsearch: " . $e->getMessage());
            }
        }
    }

    protected function formatEntry(lmbLogEntry $entry): mixed
    {
        $time = (new lmbDateTime($entry->getTime()))->format("Y-m-d h:i:s");

        $log_message = $time . ": ";
        if (isset($_SERVER['REMOTE_ADDR']))
            $log_message .= '[' . $_SERVER['REMOTE_ADDR'] . ']';
        if (isset($_SERVER['REQUEST_URI']))
            $log_message .= '[' . $_SERVER['REQUEST_METHOD'] . ': ' . $_SERVER['REQUEST_URI'] . ']';
        if (isset($_SERVER['HTTP_REFERER']))
            $log_message .= '[REF: ' . $_SERVER['HTTP_REFERER'] . ']';
        $log_message .= $entry->asText();

        return $log_message;
    }
}
