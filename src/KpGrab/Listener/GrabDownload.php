<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/8 上午10:37
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Listener;

use KpGrab\Event\Grab as GrabEvent;
use KpGrab\Result\MessageInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use KpGrab\Exception\RuntimeException;
use KpGrab\Tools\Uri;

class GrabDownload implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::GRAB_DOWNLOAD, [$this, 'runDownload']);
    }

    public function runDownload(GrabEvent $event)
    {

        $grabResult = $event->getGrabResult();
        $request = $event->getRequest();
        $grabHttpClient = $event->getGrabHttpClient();

        $downloadList = array_merge($grabResult->getGrabPageUrl(), $grabResult->getGrabStaticUrl());
        $saveDir = $request->getParam('save-dir');
        /**
         * @todo 可自己配置
         */
        $rootName = md5($event->getOrigUri()->toString());

        while (count($downloadList) > 0) {

            $url = array_shift($downloadList);

            $response = $grabHttpClient->setUri($url)->canReconnectionSend($event->getName());

            if (!$response) {
                continue;
            }

            if ($response->getStatusCode() !== Response::STATUS_CODE_200) {
                $grabResult->setMessage(new RuntimeException(sprintf(MessageInterface::ERROR_CONNECT_CODE_MESSAGE, $url, $response->getStatusCode())), $event->getName());
                continue;
            }

            $downloadSaveDir = $saveDir . '/' . $rootName;

            $urlInfo = Uri::parseAbsoluteUrl($url);

            $downloadSaveDir .= '/' . $urlInfo['path'];

            if (!is_dir($downloadSaveDir)) {
                mkdir($downloadSaveDir, 0777, true);
                chmod($downloadSaveDir, 0777);
            }

            $fileName = $urlInfo['filename'] . '.' . $urlInfo['extension'];

            file_put_contents($downloadSaveDir . '/' . $fileName, $response->getContent());

        }

    }

}