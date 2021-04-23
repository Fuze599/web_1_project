<?php
class IdeaController{
    private $_db;

    public function __construct($db) {
        $this->_db = $db;
    }

    public function run() {

        # Security
        if (empty($_SESSION['authentifie'])) {
            header("Location: index.php?action=login");
            die();
        }

        # Setting up variables
        $id_idea=$_GET['id_idea'];
        $notification="";

        # Display of the selected idea / display of the idea put in the link
        if($this->_db->idea_exist($id_idea)){
            $idea = $this->_db->select_idea($id_idea);;
            $user=$this->_db->getUsername($idea->id_user());
            $like=$this->_db->countLikes($idea->id_idea());
        }else{
            header("Location: index.php?action=home");
            die();
        }

        # Likes system
        if (!empty($_POST['form_like'])) {
            $selectIdea=$this->_db->select_idea($_POST['like_id_idea']);
            if($selectIdea->status()!='C'){
                if ($this->_db->vote_exist($_SESSION['id_user'],$_POST['like_id_idea'])) {
                    #cant vote again
                } else {
                    if($_SESSION['id_user']==$selectIdea->id_user()){
                        #cant vote for self
                    }else{
                        $notification_like = "Votre like été pris en compte.";
                        $this->_db->insert_vote($_SESSION['id_user'], $_POST['like_id_idea']);
                    }
                }
            }
        }

        # Comments system
        if(!empty($_POST['form_answer'])){
            $condition=true;
            $text = $_POST['form_comment'];
            if(strlen($text) > 150) {
                $notification = "Vous avez dépassé les 150 caractères autorisés !";

            }else if(!empty($_POST['form_comment'])){
                $this->_db->insert_comment($_GET['id_idea'],$_SESSION['id_user'],$_POST['form_comment']);
            }else{
                $notification="Pour répondre au poste il faut introduire une réponse!";
            }
        }

        #Deletes comments system
        if(!empty($_POST['form_delete_comment'])){
            if(!$this->_db->is_comment_disable($_POST['comment_idea'])){
                $this->_db->disable_comment($_POST['comment_idea']);
            }else{
                $notification="commentaire deja supprimé";
            }
        }

        # Displays the added comments
        $comments = $this->_db->select_comments_idea($id_idea);

        $tabUsers=array();
        foreach($comments as $i => $comment){
            $tabUsers[$i]=$this->_db->getUsername($comments[$i]->id_user());
        }

        require_once(VIEWS_PATH . 'idea.php');
    }

}