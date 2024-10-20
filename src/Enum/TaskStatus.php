<?php

namespace App\Enum;

enum TaskStatus: string
{
    case ToDo = 'to do';
    case Doing = 'doing';
    case Done = 'done';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::ToDo => 'A faire',
            self::Doing => 'En cours',
            self::Done => 'Terminée'
        };
    }
}