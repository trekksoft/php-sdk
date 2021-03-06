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

use GuzzleHttp\Psr7\Response;
use Smartwaiver\SmartwaiverRoutes;

/**
 * Class SmartwaiverRoutesTest
 *
 * @package Smartwaiver\Tests
 */
class SmartwaiverRoutesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Base URI for all URLs
     */
    const BASE_URI = 'https://api.smartwaiver.com';

    /**
     * Test get waiver templates route
     */
    public function testGetWaiverTemplates()
    {
        $url = SmartwaiverRoutes::getWaiverTemplates();
        $this->assertEquals(self::BASE_URI . '/v4/templates', $url);
    }

    /**
     * Test get waiver template route
     */
    public function testGetWaiverTemplate()
    {
        $url = SmartwaiverRoutes::getWaiverTemplate('TestingTemplateId');
        $this->assertEquals(self::BASE_URI . '/v4/templates/TestingTemplateId', $url);
    }

    /**
     * Test get waiver summaries route
     */
    public function testGetWaiverSummaries()
    {
        $url = SmartwaiverRoutes::getWaiverSummaries();
        $this->assertEquals(self::BASE_URI . '/v4/waivers?limit=20', $url);

        $url = SmartwaiverRoutes::getWaiverSummaries(5);
        $this->assertEquals(self::BASE_URI . '/v4/waivers?limit=5', $url);

        $url = SmartwaiverRoutes::getWaiverSummaries(20, true);
        $this->assertEquals(self::BASE_URI . '/v4/waivers?limit=20&verified=true', $url);

        $url = SmartwaiverRoutes::getWaiverSummaries(20, null, 'TestingTemplateId');
        $this->assertEquals(self::BASE_URI . '/v4/waivers?limit=20&templateId=TestingTemplateId', $url);

        $url = SmartwaiverRoutes::getWaiverSummaries(20, null, '', '2016-11-01 00:00:00');
        $this->assertEquals(self::BASE_URI . '/v4/waivers?limit=20&fromDts='.urlencode('2016-11-01 00:00:00'), $url);

        $url = SmartwaiverRoutes::getWaiverSummaries(20, null, '', '', '2016-11-01 00:00:00');
        $this->assertEquals(self::BASE_URI . '/v4/waivers?limit=20&toDts='.urlencode('2016-11-01 00:00:00'), $url);
    }

    /**
     * Test get waiver route
     */
    public function testGetWaiver()
    {
        $url = SmartwaiverRoutes::getWaiver('TestingWaiverId');
        $this->assertEquals(self::BASE_URI . '/v4/waivers/TestingWaiverId?pdf=false', $url);

        $url = SmartwaiverRoutes::getWaiver('TestingWaiverId', false);
        $this->assertEquals(self::BASE_URI . '/v4/waivers/TestingWaiverId?pdf=false', $url);

        $url = SmartwaiverRoutes::getWaiver('TestingWaiverId', true);
        $this->assertEquals(self::BASE_URI . '/v4/waivers/TestingWaiverId?pdf=true', $url);
    }

    /**
     * Test get webhook config route
     */
    public function testGetWebhookConfig()
    {
        $url = SmartwaiverRoutes::getWebhookConfig();
        $this->assertEquals(self::BASE_URI . '/v4/webhooks/configure', $url);
    }

    /**
     * Test set webhook config route
     */
    public function testSetWebhookConfig()
    {
        $url = SmartwaiverRoutes::setWebhookConfig();
        $this->assertEquals(self::BASE_URI . '/v4/webhooks/configure', $url);
    }
}