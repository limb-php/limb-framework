<?php

namespace limb\cms\src\model;

interface lmbCmsFileStorageInterface
{
    function storeFile($source, $mime_type = null);

    function removeFile($file_id);

    function getFilePath($file_id);

    function hasFile($file_id);

    function getFileSize($file_id);

    function getFileUrl($file_id);
}
