<?php

use App\Console\Commands\NameCommand;
use Framework\db\Command\MigrateCommand;

return [
    MigrateCommand::class,
    NameCommand::class,
];
