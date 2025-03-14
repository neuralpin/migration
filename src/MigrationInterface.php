<?php

namespace Neuralpin\Migration;

use PDO;

interface MigrationInterface
{
    public function setPDO(PDO $PDO);
}
