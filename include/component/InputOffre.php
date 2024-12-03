<?php

require_once 'auth.php';
require_once 'const.php';
require_once 'model/Activite.php';
require_once 'model/ParcAttractions.php';
require_once 'model/Restaurant.php';
require_once 'model/Spectacle.php';
require_once 'model/Visite.php';
require_once 'model/NonEmptyRange.php';
require_once 'model/FiniteTimestamp.php';
require_once 'component/InputDuree.php';
require_once 'component/InputImage.php';
require_once 'component/DynamicTable.php';
require_once 'model/ProfessionnelPrive.php';
require_once 'component/InputAdresse.php';

/**
 * @extends Input<Offre>
 */
final class InputOffre extends Input
{
    private readonly string $categorie;
    private readonly Professionnel $professionnel;
    private readonly InputDuree $input_indication_duree;
    private readonly InputAdresse $input_adresse;
    private readonly InputImage $input_image_principale;
    private readonly InputImage $input_image_plan;
    /** @var DynamicTable<Tarifs> */
    private readonly DynamicTable $tarifs;
    /** @var DynamicTable<NonEmptyRange<FiniteTimestamp>> */
    private readonly DynamicTable $periodes;

    function __construct(string $categorie, Professionnel $professionnel, string $id = '', string $name = '', string $form_id = '')
    {
        parent::__construct($id, $name, $form_id);
        $this->categorie = $categorie;
        $this->professionnel = $professionnel;
        $this->input_indication_duree = new InputDuree(
            $this->id('indication_duree'),
            $this->name('indication_duree'),
            $form_id,
        );
        $this->input_adresse = new InputAdresse(
            $this->id('adresse'),
            $this->name('adresse'),
            $form_id,
        );
        $this->input_image_principale = new InputImage(
            'Photo principale',
            $this->id('image_principale'),
            $this->name('image_principale'),
            $form_id,
        );
        $this->input_image_plan = new InputImage(
            'Photo du plan',
            $this->id('input_image_plan'),
            $this->name('input_image_plan'),
            $form_id,
        );
        $this->tarifs = new DynamicTable(
            ['Nom', 'Montant'],
            function (DynamicTable $dt, ?array $row) {
                $form_attr = $dt->form_id ? "form=\"$dt->form_id\"" : '';
                ?>
            <td><input <?= $form_attr ?> name="<?= $this->name('tarifs') ?>[nom][]" type="text" placeholder="Enfant, Sénior&hellip;" required readonly value="<?= $row === null ? null : $row['nom'] ?>"></td>
            <td><input <?= $form_attr ?> name="<?= $this->name('tarifs') ?>[montant][]" type="number" min="0" placeholder="Prix" required value="<?= $row === null ? null : $row['montant'] ?>"> €</td>
            <?php
            },
            function (DynamicTable $dt) {
                ?>
            <td><input type="text" placeholder="Enfant, Sénior&hellip;" required></td>
            <td><input type="number" min="0" placeholder="Prix" required> €</td>
            <?php
            },
            $this->id('table-tarifs'),
            $this->name('tarifs'),
            $form_id,
            [['nom' => 'Adulte', 'montant' => 10]],
        );
        $this->periodes = new DynamicTable(
            ['Début', 'Fin'],
            function (DynamicTable $dt, ?array $horaire) {
                $form_attr = $dt->form_id ? "form=\"$dt->form_id\"" : '';
                ?>
            <td><input <?= $form_attr ?> name="<?= $this->name('periodes') ?>[debut][]" type="datetime-local" value="<?= $horaire === null ? null : $horaire[0] ?>)"></td>
            <td><input <?= $form_attr ?> name="<?= $this->name('periodes') ?>[fin][]" type="datetime-local" value="<?= $horaire === null ? null : $horaire[1] ?>"></td>
            <?php
            },
            function (DynamicTable $dt) {
                ?>
            <td><input type="datetime-local" placeholder="Début" required></td>
            <td><input type="datetime-local" placeholder="Fin" required></td>
            <?php
            },
            $this->id('table-periodes'),
            $this->name('periodes'),
            $form_id
        );
    }

    /**
     * Récupère l'offre saisie.
     * @param array $get_or_post `$_GET` ou `$get_or_post` (selon la méthode du formulaire)
     * @param ?int $current_id_offre L'ID de l'offre à modifier ou `null` pour une création.
     * @param bool $required Si l'offre est requise. Quand l'offre est manquante, si `false` a été passé, la fonction retourne `null`. Sinon, déclenche une erreur.
     */
    function get(array $get_or_post, ?int $current_id_offre = null, bool $required = true): ?Offre
    {
        $args = [
            $current_id_offre,
            $this->input_adresse->get($_POST, required: $required),
            $this->input_image_principale->get($_POST, required: $required),
            $this->professionnel,
            Abonnement::from_db(getarg($get_or_post, $this->name('libelle_abonnement'), required: false) ?? 'gratuit'),
            getarg($get_or_post, $this->name('titre'), required: $required),
            getarg($get_or_post, $this->name('resume'), required: $required),
            getarg($get_or_post, $this->name('description_detaillee'), required: $required),
            getarg($get_or_post, $this->name('url_site_web'), required: false),
            new MultiRange(array_map(
                fn($row) => new NonEmptyRange(
                    true,
                    FiniteTimestamp::parse($row[0]),
                    FiniteTimestamp::parse($row[1]),
                    false,
                ),
                $this->periodes->get($get_or_post, required: false) ?? [],
            )),
        ];

        if ($args[1] === null)
            return null;

        $offre = match ($this->categorie) {
            Activite::CATEGORIE => new Activite(
                ...$args,
                indication_duree: $this->input_indication_duree->get($get_or_post),
                prestations_incluses: getarg($get_or_post, 'prestations_incluses'),
                age_requis: getarg($get_or_post, 'age_requis', arg_int(1), required: false),
                prestations_non_incluses: getarg($get_or_post, 'prestations_non_incluses', required: false),
            ),
            ParcAttractions::CATEGORIE => new ParcAttractions(
                ...$args,
                image_plan: $this->input_image_plan->get($_POST),
            ),
            Spectacle::CATEGORIE => new Spectacle(
                ...$args,
                indication_duree: $this->input_indication_duree->get($get_or_post),
                capacite_accueil: getarg($get_or_post, 'capacite_accueil', arg_int(0)),
            ),
            Restaurant::CATEGORIE => new Restaurant(
                ...$args,
                carte: getarg($get_or_post, 'carte'),
                richesse: getarg($get_or_post, 'richesse'),
                sert_petit_dejeuner: getarg($get_or_post, 'sert_petit_dejeuner', required: false),
                sert_brunch: getarg($get_or_post, 'sert_brunch', required: false),
                sert_dejeuner: getarg($get_or_post, 'sert_dejeuner', required: false),
                sert_diner: getarg($get_or_post, 'sert_diner', required: false),
                sert_boissons: getarg($get_or_post, 'sert_boissons', required: false),
            ),
            Visite::CATEGORIE => new Visite(
                ...$args,
                indication_duree: $this->input_indication_duree->get($get_or_post),
            ),
        };

        // Horaires
        foreach (getarg($get_or_post, $this->name('horaires'), required: false) ?? [] as $dow => $horaires) {
            $offre->ouverture_hebdomadaire[$dow] = new MultiRange(array_map(
                fn($horaire_row) => new NonEmptyRange(
                    true,
                    Time::parse($horaire_row['debut']),
                    Time::parse($horaire_row['fin']),
                    false,
                ),
                $horaires,
            ));
        }

        // Galerie
        foreach (getarg($get_or_post, $this->name('galerie'), required: false) ?? [] as $image) {
            $offre->galerie->add($image);
        }

        // Tags
        foreach (getarg($get_or_post, $this->name('tags'), required: false) ?? [] as $tag) {
            $offre->tags->add($tag);
        }

        // Tarifs
        foreach ($this->tarifs->get($get_or_post, required: false) ?? [] as $tarif_row) {
            $offre->tarifs->add($tarif_row['nom'], $tarif_row['montant']);
        }

        return $offre;
    }

    /**
     * @inheritDoc
     */
    function put(mixed $current = null): void
    {
        $id_professionnel = Auth\exiger_connecte_pro();
        $est_prive = ProfessionnelPrive::exists($id_professionnel);

        $form_attr = $this->form_id ? "form=\"$this->form_id\"" : '';
        ?>
        <h1>Créer <?= CATEGORIES_OFFRE[$this->categorie] ?></h1>

        <section id="<?= $this->id('type-abonnement') ?>">
            <h2>Abonnement</h2>
            <?php if ($est_prive) { ?>
                <ul id="<?= $this->id('liste-choix-abonnement') ?>">
                    <li>
                        <label><input <?= $form_attr ?>
                            id="<?= $this->id('libelle_abonnement_standard') ?>"
                            name="<?= $this->name('libelle_abonnement') ?>"
                            type="radio"
                            value="standard"
                            <?= $current?->abonnement?->libelle === 'standard' ? 'checked' : '' ?>>
                            Standard</label>
                    </li>
                    <li>
                        <label><input <?= $form_attr ?>
                            id="<?= $this->id('libelle_abonnement_premium') ?>"
                            name="<?= $this->name('libelle_abonnement') ?>"
                            value="premium"
                            type="radio"
                            <?= $current?->abonnement?->libelle === 'premium' ? 'checked' : '' ?>>
                            Premium</label>
                    </li>
                </ul>
                <aside>
                    <img src="/icon/icons8-haute-importance-100.png" alt="Haute importance" width="25" height="25">
                    <p>Attention! Une fois l'option choisi vous ne pourrez plus la modifier.</p>
                </aside>
            <?php } else { ?>
                <p>Comme vous êtres un professionnel public, l'offre crée sera gratuite (pas de facturation)</p>
                <!-- todo: mettre un lien utile ici -->
                <p><a href="/" target="_blank" rel="noopener noreferrer">Plus d'informations&hellip;</>
                </p>
            <?php } ?>
        </section>

        <section id="<?= $this->id('info-generales') ?>">
            <h2>Informations générales</h2>
            <div>
                <label for="<?= $this->id('titre') ?>">Titre*</label>
                <p>
                    <input <?= $form_attr ?>
                        id="<?= $this->id('titre') ?>"
                        name="<?= $this->name('titre') ?>"
                        type="text"
                        required
                        value="<?= $current?->titre ?>">
                </p>
                <label for="<?= $this->id('resume') ?>">Resumé*</label>
                <p>
                    <input <?= $form_attr ?>
                        id="<?= $this->id('resume') ?>"
                        name="<?= $this->name('resume') ?>"
                        type="text"
                        required
                        value="<?= $current?->resume ?>">
                </p>
                <label for="<?= $this->input_adresse->id ?>">Adresse*</label>
                <?php $this->input_adresse->put($current?->adresse) ?>
                <label for="<?= $this->id('site') ?>">Site Web</label>
                <p>
                    <input <?= $form_attr ?>
                        id="<?= $this->id('url_site_web') ?>"
                        name="<?= $this->name('url_site_web') ?>"
                        type="url"
                        value="<?= $current?->url_site_web ?>">
                </p>
            </div>
        </section>

        <section>
            <h2>Photo principale</h2>
            <?php $this->input_image_principale->put($current?->image_principale) ?>
        </section>
        <section id="<?= $this->id('tarifs') ?>">
            <h2>Tarifs</h2>
            <?php if ($est_prive) { ?>
                <?php $this->tarifs->put($current?->tarifs) ?>
            <?php } else { ?>
                <p>En tant que professionnel public, vous ne pouvez pas ajouter de grillle tarifaire à votre offre gratuite.</p>
            <?php } ?>
        </section>

        <section id="<?= $this->id('horaires-hebdomadaires') ?>">
            <h2>Horaires hebdomadaires</h2>
            <div>
                <?php foreach (JOURS_SEMAINE as $dow => $jour) { ?>
                    <article id="<?= $this->id($jour) ?>">
                        <h3><?= ucfirst($jour) ?></h3>
                        <button id="<?= $this->id("button-add-horaire-$dow") ?>" type="button">+</button>
                        <table id="<?= $this->id("table-horaires-$dow") ?>">
                            <thead>
                                <tr>
                                    <th>Début</th>
                                    <th>Fin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($current?->ouverture_hebdomadaire[$dow] ?? [] as $horaire) { ?>
                                    <tr>
                                        <td><input <?= $form_attr ?>
                                            name="<?= $this->name('horaires') ?>[<?= $dow ?>][debut][]"
                                            type="time"
                                            required
                                            value="<?= $horaire->lower ?>"></td>
                                        <td><input <?= $form_attr ?>
                                            name="<?= $this->name('horaires') ?>[<?= $dow ?>][fin][]"
                                            type="time"
                                            required
                                            value="<?= $horaire->upper ?>"></td>
                                        <td><button type="button">-</button></td>
                                    </tr><?php } ?>
                            </tbody>
                        </table>
                        <template id="<?= $this->id("template-horaire-tr-$dow") ?>">
                            <tr>
                                <td><input <?= $form_attr ?>
                                    name="<?= $this->name('horaires') ?>[<?= $dow ?>][debut][]"
                                    type="time"
                                    required></td>
                                <td><input <?= $form_attr ?>
                                    name="<?= $this->name('horaires') ?>[<?= $dow ?>][fin][]"
                                    type="time"
                                    required></td>
                                <td><button type="button">-</button></td>
                            </tr>
                        </template>
                    </article>
                <?php } ?>
            </div>
        </section>

        <section id="<?= $this->id('horaires-ponctuels') ?>">
            <h2>Horaires ponctuels</h2>
            <?php $this->periodes->put() ?>
        </section>

        <section id="<?= $this->id('tags') ?>">
            <h2>Tags</h2>
            <ul id="<?= $this->id('list-tag') ?>">
                <?php
                foreach ($this->categorie === Restaurant::CATEGORIE ? TAGS_RESTAURANT : DEFAULT_TAGS as $tag) {
                    ?>
                    <li><label><input <?= $form_attr ?>
                        id="<?= $this->id("tag-$tag") ?>"
                        name="<?= $this->name('tags') ?>[<?= $tag ?>]"
                        type="checkbox"><?= $tag ?></label></li>
                <?php } ?>
            </ul>
        </section>

        <section>
            <label for="<?= $this->id('description_detaillee') ?>">
                <h2>Description détaillée</h2>
            </label>
            <textarea <?= $form_attr ?>
                    id="<?= $this->id('description_detaillee') ?>"
                    name="<?= $this->name('description_detaillee') ?>"
                    required
                    value="<?= $current?->description_detaillee ?>"></textarea>
        </section>

        <section id="<?= $this->id('image-creation-offre') ?>">
            <label for="<?= $this->id('galerie') ?>">
                <h2>Galerie</h2>
            </label>
            <input <?= $form_attr ?>
                id="<?= $this->id('galerie') ?>"
                name="<?= $this->name('galerie') ?>[]"
                type="file"
                accept="image/*"
                multiple>
            <div id="<?= $this->id('galerie-preview') ?>"></div>
            <!-- todo: modif galerie with legende -->
        </section>

        <section id="<?= $this->id('infos-detaillees') ?>">
            <h2>Informations détaillées</h2>
            <?php
            switch ($this->categorie) {
                case Activite::CATEGORIE:
                    /** @var ?Activite */
                    $activite = $current;
                    ?>
                    <p><label>Âge requis&nbsp;: <input <?= $form_attr ?>
                        id="<?= $this->id('age_requis') ?>"
                        name="<?= $this->name('age_requis') ?>"
                        type="number"
                        min="1"
                        value="<?= $activite?->age_requis ?>"> an</label></p>
                    <p>Prestations incluses*</p>
                    <textarea <?= $form_attr ?>
                        id="<?= $this->id('prestations_incluses') ?>"
                        name="<?= $this->name('prestations_incluses') ?>"
                        required
                        value="<?= $activite?->prestations_incluses ?>"></textarea>
                        <p>Prestations non incluses</p>
                        <textarea <?= $form_attr ?>
                        id="<?= $this->id('prestations_non_incluses') ?>"
                        name="<?= $this->name('prestations_non_incluses') ?>"
                        value="<?= $activite?->prestations_non_incluses ?>"></textarea>
                    <?php
                    $this->put_input_indication_duree($activite?->indication_duree);
                    break;
                case ParcAttractions::CATEGORIE:
                    /** @var ?ParcAttractions */
                    $parc_attractions = $current;
                    ?>
                    ?>
                    <p><label>Âge requis&nbsp;: <input <?= $form_attr ?>
                        id="<?= $this->id('age_requis') ?>"
                        name="<?= $this->name('age_requis') ?>"
                        type="number"
                        min="1"
                        value="<?= $parc_attractions?->age_requis ?>"> an</label></p>
                    <fieldset>
                        <p><label>Plan* &nbsp;: <input <?= $form_attr ?>
                            id="<?= $this->id('image_plan') ?>"
                            name="<?= $this->name('image_plan') ?>"
                            type="file"
                            accept="image/*"
                            required></label></p>
                        <div id="<?= $this->id('image_plan-preview') ?>"></div>
                    </fieldset>
                    <?php
                    break;
                case Restaurant::CATEGORIE:
                    /** @var ?Restaurant */
                    $restaurant = $current;
                    ?>
                    ?>
                    <fieldset>
                        <legend>Niveau de richesse</legend>
                        <p><label><input <?= $form_attr ?>
                            id="<?= $this->id('richesse-1') ?>"
                            name="<?= $this->name('richesse') ?>"
                            type="radio"
                            value="1" <?= $restaurant?->richesse === 1 ? 'checked' : '' ?>>
                            €</label></p>
                        <p><label><input <?= $form_attr ?>
                            id="<?= $this->id('richesse-2') ?>"
                            name="<?= $this->name('richesse') ?>"
                            type="radio"
                            value="2" <?= $restaurant?->richesse === 2 ? 'checked' : '' ?>> 
                            €</label></p>
                        <p><label><input <?= $form_attr ?>
                            id="<?= $this->id('richesse-3') ?>"
                            name="<?= $this->name('richesse') ?>"
                            type="radio"
                            value="3" <?= $restaurant?->richesse === 3 ? 'checked' : '' ?>> €
                            €</label></p>
                    </fieldset>
                    <fieldset>
                        <legend>Repas servis</legend>
                        <p><label><input <?= $form_attr ?>
                            id="<?= $this->id('sert_petit_dejeuner') ?>"
                            type="checkbox"
                            name="<?= $this->name('sert_petit_dejeuner') ?>"
                            <?= $restaurant?->sert_petit_dejeuner ? 'checked' : '' ?>>
                            Petit déjeuner</label></p>
                        <p><label><input <?= $form_attr ?>
                            id="<?= $this->id('sert_brunch') ?>"
                            type="checkbox"
                            name="<?= $this->name('sert_brunch') ?>"
                            <?= $restaurant?->sert_brunch ? 'checked' : '' ?>>
                            Brunch</label></p>
                        <p><label><input <?= $form_attr ?>
                            id="<?= $this->id('sert_dejeuner') ?>"
                            type="checkbox"
                            name="<?= $this->name('sert_dejeuner') ?>"
                            <?= $restaurant?->sert_dejeuner ? 'checked' : '' ?>>
                            Déjeuner</label></p>
                        <p><label><input <?= $form_attr ?>
                            id="<?= $this->id('sert_diner') ?>"
                            type="checkbox"
                            name="<?= $this->name('sert_diner') ?>"
                            <?= $restaurant?->sert_diner ? 'checked' : '' ?>>
                            Dîner</label></p>
                        <p><label><input <?= $form_attr ?>
                            id="<?= $this->id('sert_boissons') ?>"
                            type="checkbox"
                            name="<?= $this->name('sert_boissons') ?>"
                            <?= $restaurant?->sert_boissons ? 'checked' : '' ?>>
                            Boissons</label></p>
                    </fieldset>
                    <p>Carte</p>
                    <textarea <?= $form_attr ?>
                        name="<?= $this->name('carte') ?>"><?= $restaurant?->carte ?></textarea>
                    <?php
                    break;
                case Spectacle::CATEGORIE:
                    /** @var ?Spectacle */
                    $spectacle = $current;
                    ?>
                    <p><label>Capacité d'accueil&nbsp;: <input <?= $form_attr ?>
                        id="<?= $this->id('capacite_accueil') ?>"
                        name="<?= $this->name('capacite_accueil') ?>"
                        type="number"
                        min="0"
                        required
                        value="<?= $spectacle?->capacite_accueil ?>">
                        pers.</label></p>
                    <?php
                    $this->put_input_indication_duree($spectacle?->indication_duree);
                    break;
                case Visite::CATEGORIE:
                    /** @var ?Visite */
                    $visite = $current;
                    $this->put_input_indication_duree($visite?->indication_duree);
                    break;
            }
            ?>
        </section>
        <?php
    }

    function put_input_indication_duree(?Duree $current)
    {
        ?>
        <label>Durée estimée&nbsp;: <?php $this->input_indication_duree->put($current) ?></label>
        <?php
    }
}
