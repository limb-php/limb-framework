<?php

namespace limb\cms\src\model;

use limb\active_record\lmbActiveRecord;
use limb\dbal\criteria\lmbSQLCriteria;
use limb\cms\src\validation\rule\CmsTextBlockUniqueFieldRule;
use limb\toolkit\lmbToolkit;
use limb\validation\lmbValidator;

class lmbCmsTextBlock extends lmbActiveRecord
{

    /**
     * @return lmbValidator
     */
    protected function _createValidator()
    {
        $validator = new lmbValidator();
        $validator->addRequiredRule('identifier', 'Field "Identifier" is required');
        $validator->addRequiredRule('content', 'Field "Content" is required');
        $validator->addRule(new CmsTextBlockUniqueFieldRule('identifier', $this, 'Field "Identifier" already exists'));

        return $validator;
    }

    static function getRawContent($identifier)
    {
        $block = lmbActiveRecord::findFirst(lmbCmsTextBlock::class, lmbSQLCriteria::equal('identifier', $identifier));
        if ($block)
            return $block->getContent();


        if (lmbToolkit::instance()->hasConf('text_blocks') && lmbToolkit::instance()->getConf('text_blocks')->has($identifier)) {
            $default_content = lmbToolkit::instance()->getConf('text_blocks')->get($identifier);
            return $default_content['content'];
        }

        return null;
    }

    static function findOneByIdentifier($identifier)
    {
        if ($block = lmbActiveRecord::findFirst(lmbCmsTextBlock::class, lmbSQLCriteria::equal('identifier', $identifier)))
            return $block;

        if (!$default_content = lmbToolkit::instance()->getConf('text_blocks')->get($identifier))
            return null;

        $block = new lmbCmsTextBlock();
        $block->import($default_content);
        $block->setIdentifier($identifier);

        return $block;
    }

}
