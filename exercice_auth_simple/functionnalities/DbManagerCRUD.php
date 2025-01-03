<?php

namespace functionnalities;

use Exception;

class DbManagerCRUD implements I_ApiCRUD, I_ApiCRUD_Film
{

    private $db;

    public function __construct()
    {
        $config = parse_ini_file('config' . DIRECTORY_SEPARATOR . 'db.ini', true);
        $dsn = $config['dsn'];
        $username = $config['username'];
        $password = $config['password'];
        $this->db = new \PDO($dsn, $username, $password);
        if (!$this->db) {
            die("Problème de connection à la base de données");
        }
    }

    /*
    public function creeTablePersonnes(): bool {
        $sql = <<<COMMANDE_SQL
            CREATE TABLE IF NOT EXISTS personnes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
                nom VARCHAR(120) NOT NULL,
                prenom VARCHAR(120) NOT NULL,
                email VARCHAR(120) NOT NULL UNIQUE,
                noTel VARCHAR(20) NOT NULL UNIQUE
            );
COMMANDE_SQL;

        try {
            $this->db->exec($sql);
            $ok = true;
        } catch (PDOException $e) {
            $e->getMessage();
            $ok = false;
        }
        return $ok;
    }
*/
    public function ajoutePersonne(Personne $personne): int
    {
        $datas = [
            'lastname' => $personne->rendNom(),
            'firstname' => $personne->rendPrenom(),
            'email' => $personne->rendEmail(),
            'telephone' => $personne->rendNoTel(),
            'password' => $this->creerHash($personne->rendMdp())
        ];
        $sql = "INSERT INTO users (lastname, firstname, email, telephone, password) VALUES "
            . "(:lastname, :firstname, :email, :telephone, :password)";

        $this->db->prepare($sql)->execute($datas);


        return $this->db->lastInsertId();
    }

    public function modifiePersonne(int $id, Personne $personne): bool
    {
        $datas = [
            'id' => $id,
            'lastname' => $personne->rendNom(),
            'firstname' => $personne->rendPrenom(),
            'email' => $personne->rendEmail(),
            'telephone' => $personne->rendNoTel(),
            'password' => $personne->rendMdp(),
        ];
        $sql = "UPDATE users SET lastname=:lastname, firstname=:firstname, email=:email, telephone=:telephone, password=:password WHERE id=:id";
        $this->db->prepare($sql)->execute($datas);
        return true;
    }

    public function rendPersonnesNom(string $nom): array
    {
        $sql = "SELECT * From users WHERE lastname = :lastname;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('lastname', $nom, \PDO::PARAM_STR);
        return $this->rendPersonnes($stmt);
    }

    public function rendPersonneEmail(string $email): array
    {
        $sql = "SELECT * From users WHERE email = :email;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('email', $email, \PDO::PARAM_STR);
        return $this->rendPersonnes($stmt);
    }

    private function rendPersonnes(\PDOStatement $stmt): array
    {
        $stmt->execute();
        $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $tabPersonnes = [];
        if ($donnees) {
            foreach ($donnees as $donneesPersonne) {
                $p = new Personne(
                    $donneesPersonne["firstname"],
                    $donneesPersonne["lastname"],
                    $donneesPersonne["email"],
                    $donneesPersonne["telephone"],
                    $donneesPersonne["password"],
                    $donneesPersonne["id"]
                );
                $tabPersonnes[] = $p;
            }
        }
        return $tabPersonnes;
    }

    public function supprimePersonne(int $id): bool
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function supprimeTablePersonne(): bool
    {
        $sql = "DROP TABLE users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function rendPersonneIdToken(string $token): int
    {
        $sql = "SELECT id From users WHERE account_activation_hash=:token;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('token', $token, \PDO::PARAM_STR);
        $stmt->execute();
        $id = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($id != null) {
            return $id[0]["id"];
        }

        return -1;
    }

    public function rendPersonneTokenId(int $id): string
    {
        $sql = "SELECT account_activation_hash From users WHERE id=:id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        $id = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $id[0]["account_activation_hash"] === null ? "" : $id[0]["account_activation_hash"];
    }

    public function changePersonneToken(int $id, string $token): int
    {
        if (empty($id))
            return -1;

        if (!$token)
            $token = null;
        $sql = "UPDATE users SET account_activation_hash=:token WHERE id=:user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('user_id', $id, \PDO::PARAM_INT);
        $stmt->bindParam('token', $token, \PDO::PARAM_STR);
        try {
            $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
        return $stmt->rowCount();
    }

    private function creerHash(string $string): string
    {
        $hash = "";

        if (!empty($string)) {
            $hash = password_hash($string, PASSWORD_DEFAULT);
        }

        return $hash;
    }
    /**
     * Rend les films qui ont le nom indiqué dans leur titre
     * @param string $title 
     * @return array tableau de tous les films corresopondants
     */
    public function rendFilms(string $title): array{
        $title = "%$title%";
        //$sql = "SELECT * From movies WHERE title = :title;";
        $sql = "SELECT * From movies WHERE title LIKE :title;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('title', $title, \PDO::PARAM_STR);
        $stmt->execute();
        $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $tabFilm = $this->listFilms($donnees);
        return $tabFilm;
    }
    /**
     * Rend les films qui ont le genre indique !non exclusif
     * @param string $genre
     * @return array tableau de tous les films correspondants
     */
    public function rendFilmsParGenre(string $genre): array{
        $genre = "%$genre%";
        //$sql = "SELECT * From movies WHERE title = :title;";
        $sql = "SELECT * From movies WHERE genres LIKE :genre;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('genre', $genre, \PDO::PARAM_STR);
        $stmt->execute();
        $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $tabFilm = $this->listFilms($donnees);
        return $tabFilm;
    }
    
    /**
     * Ajoute une nouvelle notation de filme a la base de donnée
     * INSERT INTO Ratings (movie_id,user_id,rating)
     * VALUES (1,4,2)
     * @param int $idFilm
     * @param int $idUtilisateur
     * @param int $note note de 1 à 5
     * @return bool true si l'ajout est réussé sinon false
     */
    public function ajouteNoteFilm(int $idFilm, int $idUtilisateur, int $note): bool{
        $ajoutOk = false;
        $sql = "INSERT INTO Ratings (movie_id,user_id,rating)
                VALUES (:idFilm,:idUtilisateur,:note);";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('idFilm', $idFilm, \PDO::PARAM_INT);
        $stmt->bindParam('idUtilisateur', $idUtilisateur, \PDO::PARAM_INT);
        $stmt->bindParam('note', $note, \PDO::PARAM_INT);
        try {
            return $stmt->execute();
        } catch (Exception $e) {
            //throw $e;
        }
        return $ajoutOk;
    }
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
    public function modifieNoteFilm(int $idFilm, int $idUtilisateur, int $note): bool{
        return false;
    }
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
    public function metAJourNoteMoyenneFilm(int $idFilm): bool{
        return false;
    }
    /**
     * Supprime la note d'un film pour l'utilisateur donné
     * DELETE FROM Ratings WHERE movie_id=1 and user_id=4;
     * @param int $idFilm
     * @param int $idUttilisateur
     * @return bool true si la suppression est réussie sinon false
     */
    public function supprimeNoteFilm(int $idFilm, int $idUttilisateur): bool{
        return false;
    }
    /**
     * Rend les films les mieux notés (10 par défaut)
     * @param mixed $limit nombre de films a rendre
     * @return array tableau des films
     */
    public function rendFilmsMieuxNotes($limit = 10): array{
        return [];
    }
    //films notés par un utilisateur
    /**
     * Rend les films notés par un utilisateur
     * SELECT * from movies
     * INNER JOIN Ratings on movies.id=Ratings.movie_id
     * where ratings.user_id=1
     * @param int $idUtilisateur
     * @return array tableau des films
     */
    public function rendFilmsNotesParUtilisateur(int $idUtilisateur): array{
        return [];
    }
    /**
     * Rend les films notés par des utilisateurs 
     * SELECT movies.id, imdb_id, title, director, year, genres, runtime, country, language, imdb_score,metacritic_score, Ratings.rating FROM movies 
     * INNER JOIN Ratings on movies.id=Ratings.movie_id
     * INNER JOIN users on Ratings.user_id=users.id
     * @return array tableau des films 
     */
    public function rendFilmsNotes(): array{
        return [];
    }
    private function listFilms($donnees): array
    {
        $tabFilm = [];
        if ($donnees) {
            foreach ($donnees as $donneesFilm) {
                $p = new Film(
                    id: $donneesFilm["id"],
                    imdb_id: $donneesFilm["imdb_id"],
                    title: $donneesFilm["title"],
                    director: $donneesFilm["director"],
                    year: $donneesFilm["year"],
                    genres: $donneesFilm["genres"],
                    runtime: $donneesFilm["runtime"],
                    country: $donneesFilm["country"],
                    language: $donneesFilm["language"],
                    imdb_score: $donneesFilm["imdb_score"],
                    metacritic_score: $donneesFilm["metacritic_score"]
                );
                $tabFilm[] = $p;
            }
        }
        return $tabFilm;
    }
}   // rendre toutes les infos des films

