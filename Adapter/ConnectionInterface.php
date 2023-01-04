<?php

namespace Schranz\Search\SEAL\Adapter;

use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

interface ConnectionInterface
{
    /**
     * @param array<string, mixed> $document
     */
    public function save(Index $index, array $document): void;

    public function delete(Index $index, string $identifier): void;

    public function search(Search $search): Result;
}
