<?php

class login_model extends CI_Model {

	public function getAll($tabla) {
		return $this->db->get($tabla)->result();
	}

	public function getEmpresa() {
		return $this->db->get('empresa')->first_row();
	}

	public function get($id, $tabla) {
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	public function actualizar($tabla, $where, $data) {
		$this->db->update($tabla, $data, $where);
		return $this->db->affected_rows();
	}

	public function getUsuario($usuario, $password) {
		return $this->db->where('usuario', $usuario)->where('password', $password)->get('usuario')->row();
	}

		public function getUsuarioPattern($id)
	{
		return $this->db->where('id', $id)->get('usuario')->row();
	}

	public function getAllActivos($tabla)
	{
		return $this->db->where('estado', '0')->get($tabla)->result();
	}

}
