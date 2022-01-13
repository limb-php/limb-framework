<?php
namespace limb\twig\src;

class Wysiwyg_Node extends \Twig\Node\Node
{
    public function __construct($form_id, $params = [], $line, $tag = null)
    {
        parent::__construct(['form_id' => $form_id], ['params' => $params], $line, $tag);
    }

    public function compile(\Twig\Compiler $compiler)
    {
        $params = $this->getAttribute('params');

        $helper_var = $compiler->getVarName();
        $editor_var = $compiler->getVarName();
        $datasource_var = $compiler->getVarName();
        $value_var = $compiler->getVarName();
        $w_conf_var = $compiler->getVarName();

        $compiler
            ->addDebugInfo($this)

            ->write('$context[\'wysiwyg_params\'] = ')
            ->subcompile($params)
            ->raw(";\n")

            ->write(sprintf('$%s = ', $datasource_var))
            ->write('limb\toolkit\src\lmbToolkit::instance()->getView()->getFormDatasource( ')
            ->subcompile( $this->getNode('form_id') ) // from datasource( form_id )
            ->write(' )')
            ->raw(";\n")

            ->write(sprintf('$%s = $%s', $value_var, $datasource_var))
            ->write('->get( $context[\'wysiwyg_params\'][\'name\'] )')
            ->raw(";\n")

            /*->write(sprintf('$%s = ', $value_var))
            ->subcompile( $this->getNode('datasource') ) // value from item.name
            ->write('->get( $context[\'wysiwyg_params\'][\'name\'] )')
            ->raw(";\n")*/

            ->raw("echo '<textarea ")
            ->raw( $this->_genTagAttributies($compiler, $params) )
            ->raw(">' . ")
            ->write(sprintf('$%s .', $value_var))
            ->raw("'</textarea>';\n")

            ->write(sprintf('$%s = ', $helper_var))
            ->raw("new limb\wysiwyg\src\lmbWysiwygConfigurationHelper();\n")
            ->write(sprintf('$%s', $helper_var))
            ->raw('->setProfileName( $context[\'wysiwyg_params\'][\'profile_name\'] ?? \'\' )')
            ->raw(";\n")

            ->raw("include_once( 'limb/wysiwyg/lib/CKeditor/ckeditor.php' );\n")
            ->write(sprintf('$%s = ', $editor_var))
            ->raw("new CKeditor();\n")

            ->write(sprintf('$%s', $editor_var))
            ->raw('->basePath = \'/shared/wysiwyg/ckeditor/\'')
            ->raw(";\n")
            ->raw('if(')
            ->write(sprintf('$%s', $helper_var))
            ->raw('->getOption(\'basePath\'))')
            ->raw("\n")
            ->write(sprintf('$%s', $editor_var))
            ->raw('->basePath = ')
            ->write(sprintf('$%s', $helper_var))
            ->raw('->getOption(\'basePath\')')
            ->raw(";\n")

            ->write(sprintf('$%s = array()', $w_conf_var))
            ->raw(";\n")
            ->raw('if(')
            ->write(sprintf('$%s', $helper_var))
            ->raw('->getOption(\'Config\'))')
            ->raw("\n")
            ->write(sprintf('$%s = ', $w_conf_var))
            ->write(sprintf('$%s', $helper_var))
            ->raw('->getOption(\'Config\')')
            ->raw(";\n")

            ->write(sprintf('$%s', $editor_var))
            ->raw('->replace( $context[\'wysiwyg_params\'][\'name\'], ')
            ->write(sprintf('$%s )', $w_conf_var))
            ->raw(";\n")

            ;
    }


    protected function _genTagAttributies($compiler, $params = array())
    {
      $attrs = '';
      foreach( $params->getKeyValuePairs() as $param )
      {
        $attrs .= ' ' . $param['key']->getAttribute('value') . '="' . $param['value']->getAttribute('value') . '"';
      }

      return $attrs;
    }

}
