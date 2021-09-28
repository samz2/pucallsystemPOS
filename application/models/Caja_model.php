<?php

class Caja_model extends CI_Model {

	public function getAll($tabla) {
    return $this->db->get($tabla)->result();
  }

  public function getAlls($tabla) {
    $this->db->from($tabla);
    return $this->db->count_all_results();
  }

  public function getControl($id, $tabla) {
    return $this->db->where('caja', $id)->get($tabla)->result();
  }

	public function maximo($tabla) {
    return $this->db->where('usuario', $this->usuario)->where('empresa', $this->empresa)->get($tabla)->last_row();
  }

	public function maximos($tabla) {
    return $this->db->where('empresa', $this->empresa)->get($tabla)->last_row();
  }

  public function getTotalAbono($idcaja){
    $this->db->select_sum('monto');
    $this->db->where("caja", $idcaja);
    $this->db->where("tipo", "CAJA");
    $this->db->where("modalidad !=", "VENTA");
    $this->db->where("metodopago", "EFECTIVO");
    return $this->db->get("ingreso")->row();
  }
  
  public function getTotalGasto($idcaja){
    $this->db->select_sum("montototal");
    $this->db->where("caja", $idcaja);
    $this->db->where("tipo", "CAJA");
    $this->db->where("tipopago", "EFECTIVO");
    return $this->db->get("egreso")->row();
  }

  public function resumengasto($id) {
    return $this->db->where('caja', $id)->get('egreso')->result();
  }

  public function resumenventa($id) {
		$this->db->select('vd.producto, vd.preciocompra, vd.precio');
		$this->db->from('ventadetalle vd');
		$this->db->join('venta v', 'v.id = vd.venta');
		$this->db->where('v.caja', $id);
		$this->db->group_by('vd.producto');
		$query = $this->db->get();
    return $query->result();
  }

  public function ventaresumencantidad($caja, $producto) {
		$this->db->select_sum('vd.cantidad');
		$this->db->from('ventadetalle vd');
		$this->db->join('venta v', 'v.id = vd.venta');
		$this->db->where('v.caja', $caja);
		$this->db->where('vd.producto', $producto);
		$query = $this->db->get();
		return $query->row();
	}

  public function ventaresumensubtotal($caja, $producto) {
		$this->db->select_sum('subtotal');
		$this->db->from('ventadetalle vd');
		$this->db->join('venta v', 'v.id = vd.venta');
		$this->db->where('v.caja', $caja);
		$this->db->where('vd.producto', $producto);
		$query = $this->db->get();
		return $query->row();
	}

  public function sumaresumenventa($id, $producto) {
		$this->db->select_sum('cantidad');
		$this->db->from('ventadetalle vd');
		$this->db->join('venta v', 'v.id = vd.venta');
		$this->db->where('v.caja', $id);
		$this->db->where('vd.producto', $producto);
		$query = $this->db->get();
		return $query->row();
	}

  public function get($id, $tabla) {
    return $this->db->where('id', $id)->get($tabla)->row();
  }

  public function getDetalle($tipo, $id, $tabla) {
    return $this->db->where($tipo, $id)->get($tabla)->result();
  }

  public function getVentas($fecha) {
    return $this->db->where('estado', '1')->where('usuario', $this->usuario)->where('created', $fecha)->get('venta')->result();
  }

	public function sumagasto($caja, $categoria) {
		$this->db->select_sum('montototal');
		$this->db->where('caja', $caja);
		$query = $this->db->get('egreso');
		return $query->row();
	}

  public function restaurar($id, $tabla) {
    $data['contado'] = 0;
		$data['descuento'] = 0;
		$data['credito'] = 0;
    $data['efectivo'] = 0;
		$data['tarjeta'] = 0;
    $data['gasto'] = 0;
    $data['estado'] = '0';
		$this->db->where('caja', $id)->delete('monedero');
    return $this->db->where('id', $id)->update($tabla, $data);
  }

  function generalingreso($finicio, $factual) {
    $this->db->select('concepto, sum(monto) as monto');
    $this->db->where("created BETWEEN '".$finicio."' AND '".$factual."'");
    $this->db->where('empresa', $this->empresa);
    $this->db->group_by('concepto');
    $this->db->having("concepto > 1", null, false);
    $query = $this->db->get('ingreso');
    return $query->result();
  }

  function generalegreso($finicio, $factual) {
    $this->db->select('concepto, sum(montototal) as monto');
    $this->db->where("created BETWEEN '".$finicio."' AND '".$factual."'");
    $this->db->where('empresa', $this->empresa);
    $this->db->group_by('concepto');
    $this->db->having("concepto > 1", null, false);
    $query = $this->db->get('egreso');
    return $query->result();
  }

  function getIngreso($finicio, $factual, $concepto) {
    $this->db->select_sum('monto');
    $this->db->where("created BETWEEN '".$finicio."' AND '".$factual."'");
    $this->db->where('concepto', $concepto);
    $this->db->where('empresa', $this->empresa);
    $query = $this->db->get('ingreso');
    return $query->row();
  }

  function ingresoefectivo($finicio, $factual) {
    $this->db->select_sum('monto');
    $this->db->from('ingreso i');
		$this->db->join('caja c', 'c.id = i.caja');
    $this->db->where("i.created BETWEEN '".$finicio."' AND '".$factual."'");
    $this->db->where('i.concepto', '3');
    $this->db->where('i.empresa', $this->empresa);
    $this->db->where('c.estado', '1');
    $query = $this->db->get();
    return $query->row();
  }

  function getEgreso($finicio, $factual, $concepto) {
    $this->db->select_sum('montototal');
    $this->db->where("created BETWEEN '".$finicio."' AND '".$factual."'");
    $this->db->where('concepto', $concepto);
    $this->db->where('empresa', $this->empresa);
    if($concepto == '17') { $this->db->where('caja is NOT NULL', NULL, FALSE); }
    $query = $this->db->get('egreso');
    return $query->row();
  }

  function generaldetalle($finicio, $factual, $concepto, $tabla) {
    return $this->db->where("created BETWEEN '".$finicio."' AND '".$factual."'")->where('concepto', $concepto)->get($tabla)->result();
  }

  function ingreso($producto, $fecha) {
    $this->db->select_sum('m.cantidad');
    $this->db->where('m.producto', $producto);
    $this->db->where('m.created', $fecha);
    $this->db->where('m.empresa', $this->empresa);
    $this->db->from('movimiento m');
    $this->db->join('notaingreso co', 'co.id = m.notaingreso');
    $query = $this->db->get();
    return $query->row();
  }

  public function especifico($empleado, $finicio, $factual, $empresa) {
    if($empleado) { $this->db->where('usuario', $empleado); }
    $this->db->where("created BETWEEN '".$finicio."' AND '".$factual."'");
    $this->db->where('estado', '1');
    $this->db->where('empresa', $empresa);
    $query = $this->db->get('caja');
    return $query->result();
  }

  public function getCajas($empleado, $finicio, $factual, $empresa){
    $this->db->where("created BETWEEN '".$finicio."' AND '".$factual."'");
    $this->db->where('empresa', $empresa);
    $query = $this->db->get('caja');
    return $query->result();
  }

  function salida($producto, $fecha) {
    $this->db->select_sum('m.cantidad');
    $this->db->where('m.producto', $producto);
    $this->db->where('m.created', $fecha);
    $this->db->where('m.empresa', $this->empresa);
    $this->db->from('movimiento m');
    $this->db->join('venta v', 'v.id = m.venta');
    $query = $this->db->get();
    return $query->row();
  }

  public function save($tabla, $data) {
		$this->db->insert($tabla, $data);
		return $this->db->insert_id();
	}

  public function update($where, $data, $tabla) {
		$this->db->update($tabla, $data, $where);
		return $this->db->affected_rows();
	}

  public function check($usuario) {
    $this->db->where('estado', '0');
		$this->db->where('usuario', $usuario);
		$query = $this->db->get('caja');
		return $query->result();
	}

  public function ventacaja($caja, $empresa) {
		return $this->db->where('caja', $caja)->where('empresa', $empresa)->get('venta')->result();
	}

  public function ingresocaja($caja, $empresa) {
		return $this->db->where('caja', $caja)->where('empresa', $empresa)->get('ingreso')->result();
	}

}
