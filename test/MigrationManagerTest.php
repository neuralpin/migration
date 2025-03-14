<?php

use Neuralpin\Migration\MigrationManager;
use PDO;
use PHPUnit\Framework\TestCase;

class MigrationManagerTest extends TestCase
{
    private PDO $pdo;

    private $MigrationManager;

    private string $databasePath = __DIR__.'/test.db';

    private string $migrationDirectory = __DIR__.'/migrations';

    protected function setUp(): void
    {
        // Create SQLite database
        $this->pdo = new PDO("sqlite:{$this->databasePath}");
        $this->MigrationManager = new MigrationManager($this->pdo, $this->migrationDirectory);

        // Clean up the migrations table before each test
        $this->pdo->exec('DROP TABLE IF EXISTS migrations');
        $this->pdo->exec('CREATE TABLE migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            migration TEXT NOT NULL,
            batch INTEGER NOT NULL
        );');
    }

    public function test_run_migrations_up()
    {
        $this->MigrationManager->runMigrations('up');

        $stmt = $this->pdo->query('SELECT COUNT(*) as count FROM migrations');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(1, $result['count']);

        $stmt = $this->pdo->query("SELECT batch FROM migrations WHERE migration = '2025_03_13_233440__demo'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(1, $result['batch']);
    }

    public function test_run_migrations_down()
    {
        $this->MigrationManager->runMigrations('down');

        $stmt = $this->pdo->query('SELECT COUNT(*) as count FROM migrations');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(0, $result['count']);
    }
}
