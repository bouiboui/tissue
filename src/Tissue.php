<?php


namespace bouiboui\Tissue;

use Github\Exception\InvalidArgumentException;
use Github\Exception\MissingArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

define('CONFIG_PATH', dirname(__DIR__) . '/config/config.yaml');

/**
 * Class Tissue
 * @package bouiboui\Tissue
 */
class Tissue
{
    /** @var array Configuration to be overwritten by the contents of config/config.yaml */
    private $config = [];

    /**
     * ThrowController constructor.
     * @param string $configPath
     * @throws \ErrorException
     * @throws ParseException
     */
    public function __construct($configPath = CONFIG_PATH)
    {
        if (!file_exists($configPath) || !is_readable($configPath)) {
            throw new \ErrorException('Config file not found or unreadable.');
        }
        $config = Yaml::parse(file_get_contents($configPath))['tissue'];

        if (['you', 'repo'] !== array_keys($config) ||
            ['username', 'password'] !== array_keys($config['you']) ||
            ['author', 'name'] !== array_keys($config['repo'])
        ) {
            throw new \ErrorException('Invalid config file.');
        }
        $this->config = $config;
    }

    /**
     * Create an issue from the sent request
     * @param Request $request
     * @return array
     * @throws InvalidArgumentException
     * @throws MissingArgumentException
     * @throws \ErrorException
     */
    public function throwIssue(Request $request)
    {
        $code = $request->get('code');
        $filename = $request->get('filename');
        $lineno = $request->get('line');
        $message = $request->get('message');
        $severity = $request->get('severity');
        $trace = $request->get('trace');

        if ('' === trim($message . $code . $severity . $filename . $lineno . $trace)) {
            throw new \ErrorException('At least one parameter must be set.');
        }

        $issue = new GithubIssue($message, $code, $severity, $filename, $lineno, $trace);

        return $issue->commit(
            $this->config['you']['username'],
            $this->config['you']['password'],
            $this->config['repo']['author'],
            $this->config['repo']['name']
        );

    }
}