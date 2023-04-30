<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schranz\Search\SEAL\Schema\Field;

/**
 * Type to store any PHP float value.
 *
 * @property false $searchable
 *
 * @readonly
 */
final class FloatField extends AbstractField
{
    /**
     * @param false $searchable
     * @param array<string, mixed> $options
     */
    public function __construct(
        string $name,
        bool $multiple = false,
        bool $searchable = false,
        bool $filterable = false,
        bool $sortable = false,
        array $options = [],
    ) {
        if ($searchable) { // @phpstan-ignore-line
            throw new \InvalidArgumentException('Searchability for FloatField is not yet implemented: https://github.com/schranz-search/schranz-search/issues/97');
        }

        parent::__construct(
            $name,
            $multiple,
            $searchable,
            $filterable,
            $sortable,
            $options,
        );
    }
}
