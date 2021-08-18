<?php

class Notasalida_model extends CI_Model
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

	public function getCaja()
	{
		return $this->db->where('estado', '1')->where('usuario', $this->usuario)->get('caja')->row();
	}

	public function maximoC()
	{
		return $this->db->where('usuario', $this->usuario)->get('caja')->last_row();
	}

	public function codigos($tabla, $empresa)
	{
		return $this->db->select_max('correlativo')->where('empresa', $empresa)->get($tabla)->row();
	}

	public function completarproducto($q, $empresa)
	{
		$this->db->like('nombre', $q);
		$this->db->where('tipo', '0');
		$query = $this->db->get('producto');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$categoria = $this->get($row->categoria, 'productocategoria');
				$nombre = $categoria ? $categoria->nombre : '';
				$totalStock = $this->db->select_sum("cantidad")->where('empresa', $empresa)->where('producto', $row->id)->get('stock')->row();
				$row_set[] = array(
					'producto' => $row->id,
					'label' => $row->codigo . ' | ' . $row->nombre . ' ' . ' | CATEGORIA: ' . $nombre . ' | STOCK: ' . ($totalStock->cantidad > 0 ? $totalStock->cantidad : 0),
					'preciocompra' => $row->preciocompra,
					'preciocomprapaquete' => $row->preciocomprapaquete,
					'cantidadpaquete' => $row->cantidadpaquete,
					'status_lote' => $row->status_lote
				);
			}
		} else {
			$row_set[] = array(
				'label' => "SIN INFORMACION",
				'producto' => NULL,
				'preciocompra' => NULL,
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
		} else {
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

	public function statusLote($idproducto)
	{
		$this->db->where('id', $idproducto);
		$this->db->where('status_lote', '1');
		return $this->db->get('producto')->row();
	}

	public function getStock($producto, $almacen, $lote, $empresa)
	{
		$querylote = $this->statusLote($producto);

		$this->db->where("almacen", $almacen);
		$this->db->where('producto', $producto);
		$this->db->where('empresa', $empresa);
		if ($querylote) {
			$this->db->where('lote', $lote);
		}
		return $this->db->get('stock')->row();
	}

	function dataPendientes($tabla, $empresa, $estado)
	{
		if ($empresa != '0') {
			$this->db->where('empresa', $empresa);
		}
		$this->db->where("estado", $estado);
		return $this->db->get($tabla)->result();
	}

	function dataNotaIngreso($tabla, $estado, $empresa, $finicio, $factual)
	{
		if ($empresa != '0') {
			$this->db->where('empresa', $empresa);
		}
		$this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'");
		$this->db->where('estado <>', $estado);
		return $this->db->get($tabla)->result();
	}

	public function getStockAlmacen($producto, $almacen, $lote, $empresa)
	{
		$this->db->where('producto', $producto);
		$this->db->where('almacen', $almacen);
		$this->db->where('empresa', $empresa);
		if ($lote) {
			$this->db->where('lote', $lote);
		}
		return $this->db->get("stock")->row();
	}

	public function getDetalle($id, $tabla)
	{
		return $this->db->where('notasalida', $id)->get($tabla)->result();
	}

	public function contador($id)
	{
		return $this->db->from('notasalidadetalle')->where('notasalida', $id)->count_all_results();
	}

	public function eliminar($id, $tabla)
	{
		return $this->db->where('id', $id)->delete($tabla);
	}

	public function generar($id, $tabla)
	{
		$data['estado'] = '2';
		return $this->db->where('id', $id)->update($tabla, $data);
	}

	public function actualizar($tabla)
	{
		$data['origen'] = $this->input->post('origen');
		return $this->db->where('id', $this->input->post('id'))->update($tabla, $data);
	}

	public function anular($id, $tabla)
	{
		$data['estado'] = '3';
		$data['aprobado'] = $this->session->userdata('usuario');
		$data['updated'] = date('Y-m-d');
		return $this->db->where('id', $id)->update($tabla, $data);
	}

	public function desaprobar($id, $tabla)
	{
		$data['estado'] = '0';
		return $this->db->where('id', $id)->update($tabla, $data);
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
			$totalResult = 0;
			foreach ($query->result() as $row) {
				if ($row->producto == $producto) {
					$totalResult += 1;
					$row_set[] = array(
						'label' => $row->lote . ' | ' . $row->vencimiento,
						'lote' => $row->id,
					);
				}
			}
			if ($producto == 0) {
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
}
