<?php
    namespace App\Enum;

    enum StatusEnum: string
    {
        case EN_COURS_DE_VALIDATION = 'En cours de validation';
        case VALIDE = 'Validée';
        case EN_COURS_DE_TRAITEMENT = 'En cours de traitement';
        case EXPEDIEE = 'Expédiée';
        case ANNULEE = 'Annulée';
        case TERMINEE = 'Terminée';
    }
?>

