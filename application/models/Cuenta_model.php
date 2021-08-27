<?php

class Cuenta_model extends CI_Model {

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
		$this->db->delete($tabla);
	}

	public function count_allI($venta) {
		$this->db->from('ingreso');
		$this->db->where('venta', $venta);
		return $this->db->count_all_results();
	}

	public function getAll($tabla) {
		return $this->db->get($tabla)->result();
	}

	public function get($id, $tabla) {
		return $this->db->where('id', $id)->get($tabla)->row();
	}

  public function maximo($tabla) {
    return $this->db->get($tabla)->last_row();
  }

  public function getClienteDni($q)
  {
	  $query = "select * from cliente c where c.tipodocumento = 'DNI' and c.nombre like '%$q%' or apellido like '%$q%' or documento like '%$q%'";
	  return $this->db->query($query)->result();
  }

  public function getClienteRuc($q)
  {
	  $query = "select * from cliente c where c.tipodocumento = 'RUC' and c.nombre like '%$q%' or apellido like '%$q%' or documento like '%$q%'";
	  return $this->db->query($query)->result();
  }
  public function addLeadingZeros($num)
  {
	$correlativo = "";
	$cantCeros = 6 - strlen($num);
	$i = 0;
	while($i < $cantCeros)
	{
		$correlativo .= "0";
		$i++;
	}	
	$correlativo .= $num;

	return $correlativo;
  }
}
