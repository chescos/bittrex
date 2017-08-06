<?php

namespace chescos\Tests\Bittrex;

use PHPUnit\Framework\TestCase as BaseTestCase;
use chescos\Bittrex\Bittrex;

class TestCase extends BaseTestCase
{
    protected $api;

    protected function setUp()
    {
        $key = getenv('KEY');
        $secret = getenv('SECRET');

        if(empty($key) || empty($secret)) {
            throw new \Exception('You must set your Bittrex API "KEY" and "SECRET" in the "phpunit.xml.dist" file.');
        }

        $this->api = new Bittrex(getenv('KEY'), getenv('SECRET'));
    }

    public function testApi()
    {
        $result = $this->api->getBalances();

        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }
}
