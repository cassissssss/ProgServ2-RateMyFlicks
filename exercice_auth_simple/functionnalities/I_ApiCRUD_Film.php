<?php

namespace functionnalities;

interface I_ApiCRUD_Film {
    /**
     * Rend les films qui ont le nom indiqué dans leur titre
     * @param string $title 
     * @return array tableau de tous les films corresopondants
     */
    public function rendFilms(string $title): array;
    /**
     * Rend les films qui ont le genre indique !non exclusif
     * @param string $genre
     * @return array tableau de tous les films correspondants
     */
    public function rendFilmsParGenre(string $genre): array;
    
    /**
     * Ajoute une nouvelle notation de filme a la base de donnée
     * INSERT INTO Ratings (movie_id,user_id,rating)
     * VALUES (1,4,2)
     * @param int $idFilm
     * @param int $idUtilisateur
     * @param int $note note de 1 à 5
     * @return bool true si l'ajout est réussé sinon false
     */
    public function ajouteNoteFilm(int $idFilm, int $idUtilisateur, int $note): bool;
    /**
     * Modifie la note d'un film pour l'utilisateur donné
     * UPDATE Ratings
     * SET rating=3
     * WHERE movie_id=1 AND user_id=4;
     * @param int $idFilm
     * @param int $idUtilisateur
     * @param int $note
     * @return bool true si la modification est réussie sinon false
     */
    public function modifieNoteFilm(int $idFilm, int $idUtilisateur, int $note): bool;
    /**
     * Met à jour la note moyenne d'un film
     * SELECT  avg(ratings.rating) from movies
     * INNER JOIN ratings on movies.id=ratings.movie_id
     * where movie_id=1;
     * UPDATE movies
     * set average_rating=4
     * where id=1;
     * @param int $idFilm
     * @return bool true si la mise à jour est réussie sinon false
     */
    public function metAJourNoteMoyenneFilm(int $idFilm): bool;
    /**
     * Supprime la note d'un film pour l'utilisateur donné
     * DELETE FROM Ratings WHERE movie_id=1 and user_id=4;
     * @param int $idFilm
     * @param int $idUttilisateur
     * @return bool true si la suppression est réussie sinon false
     */
    public function supprimeNoteFilm(int $idFilm, int $idUttilisateur): bool;
    /**
     * Rend les films les mieux notés (10 par défaut)
     * @param mixed $limit nombre de films a rendre
     * @return array tableau des films
     */
    public function rendFilmsMieuxNotes($limit = 10): array;
    //films notés par un utilisateur
    /**
     * Rend les films notés par un utilisateur
     * SELECT * from movies
     * INNER JOIN Ratings on movies.id=Ratings.movie_id
     * where ratings.user_id=1
     * @param int $idUtilisateur
     * @return array tableau des films
     */
    public function rendFilmsNotesParUtilisateur(int $idUtilisateur): array;
    /**
     * Rend les films notés par des utilisateurs 
     * SELECT movies.id, imdb_id, title, director, year, genres, runtime, country, language, imdb_score,metacritic_score, Ratings.rating FROM movies 
     * INNER JOIN Ratings on movies.id=Ratings.movie_id
     * INNER JOIN users on Ratings.user_id=users.id
     * @return array tableau des films 
     */
    public function rendFilmsNotes(): array;
}  