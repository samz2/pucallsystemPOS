<?php

class Nota_model extends CI_Model
{

	public function save($tabla, $data)
	{
		$this->db->insert($tabla, $data);
		return $this->db->insert_id();
	}

	public function get_by_id($id, $tabla)
	{
		$this->db->from($tabla);
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function update($where, $data, $tabla)
	{
		$this->db->update($tabla, $data, $where);
		return $this->db->affected_rows();
	}

	public function completarventa($q)
	{
		$this->db->like('numero', $q);
		$this->db->where('tipoventa <>', 'OTROS');
		$this->db->where('estado', '1');
		$this->db->where('modificar', '0');
		$query = $this->db->get('venta');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->tipoventa . ' | ' . $row->serie . '-' . $row->numero . ' | ' . $row->created,
					'venta' => $row->id,
				);
			}
		} else {
			$row_set[] = array(
				'label' => 'SIN INFORMACION',
				'venta' => NULL,
			);
		}
		echo json_encode($row_set);
	}

	public function contador($nota)
	{
		return $this->db->from('notadetalle')->where('nota', $nota)->count_all_results();
	}

	public function delete_by_id($id, $tabla)
	{
		$this->db->where('id', $id);
		return $this->db->delete($tabla);
	}

	public function delete_by_detalle($venta, $tabla)
	{
		$this->db->where('venta', $venta);
		return $this->db->delete($tabla);
	}

	public function count_all($tabla)
	{
		$this->db->from($tabla);
		return $this->db->count_all_results();
	}

	public function getAll($tabla)
	{
		return $this->db->get($tabla)->result();
	}

	public function getAlls($tabla)
	{
		$this->db->from($tabla);
		return $this->db->count_all_results();
	}

	public function get($id, $tabla)
	{
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	public function maxcodigo($tabla)
	{
		return $this->db->select_max('numero')->get($tabla)->row();
	}

	public function detallenota($nota)
	{
		return $this->db->where('nota', $nota)->get('notadetalle')->result();
	}

	public function detalleventa($venta)
	{
		return $this->db->where('venta', $venta)->get('ventadetalle')->result();
	}

	public function getStock($producto, $empresa)
	{
		return $this->db->where('producto', $producto)->where('empresa', $empresa)->get('stock')->row();
	}

	public function codigos($tiponota, $tipoventa, $empresa, $serie, $tabla)
	{
		return $this->db->select_max('consecutivo')->where('tiponota', $tiponota)->where('tipoventa', $tipoventa)->where('empresa', $empresa)->where('serie', $serie)->get($tabla)->row();
	}

	public function updatenotas($notas)
	{
		$data['emision'] = $notas['emision'];
		$data['hash'] = $notas['hash'];
		return $this->db->where('id', $notas['id'])->update('nota', $data);
	}

	public function autocompleteusuarios($q)
	{
		$this->db->like('nombre', $q);
		$this->db->or_like('apellido', $q);
		$this->db->or_like('documento', $q);
		$query = $this->db->get('usuario');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->documento . ' | ' . $row->nombre . '-' . $row->apellido,
					'usuario' => $row->id,
				);
			}
		} else {
			$row_set[] = array(
				'label' => 'SIN INFORMACION',
				'usuario' => NULL,
			);
		}
		echo json_encode($row_set);
	}

	public function getStockProceso($producto, $almacen)
	{
		return $this->db->where('producto', $producto)->where('almacen', $almacen)->get('stock')->row();
	}

	public function ProductoCombo($producto)
	{
		return $this->db->where('producto', $producto)->get('combo')->result();
	}

	public function getDetalle($idventa, $tabla){
		return $this->db->where('id', $idventa)->get($tabla)->result();
		}
}
