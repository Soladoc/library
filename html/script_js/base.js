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

    for (let i = 0; i < inputs.length; ++i) {
        inputs.item(i).addEventListener('input', () => check_input(i));
    }

    /**
     * @param {number} i
     */
    function check_input(i) {
        const input = inputs.item(i);
        const input_prev = inputs.item(i - 1); // larger unit
        const input_next = inputs.item(i + 1); // smaller unit
        if (input_prev !== null) {
            if (input.valueAsNumber === -1) {
                //debugger;
                decrement_input(i);
            } else {
                const excess = Math.trunc(input.valueAsNumber / input.max);
                if (excess > 0) {
                    input.valueAsNumber %= input.max;
                    input_prev.valueAsNumber += excess;
                    check_input(i - 1);
                }
            }
        }
        if (input_next !== null) {
            // for a min to be 0 means that every larger input is 0
            // for a min to be -1 means that some larger input is > 0
            input_next.min = input.valueAsNumber > 0 || input.min == -1 ? -1 : 0;
            check_input(i + 1);
        }
    }

    /**
     * 
     * @param {number} i &gt; 0
     */
    function decrement_input(i) {
        const input = inputs.item(i);
        const input_prev = inputs.item(i - 1); // larger unit

        if (input_prev.min) {
            input.valueAsNumber = Number(input.max) - 1;
        }
        if (input_prev.valueAsNumber > 0) {
            input_prev.stepDown();
        } else if (i > 0) {
            decrement_input(i - 1);
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