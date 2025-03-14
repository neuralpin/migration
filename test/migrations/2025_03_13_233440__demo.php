<?php

use PDO;
use Neuralpin\Migration\MigrationInterface;

return new class implements MigrationInterface
{
	private PDO $PDO;

	public function up()
	{
		$this->PDO->exec('CREATE TABLE if not exists demo (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name varchar NOT NULL
        )');

		$this->PDO->exec('INSERT INTO demo (name) VALUES ("lorem"), ("ipsum")');
	}

	public function down()
	{
		$this->PDO->exec('DROP TABLE demo');
	}

	public function setPDO(PDO $PDO)
	{
		$this->PDO = $PDO;
	}

};