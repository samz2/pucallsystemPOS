<?php

class Reserva_model extends CI_Model
{

	public function getAll($tabla)
	{
		return $this->db->get($tabla)->result();
	}

	public function get($id, $tabla)
	{
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	public function get_by_id($id)
	{
		$this->db->select('c.tipodocumento, c.documento, c.nombre, c.apellido, c.telefono, r.*');
		$this->db->from('reserva r');
		$this->db->join('cliente c', 'c.id = r.paciente');
		$this->db->where('r.id', $id);
		$query = $this->db->get();
		return $query;
	}

	public function save($tabla, $data)
	{
		$this->db->insert($tabla, $data);
		return $this->db->insert_id();
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

	public function check($id, $documento)
	{
		if ($id) {
			$this->db->where('id !=', $id);
		}
		$this->db->where('documento', $documento);
		$query = $this->db->get('cliente');
		return $query->result();
	}
	public function verReservas($finicio, $factual, $empresa, $estado = 1)
	{
		$this->db->where("fecha BETWEEN '" . $finicio . "' AND '" . $factual . "'");
		$this->db->where('estado', $estado);
		$this->db->where('empresa', $empresa);
		// if ($usuario) {
		// 	$this->db->where('usuario', $usuario);
		// }
		$this->db->order_by('id', 'desc');
		return $this->db->get('reserva')->result();
	}
}
