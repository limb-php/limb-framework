<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_agent\agent\liveinternet;

use limb\web_agent\lmbWebAgent;

/**
 * Liveinternet agent
 *
 * @package web_agent
 * @version $Id: lmbLiveInternetAgent.php 81 2007-10-11 15:41:36Z
 */
class lmbLiveInternetAgent extends lmbWebAgent
{

    protected $project;

    function __construct($project, $request = null)
    {
        parent::__construct($request);
        $this->project = $project;
        $this->values = new lmbLiveInternetValues();
    }

    function getProject()
    {
        return $this->project;
    }

    function requestStatPage($page = '')
    {
        $url = $this->getProjectUrl() . $page;
        $this->doRequest($url);
    }

    function auth($password)
    {
        $agent = new lmbWebAgent($this->request);
        $agent->getValues()->import(
            array(
                'url' => 'https://' . $this->project,
                'password' => $password,
                'ok' => ' ok '
            )
        );
        $agent->doRequest($this->getProjectUrl(), 'POST', 0);
        $agent->getCookies()->copyTo($this->cookies);
    }

    function getProjectUrl()
    {
        return 'https://www.liveinternet.ru/stat/' . $this->project . '/';
    }
}
