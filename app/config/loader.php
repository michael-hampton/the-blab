<?php
$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
	array(
		APP_PATH . $config->application->controllersDir,
		APP_PATH . $config->application->libraryDir,
		APP_PATH . $config->application->modelsDir,
		APP_PATH . $config->application->tasksDir,
                APP_PATH . $config->application->advertDir,
                APP_PATH . $config->application->albumDir,
                APP_PATH . $config->application->groupDir,
                APP_PATH . $config->application->eventDir,
                APP_PATH . $config->application->pageDir,
                APP_PATH . $config->application->productDir,
                APP_PATH . $config->application->chatDir
            
            
	)
)->register();

$loader->registerNamespaces([
    'Psr\Log' => '../app/models/PSR/',
    'Katzgrau\KLogger' => '../app/models/',
    'JCrowe\BadWordFilter' => '../app/models\JCrowe\BadWordFilter'
])->register();