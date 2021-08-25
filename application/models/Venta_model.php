
<?php

class Venta_model extends CI_Model
{

	public function getAll($tabla)
	{
		return $this->db->get($tabla)->result();
	}

	public function getStock($producto, $almacen)
	{
		return $this->db->where('producto', $producto)->where('almacen', $almacen)->get('stock')->row();
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

	public function getDetalle($id, $tabla)
	{
		return $this->db->where('venta', $id)->get($tabla)->result();
	}

	function ventas($finicio, $factual, $empresa, $usuario, $estado)
	{
		$this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'");
		$this->db->where('estado', $estado);
		$this->db->where('empresa', $empresa);
		if ($usuario) {
			$this->db->where('usuario_creador', $usuario);
		}
		$this->db->order_by('id', 'desc');
		return $this->db->get('venta')->result();
	}

	function resumen($finicio, $factual, $empresa, $estado)
	{
		$this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'");
		$this->db->where('estado', $estado);
		$this->db->where('empresa', $empresa);
		return $this->db->get('venta')->result();
	}

	function detalle($finicio, $factual, $empresa, $estado)
	{
		$this->db->select('v.id, vd.producto, vd.precio as precioventa, vd.preciocompra, vd.cantidad, vd.subtotal, vd.tipo, vd.nombre');
		$this->db->from('venta v');
		$this->db->join('ventadetalle vd', 'v.id = vd.venta');
		$this->db->where("v.created BETWEEN '" . $finicio . "' AND '" . $factual . "'");
		$this->db->where('v.estado', $estado);
		$this->db->where('v.empresa', $empresa);
		return $this->db->get()->result();
	}

	public function getCombo($producto)
	{
		return $this->db->where('producto', $producto)->get('combo')->result();
	}

	public function validar($finicio, $factual, $empresa)
	{
		$this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'");
		$this->db->where('tipoventa <>', 'OTROS');
		$this->db->where('emision', '');
		$this->db->where('empresa', $empresa);
		return $this->db->get('venta')->result();
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

	function generarpedidoR($finicio, $factual, $cliente, $usuario, $estado)
	{
		$this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'");
		$this->db->where('estado', $estado);
		if ($usuario) {
			$this->db->where('usuario_creador', $usuario);
		}
		if ($cliente) {
			$this->db->where('cliente', $cliente);
		}
		$query = $this->db->get('venta');
		return $query->result();
	}

	function generarcontador($finicio, $factual, $empresa, $estado)
	{
		return $this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'")->where('tipoventa !=', 'OTROS')->where('estado', $estado)->where('empresa', $empresa)->get('venta')->result();
	}

	function generarpedidoD($finicio, $factual, $cliente, $usuario, $estado)
	{
		$this->db->select('v.id, vd.producto, vd.preciocompra, vd.precio, vd.cantidad, vd.subtotal');
		$this->db->where("v.created BETWEEN '" . $finicio . "' AND '" . $factual . "'");
		$this->db->where('estado', $estado);
		if ($usuario) {
			$this->db->where('v.usuario_creador', $usuario);
		}
		if ($cliente) {
			$this->db->where('v.cliente', $cliente);
		}
		$this->db->from('venta v');
		$this->db->join('ventadetalle vd', 'v.id = vd.venta');
		$query = $this->db->get();
		return $query->result();
	}

	public function updateventa($venta)
	{
		$data['emision'] = $venta['emision'];
		$data['hash'] = $venta['hash'];
		return $this->db->where('id', $venta['id'])->update('venta', $data);
	}

	public function updatenotas($notas, $tabla)
	{
		$data['emision'] = $notas['emision'];
		$data['hash'] = $notas['hash'];
		return $this->db->where('id', $notas['id'])->update($tabla, $data);
	}

	public function codigos($tipoventa, $empresa, $serie, $tabla)
	{
		return $this->db->select_max('consecutivo')->where('tipoventa', $tipoventa)->where('empresa', $empresa)->where('serie', $serie)->get($tabla)->row();
	}

	public function crearnotas($id)
	{
		$venta = $this->get($id, 'venta');
		$ventas['modificar'] = '1';
		$this->db->where('id', $id)->update('venta', $ventas);
		$empresa = $this->get($venta->empresa, 'empresa');
		if ($this->input->post('tiponota') == '1') {
			$serie = substr($venta->tipoventa, 0, 1) . 'C' . substr($empresa->serie, 2, 2);
			$numero = $this->codigos($venta->tipoventa, $venta->empresa, $serie, 'notacredito');
			$numeros = $numero ? $numero->consecutivo + 1 : 1;
			$cadena = "";
			for ($i = 0; $i < 6 - strlen($numeros); $i++) {
				$cadena = $cadena . '0';
			}
			$detalle = $this->getDetalle($id, 'ventadetalle');
			foreach ($detalle as $value) {
				$productos = $this->get($value->producto, 'producto');
				if ($productos->categoria == '1') {
					$combo = $this->Controlador_model->getCombo($value->producto);
					foreach ($combo as $data) {
						$cantidad2 = $value->cantidad * $data->cantidad;
						$stock = $this->getStock($data->producto, $venta->empresa);
						$producto['cantidad'] = $stock->cantidad + $cantidad2;
						$this->db->where('id', $stock->id)->update('stock', $producto);
					}
				} else if ($productos->categoria == '0') {
					$stock = $this->getStock($value->producto, $venta->empresa);
					$producto['cantidad'] = $stock->cantidad + $value->cantidad;
					$this->db->where('id', $stock->id)->update('stock', $producto);
				}
				$this->db->where('producto', $value->producto)->where('venta', $id)->delete('movimiento');
			}
			$egreso['concepto'] = 5;
			$egreso['usuario'] = $this->session->userdata('usuario');
			$egreso['venta'] = $id;
			$egreso['monto'] = $venta->montototal;
			$egreso['observacion'] = 'NOTA DE CREDITO ' . $serie . '-' . $cadena . $numeros;
			$egreso['created'] = date('Y-m-d');
			$this->db->insert('egreso', $egreso);
			$data['empresa'] = $venta->empresa;
			$data['usuario'] = $this->session->userdata('usuario');
			$data['venta'] = $id;
			$data['tipoventa'] = $venta->tipoventa;
			$data['serie'] = $serie;
			$data['numero'] = $cadena . $numeros;
			$data['consecutivo'] = $numeros;
			$data['motivo'] = $this->input->post('motivocredito');
			$data['descripcion'] = $this->input->post('descripcion');
			$data['montototal'] = $venta->montototal;
			$data['created'] = date('Y-m-d');
			return $this->db->insert('notacredito', $data);
		} else {
			$serie = substr($venta->tipoventa, 0, 1) . 'D' . substr($empresa->serie, 2, 2);
			$numero = $this->codigos($venta->tipoventa, $venta->empresa, $serie, 'notadebito');
			$numeros = $numero ? $numero->consecutivo + 1 : 1;
			$cadena = "";
			for ($i = 0; $i < 6 - strlen($numeros); $i++) {
				$cadena = $cadena . '0';
			}
			$ingreso['usuario'] = $this->session->userdata('usuario');
			$ingreso['concepto'] = 3;
			$ingreso['venta'] = $id;
			$ingreso['metodopago'] = $this->input->post('metodopago');
			$ingreso['tipotarjeta'] = $this->input->post('tipotarjeta') ? $this->input->post('tipotarjeta') : NULL;
			$ingreso['operacion'] = $this->input->post('operacion') ? $this->input->post('operacion') : NULL;
			$ingreso['monto'] = $venta->montototal;
			$ingreso['observacion'] = 'NOTA DE DEBITO ' . $serie . '-' . $cadena . $numeros;
			$ingreso['created'] = date('Y-m-d');
			$this->db->insert('ingreso', $ingreso);
			$data['empresa'] = $venta->empresa;
			$data['usuario'] = $this->session->userdata('usuario');
			$data['venta'] = $id;
			$data['tipoventa'] = $venta->tipoventa;
			$data['serie'] = $serie;
			$data['numero'] = $cadena . $numeros;
			$data['consecutivo'] = $numeros;
			$data['motivo'] = $this->input->post('motivodebito');
			$data['descripcion'] = $this->input->post('descripcion');
			$data['montototal'] = $this->input->post('monto');
			$data['created'] = date('Y-m-d');
			return $this->db->insert('notadebito', $data);
		}
	}

	public function getnotas($id, $tabla)
	{
		return $this->db->where('venta', $id)->get($tabla)->row();
	}

	public function get_by_id($id, $tabla)
	{
		$this->db->from($tabla);
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
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

	public function delete_by_venta($venta, $tabla)
	{
		$this->db->where('venta', $venta);
		return $this->db->delete($tabla);
	}

	public function pedidodetalle($venta)
	{
		return $this->db->where('venta', $venta)->get('ventadetalle')->result();
	}

	public function comanda($venta)
	{
		$this->db->select('DISTINCT(producto), nombre, opcion, precio, variante, tipo, subtotal');
		$this->db->where('venta', $venta);
		$this->db->group_by('producto');
		$query = $this->db->get('ventadetalle');
		return $query->result();
	}

	public function getVentaDetalle($venta){
		$this->db->where('venta', $venta);
		return $this->db->get("ventadetalle")->result();
		
	}

	public function sumacomanda($venta, $producto)
	{
		$this->db->select_sum('cantidad');
		$this->db->where('venta', $venta);
		$this->db->where('producto', $producto);
		$query = $this->db->get('ventadetalle');
		return $query->row();
	}

	public function getStockProceso($producto, $almacen, $lote, $empresa)
	{
		$this->db->where('producto', $producto);
		$this->db->where('empresa', $empresa);
		$this->db->where('almacen', $almacen);
		if ($lote) {
			$this->db->where('lote', $lote);
		}
		return $this->db->get('stock')->row();
	}
}
