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
use KpGrab\Exception\InvalidArgumentException;
use KpGrab\Result\MessageInterface;
use Zend\Dom\Document;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response;

use Zend\Uri\UriFactory;

class GrabPre implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::GRAB_PRE, [$this, 'setSaveDir']);
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::GRAB_PRE, [$this, 'setOrigUri']);
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::GRAB_PRE, [$this, 'setSaveName']);
    }


    public function setSaveDir(GrabEvent $event)
    {

        $request = $event->getRequest();
        $grabOptions = $event->getGrabOptions();
        $saveDir = $request->getParam('save-dir');

        if (!$saveDir) {
            $saveDir = $grabOptions->getDefaultSaveDir();
        }

        if (!is_dir($saveDir) || !is_writable($saveDir)) {
            $event->getGrabResult()->setMessage(new InvalidArgumentException(sprintf(MessageInterface::ERROR_SAVE_DIR_MESSAGE, $saveDir)), $event->getName(), true);
        }

        $request->getParams()->set('save-dir', $saveDir);

    }

    public function setOrigUri(GrabEvent $event)
    {

        $request = $event->getRequest();

        $url = $request->getParam('url');

        $uri = UriFactory::factory($url);

        if (!$uri->isValid() || !$uri->isAbsolute()) {
            $event->getGrabResult()->setMessage(new InvalidArgumentException(MessageInterface::ERROR_URL_MESSAGE), $event->getName(), true);
            return;
        }

        $event->setOrigUri($uri);

    }


    public function setSaveName(GrabEvent $event)
    {
        $request = $event->getRequest();
        $saveName = $request->getParam('save-name');

        if (!$saveName) {
            $saveName = md5(time() . $event->getOrigUri()->toString());
        }

        $request->getParams()->set('save-name', $saveName);

    }

}