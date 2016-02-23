<?php

namespace bouiboui\Tissue;

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

            throw new \ErrorException('1/3 A partial error.');

        } catch (\ErrorException $e) {

            Tissue::setConfigPath(TEST_CONFIG_PATH);
            Tissue::create($e->getMessage(), $e->getCode(), $e->getSeverity(), $e->getFile());
        }
    }

    /**
     * Tests what an issue created with partial information looks like (2/3)
     * @throws \ErrorException
     */
    public function testPartialIssueTwo()
    {
        try {

            throw new \ErrorException('2/3 A partial error.');

        } catch (\ErrorException $e) {

            Tissue::setConfigPath(TEST_CONFIG_PATH);
            Tissue::create($e->getMessage(), null, null, null, $e->getLine(), $e->getTraceAsString());
        }
    }

    /**
     * Tests what an issue created with partial information looks like (3/3)
     * @throws \ErrorException
     */
    public function testPartialIssueThree()
    {
        try {

            throw new \ErrorException('3/3 A partial error.');

        } catch (\ErrorException $e) {

            Tissue::setConfigPath(TEST_CONFIG_PATH);
            Tissue::create(null, null, null, $e->getFile(), null, $e->getTraceAsString());
        }
    }

    /**
     * Tests a valid full request
     * @throws \ErrorException
     */
    public function testValidRequest()
    {
        try {

            throw new \ErrorException('This is your issue title and message.');

        } catch (\ErrorException $e) {

            Tissue::setConfigPath(TEST_CONFIG_PATH);
            $result = Tissue::create(
                $e->getMessage(),
                $e->getCode(),
                $e->getSeverity(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );

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

    /**
     * Tries to create an issue from an Exception
     * @throws \ErrorException
     */
    public function testFromException()
    {
        try {

            throw new \ErrorException('This issue was created from an exception.');

        } catch (\ErrorException $e) {

            Tissue::setConfigPath(TEST_CONFIG_PATH);
            Tissue::createFromException($e);
        }
    }
}

