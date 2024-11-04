<?php

namespace limb\web_app\src\validation\rule;

use limb\net\src\lmbUploadedFile;
use limb\validation\src\rule\lmbValidationRuleInterface;

class UploadFileRule implements lmbValidationRuleInterface
{
    protected $field_name; // required field
    protected $file_size;
    protected $allowed_extensions;
    protected $is_requered;
    protected $real_field_name;
    protected $custom_error;

    function __construct($field_name, $params = [], $real_field_name = null, $custom_error = null)
    {
        $this->field_name = $field_name;
        $this->real_field_name = (isset($real_field_name)) ? $real_field_name : $field_name;
        $this->custom_error = $custom_error;

        if (isset($params['max_file_size']))
            $this->file_size = $params['max_file_size'];
        if (isset($params['allowed_extensions']))
            $this->allowed_extensions = $params['allowed_extensions'];
        if (isset($params['is_required']))
            $this->is_requered = $params['is_required'];
    }

    function validate($datasource, $error_list)
    {
        $file_data = $datasource[$this->field_name] ?? null;

        if (is_array($file_data)) {
            foreach ($file_data as $file) {
                $this->_validateSingleFile($file, $error_list);
            }
        } else {
            $this->_validateSingleFile($file_data, $error_list);
        }
    }

    private function _validateSingleFile($file, $error_list)
    {
        if (!($file instanceof lmbUploadedFile)) {
            if ($this->is_requered) {
                $error_list->addError("{Field}: Uploading failed. No uploaded file.", array('Field' => $this->field_name));
            }

            return;
        }

        switch ($file->getError()) {
            case UPLOAD_ERR_NO_FILE:
                if ($this->is_requered)
                    $error_list->addError("{Field}: Uploading failed.", array('Field' => $this->field_name));
                return;

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error_list->addError("{Field}: too big file.",
                    array('Field' => $this->real_field_name));
                return;

            case UPLOAD_ERR_PARTIAL:
                $error_list->addError("{Field}: partial uploading.",
                    array('Field' => $this->real_field_name));
                return;

            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
                $error_list->addError("{Field}: Can not save file.", array('Field' => $this->real_field_name));
                return;
        }

        if (!empty($this->allowed_extensions)) {
            $file_info = pathinfo($file->getName());
            $extension = strtolower($file_info['extension']);
            $allowed_extensions_raw = implode(', ', $this->allowed_extensions);

            if (!in_array($extension, $this->allowed_extensions)) {
                $error_list->addError("{Field}: wrong file type. Allowed types: " . $allowed_extensions_raw,
                    array('Field' => $this->real_field_name));
            }
        }

        if ($this->file_size) {
            $kilobyte = 1024;
            $max_file_size = $this->file_size * $kilobyte;
            $file_size = $file->getSize();

            if ($file_size > $max_file_size) {
                $error_list->addError("Max file size for {Field} " . $this->file_size . " KB",
                    array('Field' => $this->real_field_name));
            }
        }

        if (!$error_list->isValid()) {
            $file->destroy();
        }
    }
}
