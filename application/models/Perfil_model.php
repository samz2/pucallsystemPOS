<?php

class Perfil_model extends CI_Model {

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

  public function check($id, $perfil) {
		if($id) { $this->db->where('id <>', $id); }
		$this->db->where('nombre', $perfil);
		$query = $this->db->get('perfil');
		return $query->result();
  }

	public function getAll($tabla) {
		return $this->db->get($tabla)->result();
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

	public function getMenu() {
		return $this->db->where('parent_id', 0)->get('menu')->result();
	}

	public function getPerfil($perfil) {
		return $this->db->where('perfil', $perfil)->get('perfilmenu')->result();
	}

	public function getPerfilM($perfil, $menu) {
		return $this->db->where('perfil', $perfil)->where('menu', $menu)->get('perfilmenu')->row();
	}

	public function getSubmenu($id, $tabla) {
		return $this->db->where('parent_id', $id)->get($tabla)->result();
	}

	public function getAlls($tabla) {
		$this->db->from($tabla);
		return $this->db->count_all_results();
	}

	public function perfilmenu($id, $tabla) {
		if($id) {
			$modulos = $this->getAll('menu');
			foreach ($modulos as $modulo) {
				$encontrado = false;
				foreach ($this->input->post('menu') as $menu) {
					if ($menu == $modulo->id) {
						$encontrado = true;
						$menuperfil = $this->getPerfilM($id, $menu);
						if (!$menuperfil) {
							$menus = $this->get($menu, 'menu');
							$data['perfil'] = $id;
							$data['menu'] = $menu;
							if ($menus->parent_id > 0) {
								$menuperfils = $this->getPerfilM($id, $menus->parent_id);
								if ($menuperfils) {
									$parent['is_parent'] = 1;
									$this->db->where('id', $menuperfils->id)->update($tabla, $parent);
								} else {
									$prince['perfil'] = $id;
									$prince['menu'] = $menus->parent_id;
									$prince['posicion'] = $menus->parent_id;
									$prince['parent_id'] = 0;
									$prince['is_parent'] = 1;
									$this->db->insert($tabla, $prince);
								}
								$menuperfils = $this->getPerfilM($id, $menus->parent_id);
								$data['posicion'] = 0;
								$data['parent_id'] = $menuperfils->id;
								$data['is_parent'] = 0;
							} else {
								$data['posicion'] = $menu;
								$data['parent_id'] = 0;
								$data['is_parent'] = 0;
							}
							$this->db->insert($tabla, $data);
						}
						break;
					}
				}
				if ($encontrado == false){
					$this->db->where('perfil', $id)->where('menu', $modulo->id)->delete($tabla);
				} else {
				}
			}
			return true;
		} else {
			return false;
		}
	}

}
