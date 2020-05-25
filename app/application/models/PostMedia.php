<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PostMedia extends CI_Model{
    public $post;
    public $media;

    public function get($post_media_data, $fields)
    {
        $this->db->select($fields);
        $this->db->from("PostMedia");
        $this->db->where($post_media_data);
        return $this->db->get()->result_array();
    }

    public function insert($id_post, $id_media) 
    {
        $postMediaDatas = array("post" => $id_post, "media" => $id_media);
        $this->db->insert("PostMedia", $postMediaDatas);
        return $postMediaDatas;
    }
}