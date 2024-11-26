<?php

namespace functionnalities;

interface I_ApiCRUD {
    //public function creeTablePersonnes(): bool;
    public function ajoutePersonne(Personne $personne): int;
    public function rendPersonnesNom(string $nom): array;
    public function modifiePersonne(int $id, Personne $personne): bool;
    public function supprimePersonne(int $id) : bool;
    public function supprimeTablePersonne() : bool;
}