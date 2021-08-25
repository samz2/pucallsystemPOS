<?php

class Kardex_model extends CI_Model
{

	public function getAll($tabla)
	{
		return $this->db->get($tabla)->result();
	}

	public function getProduct()
	{
		return $this->db->where('categoria', '0')->get('producto')->result();
	}

	public function getAlls($tabla)
	{
		$this->db->from($tabla);
		return $this->db->count_all_results();
	}

	public function getVDetalle($producto, $venta)
	{
		return $this->db->where('venta', $venta)->where('producto', $producto)->get('ventadetalle')->row();
	}

	public function getNIDetalle($producto, $notaingreso)
	{
		return $this->db->where('notaingreso', $notaingreso)->where('producto', $producto)->get('notaingresodetalle')->row();
	}

	public function getNSDetalle($producto, $notasalida)
	{
		return $this->db->where('notasalida', $notasalida)->where('producto', $producto)->get('notasalidadetalle')->row();
	}

	public function get($id, $tabla)
	{
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	public function getCaja()
	{
		return $this->db->where('estado', '1')->where('usuario', $this->usuario)->get('caja')->row();
	}

	public function maximoC()
	{
		return $this->db->where('usuario', $this->usuario)->get('caja')->last_row();
	}

	public function autocompletar($q)
	{
		$this->db->select("*");
		$this->db->where('tipo', '0');
		$this->db->like('nombre', $q);
		$this->db->or_like('codigo', $q);
		$query = $this->db->get('producto');
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$marca = $this->get($row['categoria'], 'productocategoria');
				$nombre = $row['categoria'] ? $marca->nombre : '';
				$row_set[] = array(
					'label' => $row['codigo'] . ' | ' . $row['nombre'] . ' ' . $nombre,
					'producto' => $row['id']
				);
			}
		} else {
			$row_set[] = array('label' => 'EL PRODUCTO NO EXISTE', 'producto' => '');
		}
		echo json_encode($row_set);
	}

	public function kardexFecha($producto, $fechainicio, $fechafinal, $tipofiltrado)
	{
		if ($tipofiltrado == '1') {
			$this->db->where('producto', $producto);
			$this->db->where("created BETWEEN '" . $fechainicio . "' AND '" . $fechafinal . "'");
		} else {
			$this->db->where("created BETWEEN '" . $fechainicio . "' AND '" . $fechafinal . "'");
		}
		$query = $this->db->get('movimiento');
		return $query->result();
	}

	public function saldoinicialxFecha($producto, $fechainicio, $fechafinal)
	{
		$this->db->where('producto', $producto);
		$this->db->where("created BETWEEN '" . $fechainicio . "' AND '" . $fechafinal . "'");
		$this->db->where('empresa', $this->session->userdata('empresa'));
		$query = $this->db->get('movimiento');
		return $query->first_row();
	}

	public function getProductos($text){
		$this->db->like("nombre", $text);
		$this->db->where("tipo", '0');
		return $this->db->get("producto")->result();
	}
}
