"use strict";

/** @type {HTMLTableSectionElement} */
const tbody_tarifs = document.getElementById('tbody-tarifs');

/** @type {HTMLTemplateElement} */
const template_tarif_row = document.getElementById('template-tarif-row');

/** @type {HTMLButtonElement} */
const button_add_tarif = document.getElementById('button-add-tarif');

/** @type {HTMLInputElement} */
const nom_tarif = document.getElementById('nom-tarif');

/** @type {HTMLInputElement} */
const montant_tarif = document.getElementById('montant-tarif');

const tr_tarif_id_prefix = 'tr-tarif-';

add_tarif_row('Adulte', 10);

nom_tarif.addEventListener('input', update_can_add_tarif);
montant_tarif.addEventListener('input', update_can_add_tarif);

button_add_tarif.addEventListener('click', () => add_tarif_row(nom_tarif.value, montant_tarif.valueAsNumber));

/**
 * @param {string} nom 
 * @param {number} montant
 */
function add_tarif_row(nom, montant) {
    /** @type {HTMLTableRowElement} */
    const tarif_row = template_tarif_row.content.children[0].cloneNode(true); // Clone the template

    tarif_row.id = tr_tarif_id_prefix + nom;
    tarif_row.children[0].innerText = nom;

    tarif_row.children[1].children[0].value = montant;

    /** @type {HTMLButtonElement} */
    const remove_button = tarif_row.children[2];
    remove_button.addEventListener('click', () => {
        tarif_row.remove();
        update_can_add_tarif();
        update_can_remove_tarif();
    });

    tbody_tarifs.appendChild(tarif_row);
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
    button_add_tarif.disabled = !nom_tarif.value
        || isNaN(montant_tarif.valueAsNumber)
        || document.getElementById(tr_tarif_id_prefix + nom_tarif.value) !== null;
}
