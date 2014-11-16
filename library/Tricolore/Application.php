<?php
namespace Tricolore;

use Tricolore\Config\Config;
use Tricolore\RoutingProvider\RoutingProvider;
use Tricolore\Services\ServiceLocator;
use Tricolore\Exception\ErrorException;
use Symfony\Component\Routing\Generator\UrlGenerator;

class Application extends ServiceLocator
{
    /**
     * Application options
     *
     * @var array
     */
    private static $options;

    /**
     * Routing object
     *
     * @var Tricolore\RoutingProvider\RoutingProvider
     */
    private static $routing;

    /**
     * Register application and services
     *
     * @param array $options
     * @return void
     */
    public static function register(array $options)
    {
        self::$options = $options;
        self::getInstance()->setupErrorReporting();

        try {
            self::$routing = new RoutingProvider();
            self::$routing->register();
        } catch(\Exception $exception) {
            self::getInstance()->get('view', [true])->handleException($exception);
        }
    }

    /**
     * Setup error reporting
     *
     * @return void
     */
    private function setupErrorReporting()
    {
        if (self::getInstance()->getEnv() === 'prod') {
            error_reporting(0);
        } elseif (self::getInstance()->getEnv() === 'dev') {
            error_reporting(E_ALL);
        }

        set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
            if (error_reporting() === 0) {
                return false;
            }

            throw new ErrorException($errstr, $errno, $errfile, $errline);
        });
    }

    /**
     * Application instance
     *
     * @return Tricolore\Application
     */
    public static function getInstance()
    {
        return new static();
    }

    /**
     * Build URL address
     *
     * @param string $route_name
     * @param array $arguments
     * @return string
     */
    public static function buildUrl($route_name = null, array $arguments = [])
    {
        if ($route_name == null) {
            return Config::key('base.full_url');
        }

        $generator = new UrlGenerator(self::$routing->getRouteCollection(), self::$routing->getContext());

        if (Config::key('router.use_httpd_rewrite') === true) {
            return $generator->generate($route_name, $arguments, $generator::ABSOLUTE_URL);
        }

        return 'index.php?/' . $generator->generate($route_name, $arguments, $generator::RELATIVE_PATH);
    }

    /**
     * Create path
     *
     * @param string $path
     * @return string
     */
    public static function createPath($path = null)
    {
        if (isset(self::$options['directory']) === false || self::$options['directory'] == null) {
            return false;
        }

        if ($path === null) {
            return self::$options['directory'];
        }

        $path = str_replace(':', DIRECTORY_SEPARATOR, $path);

        return self::$options['directory'] . $path;
    }

    /**
     * Get application environment
     *
     * @return string
     */
    public function getEnv()
    {
        $available_environments = ['dev', 'prod', 'test'];

        if (isset(self::$options['environment']) === false || self::$options['environment'] == null
            || in_array(self::$options['environment'], $available_environments, true) === false
        ) {
            return 'prod';
        }

        return self::$options['environment'];
    }

    /**
     * Get application version
     *
     * @return string
     */
    public function getVersion()
    {
        if (isset(self::$options['version']) === false || self::$options['version'] == null) {
            return 'undefined';
        }

        return self::$options['version'];
    }

    /**
     * Get memory usage
     *
     * @return int
     */
    public function getUsageMemory()
    {
        return memory_get_usage(true);
    }

    /**
     * In travis
     *
     * @return bool
     */
    public function inTravis()
    {
        return getenv('TRAVIS') ? true : false;
    }

    /**
     * Get all datasource queries 
     * 
     * @return int
     */
    public function dataSourceQueries()
    {
        return self::getInstance()->get('datasource')->getQueriesNumber();
    }
}
