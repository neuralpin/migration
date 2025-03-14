<?php

namespace Neuralpin\Migration;

use PDO;
use Neuralpin\Migration\MigrationInterface;

class MigrationManager
{
    private PDO $pdo;
    private string $migrationDirectory;

    public function __construct(PDO $pdo, string $migrationDirectory)
    {
        $this->pdo = $pdo;
        $this->migrationDirectory = $migrationDirectory;
    }

    public function runMigrations($direction = 'up')
    {
        $batch = $this->getCurrentBatch() + 1;

        $files = glob("{$this->migrationDirectory}/*.php");
        usort($files, function ($a, $b) {
            return strcmp(basename($a), basename($b));
        });

        if ($direction === 'down') {
            $files = array_reverse($files);
        }


        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            $Migration = require $file;

            if($Migration instanceof MigrationInterface)
            {
                $Migration->setPDO($this->pdo);
            }

            if ($direction === 'up') {
                if (! $this->isMigrationLogged($className)) {
                    $Migration->$direction();
                    $this->logMigration($className, $batch);
                }
            } else {
                if ($this->isMigrationLogged($className)) {
                    $Migration->$direction();
                    $this->removeMigrationLog($className);
                }
            }
        }
    }

    private function getCurrentBatch()
    {
        $stmt = $this->pdo->query('SELECT MAX(batch) as batch FROM migrations');
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return $result?->batch ?? 0;
    }

    private function isMigrationLogged($migration)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM migrations WHERE migration = :migration');
        $stmt->execute(['migration' => $migration]);

        return $stmt->fetchColumn() > 0;
    }

    private function logMigration($migration, $batch)
    {
        $stmt = $this->pdo->prepare('INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)');
        $stmt->execute(['migration' => $migration, 'batch' => $batch]);
    }

    private function removeMigrationLog($migration)
    {
        $stmt = $this->pdo->prepare('DELETE FROM migrations WHERE migration = :migration');
        $stmt->execute(['migration' => $migration]);
    }
}
