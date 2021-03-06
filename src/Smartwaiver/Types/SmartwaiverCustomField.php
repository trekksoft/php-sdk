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

namespace Smartwaiver\Types;

/**
 * Class SmartwaiverCustomField
 *
 * This class represents a custom field inside of a signed waiver.
 *
 * @package Smartwaiver\Types
 */
class SmartwaiverCustomField extends SmartwaiverType
{
    /**
     * The required fields in the constructor array to create this object
     */
    const REQUIRED_KEYS = [
        'value',
        'displayText'
    ];

    /**
     * @var string The value of the custom waiver field
     */
    public $value;

    /**
     * @var string The display text of the custom waiver field
     */
    public $displayText;

    /**
     * Create a SmartwaiverCustomField object by providing an array with all
     * the required keys. See REQUIRED_KEYS for that information.
     *
     * @param array $field  The input array containing all the information
     *
     * @throws \InvalidArgumentException Thrown if any of the required keys are missing
     */
    public function __construct(array $field)
    {
        // Check for required keys
        parent::__construct($field, self::REQUIRED_KEYS, self::class);

        // Load all the information into public variables
        $this->value = $field['value'];
        $this->displayText = $field['displayText'];
    }
}