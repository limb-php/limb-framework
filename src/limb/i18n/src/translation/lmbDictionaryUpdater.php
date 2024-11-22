<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\i18n\src\translation;

use limb\cli\src\lmbCliResponse;
use limb\fs\src\lmbFsRecursiveIterator;

/**
 * class lmbDictionaryUpdater.
 *
 * @package i18n
 * @version $Id: lmbDictionaryUpdater.php 7686 2009-03-04 19:57:12Z
 */
class lmbDictionaryUpdater
{
    protected $response;

    function __construct($backend, $response = null)
    {
        $this->backend = $backend;
        $this->response = $response ? $response : new lmbCliResponse();
    }

    function dryrun($source_dir)
    {
        $this->response->write("Dry-running in '$source_dir'...\n");

        $this->updateTranslations($source_dir, true);
    }

    function updateTranslations($source_dir, $dry_run = false)
    {
        $loader = new lmbFsDictionaryExtractor();
        $loader->registerFileParser('.php', new lmbPHPDictionaryExtractor());
        $loader->registerFileParser('.phtml', new lmbPHPDictionaryExtractor());

        $dicts = array();
        $iterator = new lmbFsRecursiveIterator($source_dir);

        $this->response->write("======== Extracting translations from source ========\n");
        $loader->traverse($iterator, $dicts, $this->response);

        if (!$translations = $this->backend->loadAll()) {
            $this->response->write("======== No existing translations found!(create them first) ========\n");
            return;
        }

        $this->response->write("======== Updating translations ========\n");

        foreach ($translations as $locale => $domains) {
            foreach ($domains as $domain => $old_dict) {
                if (isset($dicts[$domain])) {
                    $this->response->write($this->backend->info($locale, $domain) . "...");

                    $new_dict = $dicts[$domain]->merge($old_dict);
                    if (!$dry_run) {
                        $this->backend->save($locale, $domain, $new_dict);
                        $this->response->write("updated\n");
                    } else
                        $this->response->write("skipped(dry-run)\n");
                }
            }
        }
    }
}
