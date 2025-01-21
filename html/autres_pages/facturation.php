<?php
require_once 'component/Page.php';
require_once 'auth.php';
require_once 'model/Duree.php';
require_once 'model/Offre.php';
require_once 'util.php';

$page = new Page('Facturation');

const TVA = .2;

$page->put(function () {
    ?>
    <section class="centrer-enfants">
        <table id="facturation">
            <thead>
                <tr>
                    <th scope="col">Titre</th>
                    <th scope="col">Catégorie</th>

                    <th scope="col">Option</th>
                    <th scope="col">Semaines d'option</th>
                    <th scope="col">Prix option/semaine(HT)</th>
                    <th scope="col">Prix Option(HT)</th>

                    <th scope="col">Formule</th>
                    <th scope="col">Prix/J(HT)</th>
                    <th scope="col">Jours en ligne</th>

                    <th scope="col">Prix HT</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $resultat_global = 0;  // resultat global
                $resultat_offre = 0;  // resultat offre
                $id_professionnel = Auth\exiger_connecte_pro();
                $offres = Offre::from_db_all_ordered(id_professionnel: $id_professionnel);
                foreach ($offres as $offre) {
                    ?>
                    <tr>
                        <td><?= h14s($offre->titre) ?></td>
                        <td><?= h14s($offre->categorie) ?></td>
                        
                        <!-- affiche le type de l'option -->
                        <?php 
                        if ($offre->option) {
                            ?>
                            <td><?= h14s($offre->option->nom) ?></td>
                            <td><?= h14s($offre->option->nb_semaines) ?></td>
                            <td><?= h14s($offre->option->prix_hebdomadaire) ?>&nbsp;€</td>
                            <?php
                            $prixOption = $offre->option->nb_semaines * $offre->option->prix_hebdomadaire;
                            ?>
                            <td><?= h14s(round($prixOption,2)) ?>&nbsp;€</td>

                        <?php
                        }else{
                            $prixOption = false;
                        ?>
                           <td>N/A</td> 
                           <td>0</td> 
                           <td>N/A</td> 
                           <td>0</td> 
                        <?php
                        }
                        ?>
                        
                        <td><?= h14s($offre->abonnement->libelle) ?></td>
                        <td class="prix-ht"><?= h14s(round($offre->abonnement->prix_journalier, 2)) ?>&nbsp;€</td>
                        <td><?= h14s($offre->en_ligne_ce_mois_pendant->days) ?></td>
                        <?php
                        // affiche le prix de l'offre ce mois ci ou NA si l'offre est gratuite
                        if (strcasecmp($offre->abonnement->libelle, 'Gratuit') === 0) {
                            ?>
                            <td class="prix-ht">N/A</td>
                            <?php
                        } else {
                            $resultat_offre = ceil($offre->en_ligne_ce_mois_pendant->days) * $offre->abonnement->prix_journalier;
                            if ($prixOption) {
                                $resultat_offre = $resultat_offre + $prixOption; 
                            }                             
                            $resultat_global += $resultat_offre;
                            ?>
                            <td class="prix-ht"><?= round($resultat_offre, 2) ?>&nbsp;€</td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="row" colspan="9">Prix global HT</th>
                    <td class="prix-ht"><?= round($resultat_global, 2) ?>&nbsp;€</td>
                </tr>
                <tr>
                    <th scope="row" colspan="9">TVA <?= TVA * 100 ?>&nbsp;%</th>
                    <td class="prix-ht"><?= round($resultat_global * TVA, 2) ?>&nbsp;€</td>
                </tr>
                <tr>
                    <th scope="row" colspan="9">Prix global TTC</th>
                    <td class="prix-ht"><?= round($resultat_global + $resultat_global * TVA, 2) ?>&nbsp;€</td>
                </tr>
            </tfoot>
        </table>
    </section>
    <!-- Bouton pour obtenir le pdf -->
    <section>
        <a class="btn-more-info bouton_principale_pro" href="facture_fpdf.php" id="obtenir_facture_pdf" target="_blank">Version PDF</a>
    </section>
    <?php
});
?>