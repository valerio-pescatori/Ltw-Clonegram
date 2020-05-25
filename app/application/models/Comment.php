<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Comment extends CI_Model{

    public function insert($comment_data) 
    {
        $this->db->insert("Commento", $comment_data);
        return $comment_data;
    }

    public function get($comment_data, $fields){

        $this->db->select($fields);
        $this->db->from("Commento");
        $this->db->where($comment_data);
        return $this->db->get()->result_array();
    }

    public function getCommentsFromPost($post_id, $sorted = false)
    {
        $this->db->select("Utente.id as 'id_utente', Utente.nome_utente, Commento.commento, Commento.data, CONCAT('/user/', Utente.nome_utente) as url_user");
        $this->db->from("Commento");
        $this->db->join("Utente", "Utente.id = Commento.utente");
        $this->db->where("Commento.post", $post_id);
        if($sorted) $this->db->order_by("Commento.data", "ASC");
        return $this->db->get()->result_array();
    }

    public function delete($comment_data)
    {
        $this->db->delete("Commento", $comment_data);
    }
}
