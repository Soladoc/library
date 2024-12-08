import { scroller } from './util.js'

const message = document.getElementById('review-form').getElementsByClassName('message').item(0);
if (message) {
    scroller(message);
}
