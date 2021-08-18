<?php

class Notaingreso_model extends CI_Model
{

	public function getAll($tabla)
	{
		return $this->db->get($tabla)->result();
	}

	public function getAlls($tabla)
	{
		$this->db->from($tabla);
		return $this->db->count_all_results();
	}

	public function codigos($tabla, $empresa)
	{
		return $this->db->select_max('correlativo')->where('empresa', $empresa)->get($tabla)->row();
	}

	public function completarproducto($q, $empresa)
	{
		$this->db->where('tipo', '0');
		$this->db->where('estado', '0');
		$this->db->like('nombre', $q);
		$query = $this->db->get('producto');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$marca = $this->get($row->categoria, 'productocategoria');
				$totalStock = $this->db->select_sum("cantidad")->where("empresa", $empresa)->where('producto', $row->id)->get('stock')->row();
				$nombre = $marca ? $marca->nombre : '';
				$row_set[] = array(
					'label' => $row->codigo . ' | ' . $row->nombre . ' | ' . $nombre . ' | COMPRA: ' . $row->preciocompra . ' | STOCK: ' . ($totalStock->cantidad > 0 ? $totalStock->cantidad : 0),
					'producto' => $row->id,
					'preciocompra' => $row->preciocompra,
					'preciocomprapaquete' => $row->preciocomprapaquete,
					'cantidadpaquete' => $row->cantidadpaquete,
					'status_lote' => $row->status_lote
				);
			}
		} else {
			$row_set[] = array(
				'label' => 'SIN INFORMACION',
				'producto' => NULL,
				'preciocomprapaquete' => NULL,
				'preciocompra' => NULL,
				'cantidadpaquete' => NULL,
				'status_lote' => NULL
			);
		}
		echo json_encode($row_set);
	}

	public function completarempresa($q)
	{
		$this->db->like('razonsocial', $q);
		$this->db->or_like('ruc', $q);
		$query = $this->db->get('empresa');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->ruc . ' | ' . $row->razonsocial,
					'empresa' => $row->id,
				);
			}
			echo json_encode($row_set);
		}
	}

	public function completarusuario($q)
	{
		$this->db->like('nombre', $q);
		$this->db->or_like('documento', $q);
		$query = $this->db->get('usuario');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->documento . ' | ' . $row->nombre . ' ' . $row->apellido,
					'usuario' => $row->id,
				);
			}
		}else{
			$row_set[] = array(
				'label' => "SIN INFORMACION",
				'usuario' => NULL,
			);
		}
		echo json_encode($row_set);
	}

	public function get($id, $tabla)
	{
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	public function getStock($producto, $almacen, $lote, $empresa)
	{
		$this->db->where('producto', $producto);
		$this->db->where("almacen", $almacen);
		$this->db->where('empresa', $empresa);
		if ($lote) {
			$this->db->where('lote', $lote);
		}
		return $this->db->get('stock')->row();
	}

	public function detalleduplicado($notaingreso, $producto)
	{
		return $this->db->where('notaingreso', $notaingreso)->where('producto', $producto)->get('notaingresodetalle')->row();
	}

	public function getDetalle($id, $tabla)
	{
		return $this->db->where('notaingreso', $id)->get($tabla)->result();
	}

	public function contador($id)
	{
		return $this->db->from('notaingresodetalle')->where('notaingreso', $id)->count_all_results();
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

	public function completarlote($q, $producto)
	{
		$this->db->like('lote', $q);
		$this->db->or_like('vencimiento', $q);
		$query = $this->db->get('lote');
		if ($query->num_rows() > 0) {
			$totalResultado = 0;
			foreach ($query->result() as $row) {
				if ($row->producto == $producto) {
					$totalResultado += 1;
					$row_set[] = array(
						'label' => $row->lote . ' | ' . $row->vencimiento,
						'lote' => $row->id,
					);
				}
			}
			if($totalResultado == 0){
				$row_set[] = array(
					'label' => "SIN RESULTADOS",
					'lote' => NULL,
				);
			}
		} else {
			$row_set[] = array(
				'label' => "SIN RESULTADOS",
				'lote' => NULL,
			);
		}
		echo json_encode($row_set);
	}

	function dataNotaIngreso($tabla, $estado, $empresa, $finicio, $factual){
		if($empresa != '0'){
			$this->db->where('empresa', $empresa);
		}
		$this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'");
		$this->db->where('estado <>', $estado);
		return $this->db->get($tabla)->result();
	}

	function dataPendientes($tabla, $empresa, $estado){
		if($empresa != '0'){
			$this->db->where('empresa', $empresa);
		}
		$this->db->where("estado", $estado);
		return $this->db->get($tabla)->result();
	}
}
