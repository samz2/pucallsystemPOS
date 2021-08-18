<?php

class Usuario_model extends CI_Model {

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

	public function desactivar_by_id($id, $tabla) {
		$data['estado'] = '1';
		return $this->db->where('id', $id)->update($tabla, $data);
	}

	public function activar_by_id($id, $tabla) {
		$data['estado'] = '0';
		return $this->db->where('id', $id)->update($tabla, $data);
	}

	public function perfil($tabla){
		if($this->input->post('password')){
			$password = sha1($this->input->post('password'));
			$data['password'] = hash('sha256', $password);
		}
		$data['usuario'] = $this->input->post('usuario');
		$data['nombre'] = $this->input->post('nombre');
		$data['apellido'] = $this->input->post('apellido');
		$data['dni'] = $this->input->post('dni');
		$data['direccion'] = $this->input->post('direccion');
		$data['telefono'] = $this->input->post('telefono');
		return $this->db->where('id', $this->input->post('id'))->update($tabla, $data);
	}

	public function check($tabla) {
    $id = $this->input->post('id');
		$usuario = $this->input->post('usuario');
		$dni = $this->input->post('dni');
		if($id) { $this->db->where('id !=', $id); }
		if($usuario) { $this->db->where('usuario', $this->input->post('usuario')); }
		if($dni) { $this->db->where('dni', $this->input->post('dni')); }
		$query = $this->db->get($tabla);
		return $query->result();
	}

}
