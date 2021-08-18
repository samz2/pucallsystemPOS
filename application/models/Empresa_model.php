<?php

class Empresa_model extends CI_Model
{

	public function save($tabla, $data)
	{
		$this->db->insert($tabla, $data);
		return $this->db->insert_id();
	}

	public function get_by_id($id, $tabla)
	{
		$this->db->from($tabla);
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	function duplicadozona($empresa, $nombre)
	{
		$this->db->where('empresa', $empresa);
		$this->db->where('nombre', $nombre);
		return $this->db->get('zona')->row();
	}

	function duplicadomesa($empresa, $nombre, $zona)
	{
		return $this->db->where('empresa', $empresa)->where('zona', $zona)->where('nombre', $nombre)->get('mesa')->row();
	}

	public function update($where, $data, $tabla)
	{
		$this->db->update($tabla, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id, $tabla)
	{
		$this->db->where('id', $id);
		return $this->db->delete($tabla);
	}

	public function count_all($tabla)
	{
		$this->db->from($tabla);
		return $this->db->count_all_results();
	}

	public function getAll($tabla)
	{
		return $this->db->get($tabla)->result();
	}

	public function getAlls($tabla)
	{
		$this->db->from($tabla);
		return $this->db->count_all_results();
	}

	public function get($id, $tabla)
	{
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	public function check($id, $ruc)
	{
		if ($id) {
			$this->db->where('id !=', $id);
		}
		$this->db->where('ruc', $ruc);
		$query = $this->db->get('empresa');
		return $query->result();
	}

	public function maxcodigo($tabla)
	{
		return $this->db->select_max('numero')->get($tabla)->row();
	}
}
