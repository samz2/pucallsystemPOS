<?php

class Guiaremision_model extends CI_Model
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

	public function get($id, $tabla)
	{
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	public function maximo()
	{
		return $this->db->where('empresa', $this->usuario)->where('estado', '1')->get('caja')->row();
	}

	public function contador($id)
	{
		return $this->db->from('guiaremisiondetalle')->where('guiaremision', $id)->count_all_results();
	}

	public function completarventa($q)
	{
		$this->db->like('serie', $q);
		$this->db->or_like('numero', $q);
		$this->db->where('estado', '1');
		$query = $this->db->get('venta');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->tipoventa . ' | ' . $row->serie . '-' . $row->numero . ' | ' . $row->created,
					'venta' => $row->id,
				);
			}
			echo json_encode($row_set);
		}
	}

	public function completar_transportistas($q)
	{
		$this->db->like('documento', $q);
		$this->db->or_like('razonsocial', $q);
		$query = $this->db->get('transportepublico');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->documento . ' | ' . $row->razonsocial,
					'tranpsortepublico' => $row->id,
				);
			}
		} else {
			$row_set[] = array(
				'label' => "SIN INFORMACION",
				'tranpsortepublico' => NULL,
			);
		}
		echo json_encode($row_set);
	}

	public function completar_vehiculo($q)
	{
		$this->db->where('tipo', "VEHICULO");
		$this->db->like('documento', $q);
		$query = $this->db->get('transporteprivado');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->tipodocumento . ' | ' . $row->documento,
					'vehiculo' => $row->id,
				);
			}
		} else {
			$row_set[] = array(
				'label' => "SIN INFORMACION",
				'vehiculo' => NULL,
			);
		}
		echo json_encode($row_set);
	}

	public function completar_conductores($q)
	{
		$this->db->where('tipo', "CONDUCTOR");
		$this->db->like('documento', $q);
		$query = $this->db->get('transporteprivado');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->tipodocumento . ' | ' . $row->documento,
					'conductor' => $row->id,
				);
			}
		} else {
			$row_set[] = array(
				'label' => "SIN INFORMACION",
				'conductor' => NULL,
			);
		}
		echo json_encode($row_set);
	}

	public function completar_clientesdestinos($q)
	{
		$this->db->like('documento', $q);
		$this->db->or_like('nombre', $q);
		$this->db->or_like('apellido', $q);
		$query = $this->db->get('cliente');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->tipodocumento . ' | ' . $row->documento. ' | '.$row->nombre.' '.$row->apellido,
					'clientedestino' => $row->id,
				);
			}
		} else {
			$row_set[] = array(
				'label' => "SIN INFORMACION",
				'clientedestino' => NULL,
			);
		}
		echo json_encode($row_set);
	}

	public function completarT($q)
	{
		$this->db->like('nombre', $q);
		$this->db->or_like('documento', $q);
		$query = $this->db->get('cliente');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->documento . ' | ' . $row->nombre . ' ' . $row->apellido,
					'transportista' => $row->id,
				);
			}
			echo json_encode($row_set);
		}
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

	public function guiaremision($producto, $venta)
	{
		$this->db->select_sum('vd.cantidad');
		$this->db->from('guiaremisiondetalle vd');
		$this->db->join('guiaremision v', 'v.id = vd.guiaremision');
		$this->db->where('v.estado', '1');
		$this->db->where('v.venta', $venta);
		$this->db->where('vd.producto', $producto);
		$query = $this->db->get();
		return $query->row();
	}

	public function check($documento)
	{
		return $this->db->where('documento', $documento)->get('cliente')->row();
	}

	public function ultimoguiaremision()
	{
		return $this->db->select_max('consecutivo')->get('guiaremision')->row();
	}

	public function getDetalle($id, $tabla)
	{
		return $this->db->where('guiaremision', $id)->get($tabla)->result();
	}

	public function numPaquetes($guia)
	{
		$query = "select count(cantidad) as cantidad from guiaremisiondetalle where guiaremision=" . $guia;
		return $this->db->query($query)->row();
	}

	public function updateguia($guia)
	{
		$data['emision'] = $guia['emision'];
		$data['hash'] = $guia['hash'];
		return $this->db->where('id', $guia['id'])->update('guiaremision', $data);
	}

	public function ultimo($tabla)
	{
		return $this->db->get($tabla)->last_row();
	}

	public function codigos($tabla, $empresa)
	{
		return $this->db->select_max('correlativo')->where('empresa', $empresa)->get($tabla)->row();
	}
}
