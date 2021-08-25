<?php

class Compra extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model(modelo(), 'Controlador_model');
    $this->controlador = controlador();
    $this->titulo_controlador = humanize($this->controlador);
    $this->url = base_url() . $this->controlador;
    $this->vista = $this->controlador;
    $this->perfil = $this->session->userdata('perfil') ? $this->session->userdata('perfil') : FALSE;
    $this->usuario = $this->session->userdata('usuario') ? $this->session->userdata('usuario') : FALSE;
    $this->empresa = $this->session->userdata('empresa') ? $this->session->userdata('empresa') : FALSE;
    $this->compra = $this->session->userdata('compra') ? $this->session->userdata('compra') : FALSE;
  }

  public function index()
  {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista,
      'almacenes' => $this->Controlador_model->getAll("almacen"),
      'empresas' => $this->Controlador_model->getAll("empresa"),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador)),
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function ajax_generado($finicio, $factual,  $empresa)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->Controlador_model->dataCompra($this->controlador, '0', $empresa, $finicio, $factual);
    $data = [];
    foreach ($query as $key => $value) {
      $usuario = $this->Controlador_model->get($value->usuario, 'usuario');
      $proveedor = $this->Controlador_model->get($value->proveedor, 'proveedor');
      $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
      if ($value->estado == '1') {
        $estado = '<span class="label label-success">APROBADO</span>';
      }
      if ($value->estado == '2') {
        $estado = '<span class="label label-info">GENERADO</span>';
      }
      if ($value->estado == '3') {
        $estado = '<span class="label label-danger">ANULADO</span>';
      }
      //add variables for action
      $boton = '';
      //add html fodr action
      $boton .= '<a onclick="visualizar(' . $value->id . ')" class="btn btn-default btn-sm" title="Visualizar"><i class="fa fa-eye"></i></a> ';
      $boton .= '<a onclick="imprimir(' . $value->id . ')" class="btn btn-danger btn-sm" title="Imprimir"><i class="fa fa-print"></i></a> ';
      $data[] = array(
        $key + 1,
        $value->codigo,
        $empresa->ruc . " | " . $empresa->nombre . " | " . $empresa->serie,
        $value->movimiento,
        $value->serie . ' ' . $value->numero,
        $proveedor ? $proveedor->nombre : '',
        $estado,
        $value->montototal,
        $value->created,
        $boton
      );
    }
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $start,
      "recordsFiltered" => $length,
      "data" => $data
    );
    echo json_encode($result);
  }

  public function ajax_pendiente($empresa)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->Controlador_model->dataPendientes($this->controlador, $empresa, "0");
    $data = [];
    foreach ($query as $key => $value) {
      $usuario = $this->Controlador_model->get($value->usuario, 'usuario');
      $proveedor = $this->Controlador_model->get($value->proveedor, 'proveedor');
      $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
      //add variables for action
      $boton = '';
      //add html fodr action
      $boton .= '<a class="btn btn-info btn-sm" href="' . $this->url . '/actualizar/' . $value->id . '" title="Modificar"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-danger btn-sm" onclick="borrar(' . $value->id . ')" title="Borrar"><i class="fa fa-trash"></i></a> ';
      $data[] = array(
        $key + 1,
        $value->codigo,
        $empresa->ruc . " | " . $empresa->nombre . " | " . $empresa->serie,
        $value->movimiento,
        $value->serie . ' ' . $value->numero,
        $proveedor ? $proveedor->nombre : '',
        '<span class="label label-warning"  style="background:#ffc107; color:#212529">PENDIENTE</span>',
        $value->montototal,
        $value->created,
        $boton
      );
    }
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $start,
      "recordsFiltered" => $length,
      "data" => $data
    );
    echo json_encode($result);
  }

  public function crear()
  {
    $numero = $this->Controlador_model->codigos($this->controlador, $this->empresa);
    $numeros = $numero ? $numero->correlativo + 1 : 1;
    $cadena = "";
    for ($i = 0; $i < 4 - strlen($numeros); $i++) {
      $cadena = $cadena . '0';
    }
    $data['empresa'] = $this->empresa;
    $data['usuario'] = $this->usuario;
    $data['codigo'] = "C" . $cadena . $numeros;
    $data['correlativo'] = $numeros;
    $data['created'] = date('Y-m-d');
    $insert = $this->Controlador_model->save($this->controlador, $data);
    $CI = &get_instance();
    $CI->session->set_userdata('compra', $insert);
    redirect($this->url);
  }

  public function volver()
  {
    $CI = &get_instance();
    $CI->session->set_userdata('compra', NULL);
    redirect($this->url);
  }

  public function actualizar($id)
  {
    $CI = &get_instance();
    $CI->session->set_userdata('compra', $id);
    redirect($this->url);
  }

  public function botonpedido()
  {
    $data = $this->Controlador_model->get($this->compra, $this->controlador);
    $row = '';
    if ($data) {
      if ($data->estado == '0') {
        $row .= '<a onclick="grabar(' . $data->id . ')" class="btn btn-success" data-toggle="tooltip">GENERAR</a> ';
      } else {
        $row .= '<a onclick="imprimir(' . $data->id . ')" class="btn btn-danger" data-toggle="tooltip"><span class="hidden-xs">IMPRIMIR</span> <i class="fa fa-print"></i></a> ';
        $row .= '<a href="' . $this->url . '/crear" class="btn btn-warning" data-toggle="tooltip">NUEVO <i class="fa fa-plus"></i></a> ';
      }
    }
    echo $row;
  }

  public function ajax_delete($id)
  {
    $this->Controlador_model->delete_by_id($id, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }

  public function visualizar($id = FALSE)
  {
    $data = $this->Controlador_model->get($id, $this->controlador);
    $empresa = $this->Controlador_model->get($data->empresa, 'empresa');
    $cliente = $this->Controlador_model->get($data->usuario, 'usuario');
    $compradetalle = $this->Controlador_model->getDetalle($id, 'compradetalle');
    $ticket = '';
    $ticket .= '<div class="clearfix">
    <div class="pull-left">
    <h4 class="text-left">
    <span>' . $empresa->razonsocial . '</span>
    </h4>
    <span>
    <b>RUC:</b> ' . $empresa->ruc . '</br>
    <b>Direccion:</b> ' . $empresa->direccion . '</span></br>
    <span><b>Telefono:</b> ' . $empresa->telefono . ' <b>Celular:</b>-</span></div><div class="pull-right">
    <h4>' . $data->movimiento . '<br># ' . $data->serie . '-' . $data->numero . '<br></h4></div></div><hr><div class="row"><div class="col-md-12">
    <div class="pull-left">
      <span>
      <h4>SOLICITANTE</h4>
      <span>
      <span><b>Nombre: </b>' . $cliente->nombre . ' ' . $cliente->apellido . '</span><br/>
      <span><b>DNI/RUC: </b>' . ($cliente ? $cliente->documento : '') . '</span><br/>
      <span><b>DIRECCION: </b>' . $cliente->direccion . '</span><br/>
      <span><b>TELEFONO: </b>' . $cliente->telefono . '</span>
    </div>
    <div class="pull-right m-t-30"></div>
    </div>
    </div>
    <div class="m-h-50"></div>
    <div class="row"><div class="col-md-12"><div class="table-responsive">
    <table class="table table-bordered table-condensed">
    <thead>
    <tr>
    <th>#</th>
    <th>Codigo</th>
    <th>Producto</th>
    <th>Tipo</th>
    <th>Cantidad</th>
    <th>Regalo</th>
    <th>Total Items</th>
    <th>Precio</th>
    <th>Sub-total</th>
    </tr>
    </thead>
    <tbody>';
    $total = 0;
    $i = 0;
    foreach ($compradetalle as $value) {
      $i++;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $marca = $this->Controlador_model->get($producto->categoria, 'productocategoria');
      $nombre = $marca ? $marca->nombre : '';
      $total += $value->subtotal;
      $ticket .= '
      <tr>
        <td>' . $i . '</td>
        <td>' . $producto->codigo . '</td>
        <td>' . $producto->nombre . ' ' . $nombre . '</td>
        <td>' . $value->medida . '</td>
        <td>' . $value->cantidad . '</td>
        <td>' . $value->cantidaditemregalo . '</td>
        <td>' . $value->totalitem . '</td>
        <td>' . $value->precioneto . '</td>
        <td style="text-align:right">' . number_format($value->subtotal, 2) . '</td>
      </tr>';
    }
    $ticket .= '
    <tr>
      <td colspan="8" align="right">
       <strong>Total:</strong>
      </td>
      <td align="right">
       <strong>' . number_format($total, 2) . '</strong>
      </td>
    </tr>
    </tbody>
    </table>
    </div>
    </div>
    </div>';
    echo $ticket;
  }

  public function completarproducto()
  {
    $data = strtoupper($this->input->post("term"));
    $this->Controlador_model->completarproducto($data, $this->input->post("empresa"));
  }

  public function completarproveedor()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completarproveedor($q);
    }
  }

  public function completarempresa()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completarempresa($q);
    }
  }

  public function completarlote($producto)
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completarlote($q, $producto);
    }
  }

  public function completarusuario()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completarusuario($q);
    }
  }

  private function _validate()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    $totalRegistros = $this->Controlador_model->contador($this->compra);
    if ($totalRegistros == 0) {
      if ($this->input->post('empresa') == '') {
        $data['inputerror'][] = 'empresas';
        $data['error_string'][] = 'Este campo es obligatorio.';
        $data['status'] = FALSE;
      }
    }

    if ($this->input->post('usuario') == '') {
      $data['inputerror'][] = 'usuarios';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('proveedor') == '') {
      $data['inputerror'][] = 'proveedores';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_update()
  {
    $this->_validate();
    $data['proveedor'] = $this->input->post('proveedor');
    $data['usuario'] = $this->input->post('usuario');
    $totalRegistros = $this->Controlador_model->contador($this->compra);
    if ($totalRegistros == 0) {
      $data['empresa'] = $this->input->post('empresa');
      $data['igv'] = $this->input->post('igv');
    }

    $this->Controlador_model->update(array('id' => $this->compra), $data, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }

  public function imprimir($id)
  {
    $ticket = '<embed src="' . $this->url . '/comprapdf/' . $id . '" type="application/pdf" width="100%" height="400"></embed>';
    echo $ticket;
  }

  public function comprapdf($id)
  {
    $orden = $this->Controlador_model->get($id, $this->controlador);
    $ordendetalle = $this->Controlador_model->getDetalle($id, 'compradetalle');
    $data = array(
      'data' => $orden,
      'letras' => num_to_letras($orden->montototal),
      'datas' => $ordendetalle,
    );
    $this->load->view('/pdf' . $this->controlador, $data);
    $html = $this->output->get_output();
    $this->dompdf->loadHtml($html);
    $this->dompdf->setPaper('A4', 'portrait');
    $this->dompdf->render();
    $this->dompdf->stream("compra.pdf", array("Attachment" => 0));
  }

  public function ajax_list_detalle()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where('compra', $this->compra)->get('compradetalle')->result();
    $compra = $this->Controlador_model->get($this->compra, $this->controlador);
    $data = [];
    foreach ($query as $key => $value) {
      $no = $key + 1;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $marca = $this->Controlador_model->get($producto->categoria, 'productocategoria');
      $campohidden = '<input type="hidden" id="detalle' . $no . '" name="detalle" value="' . $value->id . '">';
      $queryLote = $this->Controlador_model->get($value->lote, 'lote');
      $almacenDestino = $this->Controlador_model->get($value->almacen, 'almacen');
      $boton1 = '';
      $campo1 = '';
      //add html fodr action
      if ($compra->estado == '0') {
        $boton1 = '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrardetalle(' . $value->id . ')"><i class="fa fa-trash"></i></a>';
        $campo1 = '<input type="text" size="2" class="form-control text-center money" id="cantidad' . $no . '" name="cantidad"
        onkeydown="if(event.keyCode == 13) { cambiarcantidad(' . $no . ') }" value="' . $value->cantidad . '" autocomplete="off">';
      } else {
        $campo1 = $value->cantidad;
      }

      if ($value->medida == "PAQUETE") {
        $medidacantidad = " : " . $value->medidacantidad;
      } else {
        $medidacantidad = "";
      }

      $data[] = array(
        $no . $campohidden,
        $almacenDestino->nombre,
        $producto->nombre . ' ' . ($marca ? $marca->nombre : ''),
        ($queryLote ?  $queryLote->lote . " / " . $queryLote->vencimiento : "S/L"),
        $value->medida . $medidacantidad,
        "S/. " . $value->preciounitario,
        "S/. " . $value->precioneto,
        $value->cantidaditemregalo,
        $value->cantidad,
        $value->totalitem,
        "S/. " . $value->subtotal,
        $boton1,
      );
    }
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $start,
      "recordsFiltered" => $length,
      "data" => $data
    );
    echo json_encode($result);
  }

  private function _validatedetalle()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    /*
    $detallelist = $this->Controlador_model->detalleduplicado($this->input->post('id'), $this->input->post('producto'));
    */
    $queryStatus = $this->db->where('id', $this->input->post('producto'))->where('status_lote', '1')->get('producto')->row();

    if ($this->input->post('producto') == '') {
      $data['inputerror'][] = 'productos';
      $data['error_string'][] = '';
      $data['status'] = FALSE;
    }

    if ($this->input->post('cantidad') == '') {
      $data['inputerror'][] = 'cantidad';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('almacen') == '0') {
      $data['inputerror'][] = 'almacen';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($queryStatus and $this->input->post('lote') == '') {
      $data['inputerror'][] = 'lotes';
      $data['error_string'][] = '';
      $data['status'] = FALSE;
    }

    /*
    if ($detallelist) {
      $data['inputerror'][] = 'productos';
      $data['error_string'][] = 'Este campo ya existe en la base de datos.';
      $data['status'] = FALSE;
    }
    */
    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_adddetalle()
  {
    $this->_validatedetalle();
    $producto = $this->Controlador_model->get($this->input->post('producto'), 'producto');
    $compras = $this->Controlador_model->get($this->compra, $this->controlador);

    if ($this->input->post('tipocantidad') == "PAQUETE") {
      $totalCantidad = $this->input->post('paquete') * $this->input->post('cantidad');
      $preciocompra = $this->input->post("preciocomprapaquete");
      $totalIngreso =  $preciocompra * $this->input->post('cantidad');
      $medidacantidad =  $this->input->post('paquete');
    } else {
      $totalCantidad = $this->input->post('cantidad');
      $preciocompra = $this->input->post("preciocompra");
      $totalIngreso =  $preciocompra * $totalCantidad;
      $medidacantidad =  1;
    }
    $cantidadregalo = $this->input->post('regalo') != '' ? $this->input->post('regalo') : 0;
    $data['producto'] = $this->input->post('producto');
    $data['compra'] = $this->compra;
    $data['medida'] = $this->input->post('tipocantidad');
    $data['medidacantidad'] =  $medidacantidad;
    $data['cantidaditem'] = $totalCantidad;
    $data['cantidaditemregalo'] = $cantidadregalo;
    $data['totalitem'] = $totalCantidad + $cantidadregalo;
    $data['nombre'] = $producto->nombre;
    $data['precioneto'] = $preciocompra;
    $data['preciounitario'] = $preciocompra / (1 + ($compras->igv / 100));
    $data['cantidad'] = $this->input->post('cantidad');
    $data['almacen'] = $this->input->post('almacen');
    $data['subtotal'] = $totalIngreso;
    $data['lote'] =  $producto->status_lote == '1' ? $this->input->post('lote') : NULL;
    if ($this->Controlador_model->save('compradetalle', $data)) {

      $compra['montototal'] = $compras->montototal + $totalIngreso;
      $compra['montoactual'] = $compras->montototal + $totalIngreso;
      $this->Controlador_model->update(array('id' => $this->compra), $compra, 'compra');
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_deletedetalle($id)
  {
    $compra = $this->Controlador_model->get($this->compra, $this->controlador);
    $detalle = $this->Controlador_model->get($id, 'compradetalle');
    if ($this->Controlador_model->delete_by_id($id, 'compradetalle')) {
      $data['montototal'] = $compra->montototal - $detalle->subtotal;
      $data['montoactual'] = $compra->montototal - $detalle->subtotal;
      $this->Controlador_model->update(array('id' => $detalle->compra), $data, $this->controlador);
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_updateventa()
  {
    $compra = $this->Controlador_model->get($this->compra, $this->controlador);
    $empresa = $this->Controlador_model->get($compra->empresa, 'empresa');
    $usuario = $this->Controlador_model->get($compra->usuario, 'usuario');
    $proveedor = $this->Controlador_model->get($compra->proveedor, 'proveedor');
    $contador = $this->Controlador_model->contador($this->compra);
    $data['empresa'] = $compra->empresa;
    $data['igv'] = $compra->igv;
    $nombreempresa = $empresa->tipo == '0' ? $empresa->nombre : $empresa->razonsocial;
    $data['nombreempresa'] = $empresa->ruc . ' | ' . $nombreempresa;
    $data['usuario'] = $compra->usuario;
    $data['estado'] = $compra->estado;
    $data['contador'] = $contador;
    $data['nombreusuario'] = $usuario->documento . ' | ' . $usuario->nombre . ' ' . $usuario->apellido;
    $data['proveedor'] = $proveedor ? $compra->proveedor : '';
    $data['nombreproveedor'] = $proveedor ? $proveedor->ruc . ' | ' . $proveedor->nombre : '';
    $data['codigo'] = $compra->codigo;
    $data['montototal'] = $compra->montototal;
    $data['empresaAlmacen'] = $this->db->where("empresa", $compra->empresa)->get("almacen")->result();
    echo json_encode($data);
  }

  function ajax_empresaAlmacen($idempresa)
  {
    $numero = $this->Controlador_model->codigos($this->controlador, $idempresa);
    $numeros = $numero ? $numero->correlativo + 1 : 1;
    $cadena = "";
    for ($i = 0; $i < 4 - strlen($numeros); $i++) {
      $cadena = $cadena . '0';
    }
    $dataUpdate['codigo'] = "C" . $cadena . $numeros;
    $dataUpdate['correlativo'] = $numeros;
    $dataUpdate["empresa"] = $idempresa;

    $this->Controlador_model->update(["id" => $this->compra], $dataUpdate, "compra");
    $dataCompra = $this->Controlador_model->get($this->compra, "compra");
    $dataAlmacen = $this->db->where("empresa", $idempresa)->get("almacen")->result();
    echo json_encode(["dataAlmacen" => $dataAlmacen, "codigoActualizado" =>  $dataCompra->codigo]);
  }

  public function ajax_updatecantidad()
  {
    $detalle = $this->Controlador_model->get($this->input->post('detalle'), 'compradetalle');
    $data['cantidad'] = $this->input->post('cantidad');
    $data['subtotal'] = $this->input->post('cantidad') * $detalle->precioneto;
    if ($this->Controlador_model->update(array('id' => $this->input->post('detalle')), $data, 'compradetalle')) {
      $ventas = $this->Controlador_model->get($detalle->compra, $this->controlador);
      $venta['montototal'] = $ventas->montototal - $detalle->subtotal + ($this->input->post('cantidad') * $detalle->precioneto);
      $venta['montoactual'] = $ventas->montototal - $detalle->subtotal + ($this->input->post('cantidad') * $detalle->precioneto);
      $this->Controlador_model->update(array('id' => $detalle->compra), $venta, $this->controlador);
      echo json_encode(array("status" => TRUE));
    }
  }

  private function _validatprocesar()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    if ($this->input->post('serie') == '') {
      $data['inputerror'][] = 'serie';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('numero') == '') {
      $data['inputerror'][] = 'numero';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('nombrepro') == '') {
      $data['inputerror'][] = 'nombrepro';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_addprocesar()
  {
    $this->_validatprocesar();
    $compra = $this->Controlador_model->get($this->compra, $this->controlador);
    $numero = $this->Controlador_model->ultimo('notaingreso');
    $numeros = $numero ? $numero->id + 1 : 1;
    $cadena = "";
    for ($i = 0; $i < 6 - strlen($numeros); $i++) {
      $cadena = $cadena . '0';
    }
    $notaingreso['codigo'] = "NI" . $cadena . $numeros;
    $notaingreso['usuario'] = $compra->usuario;
    $notaingreso['empresa'] = $compra->empresa;
    $notaingreso['tipoingreso'] = 'COMPRA';
    $notaingreso['compra'] = $this->compra;
    $notaingreso['estado'] = '1';
    $notaingreso['montototal'] = $compra->montototal;
    $notaingreso['comentario'] = 'INGRESO POR COMPRA ' . $this->input->post('serie') . '-' . $this->input->post('numero');
    $notaingreso['created'] = date('Y-m-d');
    $insert = $this->Controlador_model->save('notaingreso', $notaingreso);
    if ($insert) {
      $detalle = $this->Controlador_model->getDetalle($this->compra, 'compradetalle');
      foreach ($detalle as $value) {
        $cantidad = $this->Controlador_model->getStockAlmacen($value->producto, $value->almacen, $value->lote, $compra->empresa);
        $movimiento['empresa'] = $compra->empresa;
        $movimiento['usuario'] = $this->usuario;
        $movimiento['tipooperacion'] = "COMPRA";
        $movimiento['compra'] = $this->compra;
        $movimiento['notaingreso'] = $insert;
        $movimiento['tipo'] = 'ENTRADA COMPRA';
        $movimiento['producto'] = $value->producto;
        $movimiento['lote'] = $value->lote ? $value->lote : NULL;
        $movimiento['almacen'] = $value->almacen;
        $movimiento['medida'] = $value->medida;
        $movimiento['medidacantidad'] = $value->medidacantidad;
        $movimiento['cantidad'] = $value->cantidad; //? LO QUE REGISTRA
        $movimiento['cantidaditem'] = $value->cantidaditem;
        $movimiento['cantidaditemregalo'] = $value->cantidaditemregalo;
        $movimiento['totalitemoperacion'] = $value->totalitem;
        $movimiento['stockanterior'] = $cantidad ? $cantidad->cantidad : 0;
        $movimiento['stockactual'] = ($cantidad ? $cantidad->cantidad : 0) + $value->totalitem;
        //$movimiento['costopromedio'] = $cantidad ? $cantidad->costopromedio : $producto->preciocompra;
        $movimiento['created'] = date('Y-m-d');
        $movimiento['hora'] = date('H:i:s');
        $this->Controlador_model->save('movimiento', $movimiento);

        $NID['notaingreso'] = $insert;
        $NID['producto'] = $value->producto;
        $NID['almacen'] = $value->almacen;
        $NID['lote'] = $value->lote ? $value->lote : NULL;
        $NID['nombre'] = $value->nombre;
        $NID['medida'] = $value->medida;
        $NID['medidacantidad'] = $value->medidacantidad;
        $NID['precio'] = $value->precioneto;
        $NID['cantidad'] = $value->cantidad;
        $NID['cantidaditemregalo'] = $value->cantidaditemregalo;
        $NID['cantidaditem'] = $value->cantidaditem;
        $NID['totalitem'] = $value->totalitem;
        $NID['subtotal'] = $value->precioneto * $value->cantidad;
        $this->Controlador_model->save('notaingresodetalle', $NID);
        if ($cantidad) {
          $stockUpdate['cantidad'] = $cantidad->cantidad + $value->totalitem;
          //$stockUpdate['costopromedio'] = $costopromedio;
          $this->Controlador_model->update(array('id' => $cantidad->id), $stockUpdate, 'stock');
        } else {
          $stockRegister['empresa'] = $compra->empresa;
          $stockRegister['producto'] = $value->producto;
          $stockRegister['almacen'] = $value->almacen;
          $stockRegister['lote'] = $value->lote ? $value->lote : NULL;
          $stockRegister['cantidad'] = $value->totalitem;
          //$stockRegister['costopromedio'] = $costopromedio;
          $this->Controlador_model->save('stock', $stockRegister);
        }
      }
    }
    if ($this->input->post('formapago') == 'CONTADO' && ($this->input->post('metodopago') == 'EFECTIVO' or $this->input->post('metodopago') == 'INTERNET')) {
      $data['montoactual'] = 0;
      $egreso['empresa'] = $compra->empresa;
      $egreso['usuario'] = $compra->usuario;
      $egreso['concepto'] = '4';
      $egreso['compra'] = $this->compra;
      $egreso['montototal'] = $compra->montototal;
      $egreso['observacion'] = 'CANCELAR COMPRA DE ' . $compra->codigo;
      $egreso['created'] = date('Y-m-d');
      $this->Controlador_model->save('egreso', $egreso);
    }
    $data['formapago'] = $this->input->post('formapago');
    $data['movimiento'] = $this->input->post('movimiento');
    $data['metodopago'] = $this->input->post('metodopago');
    $data['estado'] = '1';
    $data['serie'] = $this->input->post('serie');
    $data['numero'] = $this->input->post('numero');
    $data['created'] = date('Y-m-d');
    if ($this->db->where('id', $this->compra)->update($this->controlador, $data)) {
      echo json_encode(array("status" => TRUE));
    }
  }

  private function _validateproveedor()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    $detallelist = $this->Controlador_model->check($this->input->post('ruc'));

    if ($this->input->post('ruc') == '') {
      $data['inputerror'][] = 'ruc';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('nombre') == '') {
      $data['inputerror'][] = 'nombre';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($detallelist) {
      $data['inputerror'][] = 'ruc';
      $data['error_string'][] = 'Este campo ya existe en la base de datos.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }




  public function ajax_addproveedor()
  {
    $this->_validateproveedor();
    $data['ruc'] = $this->input->post('ruc');
    $data['nombre'] = $this->input->post('nombre');
    $data['direccion'] = $this->input->post('direccion');
    $data['referencia'] = $this->input->post('referencia');
    $data['celular'] = $this->input->post('celular');
    $insert = $this->Controlador_model->save('proveedor', $data);
    $compra['proveedor'] = $insert;
    $this->Controlador_model->update(array('id' => $this->compra), $compra, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }

  function _validatelote()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    if ($this->input->post('c_lote') == '') {
      $data['inputerror'][] = 'c_lote';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('c_vencimiento') == '') {
      $data['inputerror'][] = 'c_vencimiento';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_addlote($producto)
  {
    $this->_validatelote();
    $data['lote'] = $this->input->post('c_lote');
    $data['producto'] = $producto;
    $data['vencimiento'] = $this->input->post('c_vencimiento');
    $data['estado'] = 1;
    $data['created_at'] = date("Y-m-d H:i:s");

    $insert = $this->Controlador_model->save('lote', $data);
    if ($insert) {
      $query = $this->Controlador_model->get($insert, 'lote');
      $textLote = $query->lote . " | " . $query->vencimiento;
      echo json_encode(array("status" => TRUE, "idlote" => $insert, "textlote" => $textLote));
    }
  }

  public function excel()
  {
    $finicio = $this->uri->segment(3);
    $factual = $this->uri->segment(4);
    $usuario = $this->perfil == 1 ? FALSE : $this->usuario;
    $pedidos = $this->Controlador_model->listar($finicio, $factual, $usuario, $this->controlador);
    // Propiedades del archivo excel
    $this->phpexcel->getProperties()->setTitle("Lista de OC")->setDescription("Lista de OC por Proveedor");
    // Setiar la solapa que queda actia al abrir el excel
    $this->phpexcel->setActiveSheetIndex(0);
    // Solapa excel para trabajar con PHP
    $sheet = $this->phpexcel->getActiveSheet();
    $sheet->setTitle("Lista de OC - Proveedor");
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $sheet->getColumnDimension('H')->setAutoSize(true);
    $style_header = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7')));
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $sheet->getStyle('A1:H1')->applyFromArray($style_header);
    $sheet->getStyle("A1:H1")->applyFromArray($style);
    $sheet->setCellValue('A1', 'ORDEN');
    $sheet->setCellValue('B1', 'O.C.');
    $sheet->setCellValue('C1', 'TIPO');
    $sheet->setCellValue('D1', 'Nº DOC.');
    $sheet->setCellValue('E1', 'RAZON SOCIAL');
    $sheet->setCellValue('F1', 'FECHA EMI.');
    $sheet->setCellValue('G1', 'ESTADO');
    $sheet->setCellValue('H1', 'MONTO');
    $i = 1;
    foreach ($pedidos as $value) {
      $i++;
      $proveedor = $this->Controlador_model->get($value->proveedor, 'proveedor');
      $sheet->setCellValue('A' . $i, $i - 1);
      $sheet->setCellValue('B' . $i, $value->codigo);
      $sheet->setCellValue('C' . $i, $value->movimiento);
      $sheet->setCellValue('D' . $i, $value->serie . '-' . $value->numero);
      $sheet->setCellValue('E' . $i, $proveedor ? $proveedor->nombre : "");
      $sheet->setCellValue('F' . $i, $value->created);
      $sheet->setCellValue('G' . $i, $value->estado);
      $sheet->setCellValue('H' . $i, $value->montototal);
    }
    // Salida
    header("Content-Type: application/vnd.ms-excel");
    $nombreArchivo = 'compra_' . date('YmdHis');
    header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
    header("Cache-Control: max-age=0");
    // Genera Excel
    $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, "Excel5");
    // Escribir
    $writer->save('php://output');
    exit;
  }

  function ajax_updateprecios()
  {
    $idproducto = $this->input->post('productoactualizar');
    $dataUpdate = [
      'preciocomprapaquete' => $this->input->post('pc_paquete'),
      'cantidadpaquete' => $this->input->post('cantidadpaquete'),
      'preciocompra' => $this->input->post('preciocompraunidad'),
      'precioventa' => $this->input->post('p_ventaunidad')
    ];
    $this->Controlador_model->update(array('id' => $idproducto), $dataUpdate, 'producto');
    $dataActualizado = $this->Controlador_model->get($idproducto, 'producto');
    $stockTotal = $this->db->select_sum('cantidad')->where('producto', $idproducto)->get("stock")->row();
    $dataActualizado->cantidadStock = $stockTotal->cantidad;
    echo json_encode(['status' => TRUE, 'dataactualizado' => $dataActualizado]);
  }

  function ajax_preciosUpdate()
  {
    $query = $this->Controlador_model->get($this->input->post('idproducto'), "producto");
    echo json_encode($query);
  }
}
