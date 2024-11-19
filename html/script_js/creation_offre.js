// @ts-nocheck
"use strict";

const type_offre = new URLSearchParams(window.location.search).get('type_offre');
const tr_tarif_id_prefix = 'tr-tarif-';

const tbody_tarifs = document.getElementById('table-tarifs').querySelector('tbody');
/** @type {HTMLTemplateElement} */
const template_tarif_tr = document.getElementById('template-tarif-tr');
/** @type {HTMLButtonElement} */
const button_add_tarif = document.getElementById('button-add-tarif');
/** @type {HTMLInputElement} */
const tarif_nom = document.getElementById('tarif-nom');
/** @type {HTMLInputElement} */
const tarif_montant = document.getElementById('tarif-montant');

add_tarif_tr('Adulte', 10);

tarif_nom.addEventListener('input', update_can_add_tarif);
tarif_montant.addEventListener('input', update_can_add_tarif);

button_add_tarif.addEventListener('click', () => add_tarif_tr(tarif_nom.value, tarif_montant.valueAsNumber));

setup_preview('image_principale');
setup_preview('gallerie');
if (type_offre === 'parc-attractions') {
    setup_preview('image_plan');
}

for (const jour of ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche']) {
    /** @type {HTMLButtonElement} */
    const button_add_horaire = document.getElementById('button-add-horaire-' + jour);
    const tbody_horaires = document.getElementById('table-horaires-' + jour).querySelector('tbody');
    button_add_horaire.addEventListener('click', () => tbody_horaires.appendChild(create_horaire_tr(jour)));
}

/**
 * 
 * @param {string} jour 
 * 
 * @return {HTMLTableRowElement}
 */
function create_horaire_tr(jour) {
    /** @type {HTMLTableRowElement} */
    const tr_horaire = document.getElementById('template-horaire-tr-' + jour).content.children[0].cloneNode(true);
    /** @type {HTMLInputElement} */
    const debut = tr_horaire.children[0];
    /** @type {HTMLInputElement} */
    const fin = tr_horaire.children[1];
    /** @type {HTMLButtonElement} */
    const btn_remove = tr_horaire.children[2];

    debut.addEventListener('input', () => fin.min = debut.value);

    btn_remove.addEventListener('click', () => tr_horaire.remove());

    return tr_horaire;
}

/**
 * @param {string} nom 
 * @param {number} montant
 */
function add_tarif_tr(nom, montant) {
    /** @type {HTMLTableRowElement} */
    const tr_tarif = template_tarif_tr.content.children[0].cloneNode(true); // Clone the template

    tr_tarif.id = tr_tarif_id_prefix + nom;
    tr_tarif.children[0].children[0].value = nom;

    tr_tarif.children[1].children[0].value = montant;

    /** @type {HTMLButtonElement} */
    const remove_button = tr_tarif.children[2];
    remove_button.addEventListener('click', () => {
        tr_tarif.remove();
        update_can_add_tarif();
        update_can_remove_tarif();
    });

    tbody_tarifs.appendChild(tr_tarif);
    update_can_add_tarif();
    update_can_remove_tarif();
}

function update_can_remove_tarif() {
    document.querySelectorAll(`[id^=${tr_tarif_id_prefix}] button`).forEach(btn => {
        console.dir(tbody_tarifs.childElementCount);
        return btn.disabled = tbody_tarifs.childElementCount < 2;
    });
}

function update_can_add_tarif() {
    button_add_tarif.disabled = !tarif_nom.value
        || isNaN(tarif_montant.valueAsNumber)
        || document.getElementById(tr_tarif_id_prefix + tarif_nom.value) !== null;
}

function setup_preview(input_image_id) {
    const e_input_image = document.getElementById(input_image_id);
    const e_preview = document.getElementById(input_image_id + '-preview');
    addEventListener('change', () => preview_image(e_input_image, e_preview));
}

/**
 * @param {HTMLInputElement} e_input_image 
 * @param {HTMLElement} e_preview 
 */
function preview_image(e_input_image, e_preview) {
    e_preview.textContent = '';

    for (const file of e_input_image.files) {
        if (!file.type.match('image.*')) {
            continue;
        }

        const reader = new FileReader();

        reader.addEventListener('load', function (event) {
            const imageUrl = event.target.result;
            const image = new Image();

            image.addEventListener('load', function () {
                e_preview.appendChild(image);
            });

            image.src = imageUrl;
            image.style.width = '200px'; // Indiquez les dimensions souhaitées ici.
            image.style.height = 'auto'; // Vous pouvez également utiliser "px" si vous voulez spécifier une hauteur.
        });

        reader.readAsDataURL(file);
    }
}
