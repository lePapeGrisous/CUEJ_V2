<?php

// déclaration d'une classe Bloc
class Bloc
{
    public $type;
    public $texte;
    public $style;
    public $src;
    public $alt;
    public $article_id;
    public $id;

    // Charger les données envoyées par POST et les filtrer
    function chargePOST() {
        if (isset($_POST['id'])) {
            $this->id = $_POST['id'];
            $this->id = strip_tags($this->id); // Retirer les balises HTML
            $this->id = htmlspecialchars($this->id, ENT_QUOTES, 'UTF-8'); // Sécuriser les caractères spéciaux
        }

        // Si une valeur a été reçue dans $_POST['type'], la copier et la filtrer
        if (isset($_POST['type'])) {
            $this->type = $_POST['type'];
            $this->type = strip_tags($this->type); // Retirer les balises HTML
            $this->type = htmlspecialchars($this->type, ENT_QUOTES, 'UTF-8'); // Sécuriser les caractères spéciaux
        }

        // Si une valeur a été reçue dans $_POST['texte'], la copier et la filtrer
        if (isset($_POST['texte'])) {
            $this->texte = $_POST['texte'];
            $this->texte = strip_tags($this->texte); // Retirer les balises HTML
            $this->texte = htmlspecialchars($this->texte, ENT_NOQUOTES, 'UTF-8'); // Sécuriser les caractères spéciaux
        }

        // Si une valeur a été reçue dans $_POST['style'], la copier et la filtrer
        if (isset($_POST['style'])) {
            $this->style = $_POST['style'];
            $this->style = strip_tags($this->style); // Retirer les balises HTML
            $this->style = htmlspecialchars($this->style, ENT_QUOTES, 'UTF-8'); // Sécuriser les caractères spéciaux
        }

        if (isset($_POST['article_id'])) {
            $this->article_id = $_POST['article_id'];
            $this->article_id = strip_tags($this->article_id); // Retirer les balises HTML
            $this->article_id = htmlspecialchars($this->article_id, ENT_QUOTES, 'UTF-8'); // Sécuriser les caractères spéciaux
        }        

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Vérification du type de fichier
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                echo "Type de fichier non autorisé.";
                exit(); // Arrêter le script si le type n'est pas autorisé
            }

            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    
            // Déplace le fichier uploadé dans le dossier "uploads"
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $this->src = $uploadFile; // Enregistre le chemin de l'image dans l'objet
            } else {
                echo "Erreur lors de l'upload de l'image.";
                exit();
            }
        } else {
            if (isset($_POST['src'])) {
                $this->src = $_POST['src'];
                $this->src = strip_tags($this->src); // Retirer les balises HTML
                $this->src = htmlspecialchars($this->src, ENT_QUOTES, 'UTF-8'); // Sécuriser les caractères spéciaux
            }
        }

        // Si une valeur a été reçue dans $_POST['alt'], la copier et la filtrer
        if (isset($_POST['alt'])) {
            $this->alt = $_POST['alt'];
            $this->alt = strip_tags($this->alt); // Retirer les balises HTML
            $this->alt = htmlspecialchars($this->alt, ENT_QUOTES, 'UTF-8'); // Sécuriser les caractères spéciaux
        }

        if (isset($_POST['article_id']) && is_numeric($_POST['article_id'])) {
            $this->article_id = intval($_POST['article_id']);
        }

        // Si une valeur a été reçue dans $_POST['id'], la copier et la convertir en entier
        if (isset($_POST['id']) && is_numeric($_POST['id'])) {
            $this->id = intval($_POST['id']);
        }
    }

    // Afficher un bloc (en fonction de son type)
    function afficher() {
        if ($this->type == 'h1') {
            echo '<h1>' . $this->texte . '</h1>';
        } elseif ($this->type == 'paragraphe') {
            echo '<p>' . $this->texte . '</p>';
        } elseif ($this->type == 'image') {
            echo '<img src="' . $this->src . '" alt="' . $this->alt . '" />';
        }
    }
    
    function create() {
        // construction de la requête :nom, :prenom sont les valeurs à insérées
        $sql = 'INSERT INTO bloc (type, texte, style, src, alt, article_id) VALUES (:type, :texte, :style, :src, :alt, :article_id);';
     
        // connexion à la base de données
        $pdo = connexion();
     
        // préparation de la requête
        $query = $pdo->prepare($sql);
     
        // on donne une valeur aux paramètres à partir des attributs de l'objet courant
        $query->bindValue(':type', $this->type, PDO::PARAM_STR);
        $query->bindValue(':texte', $this->texte, PDO::PARAM_STR);
        $query->bindValue(':style', $this->style, PDO::PARAM_STR);
        $query->bindValue(':src', $this->src, PDO::PARAM_STR);
        $query->bindValue(':alt', $this->alt, PDO::PARAM_STR);
        $query->bindValue(':article_id', $this->article_id, PDO::PARAM_INT);
     
        // exécution de la requête
        $query->execute();
     
        // on récupère la clé de l'auteur inséré
        $this->id = $pdo->lastInsertId();
    }

    static function readByArticle($article_id) {
        $sql = 'SELECT * FROM bloc WHERE article_id = :article_id';
        $pdo = connexion();
        $query = $pdo->prepare($sql);
        $query->bindValue(':article_id', $article_id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'Bloc');
    }

    static function readAll() {
        // définition de la requête SQL
        $sql= 'SELECT * FROM bloc';
     
        // connexion
        $pdo = connexion();
     
        // préparation de la requête
        $query = $pdo->prepare($sql);
     
        // exécution de la requête
        $query->execute();
     
        // récupération de toutes les lignes sous forme d'objets
        $tableau = $query->fetchAll(PDO::FETCH_CLASS,'Bloc');
     
        // retourne le tableau d'objets
        return $tableau;
    }

    public static function readOne($id) {
        $sql = 'SELECT * FROM bloc WHERE id = :id';

        $pdo = connexion();
        
        $query = $pdo->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
    
        $data = $query->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $bloc = new self();
            $bloc->id = $data['id'];
            $bloc->type = $data['type'];
            $bloc->texte = $data['texte'];
            $bloc->src = $data['src'];
            $bloc->alt = $data['alt'];
            $bloc->style = $data['style'];
            $bloc->article_id = $data['article_id'];
            return $bloc;
        }
        return null; // Retourne null si aucun bloc n'est trouvé
    }

    function update() {
        // construction de la requête :nom, :prenom sont les valeurs à insérées
        $sql = 'UPDATE bloc SET type = :type , texte = :texte , style = :style , src = :src , alt = :alt WHERE id = :id;';
     
        // connexion à la base de données
        $pdo = connexion();
     
        // préparation de la requête
        $query = $pdo->prepare($sql);
     
        // on donne une valeur aux paramètres à partir des attributs de l'objet courant
        $query->bindValue(':id', $this->id, PDO::PARAM_INT);
        $query->bindValue(':type', $this->type, PDO::PARAM_STR);
        $query->bindValue(':texte', $this->texte, PDO::PARAM_STR);
        $query->bindValue(':style', $this->style, PDO::PARAM_STR);
        $query->bindValue(':src', $this->src, PDO::PARAM_STR);
        $query->bindValue(':alt', $this->alt, PDO::PARAM_STR);
     
        // exécution de la requête
        $query->execute();
    }

    static function delete($id) {
        // construction de la requête :nom, :prenom sont les valeurs à insérées
        $sql = 'DELETE FROM bloc WHERE id = :id;';
     
        // connexion à la base de données
        $pdo = connexion();
     
        // préparation de la requête
        $query = $pdo->prepare($sql);
     
        // on lie le paramètre :id à la variable $id reçue
        $query->bindValue(':id', $id, PDO::PARAM_INT);
     
        // exécution de la requête
        $query->execute();
      }

      public function getId() {
        return $this->id;
    }
}

