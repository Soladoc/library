<?php

/**
 * Un composant d'entrée d'un modèle.
 * @template T Le modèle entré
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
     * Affiche l'HTML du composant
     * @param ?T $current La valeur à modifier, `null` pour une création.
     * @return void
     */
    abstract function put(mixed $current = null): void;

    /**
     * Gets a field "name" attribute.
     * @param string $name The inner name.
     * @return string
     */
    protected function name(string $name): string {
        return $this->name ? "$this->name[$name]" : $name;
    }

    /**
     * Gets a field "id" attribute.
     * @param string $id The inner id.
     * @return string
     */
    protected function id(string $id): string {
        return $this->id ? "{$this->id}_$id" : $id;
    }
}