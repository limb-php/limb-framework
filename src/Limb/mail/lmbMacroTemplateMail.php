<?php

namespace limb\mail;

use limb\macro\lmbMacroTemplate;

class lmbMacroTemplateMail extends lmbTemplateMail
{

    protected function _renderTemplate($tools)
    {
        $template_file = $this->template_id . '.phtml';
        $path = $tools->locateTemplateByAlias('_mail/' . $template_file);

        $template = new lmbMacroTemplate($path, $tools->getConf('macro'));
        $template->setVars($this->dataset->export());
        return $template->render();
    }

}
