<?php

class Cuenta_model extends CI_Model
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

	public function delete_by_id($id, $tabla)
	{
		$this->db->where('id', $id);
		$this->db->delete($tabla);
	}

	public function count_allI($venta)
	{
		$this->db->from('ingreso');
		$this->db->where('venta', $venta);
		return $this->db->count_all_results();
	}

	public function getAll($tabla)
	{
		return $this->db->get($tabla)->result();
	}

	public function get($id, $tabla)
	{
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	public function maximo($tabla)
	{
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
		while ($i < $cantCeros) {
			$correlativo .= "0";
			$i++;
		}
		$correlativo .= $num;

		return $correlativo;
	}

	function cuentasporpagar($finicio, $ffin, $tipodeuda, $tienda)
	{
		$this->db->where("created BETWEEN '" . $finicio . "' AND '" . $ffin . "'");
		$this->db->where("formapago", "CREDITO");
		if ($tipodeuda == "FLETE") {
			$tienda != "0" ? $this->db->where("tienda", $tienda) : "";
			$query = $this->db->get("compracostosadicionales")->result();
		} else {
			$this->db->where("estado", "1");
			$tienda != "0" ? $this->db->where("empresa", $tienda) : "";
			$query = $this->db->get("compra")->result();
		}
		return $query;
	}


	public function codigos($tipoventa, $serie)
	{
		return $this->db->select_max('consecutivo')->where('serie', $serie)->where('tipoventa', $tipoventa)->get('venta')->row();
	}

	public function pagosGenerados($id, $tipopago)
	{
		$this->db->order_by('id', 'desc');
		if ($tipopago == "FLETE") {
			$this->db->where('flete', $id);
		} else {
			$this->db->where('compra', $id);
		}
		return $this->db->get('egreso')->result();
	}

	function getPendientes($tipodeuda, $tienda)
	{
		$this->db->where('estado_pago', "0");
		$this->db->where('formapago', 'CREDITO');
		$this->db->order_by('id', 'ASC');
		if ($tipodeuda == "FLETE") {
			$tienda != "0" ? $this->db->where('tienda', $tienda) : "";
			$query = $this->db->get('compracostosadicionales')->result();
		} else {
			$tienda != "0" ? $this->db->where('empresa', $tienda) : "";
			$query = $this->db->get('compra')->result();
		}
		return $query;
	}

	function getCreditos($cliente, $tienda)
	{
		$this->db->where("cliente", $cliente);
		$this->db->where("tienda", $tienda);
		return $this->db->get("credito")->result();
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

	public function getStock($producto, $almacen)
	{
		return $this->db->where('producto', $producto)->where('almacen', $almacen)->get('stock')->row();
	}

	function queryCaja($idcaja)
	{
		$this->db->where("cajaprincipal", $idcaja);
		$this->db->where("estado", "0"); //? abierto
		return $this->db->get("caja");
	}

	public function getcaja()
	{
		$caja = $this->db->where('estado', '0')->get('caja');
		$dataHtmlCaja = "";
		if ($caja->num_rows() > 0) {
			$dataHtmlCaja .= "<option value='0'>SELECCIONE UNA CAJA</option>";
			foreach ($caja->result() as $cajas) {
				$responsable = $this->get($cajas->usuario, "usuario");
				$dataHtmlCaja .= "<option value='$cajas->id'>RESPONSABLE: $responsable->usuario | $cajas->descripcion | $cajas->created | $cajas->saldoinicial</option>";
			}
		} else {
			$dataHtmlCaja .= "<option value='0'>NO HAY CAJAS ABIERTAS</option>";
		}
		return $dataHtmlCaja;
	}
}
