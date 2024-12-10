<?php

// déclaration d'une classe Article
class Article
{
    public $h1;
    public $chapo;
    public $sign;
    public $id;
    public $bloc = [];

    function chargePOST() {
        if (isset($_POST['id'])) {
            $this->id = $_POST['id'];
            $this->id = strip_tags($this->id); // Retirer les balises HTML
            $this->id = htmlspecialchars($this->id, ENT_QUOTES, 'UTF-8'); // Sécuriser les caractères spéciaux
        }

        if (isset($_POST['h1'])) {
            $this->h1 = $_POST['h1'];
            $this->h1 = strip_tags($this->h1); // Retirer les balises HTML
            $this->h1 = htmlspecialchars($this->h1, ENT_NOQUOTES, 'UTF-8'); // Sécuriser les caractères spéciaux
        }

        if (isset($_POST['chapo'])) {
            $this->chapo = $_POST['chapo'];
            $this->chapo = strip_tags($this->chapo); // Retirer les balises HTML
            $this->chapo = htmlspecialchars($this->chapo, ENT_NOQUOTES, 'UTF-8'); // Sécuriser les caractères spéciaux
        }

        if (isset($_POST['sign'])) {
            $this->sign = $_POST['sign'];
            $this->sign = strip_tags($this->sign); // Retirer les balises HTML
            $this->sign = htmlspecialchars($this->sign, ENT_NOQUOTES, 'UTF-8'); // Sécuriser les caractères spéciaux
        }

        // Si une valeur a été reçue dans $_POST['id'], la copier et la convertir en entier
        if (isset($_POST['id']) && is_numeric($_POST['id'])) {
            $this->id = intval($_POST['id']);
        }
    }

    // Méthode pour récupérer tous les articles
    public static function readAll() {
        $sql = 'SELECT * FROM article';
        $pdo = connexion();
        $query = $pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'Article');
    }

    // Méthode pour récupérer un article par son ID
    public static function readOne($id) {
        $sql = 'SELECT * FROM article WHERE id = :id';
        $pdo = connexion();
        $query = $pdo->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $article = $query->fetchObject('Article');

        if ($article) {
            // Récupérer les blocs liés à cet article
            $article->bloc = Bloc::readByArticle($id);
        }

        return $article;
    }

    // Méthode pour créer un nouvel article
    public function create() {
        $sql = 'INSERT INTO article (h1, chapo, sign) VALUES (:h1, :chapo, :sign)';
        
        $pdo = connexion();
        
        $query = $pdo->prepare($sql);
        
        $query->bindValue(':h1', $this->h1, PDO::PARAM_STR);
        $query->bindValue(':chapo', $this->chapo, PDO::PARAM_STR);
        $query->bindValue(':sign', $this->sign, PDO::PARAM_STR);
        $query->execute();
        $this->id = $pdo->lastInsertId();
    }

    public function update() {
        $sql = 'UPDATE article SET h1 = :h1, chapo = :chapo, sign = :sign WHERE id = :id';

        $pdo = connexion();
        
        $query = $pdo->prepare($sql);
        
        $query->bindValue(':id', $this->id, PDO::PARAM_INT);
        $query->bindValue(':h1', $this->h1, PDO::PARAM_STR);
        $query->bindValue(':chapo', $this->chapo, PDO::PARAM_STR);
        $query->bindValue(':sign', $this->sign, PDO::PARAM_STR);
        
        return $query->execute();
    }

    static function delete($id) {
        // construction de la requête :nom, :prenom sont les valeurs à insérées
        // connexion à la base de données
        $pdo = connexion();
     
        $query = $pdo->prepare('DELETE FROM bloc WHERE article_id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();

        // Supprime l'article
        $query = $pdo->prepare('DELETE FROM article WHERE id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        return $query->execute();
        }

    public function getId() {
        return $this->id;
    }
}