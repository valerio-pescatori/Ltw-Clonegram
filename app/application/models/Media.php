<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Media extends CI_Model{
    public $mediafile;
    public $tipo;

    public function insert($media_data) 
    {
        $this->db->insert("Media", $media_data);
        return $this->db->insert_id();
    }

    public function get($media_data, $fields, $id_post = NULL)
    {
        $this->db->select($fields);
        $this->db->from("Media");

        if(isset($id_post)) 
        {
            $this->db->join("PostMedia", "PostMedia.media = Media.id");
            $media_data['post'] = $id_post;
        }
        
        $this->db->where($media_data);
        return $this->db->get()->result_array();
    }

    // public function getMediasFromPost($media_data, $fields, $id_post)
    // {
    //     $this->db->select($fields);
    //     $this->db->from("Media");
    //     $this->db->join("PostMedia", "PostMedia.post = ".$id_post);
    //     $this->db->where($media_data);
    //     return $this->db->get()->result_array();
    // }

    public function remove($media_data)
    {
        $this->db->delete("Media", $media_data);
    }
}

