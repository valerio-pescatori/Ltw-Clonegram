<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tag extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }

    public function insert($tag_data)
    {
        $this->db->insert("Tag", $tag_data);
        return $this->db->insert_id();
    }

    public function get($tag_data, $fields="*")
    {
        $this->db->select($fields);
        $this->db->from('Tag');
        $this->db->where($tag_data);
        return $this->db->get()->result_array();
    }

    public function exist($tag_data)
    {
        return count($this->get($tag_data)) > 0;
    }

    public function getTagsFromPost($post_id)
    {
        $this->db->select("Utente.id, Utente.nome_utente, Tag.data, Tag.visto");
        $this->db->from("Tag");
        $this->db->join("Utente", "Tag.utente_taggato = Utente.id");
        $this->db->where("Tag.post", $post_id);
        return $this->db->get()->result_array();
    }

    public function remove($tag_data)
    {
        $this->db->delete("Tag", $tag_data);
    }

}
