<?php
namespace Neuralpin\Migration;

class FileCreator
{
    public static function start()
    {
        file_put_contents('php://output', json_encode($_SERVER['argv']).PHP_EOL);

        if (count($_SERVER['argv']) !== 4) {
            file_put_contents('php://output', "Usage: composer create-migration <migration_name>".PHP_EOL);
            exit(1);
        }
        
        self::createMigrationFile($_SERVER['argv'][2], $_SERVER['argv'][3]);
    }


    public static function createMigrationFile(string $dirname, string $name) {
        $timestamp = date('Y_m_d_His');
        $migrationName = "{$timestamp}__{$name}";
        $filename = "$migrationName.php";
        $directory = realpath(__DIR__."/../$dirname");
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $filepath = "{$directory}/{$filename}";
        $template = "<?php\n\nreturn new class\n{\n\n\tpublic function up()\n\t{\n\n\t}\n\n\tpublic function down()\n\t{\n\n\t}\n\n};";

        if (file_put_contents($filepath, $template)) {
            file_put_contents('php://output', "Migration created successfully in $filepath".PHP_EOL);
        } else {
            file_put_contents('php://output', "Failed to create migration file".PHP_EOL);
        }
    }
}