<?php

namespace functionnalities;

use Exception;

class DbManagerCRUD implements I_ApiCRUD
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

}
