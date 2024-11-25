<?php
// todo: chage that to use the non-slugged versions only
const DEFAULT_TAGS = [
    'atelier',
    'cinéma',
    'cirque',
    'culturel',
    'famille',
    'histoire',
    'humour',
    'musée',
    'musique',
    'nature',
    'patrimoine',
    'son et lumière',
    'urbain',
];

const TAGS_RESTAURANT = [
    'asiatique',
    'crêperie',
    'française',
    'fruits de mer',
    'gastronomique',
    'indienne',
    'restauration rapide',
];

const CATEGORIES_OFFRE = [
    'activite' => 'une activité',
    'parc-attractions' => "un parc d'attractions",
    'restaurant' => 'un restaurant',
    'spectacle' => 'un spectacle',
    'visite' => 'une visite',
    '' => 'une offre',
];

// L'ordre des valeurs est important (0 => dimanche -> samedi => 6)
const JOURS_SEMAINE = [
    'dimanche',
    'lundi',
    'mardi',
    'mercredi',
    'jeudi',
    'vendredi',
    'samedi',
];

const CONTEXTES_VISITE = [
    'affaires',
    'couple',
    'solo',
    'famille',
    'amis',
];

const DOCUMENT_ROOT = "/var/www/html";