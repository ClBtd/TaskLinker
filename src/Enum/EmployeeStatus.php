<?php

namespace App\Enum;

enum EmployeeStatus: string
{
    case CDD = 'cdd';
    case CDI = 'cdi';
    case Freelance = 'freelance';
    case Interim = 'interim';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::CDD => 'CDD',
            self::CDI => 'CDI',
            self::Freelance => 'Freelance',
            self::Interim => 'Intérim'
        };
    }
}