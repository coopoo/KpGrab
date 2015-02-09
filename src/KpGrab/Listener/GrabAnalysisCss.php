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
use Zend\Dom\Document;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response;

use KpGrab\Tools\Uri;

/**
 * Class GrabAnalysisCss
 * @package KpGrab\Listener
 */
class GrabAnalysisCss implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    const CSS_SUFFIX = 'css';

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::GRAB_ANALYSIS_CSS, [$this, 'runAnalysisCss']);
    }

    /**
     * @param GrabEvent $event
     */
    public function runAnalysisCss(GrabEvent $event)
    {
        $grabHttpClient = $event->getGrabHttpClient();
        $grabOptions = $event->getGrabOptions();
        $grabResult = $event->getGrabResult();

        $analyzedStaticUrl = $grabResult->getGrabStaticUrl();
        $siteCssList = $grabResult->getGrabStaticUrl();

        while (count($siteCssList) > 0) {

            $url = array_shift($siteCssList);

            $urlInfo = Uri::parseAbsoluteUrl($url);

            if (!isset($urlInfo['extension']) || $urlInfo['extension'] !== Static::CSS_SUFFIX) {
                continue;
            }

            $response = $grabHttpClient->setUri($url)->canReconnectionSend($event->getName());

            if (!$response || $response->getStatusCode() !== Response::STATUS_CODE_200) {
                continue;
            }

            $cssInsideUrl = Uri::getCssUrl($response->getContent());

            $urlInfo = Uri::parseAbsoluteUrl($url);

            foreach ($cssInsideUrl as $findUrl) {

                if (!Uri::isAbsoluteUrl($findUrl)) {
                    $findUrl = $urlInfo['scheme'] . '://' . $urlInfo['host'] . $urlInfo['path'] . '/' . $findUrl;
                }

                $findUrlInfo = Uri::parseAbsoluteUrl($findUrl);

                if (!isset($findUrlInfo['extension'])) {
                    continue;
                }

                if (!in_array($findUrlInfo['extension'], $grabOptions->getGrabAllowStaticSuffix())) {
                    continue;
                }

                if ($event->getOrigUri()->getHost() !== $findUrlInfo['host']) {
                    continue;
                }

                $findUrl = Uri::getRealUrl($findUrl);

                if (!in_array($findUrl, $analyzedStaticUrl)
                ) {
                    $analyzedStaticUrl[] = $findUrl;
                    $siteCssList[] = $findUrl;
                }
            }

        }

        $grabResult->setGrabStaticUrl($analyzedStaticUrl);
    }

}