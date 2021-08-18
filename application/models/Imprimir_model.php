<?php

class Imprimir_model extends CI_Model {

	public function getAll($tabla) {
		return $this->db->get($tabla)->result();
	}

	public function get($id, $tabla) {
		return $this->db->where('id', $id)->get($tabla)->row();
	}

}
