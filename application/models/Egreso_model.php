<?php

class Egreso_model extends CI_Model {

  public function getAll($tabla) {
    return $this->db->get($tabla)->result();
  }

  public function getConcepto() {
    return $this->db->where('tipo', 'EGRESO')->get('concepto')->result();
  }

  public function getAlls($tabla) {
    $this->db->from($tabla);
    return $this->db->count_all_results();
  }

  public function save($tabla, $data) {
		$this->db->insert($tabla, $data);
		return $this->db->insert_id();
	}

	public function delete_by_id($id, $tabla) {
		$this->db->where('id', $id);
		$this->db->delete($tabla);
	}

  public function get($id, $tabla) {
    return $this->db->where('id', $id)->get($tabla)->row();
  }

  public function maximo() {
    return $this->db->where('empresa', $this->usuario)->where('estado', '1')->get('caja')->row();
  }

  function getEgresoEmpresa($finicio, $factual, $tabla){
    $this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'");
    $this->db->where("tipo", "EMPRESA");
    $this->db->order_by('id', 'desc');
    return $this->db->get($tabla)->result();
  }

  function getCaja($idcaja){
    $this->db->where("cajaprincipal", $idcaja);
    $this->db->where("estado", "0"); //? abierto
    return $this->db->get("caja");
  }

}
