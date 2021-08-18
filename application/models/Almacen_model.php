<?php

class Almacen_model extends CI_Model {

  public function save($tabla, $data) {
		$this->db->insert($tabla, $data);
		return $this->db->insert_id();
	}

  public function get_by_id($id, $tabla) {
		$this->db->from($tabla);
		$this->db->where('id',$id);
		$query = $this->db->get();
		return $query->row();
	}

  public function update($where, $data, $tabla) {
		$this->db->update($tabla, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id, $tabla) {
		$this->db->where('id', $id);
		return $this->db->delete($tabla);
	}

	public function count_all($tabla) {
		$this->db->from($tabla);
		return $this->db->count_all_results();
	}

	public function getAll($tabla) {
		return $this->db->get($tabla)->result();
	}

	public function getAlls($tabla) {
		$this->db->from($tabla);
		return $this->db->count_all_results();
	}

	public function get($id, $tabla) {
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	public function getCaja() {
		return $this->db->where('estado', '1')->where('empresa', $this->empresa)->get('caja')->row();
	}

	public function maximoC() {
    return $this->db->select_max('id')->where('empresa', $this->empresa)->get('caja')->row();
  }

	public function maxcodigo($tabla) {
		return $this->db->select_max('numero')->get($tabla)->row();
	}

}
