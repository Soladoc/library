'use strict';

for (const e of document.getElementsByClassName('input-duration')) setup_input_duration(e);
for (const e of document.getElementsByClassName('input-address')) setup_input_address(e);

/**
 * @param {HTMLElement} element
 */
function setup_input_duration(element) {
    // Behaviors
    // - Increase/Decrease next/previous field when reaching max/min value
    // - Disable to preven going below zero
    const inputs = element.getElementsByTagName('input');
    for (let i = 1; i < inputs.length; ++i) {
        inputs.item(i).addEventListener('beforeinput', (e) => check_input(i, e.data));
    }

    /**
     * @param {InputEvent} e 
     * @param {number} i 
     */
    function check_input(i, new_value) {
        const input = inputs.item(i);
        if (new_value == input.max) {
            input.valueAsNumber = 0;

            const parent = inputs.item(i - 1);
            if (i > 1) check_input(i - 1, parent.valueAsNumber + 1);
            parent.stepUp();
        } else if (new_value == -1) {
            const parent = inputs.item(i - 1);
            if (parent.valueAsNumber > 0) {
                input.valueAsNumber = input.max - 1;

                if (i > 1) check_input(i - 1, parent.valueAsNumber - 1);
                parent.stepDown();
            } else {
                input.valueAsNumber = 0;
            }

        }
    }
}


/**
 * @param {HTMLElement} element
 */
function setup_input_address(element) {
    // Behaviors
    // - Update readonly summary accordingly
    const input_summary = element.querySelector('summary input');
    const inputs = Array.from(document.querySelectorAll('label input'));
    inputs.forEach(input => {
        input.addEventListener('input', () => input_summary.value = format_adresse(...inputs.map(i => i.value)));
    });
}
function format_adresse(commune, localite, nom_voie, numero_voie, complement_numero, precision_int, precision_ext) {
    return elvis(precision_ext, ', ')
        + elvis(precision_int, ', ')
        + elvis(numero_voie, ' ')
        + elvis(complement_numero, ' ')
        + elvis(nom_voie, ', ')
        + elvis(localite, ', ')
        + commune;
}

function elvis(value, suffix) {
    return value ? `${value}${suffix}` : '';
}