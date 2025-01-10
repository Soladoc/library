const validateButton = document.getElementById('validateButton');
const alternateButton = document.getElementById('alternateButton');

alternateButton.addEventListener('click', () => validateButton.disabled = false);

validateButton.addEventListener('click', function (e) {
    if (this.disabled) {
        e.preventDefault();
    }
});