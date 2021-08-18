<?php

class Productocategoria_model extends CI_Model
{

	public function save($tabla, $data)
	{
		$this->db->insert($tabla, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data, $tabla)
	{
		$this->db->update($tabla, $data, $where);
		//return $this->db->_update($tabla, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id, $tabla)
	{
		$this->db->where('id', $id);
		return $this->db->delete($tabla);
	}

	public function desactivar_by_id($id, $tabla)
	{
		$data['estado'] = '1';
		return $this->db->where('id', $id)->update($tabla, $data);
	}

	public function activar_by_id($id, $tabla)
	{
		$data['estado'] = '0';
		return $this->db->where('id', $id)->update($tabla, $data);
	}

	public function count_all($tabla)
	{
		$this->db->from($tabla);
		return $this->db->count_all_results();
	}

	public function codigos()
	{
		return $this->db->select_max('numero')->where('empresa', $this->empresa)->get('productocategoria')->row();
	}

	public function getAll($tabla)
	{
		return $this->db->get($tabla)->result();
	}

	public function ultimo($tabla)
	{
		return $this->db->get($tabla)->last_row();
	}

	public function get($id, $tabla)
	{
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	public function crear($tabla, $data)
	{
		$this->db->insert($tabla, $data);
		return $this->db->insert_id();
	}

	public function actualizar($tabla, $where, $data)
	{
		$this->db->update($tabla, $data, $where);
		return $this->db->affected_rows();
	}

	public function check($id, $nombre)
	{
		if ($id) {
			$this->db->where('id <>', $id);
		}
		$this->db->where('nombre', $nombre);
		$this->db->where('empresa', $this->empresa);
		$query = $this->db->get('productocategoria');
		return $query->result();
	}
}
