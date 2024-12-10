<?php 

// On inclut la classe Bloc et la connexion à la base de données
include('include/Bloc.php');
include('include/Article.php');
include('include/connexion.php');
include('include/twig.php');

// Initialiser Twig
$twig = init_twig();

// récupération de la variable page sur l'URL
$page = isset($_GET['page']) ? $_GET['page'] : '';

// récupération de la variable action sur l'URL
$action = isset($_GET['action']) ? $_GET['action'] : 'read';

// récupération de l'id s'il existe
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Créer une instance de la classe Bloc pour certaines actions comme 'ajouter' ou 'update'
$bloc = new Bloc();
$article = new Article();

switch ($action) {
    // Affichage de la liste des blocs
    case 'read_blocs':
        $tableau = Bloc::readAll(); // Récupère tous les blocs
        echo $twig->render('template.twig', ['blocs' => $tableau]);
        break;

    case 'modifier_bloc':
        if ($id > 0) {
            $bloc = Bloc::readOne($id); // Récupère le bloc à modifier
            if ($bloc) {
                echo $twig->render('modifier.twig', ['bloc' => $bloc]);
            } else {
                echo 'Bloc introuvable.';
            }
        }
        break;

    case 'update_bloc':
        if ($id > 0) {
            $bloc = Bloc::readOne($id); // Récupère le bloc avec l'ID
            if ($bloc) {
                $article = Article::readOne($bloc->article_id); // Récupère l'article lié à ce bloc
                $bloc->chargePOST(); // Charge les nouvelles données POST
                $bloc->update(); // Met à jour le bloc
                header('Location: index.php?action=details_article&id=' . $article->getId());
                exit();
            }
        }
        break;
        
    case 'ajouter_bloc':
        if (isset($_POST['article_id']) && $_POST['article_id'] > 0) {
            $article = Article::readOne($_POST['article_id']); // Récupère l'article associé
            if ($article) {
                $bloc = new Bloc();
                $bloc->chargePOST(); // Charge les données POST
                $bloc->create(); // Crée le bloc
                header('Location: index.php?action=details_article&id=' . $article->getId());
                exit();
            } else {
                echo 'Article introuvable.';
            }
        }
        break;

    case 'delete_bloc':
        if ($id > 0) {
            $bloc = Bloc::readOne($id); // Récupère le bloc pour s'assurer de l'existence de l'article
            if ($bloc) {
                $article = Article::readOne($bloc->article_id); // Récupère l'article lié à ce bloc
                Bloc::delete($id); // Supprime le bloc
                header('Location: index.php?action=details_article&id=' . $article->getId());
                exit();
            }
        }
        break;
        

    // Affichage de la liste des articles
    case 'index':
        default: // Par défaut, si aucune action n'est spécifiée
            $articles = Article::readAll(); // Récupère la liste des articles
            echo $twig->render('index.twig', [
                'articles' => $articles, // Liste des articles
            ]);
            break;
            
    case 'ajouter_article':
        if ($_POST) {
            $article = new Article();
            $article->chargePOST(); // Charge les données du formulaire
            $article->create(); // Met à jour l'article dans la base de données
            header('Location: index.php?action=details_article&id=' . $article->getId());
            exit();
        }
    break;

    case 'read_articles':
        $article = Article::readAll();
        echo $twig->render('articles.twig', ['articles' => $article]);
        break;

    // Affichage du détail d'un article avec ses blocs
    case 'details_article':
        $article = Article::readOne($id); // Récupérer l'article avec l'ID donné
        $articles = Article::readAll(); // Récupérer la liste des articles
        echo $twig->render('article_details.twig', [
            'article' => $article,
            'articles' => $articles, // Liste des articles pour le menu
        ]);
        break;

        // Affichage du formulaire de modification d'un article
    case 'modifier_article':
        if ($id > 0) {
            $article = Article::readOne($id); // Récupère l'article à modifier
            echo $twig->render('article_modifier.twig', ['article' => $article]);
        } else {
            echo 'ID invalide pour modifier l\'article.';
        }
        break;

    // Mise à jour de l'article
    case 'update_article':
        if ($_POST) {
            $article = new Article();
            $article->chargePOST(); // Charge les données du formulaire
            $article->update(); // Met à jour l'article dans la base de données
            header('Location: index.php?action=details_article&id=' . $article->getId());
            exit();
        }
        break;

        // Supprimer un article
    case 'delete_article':
        if ($id > 0) {
            $article = Article::readOne($id); // Vérifie que l'article existe
            if ($article) {
                Article::delete($id); // Supprime l'article
                header('Location: index.php');                
                exit();
            }
        }
        break;
}
