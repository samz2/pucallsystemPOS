<?php

class Producto_model extends CI_Model
{

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

	public function get_by_id($id, $tabla)
	{
		$this->db->from($tabla);
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function delete_by_id($id, $tabla)
	{
		$this->db->where('id', $id);
		return $this->db->delete($tabla);
	}

	public function codigos($tipo, $categoria)
	{
		$this->db->select_max('numero');
		$this->db->where('tipo', $tipo);
		$this->db->where('categoria', $categoria);
		return $this->db->get('producto')->row();
	}

	public function codigoscategoria()
	{
		return $this->db->select_max('numero')->where('empresa', $this->empresa)->get('productocategoria')->row();
	}


	public function codigo($tabla)
	{
		return $this->db->select_max('numero')->where('empresa', $this->empresa)->get($tabla)->row();
	}

	public function getAll($tabla)
	{
		return $this->db->get($tabla)->result();
	}

	public function ultimo($tabla)
	{
		return $this->db->get($tabla)->last_row();
	}

	public function desactivar_by_id($id, $tabla)
	{
		$data['estado'] = '1';
		return $this->db->where('id', $id)->update($tabla, $data);
	}

	public function activar_by_id($id, $tabla)
	{
		$data['estado'] = '0';
		return $this->db->where('id', $id)->update($tabla, $data);
	}

	public function getstock($producto, $empresa)
	{
		return $this->db->select_sum("cantidad")->where('producto', $producto)->where('empresa', $empresa)->get('stock')->row();
	}

	public function get($id, $tabla)
	{
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	function getDup($combo, $producto)
	{
		return $this->db->where('producto', $combo)->where('item_id', $producto)->get('combo')->row();
	}

	public function autocompletar($q)
	{
		$this->db->like('nombre', $q);
		$this->db->where('tipo', '0');
		$query = $this->db->get('producto');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$categoria = $this->get($row->categoria, 'productocategoria');
				$row_set[] = array(
					'label' => $row->codigo . ' | ' . $row->nombre . ' ' . ($row->categoria ? $categoria->nombre : ''),
					'producto' => $row->id,
				);
			}
		} else {
			$row_set[] = array(
				'label' => "SIN INFORMACION",
				'producto' => "",
			);
		}
		echo json_encode($row_set);
	}

	public function autocompletarlotes($q, $idproducto)
	{
		$this->db->where('producto', $idproducto);
		$this->db->like('lote', $q);
		$query = $this->db->get('lote');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->lote . " | " . $row->vencimiento,
					'lote' => $row->id,
				);
			}
		} else {
			$row_set[] = array(
				'label' => "SIN INFORMACION",
				'lote' => NULL,
			);
		}
		echo json_encode($row_set);
	}

	public function check($nombre)
	{
		$this->db->where('nombre', $nombre);
		$query = $this->db->get('producto');
		return $query->row();
	}

	function checkCodigoBarra($codigBarra)
	{
		$this->db->where('codigoBarra', $codigBarra);
		$query = $this->db->get('producto');
		return $query->row();
	}

	function checkCodigoBarraUpdate($id, $codigBarra)
	{
		$this->db->where('id <>', $id);
		$this->db->where('codigoBarra', $codigBarra);
		$query = $this->db->get('producto');
		return $query->row();
	}

	function checkUpdate($id, $nombre)
	{
		$this->db->where('id <>', $id);
		$this->db->where('nombre', $nombre);
		$query = $this->db->get('producto');
		return $query->row();
	}

	public function productoTop($year)
	{
		$this->db->select('vd.producto, sum(vd.cantidad) AS totalcantidad');
		$this->db->from('ventadetalle vd');
		$this->db->join('venta v', 'v.id = vd.venta');
		$this->db->where('tipo', "0");
		$this->db->where('MONTH(v.created)', $year);
		$this->db->order_by('totalcantidad', 'desc');
		$this->db->group_by('vd.producto');
		$this->db->limit(5);
		return $this->db->get()->result();
	}

	public function contador($tabla)
	{
		return $this->db->from($tabla)->count_all_results();
	}

	function consolidar($finicio, $factual, $empresa)
	{
		$data = array("vd.producto", "SUM(cantidad) as numero");
		$this->db->select($data);
		$this->db->from('ventadetalle vd');
		$this->db->join('venta v', 'v.id = vd.venta');
		$this->db->where('v.estado', '1');
		$this->db->where("v.created BETWEEN '" . $finicio . "' AND '" . $factual . "'");
		$this->db->where('v.empresa', $empresa);
		//? group by, signififca la columna que queremos contar
		$this->db->group_by('vd.producto');
		$this->db->having("vd.producto > 1", null, false);
		$query = $this->db->get();
		return $query->result();
	}

	function vendido($finicio, $factual, $empresa)
	{
		$data = array("vd.producto", "SUM(vd.cantidad) as suma");
		$this->db->select($data);
		$this->db->where('v.estado', '1');
		$this->db->where("v.created BETWEEN '" . $finicio . "' AND '" . $factual . "'");
		$empresa != "0" ? $this->db->where('v.empresa', $empresa) : "";
		$this->db->from('ventadetalle vd');
		$this->db->join('venta v', 'v.id = vd.venta');
		$this->db->where('vd.tipo', "0");
		$this->db->group_by('vd.producto');
		$this->db->order_by("suma", "desc");
		$query = $this->db->get();
		return $query->result();
	}

	function vendidototal($finicio, $factual, $empresa)
	{
		$data = array("SUM(vd.cantidad) as numero");
		$this->db->select($data);
		$this->db->from('ventadetalle vd');
		$this->db->join('venta v', 'v.id = vd.venta');
		$this->db->where('v.estado', '1');
		$this->db->where('vd.tipo', "0");
		$this->db->where("v.created BETWEEN '" . $finicio . "' AND '" . $factual . "'");
		$empresa != "0" ? $this->db->where('v.empresa', $empresa) : "";
		$query = $this->db->get();
		return $query->row();
	}

	function getFamilia($categoria)
	{
		return $this->db->where("categoria", $categoria)->get("producto")->result();
	}

	function primerMov($producto, $fechainicio, $fechafinal, $empresa)
	{
		$this->db->where('producto', $producto);
		$this->db->where("created BETWEEN '" . $fechainicio . "'  AND  '" . $fechafinal . "'");
		$this->db->where('empresa', $empresa);
		$query = $this->db->get('movimiento');
		return $query->first_row();
	}

	function SumMovi($producto, $fechainicio, $fechafinal, $empresa)
	{
		$this->db->select_sum('cantidad');
		$this->db->where('tipo', 'ENTRADA');
		$this->db->where('producto', $producto);
		$this->db->where("created BETWEEN '" . $fechainicio . "'  AND  '" . $fechafinal . "'");
		$this->db->where('empresa', $empresa);
		$query = $this->db->get('movimiento');
		return $query->row();
	}

	public function valorizado($empresa)
	{
		$this->db->select("producto, costopromedio , SUM(cantidad) as totalstock");
		$empresa != "0" ? $this->db->where('empresa', $empresa) : "";
		$this->db->group_by("producto");
		return $this->db->get('stock')->result();
	}

	public function getProductos()
	{
		$this->db->where("estado", '0');
		$this->db->order_by("nombre", 'ASC');
		return $this->db->get("producto")->result();
	}

	public function getAlmacenes()
	{
		$this->db->order_by("id", 'ASC');
		return $this->db->get("almacen")->result();
	}

	public function getTotalStock($producto, $empresa)
	{
		$this->db->select_sum("cantidad");
		$this->db->where("producto", $producto);
		$empresa != "0" ? $this->db->where("empresa", $empresa) : "";
		return $this->db->get("stock")->row();
	}

	public function queryLotificar($idproducto, $empresa)
	{
		$this->db->where("producto", $idproducto);
		$this->db->where("empresa", $empresa);
		$this->db->where("lote");
		return $this->db->get("stock");
	}

	public function queryCantidad($idproducto, $idalmacen)
	{
		$this->db->select_sum("cantidad");
		$this->db->where("almacen", $idalmacen);
		$this->db->where("producto", $idproducto);
		$this->db->where("lote");
		return $this->db->get("stock")->row();
	}

	public function queryDescontarAlmacen($producto, $almacen)
	{
		$this->db->where("producto", $producto);
		$this->db->where("almacen", $almacen);
		return $this->db->where("lote")->get("stock")->row();
	}

	public function maxcodigo($tabla)
	{
		return $this->db->select_max('numero')->get($tabla)->row();
	}
	public function topvendedores($fechainicio, $fechafinal, $perfiles, $empresavendedor)
	{
		if ($perfiles == "TODOS") {
			$perfil = "";
		} else {
			$perfil = "WHERE p.nombre ='" . $perfiles . "'";
		}
		$query = $this->db->query("
			select u.id, u.nombre as 'nombrevendedor', p.nombre, 
			(select sum(deudatotal) FROM venta where usuario_proceso = u.id AND created BETWEEN '" . $fechainicio . "' AND '" . $fechafinal . "' AND empresa = " . $empresavendedor . " ) as 'ventatotal', 
			(select sum(deudatotal) FROM venta where usuario_proceso = u.id AND created BETWEEN '" . $fechainicio . "' AND '" . $fechafinal . "' AND formapago = 'CONTADO' AND empresa = " . $empresavendedor . " ) as 'contadototal', 
			(select sum(deudatotal) FROM venta where usuario_proceso = u.id AND created BETWEEN '" . $fechainicio . "' AND '" . $fechafinal . "' AND formapago = 'CREDITO' AND empresa = " . $empresavendedor . ") as 'creditototal'
			from usuario u join perfil p on u.perfil = p.id " . $perfil . " ORDER BY ventatotal DESC");
		return $query->result();
	}

	function gerAlmacenes($empresa)
	{
		$empresa != "0" ? $this->db->where("empresa", $empresa) : "";
		$this->db->order_by("id", "ASC");
		return $this->db->get("almacen")->result();
	}

	public function codigosnotaingreso($tabla, $empresa)
	{
		return $this->db->select_max('correlativo')->where('empresa', $empresa)->get($tabla)->row();
	}
}
