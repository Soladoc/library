<?php
require_once 'model/Offre.php';
final class Activite extends Offre
{
    private const TABLE = '_activite';

    readonly Duree $indication_duree;
    readonly ?int $age_requis;
    readonly string $prestations_incluses;
    readonly ?string $prestations_non_incluses;

    /**
     * Construit une nouvelle activité.
     * @param ?int $id
     * @param Adresse $adresse
     * @param Image $image_principale
     * @param Professionnel $professionnel
     * @param Abonnement $abonnement
     * @param string $titre
     * @param string $resume
     * @param string $description_detaillee
     * @param ?string $url_site_web
     * @param MultiRange<Timestamp> $periodes_ouverture
     * @param Timestamp $modifee_le
     * @param bool $en_ligne
     * @param float $note_moyenne
     * @param ?float $prix_min
     * @param Timestamp $creee_le
     * @param Duree $en_ligne_ce_mois_pendant
     * @param Timestamp $changement_ouverture_suivant_le
     * @param bool $est_ouverte
     * @param Duree $indication_duree
     * @param ?int $age_requis
     * @param string $prestations_incluses
     * @param ?string $prestations_non_incluses
     */
    function __construct(
        ?int $id,
        Adresse $adresse,
        Image $image_principale,
        Professionnel $professionnel,
        Abonnement $abonnement,
        string $titre,
        string $resume,
        string $description_detaillee,
        ?string $url_site_web,
        MultiRange $periodes_ouverture,
        Timestamp $modifee_le,
        bool $en_ligne,
        float $note_moyenne,
        ?float $prix_min,
        Timestamp $creee_le,
        Duree $en_ligne_ce_mois_pendant,
        Timestamp $changement_ouverture_suivant_le,
        bool $est_ouverte,
        Duree $indication_duree,
        ?int $age_requis,
        string $prestations_incluses,
        ?string $prestations_non_incluses,
    ) {
        parent::__construct(
            $id,
            $adresse,
            $image_principale,
            $professionnel,
            $abonnement,
            $titre,
            $resume,
            $description_detaillee,
            $url_site_web,
            $periodes_ouverture,
            $modifee_le,
            $en_ligne,
            $note_moyenne,
            $prix_min,
            $creee_le,
            $en_ligne_ce_mois_pendant,
            $changement_ouverture_suivant_le,
            $est_ouverte,
        );
        $this->indication_duree = $indication_duree;
        $this->age_requis = $age_requis;
        $this->prestations_incluses = $prestations_incluses;
        $this->prestations_non_incluses = $prestations_non_incluses;
    }
}
