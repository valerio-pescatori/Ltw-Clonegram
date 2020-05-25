<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Post extends CI_Model
{

    public function __construct(){
        parent::__construct();
        $this->load->helper("url");
        $this->load->model("user");
        $this->load->model("likes");
        $this->load->model("comment");
        $this->load->model("tag");
    }

    public function insert($post_data)
    {
        $this->db->insert("Post", $post_data);
        return $this->db->insert_id();
    }

    public function exist($post_data)
    {
        $this->db->select('id');
        $this->db->from("Post");
        $this->db->where($post_data);
        return count($this->db->get()->result_array())>0;
    }

    public function remove($post_data)
    {
        $this->db->delete("Post", $post_data);
    }

    /* 

        - PARAMETRI:
            - TIPO_DI_RICHIESTA = { HOME, PROFILE, TAGGED, SINGLE, EXPLORE }
            - ID_POST
            - ID_UTENTE

        - CAMPI OBBLIGATORI:
            - ID_POST
            - MEDIAFILE
            - NUM_LIKES
            - NUM_COMMENTI
        
        - CAMPI OPZIONALI:
            - NOME_UTENTE
            - DATA
            - UTENTE.MEDIAFILE
            - DESCRIZIONE
            - COMMENTI:
                - NOME_UTENTE
                - UTENTE.MEDIAFILE
                - TESTO
                - DATA
            - LIKE:
                - NOME_UTENTE
                - UTENTE.MEDIAFILE
                - NOME 
                - COGNOME
                - STATO_RICHIESTA_FOLLOW
            - TAGGATI:
                - NOME_UTENTE
                - UTENTE.MEDIAFILE
                - NOME 
                - COGNOME
                - STATO_RICHIESTA_FOLLOW
    */
    public function getPosts($params)
    {
        switch ($params['req_type']) {
            case "EXPLORE":
                $this->db->select("
                    (SELECT COUNT(*) FROM Likes l1 WHERE l1.post=Post.id) as likes, 
                    (SELECT COUNT(*) FROM Commento c1 WHERE c1.post=Post.id) as comments, 
                    Post.id as id_post,
                    CONCAT('/post/', Post.id) as url,
                    data, Media.mediafile as mediafile, Media.tipo as tipo", FALSE);

                $this->db->order_by("likes DESC", "comments DESC", "data DESC");
                $this->db->from("Post");
                $this->db->join("Utente", "Utente.id = Post.utente");
                $this->db->join("PostMedia", "Post.id = PostMedia.post");
                $this->db->join("Media", "PostMedia.media = Media.id");
                $this->db->where("Utente.profilo_privato", "0");
                $this->db->where("Post.utente !=", $params['id_utente']);
                $this->db->limit(24);

                $temp = $this->db->get()->result_array();
                return $temp;
            break;
            case "HOME":
                
                if (!isset($params['id_utente'])) return NULL;
                
                $this->db->select("Post.id as id_post, Post.data, Post.descrizione, Utente.id as id_utente, Utente.nome_utente, Media.mediafile, Media.tipo");
                $this->db->from("Post");
                $this->db->join("PostMedia", "Post.id = PostMedia.post");
                $this->db->join("Media", "PostMedia.media = Media.id");
                $this->db->join("Follow", "Follow.utente_seguito = Post.utente");
                $this->db->join("Utente", "Utente.id = Post.utente");
                $this->db->where("Follow.utente_segue", $params['id_utente']);
                $this->db->where("Follow.accettata", 1);
                $this->db->order_by("Post.data", "DESC");

                $temp = $this->db->get()->result_array();
                // aggiungiamo foto profilo, likes e commenti
                foreach ($temp as &$post) {
                    $post["foto_profilo"] = $this->user->getProfilePicture($post["id_utente"]);
                    $post["likes"] = $this->follow->getLinksStatus($this->user_id, $this->likes->getLikesFromPost($post["id_post"])); // count su js
                    $post["comments"] = $this->comment->getCommentsFromPost($post["id_post"], true);
                    $post["tags"] = $this->tag->getTagsFromPost($post["id_post"]);
                    $post["url"] = "/post/".$post["id_post"];
                    $post["data"] = strtotime($post["data"]) * 1000;
                }
                return $temp;
                break;

            case "PROFILE":
                if (!isset($params['id_utente'])) return NULL;
                
                $this->db->select("post as id_post, mediafile, tipo, Post.data, tipo");
                $this->db->from("Post");
                $this->db->join("PostMedia", "Post.id = PostMedia.post");
                $this->db->join("Media", "PostMedia.media = Media.id");
                $this->db->where("Post.utente", $params["id_utente"]);
                $this->db->order_by("Post.data", "DESC");
                $temp = $this->db->get()->result_array();
                foreach ($temp as &$post)
                {
                    $post["likes"] = count($this->likes->getLikesFromPost($post["id_post"]));
                    $post["comments"] = count($this->comment->getCommentsFromPost($post["id_post"]));
                    $post["url"] = "/post/".$post["id_post"];
                }
                return $temp;
                break;
                
            case "TAGGED":
                if (!isset($params['id_utente'])) return NULL;
                $this->db->select("Post.id as id_post, mediafile, tipo");
                $this->db->from("Post");
                $this->db->join("PostMedia", "Post.id = PostMedia.post");
                $this->db->join("Media", "PostMedia.media = Media.id");
                $this->db->join("Tag", "Post.id = Tag.post");
                $this->db->where("Tag.utente_taggato", $params["id_utente"]);
                $this->db->order_by("Post.data", "DESC");
                $temp = $this->db->get()->result_array();
                foreach ($temp as &$post)
                {
                    $post["likes"] = count($this->likes->getLikesFromPost($post["id_post"]));
                    $post["comments"] = count($this->comment->getCommentsFromPost($post["id_post"]));
                    $post["url"] = "/post/".$post["id_post"];
                    $post["id_post"] = $post["id_post"];
                }
                return $temp;
                break;

            case "SINGLE":
                if(!isset($params['id_post'])) return NULL;
                
                $this->db->select("Post.id as 'id_post', Post.data, Post.descrizione, Utente.id as 'id_utente', Utente.nome_utente, Media.mediafile, Media.tipo");
                $this->db->from("Post");
                $this->db->join("PostMedia", "Post.id = PostMedia.post");
                $this->db->join("Media", "PostMedia.media = Media.id");
                $this->db->join("Utente", "Utente.id = Post.utente");
                $this->db->where("Post.id", $params["id_post"]);
                $temp = $this->db->get()->result_array();
                if(empty($temp)) return NULL;
                $temp = $temp[0];
                $temp["foto_profilo"] = $this->user->getProfilePicture($temp["id_utente"]);
                $temp["likes"] = $this->follow->getLinksStatus($this->user_id, $this->likes->getLikesFromPost($temp["id_post"]));
                $temp["comments"] = $this->comment->getCommentsFromPost($temp["id_post"], true);
                $temp["tags"] = $this->tag->getTagsFromPost($temp["id_post"]);
                $temp["data"] = strtotime($temp["data"]) * 1000;
                return $temp;
                break;
        }
        return array();
    }
}
