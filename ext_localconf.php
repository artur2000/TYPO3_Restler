<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (class_exists('\Doctrine\Common\Annotations\AnnotationReader')) {
    // base restler annotations
    $restlerAnnotations = ['url',
        'access',
        'smart-auto-routing',
        'class',
        'cache',
        'expires',
        'throttle',
        'status',
        'header',
        'param',
        'throws',
        'return',
        'var',
        'format',
        'view',
        'errorView'];

    foreach ($restlerAnnotations as $ignoreAnnotation) {
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName($ignoreAnnotation);
    }

    // restler plugin annotations
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('restler_typo3cache_expires');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('restler_typo3cache_tags');
}

// add restler-configuration-class
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['restler']['restlerConfigurationClasses'][] = 'Aoe\\Restler\\System\\Restler\\Configuration';

// add restler page routing for system Typo3 V9 and up
$GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['enhancers']['Restler'] = Aoe\Restler\System\TYPO3\RestlerEnhancer::class;

/**
 * register cache which can cache response of REST-endpoints
 */
if (false === isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_restler'])) {
    // only configure cache, when cache is not already configured (e.g. by any other extension which base on this extension)
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_restler'] = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
        'backend' => \TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend::class,
        'options' => ['defaultLifetime' => 0]
    ];
}

/**
 * register cache which will be used from restler (to e.g. cache the routes.php)
 */
if (false === isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_restler_cache'])) {
    // only configure cache, when cache is not already configured (e.g. by any other extension which base on this extension)
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_restler_cache'] = [
        'backend' => \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
        'groups' => ['system']
    ];
}

// Routing for pre Typo3 V9 systems
if (!interface_exists('\Psr\Http\Server\MiddlewareInterface')) {
    if (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_FE) {
        // Register request handler for API
        \TYPO3\CMS\Core\Core\Bootstrap::getInstance()->registerRequestHandlerImplementation(\Aoe\Restler\Http\RestRequestHandler::class);
    }
}