<?php
require_once 'util.php';
require_once 'queries.php';
require_once 'redirect.php';
require_once 'component/Page.php';

$args = [
    'id' => getarg($_GET, 'id', arg_int())
];

$page = new Page("detail_compte_membre : {$args['id']}",
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'],
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.js' => 'async']);


$id = $args['id'];
$membre = DB\query_compte_membre($args['id']);
$pro = DB\query_compte_professionnel($args['id']);

if ($membre !== false) {
    // echo '<pre>';
    // print_r($membre);
    // echo '</pre>';
    $pseudo = $membre['pseudo'];
    $email = $membre['email'];
    $mdp = unserialize($membre['mdp_hash']);
    $nom = $membre['nom'];
    $prenom = $membre['prenom'];
    $telephone = $membre['telephone'];
    $id_adresse = $membre['id_adresse'];
    $adresse = DB\query_adresse($id_adresse);
} else if ($pro !== false) {
    // echo '<pre>';
    // print_r($pro);
    // echo '</pre>';
    $denomination = $pro['denomination'];
    $email = $pro['email'];
    $mdp_hash = unserialize($pro['mdp_hash']);
    $nom = $pro['nom'];
    $prenom = $pro['prenom'];
    $telephone = $pro['telephone'];
    $id_adresse = $pro['id_adresse'];
    $adresse = DB\query_adresse($id_adresse);

    if (DB\exists_pro_prive($id)) {
        $siren = DB\query_get_siren($id);
    }
} else {
    html_error("le compte d'ID {$args['id']} n'existe pas");
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations Compte</title>
    <style>
    /* Section principale */
#info_compte {
    max-width: 800px;
    margin: 2rem auto;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

/* Alignement des informations */
#info_compte > div {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ddd;
    padding: 0.5rem 0;
}

/* Style des étiquettes */
#info_compte p {
    margin: 0;
    font-weight: bold;
    color: #555;
}

/* Style des valeurs */
#info_compte span {
    font-weight: normal;
    color: #333;
}

/* Bouton Modifier */
#btn_modifier {
    display: inline-block;
    text-decoration: none;
    background-color: #3498db;
    color: white;
    padding: 0.8rem 1.5rem;
    border-radius: 5px;
    font-size: 1rem;
    margin-top: 1.5rem;
    text-align: center;
}

#btn_modifier:hover {
    background-color: #2980b9;
}

/* ID spécifiques pour chaque champ */
#info_pseudo, #info_denomination, #info_siren, 
#info_nom, #info_prenom, #info_email, 
#info_telephone, #info_adresse {
    margin-top: 0.5rem;
}

#label_pseudo, #label_denomination, #label_siren, 
#label_nom, #label_prenom, #label_email, 
#label_telephone, #label_adresse {
    color: #444;
    font-size: 1rem;
}

#value_pseudo, #value_denomination, #value_siren, 
#value_nom, #value_prenom, #value_email, 
#value_telephone, #value_adresse {
    font-size: 1rem;
    color: #222;
}

    </style>
</head>
<body>
<?php $page->put_head() ?>

    
    <section id="info_compte">
            <!-- PHP dynamique commence ici -->
            <?php if ($membre !== false): ?>
                <div id="info_pseudo">
                    <p id="label_pseudo">Pseudo :</p>
                    <span id="value_pseudo"><?= $pseudo ?></span>
                </div>
            <?php elseif ($pro !== false): ?>
                <div id="info_denomination">
                    <p id="label_denomination">Dénomination :</p>
                    <span id="value_denomination"><?= $denomination ?></span>
                </div>
                <?php if (DB\exists_pro_prive($id)): ?>
                    <div id="info_siren">
                        <p id="label_siren">Siren :</p>
                        <span id="value_siren"><?= $siren ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div id="info_nom">
                <p id="label_nom">Nom :</p>
                <span id="value_nom"><?= $nom ?></span>
            </div>

            <div id="info_prenom">
                <p id="label_prenom">Prénom :</p>
                <span id="value_prenom"><?= $prenom ?></span>
            </div>

            <div id="info_email">
                <p id="label_email">Email :</p>
                <span id="value_email"><?= $email ?></span>
            </div>

            <div id="info_telephone">
                <p id="label_telephone">Numéro de téléphone :</p>
                <span id="value_telephone"><?= $telephone ?></span>
            </div>

            <div id="info_adresse">
                <p id="label_adresse">Adresse :</p>
                <span id="value_adresse"><?= format_adresse($adresse) ?></span>
            </div>

            <a id="btn_modifier" href="modif_compte.php?id=<?= $id ?>">Modifier</a>
        </section>
    </main>
    <?php $page->put_footer() ?>

   
</body>
</html>
