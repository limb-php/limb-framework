<?php

namespace limb\web_app\src\Helpers;

use limb\core\src\lmbString;

class lmbRouteHelper
{

    static function getControllerNameByClass($controllerNameOrClass): string
    {
        if( is_string($controllerNameOrClass) && !class_exists($controllerNameOrClass) )
            return $controllerNameOrClass;

        $refController = new \ReflectionClass($controllerNameOrClass);
        $ctrlClassName = $refController->getShortName();
        $ctrlClassNamespace = $refController->getNamespaceName();

        if($pos = strpos($ctrlClassName, 'Controller'))
            $ctrlClassName = substr($ctrlClassName, 0, $pos);

        if($pos = strpos($ctrlClassNamespace, '\\controller')) {
            $ctrlClassNamespace = substr($ctrlClassNamespace, $pos + 11);
        } elseif($pos = strpos($ctrlClassNamespace, '\\Controllers')) {
            $ctrlClassNamespace = substr($ctrlClassNamespace, $pos + 12);
        }

        if($ctrlClassNamespace) {
            $ctrlClassNamespaceArr = explode('\\', $ctrlClassNamespace);
            $ctrlClassNameArr = array_merge($ctrlClassNamespaceArr ,[$ctrlClassName]);
        } else {
            $ctrlClassNameArr = [$ctrlClassName];
        }

        $ctrlClassNameArr = array_map(function($part) { return lmbString::under_scores($part); }, $ctrlClassNameArr);

        return implode('.', $ctrlClassNameArr);
    }

}
