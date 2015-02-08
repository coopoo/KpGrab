<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/7 下午3:06
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

class Module implements ConfigProviderInterface,
    AutoloaderProviderInterface,ServiceProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
    public function getServiceConfig(){
        return [
            'invokables'=>[
                'GrabAnalysisSite'=>'KpGrab\Service\Invokable\AnalysisSite',
                'GrabEvent'=>'KpGrab\Event\Grab'
            ],
            'factories'=>[
                'GrabOptions' => 'KpGrab\Service\Factory\GrabOptions',
                'GrabHttpClient'=>'KpGrab\Service\Factory\GrabHttpClient'
            ]
        ];
    }


    public function getViewHelperConfig(){

        return [
            'invokables'=>[

            ]
        ];
    }


}