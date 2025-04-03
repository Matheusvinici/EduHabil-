<?php
namespace App\Enums;

enum Deficiencia: string
{
    case VISUAL = 'DV';
    case AUDITIVA = 'DA';
    case FISICA = 'DF';
    case INTELECTUAL = 'DI';
    case AUTISMO = 'TEA';
    case NENHUMA = 'ND';
    
    public function label(): string
    {
        return match($this) {
            self::VISUAL => 'Deficiência Visual',
            self::AUDITIVA => 'Deficiência Auditiva',
            self::FISICA => 'Deficiência Física',
            self::INTELECTUAL => 'Deficiência Intelectual',
            self::AUTISMO => 'Autismo',
            self::NENHUMA => 'Nenhuma',
        };
    }
}