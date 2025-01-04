<?php

namespace functionnalities;

use \Exception;

/**
 * Permet de simuler une personne ayant :
 *  - un id (facultatif)
 *  - un nom
 *  - un prénom
 *  - un email
 *  - un numéro de téléphone
 */
class Film {

    private $id;
    private $imdb_id;
    private $title;
    private $director;
    private $year;
    private $genres;
    private $runtime;
    private $country;
    private $language;
    private $imdb_score;
    private $metacritic_score;

    /**
     * Construit une nouvelle personne avec les paramètres spécifiés
     * @param int $id Prénom
     * @throws Exception Lance une expection si un des paramètres n'est pas spécifié
     */
    public function __construct(int $id, string $imdb_id, string $title, string $director, string $year, string $genres, string $runtime, string $country,
    string $language, string $imdb_score, string $metacritic_score) {
        if (empty($id)) {
            throw new Exception('Il faut un id');
        }
        if (empty($imdb_id)) {
            throw new Exception('Il faut un id imdb');
        }
        if(empty($title)){
            throw new Exception('Il faut un titre');
        }
        if(empty($director)){
            throw new Exception('Il faut un directeur');
        }
        if (empty($year)) {
            throw new Exception('Il faut une année de sortie');
        }
        if(empty($genres)){
            throw new Exception('Il faut une genre');
        }
        if(empty($runtime)){
            throw new Exception('Il faut une durée');
        }
        if(empty($country)){
            throw new Exception('Il faut un pays');
        }
        if(empty($language)){
            throw new Exception('Il faut une langue');
        }
        if(empty($imdb_score)){
            throw new Exception('Il faut un score imdb');
        }
        if(empty($metacritic_score) || !$metacritic_score > 0){
            $metacritic_score = 0;
        }

        $this->id = $id;
        $this->imdb_id = $imdb_id;
        $this->title = $title;
        $this->director = $director;
        $this->year = $year;
        $this->genres = $genres;
        $this->runtime = $runtime;
        $this->country = $country;
        $this->language = $language;
        $this->imdb_score = $imdb_score;
        $this->metacritic_score = $metacritic_score;
    }


    /**
     * Rend l'id de la personne
     * @return int L'identifiant
     */
    public function rendId(): int {
        return $this->id;
    }

    public function rendImdbId(): string {
        return $this->imdb_id;
    }
    public function rendTitle(): string {
        return $this->title;
    }
    public function rendDirector(): string {
        return $this->director;
    }
    public function rendYear(): string {
        return $this->year;
    }
    public function rendGenres(): string {
        return $this->genres;
    }
    public function rendRuntime(): string {
        return $this->runtime;
    }
    public function rendCountry(): string {
        return $this->country;
    }
    public function rendLanguage(): string {
        return $this->language;
    }
    public function rendImdbScore(): string {
        return $this->imdb_score;
    }
    public function rendMetacriticScore(): string {
        return $this->metacritic_score;
    }

    /**
     * Rend une chaine de caractère contenant tous les attributs
     * d'un film
     * @return string Les attributs du film
     */
    public function __toString(): string {
        return $this->id . " " . 
                $this->imdb_id . " " .
                $this->title . " " .
                $this->director . " " .
                $this->year . " " .
                $this->genres . " " .
                $this->country . " " .
                $this->language . " " .
                $this->runtime . "min. " .
                $this->imdb_score . " " .
                $this->metacritic_score . " " .
                "<br>";
    }
}