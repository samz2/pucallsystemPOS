<?php

class Cocina_model extends CI_Model {

	public function getAll($tabla) {
		return $this->db->get($tabla)->result();
	}

	public function get($id, $tabla) {
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	public function delete($id, $tabla) {
		$this->db->where('id', $id);
		$this->db->delete($tabla);
	}

  public function save($tabla, $data) {
		$this->db->insert($tabla, $data);
		return $this->db->insert_id();
	}

  public function update($where, $data, $tabla) {
		$this->db->update($tabla, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id, $tabla) {
		$this->db->where('id', $id);
		return $this->db->delete($tabla);
	}

}
