<?php
// src/Doctrine/EnumType.php

namespace App\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class EnumType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return 'ENUM("Organisateur","Admin","Participant")';
    }

    public function getName()
    {
        return 'enum';
    }
}