<?php
/**
 * Copyright 2017 Smartwaiver
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace Smartwaiver\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;

use Smartwaiver\SmartwaiverRawResponse;
use Smartwaiver\SmartwaiverResponse;
use Smartwaiver\Tests\Factories\APISuccessResponses;

use Smartwaiver\Smartwaiver;
use Smartwaiver\Types\SmartwaiverTemplate;
use Smartwaiver\Types\SmartwaiverWaiver;
use Smartwaiver\Types\SmartwaiverWaiverSummary;
use Smartwaiver\Types\SmartwaiverWebhook;

/**
 * Class SmartwaiverTest
 *
 * This class tests the specific responsibilities of the Smartwaiver class,
 * namely, turning the function calls with certain parameters into the correct
 * HTTP route calls.
 *
 * @package Smartwaiver\Tests
 */
class SmartwaiverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * API Key used for tests
     */
    const TEST_API_KEY = 'TestApiKey';

    /**
     * Test the 'getWaiverTemplates' function.
     */
    public function testGetWaiverTemplates()
    {
        $numTemplates = 3;
        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::templates($numTemplates));

        $templates = $sw->getWaiverTemplates();

        $this->assertCount($numTemplates, $templates);
        foreach($templates as $template) {
            $this->assertInstanceOf(SmartwaiverTemplate::class, $template);
        }

        $this->checkGetRequests($container, ['/v4/templates']);
    }

    /**
     * Test the 'getWaiverTemplate' function.
     */
    public function testGetWaiverTemplate()
    {
        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::template());

        $template = $sw->getWaiverTemplate('TestingTemplateId');
        $this->assertInstanceOf(SmartwaiverTemplate::class, $template);

        $this->checkGetRequests($container, ['/v4/templates/TestingTemplateId']);
    }

    /**
     * Test the getWaivers function with default parameters
     */
    public function testGetWaiversDefault()
    {
        $numWaivers = 5;
        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::waivers($numWaivers));

        $waivers = $sw->getWaiverSummaries();

        $this->assertCount($numWaivers, $waivers);
        foreach($waivers as $waiver) {
            $this->assertInstanceOf(SmartwaiverWaiverSummary::class, $waiver);
        }

        $this->checkGetRequests($container, ['/v4/waivers?limit=20']);
    }

    /**
     * Test all the individual parameters
     */
    public function testGetWaiversParams()
    {
        $paths = [
            '/v4/waivers?limit=5',
            '/v4/waivers?limit=20&verified=true',
            '/v4/waivers?limit=20&templateId=alkagaldeab',
            '/v4/waivers?limit=20&fromDts='.urlencode('2016-11-01 00:00:00'),
            '/v4/waivers?limit=20&toDts='.urlencode('2016-11-01 00:00:00'),
        ];

        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::waivers(1), count($paths));

        $sw->getWaiverSummaries(5);
        $sw->getWaiverSummaries(20, true);
        $sw->getWaiverSummaries(20, null, 'alkagaldeab');
        $sw->getWaiverSummaries(20, null, '', '2016-11-01 00:00:00');
        $sw->getWaiverSummaries(20, null, '', '', '2016-11-01 00:00:00');

        $this->checkGetRequests($container, $paths);
    }

    /**
     * Test the getWaiver function
     */
    public function testGetWaiver()
    {
        $paths = [
            '/v4/waivers/6jebdfxzvrdkd?pdf=false',
            '/v4/waivers/6jebdfxzvrdkd?pdf=false',
            '/v4/waivers/6jebdfxzvrdkd?pdf=true',
        ];

        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::waiver(), count($paths));

        $waiver = $sw->getWaiver('6jebdfxzvrdkd');
        $this->assertInstanceOf(SmartwaiverWaiver::class, $waiver);

        $sw->getWaiver('6jebdfxzvrdkd', false);
        $sw->getWaiver('6jebdfxzvrdkd', true);

        $this->checkGetRequests($container, $paths);
    }

    /**
     * Test the getWebhook function
     */
    public function testGetWebhook()
    {
        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::webhooks());

        $webhook = $sw->getWebhookConfig();
        $this->assertInstanceOf(SmartwaiverWebhook::class, $webhook);

        $this->checkGetRequests($container, ['/v4/webhooks/configure']);
    }

    /**
     * Test the setWebhookConfig function
     */
    public function testSetWebhookConfig()
    {
        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::webhooks());

        $webhook = $sw->setWebhookConfig(
            'https://endpoint.example.org',
            SmartwaiverWebhook::WEBHOOK_BEFORE_AND_AFTER_EMAIL
        );
        $this->assertInstanceOf(SmartwaiverWebhook::class, $webhook);

        // Check that the right requests were sent
        $this->assertCount(1, $container);
        $this->assertEquals('PUT', $container[0]['request']->getMethod());
        $this->assertEquals('/v4/webhooks/configure', $container[0]['request']->getRequestTarget());
        $this->assertEquals([self::TEST_API_KEY], $container[0]['request']->getHeader('sw-api-key'));
        $this->assertEquals(['application/json'], $container[0]['request']->getHeader('Content-Type'));
        $this->assertEquals(
            '{"endpoint":"https:\/\/endpoint.example.org","emailValidationRequired":"both"}',
            $container[0]['request']->getBody()->getContents()
        );
    }

    /**
     * Test the setWebhook function
     */
    public function testSetWebhook()
    {
        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::webhooks());

        $swWebhook = new SmartwaiverWebhook([
            'endpoint' => 'https://endpoint.example.org',
            'emailValidationRequired' => SmartwaiverWebhook::WEBHOOK_BEFORE_AND_AFTER_EMAIL
        ]);

        $webhook = $sw->setWebhook($swWebhook);
        $this->assertInstanceOf(SmartwaiverWebhook::class, $webhook);

        // Check that the right requests were sent
        $this->assertCount(1, $container);
        $this->assertEquals('PUT', $container[0]['request']->getMethod());
        $this->assertEquals('/v4/webhooks/configure', $container[0]['request']->getRequestTarget());
        $this->assertEquals([self::TEST_API_KEY], $container[0]['request']->getHeader('sw-api-key'));
        $this->assertEquals(['application/json'], $container[0]['request']->getHeader('Content-Type'));
        $this->assertEquals(
            '{"endpoint":"https:\/\/endpoint.example.org","emailValidationRequired":"both"}',
            $container[0]['request']->getBody()->getContents()
        );
    }

    /**
     * Test the 'getWaiverTemplatesRaw' function.
     */
    public function testGetWaiverTemplatesRaw()
    {
        $numTemplates = 3;
        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::templates($numTemplates));

        $response = $sw->getWaiverTemplatesRaw();

        $this->assertInstanceOf(SmartwaiverRawResponse::class, $response);

        $this->checkGetRequests($container, ['/v4/templates']);
    }

    /**
     * Test the 'getWaiverTemplateRaw' function.
     */
    public function testGetWaiverTemplateRaw()
    {
        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::template());

        $response = $sw->getWaiverTemplateRaw('TestingTemplateId');
        $this->assertInstanceOf(SmartwaiverRawResponse::class, $response);

        $this->checkGetRequests($container, ['/v4/templates/TestingTemplateId']);
    }

    /**
     * Test the 'getWaiversRaw' function with default parameters
     */
    public function testGetWaiversDefaultRaw()
    {
        $numWaivers = 5;
        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::waivers($numWaivers));

        $response = $sw->getWaiverSummariesRaw();
        $this->assertInstanceOf(SmartwaiverRawResponse::class, $response);

        $this->checkGetRequests($container, ['/v4/waivers?limit=20']);
    }

    /**
     * Test all the individual parameters
     */
    public function testGetWaiversRawParams()
    {
        $paths = [
            '/v4/waivers?limit=5',
            '/v4/waivers?limit=20&verified=true',
            '/v4/waivers?limit=20&templateId=alkagaldeab',
            '/v4/waivers?limit=20&fromDts='.urlencode('2016-11-01 00:00:00'),
            '/v4/waivers?limit=20&toDts='.urlencode('2016-11-01 00:00:00'),
        ];

        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::waivers(1), count($paths));

        $sw->getWaiverSummariesRaw(5);
        $sw->getWaiverSummariesRaw(20, true);
        $sw->getWaiverSummariesRaw(20, null, 'alkagaldeab');
        $sw->getWaiverSummariesRaw(20, null, '', '2016-11-01 00:00:00');
        $sw->getWaiverSummariesRaw(20, null, '', '', '2016-11-01 00:00:00');

        $this->checkGetRequests($container, $paths);
    }

    /**
     * Test the 'getWaiverRaw' function
     */
    public function testGetWaiverRaw()
    {
        $paths = [
            '/v4/waivers/6jebdfxzvrdkd?pdf=false',
            '/v4/waivers/6jebdfxzvrdkd?pdf=false',
            '/v4/waivers/6jebdfxzvrdkd?pdf=true',
        ];

        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::waiver(), count($paths));

        $response = $sw->getWaiverRaw('6jebdfxzvrdkd');
        $this->assertInstanceOf(SmartwaiverRawResponse::class, $response);

        $sw->getWaiverRaw('6jebdfxzvrdkd', false);
        $sw->getWaiverRaw('6jebdfxzvrdkd', true);

        $this->checkGetRequests($container, $paths);
    }

    /**
     * Test the 'getWebhookRaw' function
     */
    public function testGetWebhookRaw()
    {
        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::webhooks());

        $response = $sw->getWebhookConfigRaw();
        $this->assertInstanceOf(SmartwaiverRawResponse::class, $response);

        $this->checkGetRequests($container, ['/v4/webhooks/configure']);
    }

    /**
     * Test the 'setWebhookConfigRaw' function
     */
    public function testSetWebhookConfigRaw()
    {
        $container = [];
        $sw = $this->createMockedSmartwaiver($container, APISuccessResponses::webhooks());

        $response = $sw->setWebhookConfigRaw(
            'https://endpoint.example.org',
            SmartwaiverWebhook::WEBHOOK_BEFORE_AND_AFTER_EMAIL
        );
        $this->assertInstanceOf(SmartwaiverRawResponse::class, $response);

        // Check that the right requests were sent
        $this->assertCount(1, $container);
        $this->assertEquals('PUT', $container[0]['request']->getMethod());
        $this->assertEquals('/v4/webhooks/configure', $container[0]['request']->getRequestTarget());
        $this->assertEquals([self::TEST_API_KEY], $container[0]['request']->getHeader('sw-api-key'));
        $this->assertEquals(['application/json'], $container[0]['request']->getHeader('Content-Type'));
        $this->assertEquals(
            '{"endpoint":"https:\/\/endpoint.example.org","emailValidationRequired":"both"}',
            $container[0]['request']->getBody()->getContents()
        );
    }

    /**
     * Test the ability to get the last response the SDK received
     */
    public function testLastResponse()
    {
        $templateArray = APISuccessResponses::templateArray();

        $container = [];
        $sw = $this->createMockedSmartwaiver($container, json_encode($templateArray));

        $sw->getWaiverTemplate('TestingTemplateId');
        $swResponse = $sw->getLastResponse();
        $this->assertInstanceOf(SmartwaiverResponse::class, $swResponse);

        $this->assertEquals(4, $swResponse->version);
        $this->assertEquals('a0256461ca244278b412ab3238f5efd2', $swResponse->id);
        $this->assertEquals('2017-01-23T09:15:45.645Z', $swResponse->ts);
        $this->assertEquals('template', $swResponse->type);
        $this->assertEquals($templateArray['template'], $swResponse->responseData);
    }

    /**
     * Create a Smartwaiver Object with Mocked HTTP responses
     *
     * @param array $container The container to put the mocked requests in
     * @param string $response The response to send
     * @param int $numResponses The number of responses to send
     * @return Smartwaiver
     */
    private function createMockedSmartwaiver(&$container, $response, $numResponses = 1)
    {
        // Convert responses to Guzzle Responses
        $mockResponse = [];
        for($i=0; $i<$numResponses; $i++)
            array_push($mockResponse, new Response(200, [], $response));

        // Set up the Mock Handler
        $mock = new MockHandler($mockResponse);
        $history = Middleware::history($container);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        // Create the Smartwaiver object with the mocked handler
        $sw = new Smartwaiver(self::TEST_API_KEY, ['handler' => $handler]);

        return $sw;
    }

    /**
     * Check that the given container contains the appropriate GET requests
     * specified in the path array
     *
     * @param Request[] $container The container of mocked guzzle requests
     * @param string[] $paths The paths of the expected requests
     */
    private function checkGetRequests($container, $paths)
    {
        // Check that the right requests were sent
        $this->assertCount(count($paths), $container);
        for($i=0; $i<count($paths); $i++) {
            $this->assertEquals('GET', $container[$i]['request']->getMethod());
            $this->assertEquals($paths[$i], $container[$i]['request']->getRequestTarget());
            $this->assertEquals([self::TEST_API_KEY], $container[$i]['request']->getHeader('sw-api-key'));
            $this->assertEquals(['SmartwaiverSDK:4.0.0-php:'.phpversion()], $container[$i]['request']->getHeader('User-Agent'));
        }
    }

}