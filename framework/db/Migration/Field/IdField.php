<?php

namespace Framework\db\Migration\Field;

use Framework\db\Exception\MigrationException;

class IdField extends Field
{
    public function default()
    {
        throw new MigrationException('ID fields cannot have a default value');
    }
}
