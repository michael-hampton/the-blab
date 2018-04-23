<?php

use Phalcon\Mvc\View;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaData;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Config;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();
define ("APPLICATION_ENV", "DEVELOPMENT");

$di->set (
        'dispatcher', function () {
    // Create an EventsManager
    $eventsManager = new EventsManager();

    // Camelize actions
    $eventsManager->attach (
            'dispatch:beforeDispatchLoop', function ( \Phalcon\Events\Event $event, $dispatcher) {

        if ( !empty ($_SESSION['user']['user_id']) )
        {

            try {
                $objUser = new User ($_SESSION['user']['user_id']);
                $objUser->updateLoginTime ();
            } catch (Exception $ex) {
                trigger_error ($ex->getMessage (), E_USER_WARNING);
            }
        }
    }
    );

    $dispatcher = new \Phalcon\Mvc\Dispatcher();

    $dispatcher->setEventsManager ($eventsManager);

    return $dispatcher;
}
);
//	$di->set('dispatcher', function() {
//    //Create an EventsManager
//    $eventsManager = new EventsManager();
//Attach a listener
//    $eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception) {
//        //Handle 404 exceptions
//        if ($exception instanceof DispatchException) {
//	    var_dump($exception);
//		echo "here";
//		//header("Location:http://www.google.co.uk");
//		die();
//            /*$dispatcher->forward(array(
//                'controller' => 'index',
//                'action' => 'show404'
//            ));*/
//            return false;
//        }
//    });
//    $dispatcher = new \Phalcon\Mvc\Dispatcher();
//    //Bind the EventsManager to the dispatcher
//    $dispatcher->setEventsManager($eventsManager);
//    return $dispatcher;
//}, true);

$di->set ('view', function() {
    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir ('../app/views/');



    return $view;
});

$di->set ('flash', function() {
    $flash = new \Phalcon\Flash\Session ([
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ]);
    return $flash;
});

require APP_PATH . 'app/config/config.php';
$configuration = new \Phalcon\Config ($settings);

$di->set (
        'configuration', function () use ($configuration) {
    return $configuration;
}
);

//Setup a base URI so that all generated URIs include the "tutorial" folder
$di->set ('url', function() {
    $url = new \Phalcon\Mvc\Url();
    $url->setBaseUri ('/phalcon/');
    return $url;
});

session_start ();
