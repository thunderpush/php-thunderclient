<?php

/*
 * This file is part of the Thunderpush package.
 *
 * (c) Krzysztof Jagiełło <https://github.com/kjagiello>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;

class ApiTest extends \PHPUnit_Framework_TestCase
{

    public function setup() {
        $this->mock = new MockHandler();
        $this->key = 'key';
        $this->secret = 'secret';
        $this->host = 'localhost';
        $this->port = 80;
        $this->https = false;
        $this->thunder = new Thunder(
            $this->key, $this->secret, $this->host, $this->port, $this->https,
            HandlerStack::create($this->mock)
        );

        $property = new \ReflectionProperty('Thunder', 'client');
        $property->setAccessible(true);
        $this->client = $property->getValue($this->thunder);
    }

    /**
     * @covers Thunder::get_user_count
     */
    public function testGetUserCount()
    {
        $method = new \ReflectionMethod('Thunder', 'get_user_count');
        $method->setAccessible(true);

        $this->mock->append(new Response(200, array(), '{"count": 1}'));
        $actual = $method->invokeArgs($this->thunder, array());
        $this->assertEquals(1, $actual);
    }

    /**
     * @covers Thunder::get_users_in_channel
     */
    public function testGetUsersInChannel()
    {
        $method = new \ReflectionMethod('Thunder', 'get_users_in_channel');
        $method->setAccessible(true);

        $this->mock->append(new Response(200, array(), '{"users": 1}'));
        $actual = $method->invokeArgs($this->thunder, array('foo'));
        $this->assertEquals(1, $actual);
    }

    /**
     * @covers Thunder::send_message_to_user
     */
    public function testSendMessageToUser()
    {
        $method = new \ReflectionMethod('Thunder', 'send_message_to_user');
        $method->setAccessible(true);

        $this->mock->append(new Response(200, array(), '{"count": 1}'));
        $actual = $method->invokeArgs($this->thunder, array('foo', 'bar'));
        $this->assertEquals(1, $actual);
    }

    /**
     * @covers Thunder::send_message_to_channel
     */
    public function testSendMessageToChannel()
    {
        $method = new \ReflectionMethod('Thunder', 'send_message_to_channel');
        $method->setAccessible(true);

        $this->mock->append(new Response(200, array(), '{"count": 1}'));
        $actual = $method->invokeArgs($this->thunder, array('foo', 'bar'));
        $this->assertEquals(1, $actual);
    }

    /**
     * @covers Thunder::is_user_online
     */
    public function testIsUserOnline()
    {
        $method = new \ReflectionMethod('Thunder', 'is_user_online');
        $method->setAccessible(true);

        $this->mock->append(new Response(200, array(), '{"online": 1}'));
        $actual = $method->invokeArgs($this->thunder, array('foo', 'bar'));
        $this->assertEquals(1, $actual);
    }

    /**
     * @covers Thunder::disconnect_user
     */
    public function testDisconnectUser()
    {
        $method = new \ReflectionMethod('Thunder', 'disconnect_user');
        $method->setAccessible(true);

        $this->mock->append(new Response(204, array(), ''));
        $actual = $method->invokeArgs($this->thunder, array('foo', null));
        $this->assertEquals(1, $actual);
    }
}
