<?php

$conf = array();

$conf['ACTIVE'] = true;

$conf['HTML_MINIFY'] = false;
$conf['HTML_GZIP'] = true;
$conf['HTML_JS_MINIFY_ENABLE'] = false;
$conf['HTML_CSS_MINIFY_ENABLE'] = false;

$conf['FILE_SEPARATOR'] = ',';

$conf['CSS_MINIFY_ENABLE'] = true;
$conf['CSS_DEBUG_MODE'] = true;
$conf['CSS_CREATE_GZIP'] = true;

$conf['JS_MINIFY_ENABLE'] = true;
$conf['JS_DEBUG_MODE'] = true;
$conf['JS_MINIFY_LIBRARY'] = 'jsmin'; // jsmin, packer, yui
$conf['JS_PREPEND_RELATIVE_PATH'] = '/min/';
$conf['JS_CREATE_GZIP'] = true;

