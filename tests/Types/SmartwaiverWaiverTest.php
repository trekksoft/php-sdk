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

use InvalidArgumentException;
use Smartwaiver\Tests\Factories\SmartwaiverTypes;
use Smartwaiver\Types\SmartwaiverCustomField;
use Smartwaiver\Types\SmartwaiverGuardian;
use Smartwaiver\Types\SmartwaiverParticipant;
use Smartwaiver\Types\SmartwaiverWaiver;

/**
 * Class SmartwaiverWaiverTest
 *
 * @package Smartwaiver\Tests
 */
class SmartwaiverWaiverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test whether a required keys error is generated correctly
     */
    public function testRequiredKeys()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot create a SmartwaiverWaiver with missing field: waiverId');

        $waiver = SmartwaiverTypes::createWaiver();
        unset($waiver['waiverId']);

        $swWaiver = new SmartwaiverWaiver($waiver);
    }

    /**
     * Test whether all data values are correctly assigned
     */
    public function testSuccess()
    {
        $waiver = SmartwaiverTypes::createWaiver();
        $swWaiver = new SmartwaiverWaiver($waiver);

        $this->assertEquals($waiver['waiverId'], $swWaiver->waiverId);
        $this->assertEquals($waiver['templateId'], $swWaiver->templateId);
        $this->assertEquals($waiver['title'], $swWaiver->title);
        $this->assertEquals($waiver['createdOn'], $swWaiver->createdOn);
        $this->assertEquals($waiver['expirationDate'], $swWaiver->expirationDate);
        $this->assertEquals($waiver['expired'], $swWaiver->expired);
        $this->assertEquals($waiver['verified'], $swWaiver->verified);
        $this->assertEquals($waiver['kiosk'], $swWaiver->kiosk);
        $this->assertEquals($waiver['firstName'], $swWaiver->firstName);
        $this->assertEquals($waiver['middleName'], $swWaiver->middleName);
        $this->assertEquals($waiver['lastName'], $swWaiver->lastName);
        $this->assertEquals($waiver['dob'], $swWaiver->dob);
        $this->assertEquals($waiver['isMinor'], $swWaiver->isMinor);
        $this->assertEquals($waiver['clientIP'], $swWaiver->clientIP);
        $this->assertEquals($waiver['tags'], $swWaiver->tags);

        $this->assertCount(count($waiver['participants']), $swWaiver->participants);
        foreach($swWaiver->participants as $participant) {
            $this->assertInstanceOf(SmartwaiverParticipant::class, $participant);
        }

        $this->assertEquals($waiver['email'], $swWaiver->email);
        $this->assertEquals($waiver['marketingAllowed'], $swWaiver->marketingAllowed);
        $this->assertEquals($waiver['addressLineOne'], $swWaiver->addressLineOne);
        $this->assertEquals($waiver['addressLineTwo'], $swWaiver->addressLineTwo);
        $this->assertEquals($waiver['addressCity'], $swWaiver->addressCity);
        $this->assertEquals($waiver['addressState'], $swWaiver->addressState);
        $this->assertEquals($waiver['addressZip'], $swWaiver->addressZip);
        $this->assertEquals($waiver['addressCountry'], $swWaiver->addressCountry);
        $this->assertEquals($waiver['emergencyContactName'], $swWaiver->emergencyContactName);
        $this->assertEquals($waiver['emergencyContactPhone'], $swWaiver->emergencyContactPhone);
        $this->assertEquals($waiver['insuranceCarrier'], $swWaiver->insuranceCarrier);
        $this->assertEquals($waiver['insurancePolicyNumber'], $swWaiver->insurancePolicyNumber);
        $this->assertEquals($waiver['driversLicenseNumber'], $swWaiver->driversLicenseNumber);
        $this->assertEquals($waiver['driversLicenseState'], $swWaiver->driversLicenseState);

        $this->assertCount(count($waiver['customWaiverFields']), $swWaiver->customWaiverFields);
        foreach($swWaiver->customWaiverFields as $key => $customWaiverField) {
            $this->assertInstanceOf(SmartwaiverCustomField::class, $customWaiverField);
        }

        $this->assertInstanceOf(SmartwaiverGuardian::class, $swWaiver->guardian);
        $this->assertEquals($waiver['pdf'], $swWaiver->pdf);
    }

    /**
     * Test that participants not being an array throws an exception
     */
    public function testParticipantsIsNotArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Participants field must be an array');

        $waiver = SmartwaiverTypes::createWaiver();
        $waiver['participants'] = '';

        $swWaiver = new SmartwaiverWaiver($waiver);
    }

    /**
     * Test that having no participants throws an exception
     */
    public function testNoParticipants()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('There must be at least one participant');

        $waiver = SmartwaiverTypes::createWaiver();
        $waiver['participants'] = [];

        $swWaiver = new SmartwaiverWaiver($waiver);
    }
}
