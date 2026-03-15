<?php

namespace limb\dbal\src\drivers;

interface lmbDbInfoInterface
{
    function getName();
    function getTable($name);
    function hasTable($name);
    function getTableList();
    function getTables();

    //function loadTables();
}