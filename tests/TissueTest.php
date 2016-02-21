<?php

namespace bouiboui\Tissue;

use Github\Exception\ErrorException;
use Symfony\Component\HttpFoundation\Request;

define('TEST_CONFIG_PATH', dirname(__DIR__) . '/config/config.test.yaml');

/**
 * Class TissueTest
 * @package bouiboui\Tissue
 */
class TissueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests what an issue created with partial information looks like (1/3)
     * @throws \ErrorException
     */
    public function testPartialIssueOne()
    {
        try {

            throw new ErrorException('1/3 A partial error.');

        } catch (\ErrorException $e) {

            $c = new Tissue(TEST_CONFIG_PATH);
            $r = Request::create('localhost', 'GET', [
                'code' => $e->getCode(),
                'filename' => $e->getFile(),
                'message' => $e->getMessage(),
                'severity' => $e->getSeverity(),
            ]);

            $c->throwIssue($r);
        }
    }

    /**
     * Tests what an issue created with partial information looks like (2/3)
     * @throws \ErrorException
     */
    public function testPartialIssueTwo()
    {
        try {

            throw new ErrorException('2/3 A partial error.');

        } catch (\ErrorException $e) {

            $c = new Tissue(TEST_CONFIG_PATH);
            $r = Request::create('localhost', 'GET', [
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $c->throwIssue($r);
        }
    }

    /**
     * Tests what an issue created with partial information looks like (3/3)
     * @throws \ErrorException
     */
    public function testPartialIssueThree()
    {
        try {

            throw new ErrorException('3/3 A partial error.');

        } catch (\ErrorException $e) {

            $c = new Tissue(TEST_CONFIG_PATH);
            $r = Request::create('localhost', 'GET', [
                'filename' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            $c->throwIssue($r);
        }
    }

    /**
     * Tests a valid full request
     * @throws \ErrorException
     */
    public function testValidRequest()
    {
        try {

            throw new ErrorException('This is your issue title and message.');

        } catch (\ErrorException $e) {

            $c = new Tissue(TEST_CONFIG_PATH);
            $r = Request::create('localhost', 'GET', [
                'code' => $e->getCode(),
                'filename' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'severity' => $e->getSeverity(),
                'trace' => $e->getTraceAsString(),
            ]);

            $result = $c->throwIssue($r);

            static::assertNotNull($result, 'null result received');
            static::assertTrue(array_key_exists('duplicate', $result), 'duplicate parameter missing');
            if (!$result['duplicate']) {
                static::assertTrue(array_key_exists('number', $result), 'id parameter missing');
                static::assertTrue(array_key_exists('url', $result), 'url parameter missing');
                static::assertTrue(is_int($result['number']), 'id must be an int');
                static::assertTrue(is_string($result['url']), 'url must be a string');
                static::assertNotFalse(filter_var($result['url'], FILTER_VALIDATE_URL), 'url must be a url (duh)');
                static::assertEquals(false, $result['duplicate']);
            } else {
                static::assertTrue(is_bool($result['duplicate']));
                static::assertEquals(true, $result['duplicate']);
            }
        }
    }
}