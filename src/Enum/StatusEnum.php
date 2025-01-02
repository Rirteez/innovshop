<?php
    namespace App\Enum;

    enum StatusEnum: string
    {
        case EN_COURS = 'En cours';
        case EXPEDIEE = 'Expediée';
        case TERMINEE = 'Terminée';
        case ANNULEE = 'Annulée';


        public function getLabel(): string
        {
            return match ($this) {
                self::EN_COURS => 'En cours de validation',
                self::EXPEDIEE => 'Expédiée',
                self::TERMINEE => 'Terminée',
                self::ANNULEE => 'Annulée',
            };
        }


        public static function getChoices(): array
        {
            return [
                'En cours' => self::EN_COURS,
                'Expédiée' => self::EXPEDIEE,
                'Terminée' => self::TERMINEE,
                'Annulée' => self::ANNULEE,
            ];
        }

        public static function casesToArray(): array
        {
            $cases = [];
            foreach (self::cases() as $case) {
                $cases[$case->value] = $case->value;
            }
            return $cases;
        }

        public function toString(): string
        {
            return $this->value;
        }
    }
?>

