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
    const DEFAULT_CONFIG_PATH = 'config/config.yaml';
    private static $configPath = self::DEFAULT_CONFIG_PATH;

    /** @var array Configuration to be overwritten by the contents of config/config.yaml */
    private static $config;

    /**
     * Change config path (useful for tests)
     * @param $configPath
     */
    public static function setConfigPath($configPath)
    {
        static::$configPath = $configPath;
    }

    /**
     * Bind Tissue to the Uncaught Exception Handler.
     *
     * If there is an existing uncaught exception handler it will be removed.
     * @throws ErrorException
     * @throws InvalidArgumentException
     * @throws MissingArgumentException
     * @throws ParseException
     */
    public static function bindUncaughtExceptionHandler()
    {
        set_exception_handler(function (\Exception $e) {
            static::createFromException($e);
        });
    }

    /**
     * Create an issue from an Exception
     * @param \Exception $e
     * @throws ErrorException
     */
    public static function createFromException(\Exception $e)
    {
        $severity = null;
        // getSeverity doesn't exist in base Exceptions
        if (method_exists($e, 'getSeverity')) {
            $severity = $e->getSeverity();
        }
        static::create(
            $e->getMessage(),
            $e->getCode(),
            $severity,
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
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
     * @throws ErrorException
     * @throws InvalidArgumentException
     * @throws MissingArgumentException
     * @throws ParseException
     */
    public static function create($message = null, $code = null, $severity = null, $path = null, $lineno = null, $trace = null)
    {
        static::loadConfig();

        if (null === array_unique([$message, $code, $severity, $path, $lineno, $trace])) {
            throw new ErrorException('At least one parameter must be set.');
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
     * Loads configuration
     * @throws ErrorException
     * @throws ParseException
     */
    static private function loadConfig()
    {
        // Only load once
        if (null !== static::$config) {
            return;
        }
        if (!file_exists(static::$configPath) || !is_readable(static::$configPath)) {
            throw new ErrorException('Config file not found or unreadable.');
        }
        $config = Yaml::parse(file_get_contents(static::$configPath))['tissue'];

        if (['you', 'repo'] !== array_keys($config) ||
            ['username', 'password'] !== array_keys($config['you']) ||
            ['author', 'name'] !== array_keys($config['repo'])
        ) {
            throw new ErrorException('Invalid config file.');
        }
        static::$config = $config;
    }
}
