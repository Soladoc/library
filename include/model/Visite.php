<?php
require_once 'model/Offre.php';

final class Visite extends Offre
{
    const CATEGORIE = 'visite';

    private const TABLE = '_visite';

    readonly Duree $indication_duree;

    // todo: langues

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
     * @param Timestamp $modifiee_le
     * @param bool $en_ligne
     * @param float $note_moyenne
     * @param ?float $prix_min
     * @param Timestamp $creee_le
     * @param Duree $en_ligne_ce_mois_pendant
     * @param Timestamp $changement_ouverture_suivant_le
     * @param bool $est_ouverte
     * @param Duree $indication_duree
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
        Timestamp $modifiee_le,
        bool $en_ligne,
        float $note_moyenne,
        ?float $prix_min,
        Timestamp $creee_le,
        Duree $en_ligne_ce_mois_pendant,
        Timestamp $changement_ouverture_suivant_le,
        bool $est_ouverte,
        Duree $indication_duree,
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
            $modifiee_le,
            $en_ligne,
            $note_moyenne,
            $prix_min,
            $creee_le,
            $en_ligne_ce_mois_pendant,
            $changement_ouverture_suivant_le,
            $est_ouverte,
        );
        $this->indication_duree = $indication_duree;
    }
}
