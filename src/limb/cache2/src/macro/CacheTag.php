<?php

namespace limb\cache2\src\macro;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * class CacheTag.
 * @tag cache
 * @req_attributes key
 * @restrict_self_nesting
 */
class CacheTag extends lmbMacroTag
{
    protected $_storage;
    const default_storage = 'lmbToolkit::instance()->getPartialHtmlCacheStorage()';

    protected function _generateContent($code_writer)
    {
        $storage_var = $code_writer->generateVar();
        $cache_key = $this->getEscaped('key');
        if (!$storage = $this->get('storage'))
            $storage = self::default_storage;
        $code_writer->writePHP($storage_var . " = " . $storage . ";");

        $ttl = $this->get('ttl');

        $cached_html = $code_writer->generateVar();
        $code_writer->writePHP("{$cached_html} = {$storage_var}->get(" . $cache_key . ");\n");

        $code_writer->writePHP("if(!is_null({$cached_html})) {\n");
        $code_writer->writePHP("  echo {$cached_html};\n");

        $code_writer->writePHP("} else {\n");
        $code_writer->writePHP("  ob_start();\n");
        parent:: _generateContent($code_writer);
        $rendered_html = $code_writer->generateVar();
        $code_writer->writePHP("  {$rendered_html} = ob_get_contents();\n");
        $code_writer->writePHP("  ob_end_flush();\n");

        $ttl_text = ($ttl) ? ", '$ttl'" : '';
        $code_writer->writePHP("{$storage_var}->set(" . $cache_key . ", {$rendered_html}" . $ttl_text . ");\n");

        $code_writer->writePHP("}\n");
    }
}
