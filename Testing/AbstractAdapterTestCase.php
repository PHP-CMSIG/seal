<?php

namespace Schranz\Search\SEAL\Testing;

use PHPUnit\Framework\TestCase;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Engine;
use Schranz\Search\SEAL\Exception\DocumentNotFoundException;
use Schranz\Search\SEAL\Schema\Schema;

abstract class AbstractAdapterTestCase extends TestCase
{
    protected static AdapterInterface $adapter;

    protected static Engine $engine;

    protected static Schema $schema;

    private static TaskHelper $taskHelper;

    public function setUp(): void
    {
        self::$taskHelper = new TaskHelper();
    }

    protected static function getEngine(): Engine
    {
        if (!isset(self::$engine)) {
            self::$schema = TestingHelper::createSchema();

            self::$engine = new Engine(
                self::$adapter,
                self::$schema,
            );
        }

        return self::$engine;
    }

    public function testIndex(): void
    {
        $engine = self::getEngine();
        $indexName = TestingHelper::INDEX_SIMPLE;

        $this->assertFalse($engine->existIndex($indexName));

        $task = $engine->createIndex($indexName, ['return_slow_promise_result' => true]);
        $task->wait();
        static::waitForCreateIndex(); // TODO remove when all adapter migrated to $task->wait();

        $this->assertTrue($engine->existIndex($indexName));

        $task = $engine->dropIndex($indexName, ['return_slow_promise_result' => true]);
        $task->wait();
        static::waitForDropIndex(); // TODO remove when all adapter migrated to $task->wait();

        $this->assertFalse($engine->existIndex($indexName));
    }

    public function testSchema(): void
    {
        $engine = self::getEngine();
        $indexes = self::$schema->indexes;

        $task = $engine->createSchema(['return_slow_promise_result' => true]);
        $task->wait();
        static::waitForCreateIndex(); // TODO remove when all adapter migrated to $task->wait();

        foreach (array_keys($indexes) as $index) {
            $this->assertTrue($engine->existIndex($index));
        }

        $task = $engine->dropSchema(['return_slow_promise_result' => true]);
        $task->wait();
        static::waitForDropIndex(); // TODO remove when all adapter migrated to $task->wait();

        foreach (array_keys($indexes) as $index) {
            $this->assertFalse($engine->existIndex($index));
        }
    }

    public function testDocument(): void
    {
        $engine = self::getEngine();
        $task = $engine->createSchema(['return_slow_promise_result' => true]);
        $task->wait();
        static::waitForCreateIndex(); // TODO remove when all adapter migrated to $task->wait();

        $documents = TestingHelper::createComplexFixtures();

        foreach ($documents as $document) {
            self::$taskHelper->tasks[] = $engine->saveDocument(TestingHelper::INDEX_COMPLEX, $document, ['return_slow_promise_result' => true]);
        }

        self::$taskHelper->waitForAll();
        static::waitForAddDocuments(); // TODO remove when all adapter migrated to $task->wait();

        $loadedDocuments = [];
        foreach ($documents as $document) {
            $loadedDocuments[] = $engine->getDocument(TestingHelper::INDEX_COMPLEX, $document['id']);
        }

        $this->assertSame(
            count($documents),
            count($loadedDocuments),
        );

        foreach ($loadedDocuments as $key => $loadedDocument) {
            $expectedDocument = $documents[$key];

            $this->assertSame($expectedDocument, $loadedDocument);
        }

        foreach ($documents as $document) {
            self::$taskHelper->tasks[] = $engine->deleteDocument(TestingHelper::INDEX_COMPLEX, $document['id'], ['return_slow_promise_result' => true]);
        }

        self::$taskHelper->waitForAll();
        static::waitForDeleteDocuments(); // TODO remove when all adapter migrated to $task->wait();

        foreach ($documents as $document) {
            $exceptionThrown = false;

            try {
                $engine->getDocument(TestingHelper::INDEX_COMPLEX, $document['id']);
            } catch (DocumentNotFoundException $e) {
                $exceptionThrown = true;
            }

            $this->assertTrue(
                $exceptionThrown,
                'Expected the exception "DocumentNotFoundException" to be thrown.'
            );
        }
    }

    public static function setUpBeforeClass(): void
    {
        self::getEngine()->dropSchema();
    }

    public static function tearDownAfterClass(): void
    {
        self::getEngine()->dropSchema();
    }

    /**
     * @deprecated Use return AsyncTask instead.
     *
     * For async adapters, we need to wait for the index to add documents.
     */
    protected static function waitForAddDocuments(): void
    {
        // TODO remove when all adapter migrated to $task->wait();
    }

    /**
     * @deprecated Use return AsyncTask instead.
     *
     * For async adapters, we need to wait for the index to delete documents.
     */
    protected static function waitForDeleteDocuments(): void
    {
        // TODO remove when all adapter migrated to $task->wait();
    }

    /**
     * @deprecated Use return AsyncTask instead.
     *
     * For async adapters, we need to wait for the index to be created.
     */
    protected static function waitForCreateIndex(): void
    {
        // TODO remove when all adapter migrated to $task->wait();
    }

    /**
     * @deprecated Use return AsyncTask instead.
     *
     * For async adapters, we need to wait for the index to be deleted.
     */
    protected static function waitForDropIndex(): void
    {
        // TODO remove when all adapter migrated to $task->wait();
    }
}
