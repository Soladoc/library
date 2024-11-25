<?php

/**
 * @template TModel Le type de modèle entrée.
 */
abstract class Input
{
    readonly string $form_id;
    readonly string $id;
    readonly string $name;

    /**
     * Summary of __construct
     * @param string $form_id l'ID du formulaire auquel appartient le contrôle. Pas nécéssaire de le spécifier si l'élément est déjà dans un `<form>`.
     * @param string $id L'ID de l'élément à ajouter. Optionnel, ne pas spécifier pour pas d'ID.
     * @param string $name L'attribut "name" à utiliser. En PHP, ce sera un tableau avec les différents champs du composants.
     */
    function __construct(string $id, string $name, string $form_id)
    {
        $this->form_id = $form_id;
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Récupère l'entrée.
     * @param array $get_or_post `$_GET` ou `$_POST`.
     * @return ?TModel
     */
    abstract function getarg(array $get_or_post, bool $required = true);

    /**
     * Affiche l'HTML du composant.
     * @param ?TModel $current L'entité à mettre a jour ou `null` pour une création.
     * @return void
     */
    abstract function put($current = null): void;
}