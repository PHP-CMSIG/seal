<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

/**
 * Type to store date and date times.
 */
final class DateTimeField extends AbstractField
{
    public function __construct(string $name, bool $multiple = false)
    {
        parent::__construct($name, FieldType::DATETIME, $multiple);
    }
}
