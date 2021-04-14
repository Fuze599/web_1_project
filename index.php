<?php
date_default_timezone_set('Europe/Brussels');
define('VIEWS_PATH', 'views/');
define('CONTROLLERS_PATH', 'controllers/');
define('DATEDUJOUR', date('j/m/Y'));
define('NOW', date('Y-m-d H:i:s'));

# Active le mécanisme des sessions
session_start();

# Automatisation de l'inclusion des classes de la couche modèle
function chargerClasse($classe)
{
    require_once('models/' . $classe . '.class.php');
}
spl_autoload_register('chargerClasse');

# Connexion à la db;
$db=Db::getInstance();


# S'il n'y a pas de variable GET 'action' dans l'URL, elle est créée ici à la valeur 'accueil'
if (empty($_GET['action'])) {
    $_GET['action'] = 'login';
}
$header_footer=true;

if(empty($_SESSION['authentifie'])){
    $header_footer=false;
} else {
    $_SESSION['admin'] = $db->is_admin($_SESSION['id_user']);
    $_SESSION['disabled']= $db->is_disabled($_SESSION['id_user']);
    if ($_SESSION['disabled'] == 1) {
        $_SESSION = array();
        header("Location: index.php?action=login");
        die();
    }
}

include(VIEWS_PATH. 'header.php');


switch ($_GET['action']) {
    /*
    case 'admin':
        require_once(CONTROLLERS_PATH.'AdminController.php');
        $controller = new AdminController($db);
        break;
    */
    case 'accueil':
        require_once(CONTROLLERS_PATH.'AccueilController.php');
        $controller = new AccueilController($db);
        break;
    case 'logout':
        require_once(CONTROLLERS_PATH.'LogoutController.php');
        $controller = new LogoutController();
        break;
    case 'newIdea':
        require_once(CONTROLLERS_PATH . 'NewIdeaController.php');
        $controller = new NewIdeaController($db);
        break;
    case 'idea':
        require_once(CONTROLLERS_PATH . 'IdeaController.php');
        $controller = new IdeaController($db);
        break;
    case 'gestion_user':
        require_once(CONTROLLERS_PATH.'GestionUtilisateurController.php');
        $controller = new GestionUtilisateurController($db);
        break;
    case 'gestion_idea':
        require_once(CONTROLLERS_PATH.'GestionIdeesController.php');
        $controller = new GestionIdeesController($db);
        break;
    case 'profil':
        require_once(CONTROLLERS_PATH.'ProfilController.php');
        $controller = new ProfilController($db);
        break;
    default:
        require_once(CONTROLLERS_PATH . 'LoginController.php');
        $controller = new LoginController($db);
        break;
}

# Exécution du contrôleur défini dans le switch précédent
$controller->run();