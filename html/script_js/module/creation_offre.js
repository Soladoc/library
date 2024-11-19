import { DynamicTable } from './DynamicTable.js';

const type_offre = new URLSearchParams(window.location.search).get('type_offre');

// Grille tarifaire
{
    const tarifs = new DynamicTable(
        document.getElementById('table-tarifs'),
        document.getElementById('template-tarif-tr'),
        function (values) {
            return !this.has_row(row => row[0] === values[0]);
        },
        (values, tr) => {
            tr.children[0].children[0].value = values[0];
            tr.children[1].children[0].value = values[1];
        }
    );

    tarifs.add_row(['Adulte', 10]);
}


// Horaires
{
    for (const jour of ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche']) {
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
        /**@type {HTMLTableRowElement}*/ const tr_horaire = document.getElementById('template-horaire-tr-' + jour).content.children[0].cloneNode(true);
        /**@type {HTMLInputElement}*/ const debut = tr_horaire.children[0];
        /**@type {HTMLInputElement}*/ const fin = tr_horaire.children[1];
        /**@type {HTMLButtonElement}*/ const btn_remove = tr_horaire.children[2];

        debut.addEventListener('input', () => fin.min = debut.value);

        btn_remove.addEventListener('click', () => tr_horaire.remove());

        return tr_horaire;
    }
}

// Image previews
{
    setup_preview('image_principale');
    setup_preview('gallerie');
    if (type_offre === 'parc-attractions') {
        setup_preview('image_plan');
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
}