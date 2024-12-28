<?php
    namespace App\Enum;

    enum StatusEnum: string
    {
        case EN_COURS = 'En cours';
        case EXPEDIEE = 'Expediée';
        case TERMINEE = 'Terminée';
        case ANNULEE = 'Annulée';

        // Méthode pour obtenir les libellés lisibles pour l'interface utilisateur
        public function getLabel(): string
        {
            return match ($this) {
                self::EN_COURS => 'En cours de validation',
                self::EXPEDIEE => 'Expédiée',
                self::TERMINEE => 'Terminée',
                self::ANNULEE => 'Annulée',
            };
        }

        // Méthode statique pour récupérer toutes les valeurs pour les formulaires ou les choix
        public static function getChoices(): array
        {
            return [
                'En cours' => self::EN_COURS->value,
                'Expediée' => self::EXPEDIEE->value,
                'Terminée' => self::TERMINEE->value,
                'Annulée' => self::ANNULEE->value,
            ];
        }
    }
?>

