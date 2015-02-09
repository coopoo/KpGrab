<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/8 上午10:10
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Event;

use KpGrab\Options\GrabOptionsAwareInterface;
use KpGrab\Options\GrabOptionsAwareTrait;
use Zend\EventManager\Event;

/**
 * Class Grab
 * @package KpGrab\Event
 */
class Grab extends Event implements GrabOptionsAwareInterface
{
    use GrabOptionsAwareTrait;

    /**
     * Grab events
     */
    const GRAB_PRE = 'grab.pre';
    const GRAB_ANALYSIS_PAGE = 'grab.analysis.page';
    const GRAB_ANALYSIS_STATIC = 'grab.analysis.static';
    const GRAB_ANALYSIS_CSS = 'grab.analysis.css';
    const GRAB_DOWNLOAD = 'grab.download';
    const GRAB_POST = 'grab.post';

    /**
     * @var \KpGrab\Result\Grab
     */
    protected $grabResult;
    /**
     * @var \Zend\Console\Request
     */
    protected $request;
    /**
     * @var \KpGrab\Http\Client;
     */
    protected $grabHttpClient;

    /**
     * @var \Zend\Uri\Http
     */
    protected $origUri;


    /**
     * @return \KpGrab\Result\Grab
     */
    public function getGrabResult()
    {
        return $this->grabResult;
    }

    /**
     * @param $grabResult
     * @return $this
     */
    public function setGrabResult($grabResult)
    {
        $this->grabResult = $grabResult;
        return $this;
    }


    /**
     * @return \Zend\Console\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return \KpGrab\Http\Client
     */
    public function getGrabHttpClient()
    {
        return $this->grabHttpClient;
    }

    /**
     * @param $grabHttpClient
     * @return $this
     */
    public function setGrabHttpClient($grabHttpClient)
    {
        $this->grabHttpClient = $grabHttpClient;
        return $this;
    }


    /**
     * @return \Zend\Uri\Http
     */
    public function getOrigUri()
    {
        return $this->origUri;
    }

    /**
     * @param $origUri
     * @return $this
     */
    public function setOrigUri($origUri)
    {
        $this->origUri = $origUri;
        return $this;
    }


}