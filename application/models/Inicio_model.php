<?php

class Inicio_model extends CI_Model
{

	public function getAll($tabla)
	{
		return $this->db->get($tabla)->result();
	}

	public function getProductosMasVendidos()
	{
		$query = "select sum(v.cantidad) as cantidad, p.* from ventadetalle v right join producto p on p.id = v.producto group by p.nombre order by cantidad desc";
		return $this->db->query($query)->result();
	}

	public function autocompletar($q)
	{
		$this->db->like('nombre', $q);
		$this->db->or_like('apellido', $q);
		$this->db->or_like('documento', $q);
		$query = $this->db->get('cliente');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_set[] = array(
					'label' => $row->documento . ' | ' . $row->nombre . ' ' . $row->apellido,
					'cliente' => $row->id,
				);
			}
		} else {
			$row_set[] = array('label' => 'EL CLIENTE NO ESTA REGISTRADO', 'cliente' => '');
		}
		echo json_encode($row_set);
	}

	public function autocompletarcodigobarra($q)
	{
		$this->db->like('codigoBarra', $q);

		$query = $this->db->get('producto');

		if ($query->num_rows() > 0) {

			foreach ($query->result() as $row) {
				$verificarStock = $this->db->where("producto", $row->id)->where("cantidad >", 0)->get("stock")->row();

				if ($verificarStock) {
					$row_set[] = array(
						'label' => $row->codigoBarra,
						'idproducto' => $row->id,
						'idcategoria' => $row->categoria,
						'precioproducto' => $row->precioventa,
						'status' => TRUE
					);
				} else {
					$row_set[] = array(
						'label' => "El imei: " . $row->codigoBarra . ", esta sin stock",
						'idproducto' => "",
						'idcategoria' => "",
						'precioproducto' => "",
						'status' => FALSE
					);
				}
			}
		} else {
			$row_set[] = array(
				'label' => 'SIN RESULTADOS',
				'status' => FALSE
			);
		}

		echo json_encode($row_set);
	}
	
	function getCodigoBarra($codigobarra)
	{
	 $this->db->where("codigoBarra", $codigobarra);
	 $this->db->where("estado", "0");
	 return $this->db->get("producto")->row();
	}

	public function get($id, $tabla)
	{
		return $this->db->where('id', $id)->get($tabla)->row();
	}

	public function eliminar($id, $tabla)
	{
		return $this->db->where('id', $id)->delete($tabla);
	}

	public function numeroventa($registro, $mesa)
	{
		$this->db->where('registro', $registro);
		$this->db->from('venta');
		return $this->db->count_all_results();
	}

	public function getVD($venta)
	{
		return $this->db->where('venta', $venta)->get('ventadetalle')->result();
	}

	public function ultimo($tabla)
	{
		return $this->db->get($tabla)->last_row();
	}

	public function pedidodetalle($venta)
	{
		return $this->db->where('venta', $venta)->get('ventadetalle')->result();
	}

	public function check($documento)
	{

		return $this->db->where('documento', $documento)->get('cliente')->row();
	}

	public function modificarclienteventa($venta)
	{
		$data['cliente'] = $venta['cliente'];
		return $this->db->where('id', $venta['id'])->update('venta', $data);
	}

	public function comanda($venta)
	{
		$this->db->select('DISTINCT(producto), nombre, opcion, precio,variante, tipo');
		$this->db->where('venta', $venta);
		$this->db->group_by('producto');
		$query = $this->db->get('ventadetalle');
		return $query->result();
	}

	public function sumacomanda($venta, $producto)
	{
		$this->db->select_sum('cantidad');
		$this->db->where('venta', $venta);
		$this->db->where('producto', $producto);
		$query = $this->db->get('ventadetalle');
		return $query->row();
	}

	public function resumenventa($caja)
	{
		$this->db->select('vd.producto, vd.preciocompra, vd.precio');
		$this->db->from('ventadetalle vd');
		$this->db->join('venta v', 'v.id = vd.venta');
		$this->db->where('v.caja', $caja);
		$this->db->group_by('vd.producto');
		$query = $this->db->get();
		return $query->result();
	}

	public function sumaresumenventa($registro, $producto)
	{
		$this->db->select_sum('vd.cantidad');
		$this->db->from('ventadetalle vd');
		$this->db->join('venta v', 'v.id = vd.venta');
		$this->db->where('v.registro', $registro);
		$this->db->where('vd.producto', $producto);
		$query = $this->db->get();
		return $query->row();
	}

	public function codigos($id, $serie)
	{
		return $this->db->select_max('consecutivo')->where('serie', $serie)->where('tipoventa', $id)->get('venta')->row();
	}

	public function updateventa($venta)
	{
		$data['hash'] = $venta['hash'];
		return $this->db->where('id', $venta['id'])->update('venta', $data);
	}

	public function getStock($empresa, $producto)
	{
		return $this->db->where('empresa', $empresa)->where('producto', $producto)->get('stock')->first_row();
	}

	public function getCode($code)
	{
		return $this->db->where('codigo', $code)->get('producto')->first_row();
	}

	public function getCaja($caja, $tabla)
	{
		return $this->db->where('caja', $caja)->get($tabla)->result();
	}

	public function apertura($id)
	{
		return $this->db->where('estado', '0')->where('usuario', $this->usuario)->get('caja')->row();
	}

	public function combo($id)
	{
		return $this->db->where('producto', $id)->get('combo')->result();
	}

	public function pedidopendiente($id, $caja)
	{
		return $this->db->where('estado', '0')->where('id', $id)->where('caja', $caja)->get('venta')->row();
	}

	public function holds($caja)
	{
		return $this->db->where('caja', $caja)->get('venta')->result();
	}

	public function contador($venta)
	{
		return $this->db->from('ventadetalle')->where('venta', $venta)->count_all_results();
	}

	public function pagos($venta)
	{
		return $this->db->where('venta', $venta)->get('ingreso')->result();
	}

	public function hold($caja)
	{
		return $this->db->where('caja', $caja)->get('venta')->last_row();
	}

	public function ventapendiente($caja)
	{
		return $this->db->where('estado', '0')->where('caja', $caja)->get('venta')->result();
	}

	public function pedidodetalleE($caja)
	{
		return $this->db->where('caja', $caja)->get('ventadetalle')->result();
	}

	public function pedidodetalleES($venta, $producto)
	{
		return $this->db->where('venta', $venta)->where('producto', $producto)->get('ventadetalle')->row();
	}
	//! no funciona
	public function pedidoVent($venta)
	{
		return $this->db->where('venta', $venta)->get('ventadetalle')->result();
	}

	public function sumagasto($caja, $categoria)
	{
		$this->db->select_sum('montototal');
		$this->db->where('caja', $caja);
		return $this->db->get('egreso')->row();
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

	public function delete_by_venta($id, $tabla)
	{
		$this->db->where('venta', $id);
		return $this->db->delete($tabla);
	}

	public function get_by_id($id, $tabla)
	{
		$this->db->from($tabla);
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function getDetalle($id, $tabla)
	{
		return $this->db->where('venta', $id)->get($tabla)->result();
	}

	public function cajabierta($estado, $empresa)
	{
		return $this->db->where('empresa', $empresa)->where('estado', $estado)->get('caja')->last_row();
	}

	public function maximo($tabla, $empresa)
	{
		return $this->db->where('empresa', $empresa)->get($tabla)->last_row();
	}

	public function getmesa($venta)
	{
		$this->db->select('m.nombre as nombremesa, z.nombre as nombrezona');
		$this->db->from('mesa m');
		$this->db->join('venta v', 'v.mesa = m.id');
		$this->db->join('zona z', 'z.id = m.zona');
		$this->db->where('v.id', $venta);
		return $this->db->get()->row();
	}

	public function pedidotemporal($venta)
	{
		return $this->db->where('estado', '0')->where('venta', $venta)->get('ventatemporal')->result();
	}

	public function pedidotemporalE($venta)
	{
		return $this->db->where('venta', $venta)->get('ventatemporal')->result();
	}

	public function pedidotemporaldet($venta, $producto)
	{
		return $this->db->where('estado', '0')->where('venta', $venta)->where('producto', $producto)->get('ventatemporal')->row();
	}

	public function getMesas()
	{
		return $this->db->where('estado', '0')->get('mesa')->result();
	}

	public function getMesasEmpresa($empresa)
	{
		return $this->db->where('estado', '0')->where('empresa', $empresa)->get('mesa')->result();
	}

	public function getZonasEmpresa($empresa)
	{
		return $this->db->where('empresa', $empresa)->get('zona')->result();
	}

	public function zonamesas($zona, $empresa)
	{
		return $this->db->where('zona', $zona)->where('empresa', $empresa)->get('mesa')->result();
	}

	public function backup()
	{
		$this->load->dbutil();
		$prefs = array(
			'format' => 'zip',
			'filename' => 'my_db_backup.sql'
		);
		$backup = &$this->dbutil->backup($prefs);
		$db_name = 'backup-on-' . date("Y-m-d-H-i-s") . '.zip';
		$save = 'backup/' . $db_name;
		$this->load->helper('file');
		write_file($save, $backup);
	}

	public function resumengasto($caja)
	{
		return $this->db->where('caja', $caja)->get('egreso')->result();
	}

	public function ventaresumencantidad($caja, $producto)
	{
		$this->db->select_sum('cantidad');
		$this->db->from('ventadetalle vd');
		$this->db->join('venta v', 'v.id = vd.venta');
		$this->db->where('v.caja', $caja);
		$this->db->where('vd.producto', $producto);
		$query = $this->db->get();
		return $query->row();
	}

	public function ventaresumensubtotal($caja, $producto)
	{
		$this->db->select_sum('subtotal');
		$this->db->from('ventadetalle vd');
		$this->db->join('venta v', 'v.id = vd.venta');
		$this->db->where('v.caja', $caja);
		$this->db->where('vd.producto', $producto);
		$query = $this->db->get();
		return $query->row();
	}

	public function habilitar($id, $tabla)
	{
		$data['created'] = date('Y-m-d');
		return $this->db->where('id', $id)->update($tabla, $data);
	}

	public function getControl($id, $tabla)
	{
		return $this->db->where('caja', $id)->get($tabla)->result();
	}

	public function getAlertaStock()
	{
		
		$query = $this->db->query("select p.nombre, p.id as idproducto, s.cantidad from stock s inner join producto p on p.id = s.producto where s.cantidad <= p.alertqt");
		return $query->result();
		
		
	}

	public function queryCategoria($idcategoriaP)
	{
		return $this->db->where("id", $idcategoriaP)->where('estadoextras', '1')->get("productocategoria")->row();
	}

	public function getExtras($idcategoria)
	{
		return $this->db->where("categoria", $idcategoria)->where("estado", '0')->where("statusdelete", '0')->get("productomodificador");
	}

	public function getVariante($producto)
	{
		return $this->db->where('producto', $producto)->get('productovariante');
	}

	public function getModificador($producto)
	{
		return $this->db->where('categoria', $producto)->get('productomodificador')->result();
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
	public function ProductoCombo($producto)
	{
		return $this->db->where('producto', $producto)->get('combo')->result();
	}

	public function getVentaDetalle($venta)
	{
		return $this->db->where('venta', $venta)->get("ventadetalle")->result();
	}

	public function UpdateVentaDetalleDeuda($venta)
	{
		$data['estadopago'] = '1';
		return $this->db->where('venta', $venta)->update('ventadetalle', $data);
	}

	public function stockAlmacen($producto, $almacen, $empresa)
	{
		$this->db->select_sum('cantidad');
		$this->db->where("producto", $producto);
		$this->db->where("almacen", $almacen);
		$this->db->where("empresa", $empresa);
		return $this->db->get("stock")->row();
	}

	public function queryCPBoletas()
	{
		$this->db->select("created, COUNT(*) as totalfecha");
		$this->db->where('tipoventa', 'BOLETA');
		$this->db->where('estado', '1');
		$this->db->where('emision', '');
		$this->db->group_by('created');
		return $this->db->get("venta");
	}

	public function queryCPFacturas()
	{
		$this->db->select("created, COUNT(*) as cantidadFactura");
		$this->db->where('tipoventa', 'FACTURA');
		$this->db->where('estado', '1');
		$this->db->where('emision', '');
		$this->db->group_by('created');
		return $this->db->get("venta");
	}

	public function existenciaStock($producto, $almacen, $lote, $cantidad, $empresa)
	{
		$this->db->where('producto',  $producto);
		$this->db->where('almacen',  $almacen);
		if ($lote) {
			$this->db->where('lote',  $lote);
		}
		$this->db->where('empresa',  $empresa);
		return $this->db->get('stock')->row();
	}

	function stockproducto($producto, $empresa, $almacen, $lote)
	{
		$this->db->where('producto',  $producto);
		$this->db->where('empresa',  $empresa);
		$this->db->where('almacen',  $almacen);
		if ($lote) {
			$this->db->where('lote',  $lote);
		}
		return $this->db->get('stock')->row();
	}

	function queryLotes($empresa, $producto, $almacen){
		$this->db->where("empresa", $empresa);
		$this->db->where("producto", $producto);
		$this->db->where("almacen", $almacen);
		return $this->db->where("lote IS NOT NULL")->get("stock");
	}

	function queryStock($producto, $almacen, $empresa){
		$this->db->where("producto", $producto);
		$this->db->where("almacen", $almacen);
		$this->db->where("empresa", $empresa);
		$this->db->where("cantidad >", 0);
		return $this->db->get("stock")->row();
	}

	function dataLotes($producto, $almacen, $empresa){
		$this->db->where("producto", $producto);
		$this->db->where("almacen", $almacen);
		$this->db->where("empresa", $empresa);
		$this->db->where("lote IS NOT NULL");
		return $this->db->get("stock");
	}

	function totalVentasNoProcesadas($usuario, $empresa){
		$this->db->where('usuario_creador', $usuario);
		$this->db->where('empresa', $empresa);
		$this->db->where('estado', '0');
		$this->db->where('formapago', 'CONTADO');
		$this->db->where('mesa IS NULL');
		return $this->db->get('venta');
	}


	function estadoVenta($idventa, $empresa, $estado){
		$this->db->where("id", $idventa); 
		$this->db->where("empresa", $empresa); 
		$this->db->where("estado", $estado);
		$this->db->or_where("estado", "4");
		return $this->db->get("venta")->row();
	}

	function getCajaPagos($idcaja, $tabla, $metodoPago){
		$this->db->select("SUM(monto) as totalpagos");
		$this->db->where("tipo", "VENTA");
		$this->db->where("caja", $idcaja);
		$this->db->where("metodopago", $metodoPago);
		return $this->db->get($tabla)->row();
	}

	function geVentastCaja($idcaja, $tabla){
		$this->db->where("caja", $idcaja);
		$this->db->where("estado", "1");
		return $this->db->get($tabla)->result();
	}

	function totalGenerados($idcaja, $metodopago){
		$this->db->where("caja", $idcaja);
		$this->db->where("metodopago", $metodopago);
		$this->db->where("tipo", "VENTA");
		return $this->db->get("ingreso")->num_rows();
	}

	function abonosCaja($idcaja){
		$this->db->where("caja", $idcaja);
		$this->db->where("tipo", "OPERACION");
		return $this->db->get("ingreso")->result();
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
	
}
