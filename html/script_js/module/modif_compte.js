
const input_api_key = document.getElementById('api_key');
const button_regenerate_api_key = document.getElementById('button-regenerate-uuid');
const button_delete_api_key = document.getElementById('button-delete-uuid');

button_regenerate_api_key.addEventListener('click', () => input_api_key.value = crypto.randomUUID());
button_delete_api_key.addEventListener('click', () => input_api_key.value = '');