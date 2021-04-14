<?php

class ProfilController {

    private $_db;

    public function __construct($db) {
        $this->_db = $db;
    }

    public function run() {
        if (empty($_SESSION['authentifie'])) {
            header("Location: index.php?action=login");
            die();
        }

        if ($_SESSION['admin']) {
            $statutColor = "red";
            $statutName = "Administrateur";
        } else {
            $statutColor = "green";
            $statutName = "Utilisateur";
        }

        $tabIdeas = $this->_db->select_idea_where_user_is($_SESSION['id_user']);

        $tabComment = $this->_db->select_comments_where_user_is($_SESSION['id_user']);

        $tabLike = $this->_db->select_idea_user_like($_SESSION['id_user']);

        require_once(VIEWS_PATH . 'profil.php');
    }

}