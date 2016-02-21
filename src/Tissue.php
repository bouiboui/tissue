<?php


namespace bouiboui\Tissue;

use Github\Exception\InvalidArgumentException;
use Github\Exception\MissingArgumentException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Tissue
 * @package bouiboui\Tissue
 */
class Tissue
{

    /** @var array Configuration to be overwritten by the contents of config/config.yaml */
    private static $config = [];
    private static $configPath;

    /**
     * Change config path (useful for tests)
     * @param $configPath
     */
    public static function setConfigPath($configPath)
    {
        static::$configPath = $configPath;
    }

    /**
     * Create an issue from the sent request
     * @param null $message
     * @param null $code
     * @param null $severity
     * @param null $path
     * @param null $lineno
     * @param null $trace
     * @return array
     * @throws \ErrorException
     * @throws InvalidArgumentException
     * @throws MissingArgumentException
     * @throws ParseException
     */
    public static function create($message = null, $code = null, $severity = null, $path = null, $lineno = null, $trace = null)
    {
        static::loadConfig();

        if (null === array_unique([$message, $code, $severity, $path, $lineno, $trace])) {
            throw new \ErrorException('At least one parameter must be set.');
        }

        $issue = new GithubIssue($message, $code, $severity, $path, $lineno, $trace);

        return $issue->commit(
            static::$config['you']['username'],
            static::$config['you']['password'],
            static::$config['repo']['author'],
            static::$config['repo']['name']
        );

    }

    /**
     * Bind Tissue to the Uncaught Exception Handler.
     *
     * If there is an existing uncaught exception handler it
     *  will be removed.
     */
    public static function bindUncaughtExceptionHandler()
    {
        set_exception_handler(
            function($e) {
                static::create(
                    $e->getMessage(),
                    $e->getCode(),
                    $e->getSeverity(),
                    $e->getFile(),
                    $e->getLine(),
                    $e->getTraceAsString()
                );
            }
        );
    }

    /**
     * Loads configuration
     * @throws \ErrorException
     * @throws ParseException
     */
    static private function loadConfig()
    {
        if (null === static::$configPath) {
            static::$configPath = 'config/config.yaml';
        }
        if (!file_exists(static::$configPath) || !is_readable(static::$configPath)) {
            throw new \ErrorException('Config file not found or unreadable.');
        }
        $config = Yaml::parse(file_get_contents(static::$configPath))['tissue'];

        if (['you', 'repo'] !== array_keys($config) ||
            ['username', 'password'] !== array_keys($config['you']) ||
            ['author', 'name'] !== array_keys($config['repo'])
        ) {
            throw new \ErrorException('Invalid config file.');
        }
        static::$config = $config;
    }
}
