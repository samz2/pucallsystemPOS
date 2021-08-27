<?php

class Guiaremision_model extends CI_Model {

  public function getAll($tabla) {
    return $this->db->get($tabla)->result();
  }

  public function getAlls($tabla) {
    $this->db->from($tabla);
    return $this->db->count_all_results();
  }

  public function save($tabla, $data) {
		$this->db->insert($tabla, $data);
		return $this->db->insert_id();
	}

  public function update($where, $data, $tabla) {
		$this->db->update($tabla, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id, $tabla) {
		$this->db->where('id', $id);
		return $this->db->delete($tabla);
	}

  public function get($id, $tabla) {
    return $this->db->where('id', $id)->get($tabla)->row();
  }

  public function maximo() {
    return $this->db->where('empresa', $this->usuario)->where('estado', '1')->get('caja')->row();
  }

	public function contador($id) {
		return $this->db->from('guiaremisiondetalle')->where('guiaremision', $id)->count_all_results();
	}

  public function completarventa($q) {
		$this->db->like('serie', $q);
		$this->db->or_like('numero', $q);
    $this->db->where('estado', '1');
		$query = $this->db->get('venta');
		if($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->tipoventa.' | '.$row->serie.'-'.$row->numero.' | '.$row->created,
					'venta' => $row->id,
				);
			}
			echo json_encode($row_set);
		}
	}

	public function completarT($q) {
		$this->db->like('nombre', $q);
		$this->db->or_like('documento', $q);
		$query = $this->db->get('cliente');
		if($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->documento.' | '.$row->nombre.' '.$row->apellido,
					'transportista' => $row->id,
				);
			}
			echo json_encode($row_set);
		}
	}

	public function completarproducto($q, $venta) {
		$this->db->like('nombre', $q);
    $this->db->where('venta', $venta);
		$query = $this->db->get('ventadetalle');
		if($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
        $producto = $this->get($row->producto, 'producto');
				if($producto->categoria == 0) {
					$cantidad = $this->guiaremision($row->producto, $venta);
					$saldo = $row->cantidad - ($cantidad ? $cantidad->cantidad : 0);
					if($saldo > 0) {
						$row_set[] = array(
							'label' => $producto->codigoexterno.' | '.$row->nombre.' | SALDO: '.$saldo,
							'saldo' => $saldo,
							'producto' => $row->producto
						);
					}
				} else {
					$saldo = 0;
					$row_set[] = array(
						'label' => $producto->codigoexterno.' | '.$row->nombre.' | SALDO: '.$saldo,
						'saldo' => $saldo,
						'producto' => $row->producto
					);
				}
			}
		} else {
			$row_set[] = array('label' => 'EL PRODUCTO NO CUENTA CON STOCK', 'saldo' => '', 'producto' => '');
		}
		echo json_encode($row_set);
	}

  public function guiaremision($producto, $venta) {
    $this->db->select_sum('vd.cantidad');
		$this->db->from('guiaremisiondetalle vd');
		$this->db->join('guiaremision v', 'v.id = vd.guiaremision');
    $this->db->where('v.estado', '1');
		$this->db->where('v.venta', $venta);
		$this->db->where('vd.producto', $producto);
		$query = $this->db->get();
		return $query->row();
  }

  public function check($documento) {
	return $this->db->where('documento', $documento)->get('cliente')->row();
}

  public function ultimoguiaremision() {
		return $this->db->select_max('consecutivo')->get('guiaremision')->row();
	}

  public function getDetalle($id, $tabla) {
		return $this->db->where('guiaremision', $id)->get($tabla)->result();
	}

	public function numPaquetes($guia)
	{
		$query = "select count(cantidad) as cantidad from guiaremisiondetalle where guiaremision=".$guia;
		return $this->db->query($query)->row();
	}

	public function updateguia($guia) {
		$data['emision'] = $guia['emision'];
		$data['hash'] = $guia['hash'];
		return $this->db->where('id', $guia['id'])->update('guiaremision', $data);
	}

	public function ultimo($tabla) {
		return $this->db->get($tabla)->last_row();
	}

}
