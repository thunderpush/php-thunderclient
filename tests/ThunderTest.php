<?php

/*
 * This file is part of the Thunderpush package.
 *
 * (c) Krzysztof Jagiełło <https://github.com/kjagiello>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Guzzle\Http\Client;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response;

class ThunderTest extends \PHPUnit_Framework_TestCase
{

    public function setup() {
        $this->key = 'key';
        $this->secret = 'secret';
        $this->host = 'localhost';
        $this->port = 80;
        $this->thunder = new Thunder($this->key, $this->secret, $this->host, $this->port);

        $property = new \ReflectionProperty('Thunder', 'client');
        $property->setAccessible(true);
        $this->client = $property->getValue($this->thunder);

        $this->mock = new MockPlugin();
        $this->client->addSubscriber($this->mock);

    }

    /**
     * @covers Thunder::make_url
     */
    public function testMakeUrl()
    {
        $expected = sprintf(Thunder::API_URL, Thunder::API_VERSION, $this->key, 'foo');

        $method = new \ReflectionMethod('Thunder', 'make_url');
        $method->setAccessible(true);

        $actual = $method->invokeArgs($this->thunder, array('foo'));
        $this->assertEquals($expected, $actual);

        $actual = $method->invokeArgs($this->thunder, array('foo', 'bar'));
        $this->assertEquals($expected . 'bar/', $actual);
    }

    /**
     * @covers Thunder::make_request
     */
    public function testMakeRequest()
    {
        $method = new \ReflectionMethod('Thunder', 'make_request');
        $method->setAccessible(true);

        $this->mock->addResponse(new Response(200));
        $actual = $method->invokeArgs($this->thunder, array('GET', array('foo', 'bar')));
        $this->assertEquals(array(
            'status' => 200,
            'data' => array()
        ), $actual);

        $this->mock->addResponse(new Response(200));
        $actual = $method->invokeArgs($this->thunder, array('POST', array('foo', 'bar'), array('foo' => 'bar')));
        $this->assertEquals(array(
            'status' => 200,
            'data' => array()
        ), $actual);

        $this->mock->addResponse(new Response(200));
        $actual = $method->invokeArgs($this->thunder, array('DELETE', array('foo', 'bar')));
        $this->assertEquals(array(
            'status' => 200,
            'data' => array()
        ), $actual);

        $this->setExpectedException('UnsupportedMethodException');
        $this->mock->addResponse(new Response(200));
        $actual = $method->invokeArgs($this->thunder, array('UNSUPPORTED_METHOD', array('foo', 'bar')));
    }

    /**
     * @covers Thunder::build_response
     */
    public function testBuildResponse()
    {
        $method = new \ReflectionMethod('Thunder', 'build_response');
        $method->setAccessible(true);

        $response = array(
            'data' => array('foo' => 'bar'),
            'status' => 200
        );
        $actual = $method->invokeArgs($this->thunder, array($response, 'foo'));
        $this->assertEquals('bar', $actual);

        $response = array(
            'status' => 204
        );
        $actual = $method->invokeArgs($this->thunder, array($response, NULL));
        $this->assertTrue($actual);

        $response = array(
            'status' => 204
        );
        $actual = $method->invokeArgs($this->thunder, array($response, 'foo'));
        $this->assertNull($actual);

        $response = array(
            'status' => 555
        );
        $actual = $method->invokeArgs($this->thunder, array($response, 'foo'));
        $this->assertNull($actual);
    }

}