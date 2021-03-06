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

namespace Smartwaiver\Tests\Factories;

/**
 * Class SmartwaiverTypes
 *
 * @package Smartwaiver\Tests\Factories
 */
class SmartwaiverTypes
{
    /**
     * Create an input array for a SmartwaiverTemplate object
     *
     * @return array
     */
    public static function createTemplate()
    {
        return [
            'templateId' => 'sprswrvh2keeh',
            'title' => 'Demo Waiver',
            'publishedVersion' => 78015,
            'publishedOn' => '2017-01-24 11:14:25',
            'webUrl' => 'https://www.smartwaiver.com/w/sprswrvh2keeh/web/',
            'kioskUrl' => 'https://www.smartwaiver.com/w/sprswrvh2keeh/kiosk/'
        ];
    }

    /**
     * Create an input array for a SmartwaiverWaiverSummary object
     *
     * @return array
     */
    public static function createWaiverSummary()
    {
        return [
            'waiverId' => '6jebdfxzvrdkd',
            'templateId' => 'sprswrvh2keeh',
            'title' => 'Demo Waiver',
            'createdOn' => '2017-01-24 13:12:29',
            'expirationDate' => '',
            'expired' => false,
            'verified' => true,
            'kiosk' => false,
            'firstName' => 'Kyle',
            'middleName' => '',
            'lastName' => 'Smith',
            'dob' => '2005-12-25',
            'isMinor' => true,
            'tags' => [
                'Green Team'
            ]
        ];
    }

    /**
     * Create an input array for a SmartwaiverWaiver object
     *
     * @return array
     */
    public static function createWaiver()
    {
        $waiver = self::createWaiverSummary();
        $waiver['participants'] = [self::createParticipant()];
        $waiver['clientIP'] = '192.0.2.0';
        $waiver['email']  = 'kyle@example.com';
        $waiver['marketingAllowed']  = false;
        $waiver['addressLineOne']  = '626 NW Arizona Ave.';
        $waiver['addressLineTwo']  = 'Suite 2';
        $waiver['addressCity']  = 'Bend';
        $waiver['addressState']  = 'OR';
        $waiver['addressZip']  = '97703';
        $waiver['addressCountry']  = 'US';
        $waiver['emergencyContactName']  = 'John Smith';
        $waiver['emergencyContactPhone']  = '111-111-1111';
        $waiver['insuranceCarrier']  = 'My Insurance';
        $waiver['insurancePolicyNumber']  = '1234567';
        $waiver['driversLicenseNumber']  = '9876543';
        $waiver['driversLicenseState']  = 'OR';
        $waiver['customWaiverFields']  = [
            'zrmgxh4ft8sqh' => SmartwaiverTypes::createCustomField()
        ];
        $waiver['guardian']  = SmartwaiverTypes::createGuardian();
        $waiver['pdf'] = '';
        return $waiver;
    }

    /**
     * Create an input array for a SmartwaiverParticipant object
     *
     * @return array
     */
    public static function createParticipant()
    {
        return [
            'firstName' => 'Kyle',
            'middleName' => '',
            'lastName' => 'Smith',
            'dob' => '2005-12-25',
            'isMinor' => 'true',
            'gender' => 'Male',
            'phone' => '111-111-1111',
            'tags' => [
                'Beginner'
            ],
            'customParticipantFields' => [
                'w5qe9kkh3bxpe' => SmartwaiverTypes::createCustomField()
            ]
        ];
    }

    /**
     * Create an input array for a SmartwaiverWebhook object
     *
     * @return array
     */
    public static function createWebhook()
    {
        return [
            'endpoint' => 'endpoint',
            'emailValidationRequired' => 'both'
        ];
    }

    /**
     * Create an input array for a SmartwaiverCustomField object
     *
     * @return array
     */
    public static function createCustomField()
    {
        return [
            'value' => 'A friend',
            'displayText' => 'How did you hear about this company?'
        ];
    }

    /**
     * Create an inpute array for a SmartwaiverGuardian object
     *
     * @return array
     */
    public static function createGuardian()
    {
        return [
            'firstName' => 'Jane',
            'middleName' => '',
            'lastName' => 'Smith',
            'phone' => '111-111-1111',
            'relationship' => 'Mother'
        ];
    }
}
