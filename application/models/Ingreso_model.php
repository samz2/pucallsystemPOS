<?php

class Ingreso_model extends CI_Model {

  public function getAll($tabla) {
    return $this->db->get($tabla)->result();
  }

  public function getConcepto() {
    return $this->db->where('tipo', 'INGRESO')->get('concepto')->result();
  }

  public function getVenta() {
    return $this->db->where('estado', '1')->where('montoactual !=', 0)->get('venta')->result();
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

  public function getcaja()
  {
    $caja = $this->db->where('estado', '0')->get('caja');
    $dataHtmlCaja = "";
    if ($caja->num_rows() > 0) {
      $dataHtmlCaja .= "<option value=''>SELECCIONE UNA CAJA</option>";
      foreach ($caja->result() as $cajas) {
        $dataHtmlCaja .= "<option value='$cajas->id'> $cajas->descripcion | $cajas->created | $cajas->saldoinicial</option>";
      }
    } else {
      $dataHtmlCaja .= "<option value=''>NO HAY CAJAS ABIERTAS</option>";
    }
    return $dataHtmlCaja;
  }

}
