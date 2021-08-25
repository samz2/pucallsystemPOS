<?php

class Notasalida extends CI_Controller
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
    $this->notasalida = $this->session->userdata('notasalida') ? $this->session->userdata('notasalida') : FALSE;
  }

  public function index()
  {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista,
      'almacenes' => $this->Controlador_model->getAll("almacen"),
      'empleados' => $this->Controlador_model->getAll("usuario"),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador)),
      'empresas' => $this->Controlador_model->getAll("empresa"),
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function ajax_generado($finicio, $factual, $empresa)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->Controlador_model->dataNotaIngreso($this->controlador, '0', $empresa, $finicio, $factual);
    $data = [];
    foreach ($query as $key => $value) {
      $usuario = $this->Controlador_model->get($value->usuario, 'usuario');
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
        $empresa->ruc . " | " . $empresa->nombre . " | " . $empresa->serie,
        $value->codigo,
        $value->tiposalida,
        $usuario ? $usuario->nombre : '',
        $estado,
        $value->monto,
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
      $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
      //add variables for action
      $boton = '';
      //add html fodr action
      $boton .= '<a class="btn btn-info btn-sm" href="' . $this->url . '/actualizar/' . $value->id . '" title="Modificar"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-danger btn-sm" onclick="borrar(' . $value->id . ')" title="Borrar"><i class="fa fa-trash"></i></a> ';
      $data[] = array(
        $key + 1,
        $empresa->ruc . " | " . $empresa->nombre . " | " . $empresa->serie,
        $value->codigo,
        $value->tiposalida,
        $usuario ? $usuario->nombre : '',
        '<span class="label label-warning" style="background:#ffc107; color:#212529">PENDIENTE</span>',
        $value->monto,
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
    $numero = $this->Controlador_model->codigos($this->controlador,  $this->empresa);
    $numeros = $numero ? $numero->id + 1 : 1;
    $cadena = "";
    for ($i = 0; $i < 4 - strlen($numeros); $i++) {
      $cadena = $cadena . '0';
    }
    $data['codigo'] = "NS" . $cadena . $numeros;
    $data['empresa'] = $this->empresa;
    $data['usuario'] = $this->usuario;
    $data['tiposalida'] = 'AJUSTE STOCK';
    $data['estado'] = '0';
    $data['created'] = date('Y-m-d');
    $insert = $this->Controlador_model->save($this->controlador, $data);
    $CI = &get_instance();
    $CI->session->set_userdata('notasalida', $insert);
    redirect($this->url);
  }

  public function volver()
  {
    $CI = &get_instance();
    $CI->session->set_userdata('notasalida', NULL);
    redirect($this->url);
  }

  public function actualizar($id)
  {
    $CI = &get_instance();
    $CI->session->set_userdata('notasalida', $id);
    redirect($this->url);
  }

  public function botonpedido()
  {
    $data = $this->Controlador_model->get($this->notasalida, $this->controlador);
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
    $destino = $this->Controlador_model->get($data->almacendestino, 'almacen');
    $empresa = $this->Controlador_model->get($data->empresa, 'empresa');
    $cliente = $this->Controlador_model->get($data->usuario, 'usuario');

    if ($data->tiposalida == 'TRASLADO DE ALMACEN') {
      $dataHtmlTraslado = '
      <span style="display:block">
      <b>Destino:</b>' . $destino->nombre . '
      </span>';
    } else {
      $dataHtmlTraslado = "";
    }

    $notasalidadetalle = $this->Controlador_model->getDetalle($id, 'notasalidadetalle');
    $ticket = '';
    $ticket .= '<div class="clearfix">
    <div class="pull-left"><h4 class="text-left">
    <span>' . $empresa->razonsocial . '</span>
    </h4>
    <span>
    <b>RUC:</b> ' . $empresa->ruc . '</br>
    <b>Direccion:</b> ' . $empresa->direccion . '</span></br>
    <span><b>Telefono:</b> ' . $empresa->telefono . '</div>
    <div class="pull-right">
    <h4>' . $data->tiposalida . '<br># ' . $data->codigo . '<br></h4>
    </div>
    </div>
    <hr>
    <div class="row">
    <div class="col-md-12">
    <div class="pull-left m-t-30">
      <span><h4>SOLICITANTE</h4><span>
        <span style="display:block">
        <b>Nombre: </b>' . $cliente->nombre . ' ' . $cliente->apellido . '
        </span>
        <span style="display:block">
        <b>Observacion: </b>' . $data->comentario . '
        </span>
        ' . $dataHtmlTraslado . '
      <br/>
    </div>
    <div class="pull-right m-t-30"></div></div></div><div class="m-h-50"></div><div class="row"><div class="col-md-12">
    <div class="table-responsive">
    <table class="table table-bordered table-condensed">
    <thead>
    <tr>
    <th>#</th>
    <th>Codigo</th>
    <th>Producto</th>
    <th>Tipo</th>
    <th>Cantidad</th>
    <th>C. Item</th>
    <th>Precio</th>
    <th>Sub-total</th>
    </tr>
    </thead>
    <tbody>';
    $total = 0;
    $i = 0;
    foreach ($notasalidadetalle as $value) {
      $i++;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $categoria = $this->Controlador_model->get($producto->categoria, 'productocategoria');
      $total += $value->subtotal;
      $ticket .= '
      <tr>
      <td>' . $i . '</td>
      <td>' . $producto->codigo . '</td>
      <td>' . $producto->nombre . ' ' . ($categoria ? $categoria->nombre : '') . '</td>
      <td>' . $value->medida . '</td>
      <td>' . $value->cantidad . '</td>
      <td>' . $value->cantidaditem . '</td>
      <td>' . $value->precio . '</td>
      <td style="text-align:right">' . number_format($value->subtotal, 2) . '</td>
      </tr>';
    }
    $ticket .= '<tr><td colspan="7" align="right"><strong>Total:</strong></td><td align="right">
    <strong>' . number_format($total, 2) . '</strong></td></tr></tbody></table></div></div></div>';
    echo $ticket;
  }

  public function completarproducto()
  {
    $data = strtoupper($this->input->post("term"));
    $this->Controlador_model->completarproducto($data, $this->input->post("empresa"));
  }

  public function completarempresa()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completarempresa($q);
    }
  }

  public function completarusuario()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completarusuario($q);
    }
  }

  public function completarlote($producto)
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completarlote($q, $producto);
    }
  }

  public function imprimir($id)
  {
    $ticket = '<embed src="' . $this->url . '/notasalidapdf/' . $id . '" type="application/pdf" width="100%" height="400"></embed>';
    echo $ticket;
  }

  public function notasalidapdf($id)
  {
    $notasalida = $this->Controlador_model->get($id, $this->controlador);
    $notasalidadetalle = $this->Controlador_model->getDetalle($id, 'notasalidadetalle');
    $data = array(
      'data' => $notasalida,
      'letras' => num_to_letras($notasalida->monto),
      'datas' => $notasalidadetalle,
    );
    $this->load->view('/pdf' . $this->controlador, $data);
    $html = $this->output->get_output();
    $this->dompdf->loadHtml($html);
    $this->dompdf->setPaper('A4', 'portrait');
    $this->dompdf->render();
    $this->dompdf->stream("ordencompra.pdf", array("Attachment" => 0));
  }

  private function _validate()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    $totalRegistros = $this->Controlador_model->contador($this->notasalida);
    if ($totalRegistros == 0) {
      if ($this->input->post('empresa') == '') {
        $data['inputerror'][] = 'empresa';
        $data['error_string'][] = 'Este campo es obligatorio.';
        $data['status'] = FALSE;
      }
    }

    if ($this->input->post('usuario') == '') {
      $data['inputerror'][] = 'usuarios';
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
    $data['usuario'] = $this->input->post('usuario');
    $totalRegistros = $this->Controlador_model->contador($this->notasalida);
    if ($totalRegistros == 0) {
      $data['empresa'] = $this->input->post('empresa');
    }
    $this->Controlador_model->update(array('id' => $this->notasalida), $data, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_list_detalle()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where('notasalida', $this->notasalida)->get('notasalidadetalle')->result();
    $compra = $this->Controlador_model->get($this->notasalida, $this->controlador);
    $data = [];
    foreach ($query as $key => $value) {
      $no = $key + 1;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $marca = $this->Controlador_model->get($producto->categoria, 'productocategoria');
      $dataAlmacen = $this->Controlador_model->get($value->almacen, 'almacen');
      $dataEmpresa = $this->Controlador_model->get($dataAlmacen->empresa, 'empresa');
      $campohidden = '<input type="hidden" id="detalle' . $no . '" name="detalle" value="' . $value->id . '">';
      //add variables for action
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
      if ($value->lote) {
        $querylote = $this->Controlador_model->get($value->lote, "lote");
        $dataLote = $querylote->lote;
      } else {
        $dataLote = "S/L";
      }
      $data[] = array(
        $no . $campohidden,
        $dataEmpresa  ? $dataEmpresa->ruc . " | " . $dataEmpresa->serie . " | " . $dataEmpresa->nombre : "",
        $producto->codigo,
        $value->nombre,
        $dataLote,
        $value->precio,
        $value->cantidad,
        $value->cantidaditem,
        $value->subtotal,
        $dataAlmacen->nombre,
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
    $queryStatus = $this->Controlador_model->statusLote($this->input->post('producto'));
    $dataNotaSalida = $this->Controlador_model->get($this->notasalida, "notasalida");
    $stock = $this->Controlador_model->getStock($this->input->post('producto'), $this->input->post("almacen"), $this->input->post("lote"), $dataNotaSalida->empresa);
    $cantidadaActual = $stock ? $stock->cantidad : 0;

    if ($this->input->post('tipocantidad') == "UNIDAD") {
      $cantidadSacar = $this->input->post('cantidad');
    } else {
      $cantidadSacar = $this->input->post('paquete') * ($this->input->post('cantidad') == '' ? 0 : $this->input->post('cantidad'));
    }

    if ($this->input->post('producto') == '') {
      $data['inputerror'][] = 'productos';
      $data['error_string'][] = '';
      $data['status'] = FALSE;
    }

    if ($cantidadSacar == '') {
      $data['inputerror'][] = 'cantidad';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($queryStatus and $this->input->post('lote') == '') {
      $data['inputerror'][] = 'lotes';
      $data['error_string'][] = '';
      $data['status'] = FALSE;
    }

    if ($this->input->post('almacen') == '0') {
      $data['inputerror'][] = 'almacen';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    } else {
      $dataAlmacen = $this->Controlador_model->get($this->input->post('almacen'), 'almacen');
      if ($queryStatus and $this->input->post('lote') == '') {
        $data['inputerror'][] = 'cantidad';
        $data['error_string'][] = 'Debes seleccionar un lote';
        $data['status'] = FALSE;
      } else {
        if ($cantidadSacar > $cantidadaActual) {
          $datalote = "";
          if ($queryStatus) {
            $querylote = $this->Controlador_model->get($this->input->post("lote"), "lote");
            $datalote = ', con lote "' . $querylote->lote . '"';
          }
          $data['inputerror'][] = 'cantidad';
          $data['error_string'][] = 'Saldo insuficiente en el almacen "' . $dataAlmacen->nombre . '"' . $datalote;
          $data['status'] = FALSE;
        }
      }
    }


    /*
    if($detallelist) {
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
    $compras = $this->Controlador_model->get($this->notasalida, $this->controlador);
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
    $data['producto'] = $this->input->post('producto');
    $data['notasalida'] = $this->notasalida;
    $data['nombre'] = $producto->nombre;
    $data['medida'] = $this->input->post('tipocantidad');
    $data['medidacantidad'] =  $medidacantidad;
    $data['cantidaditem'] = $totalCantidad;
    $data['almacen'] = $this->input->post('almacen');
    $data['lote'] = $this->input->post('lote') ? $this->input->post('lote') : NULL;
    $data['precio'] = $preciocompra;
    $data['cantidad'] = $this->input->post('cantidad');
    $data['subtotal'] = $totalIngreso;

    if ($this->Controlador_model->save('notasalidadetalle', $data)) {
      $compra['monto'] = $compras->monto + $totalIngreso;
      $this->Controlador_model->update(array('id' => $this->notasalida), $compra, 'notasalida');
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_deletedetalle($id)
  {
    $detalle = $this->Controlador_model->get($id, 'notasalidadetalle');
    $compra = $this->Controlador_model->get($detalle->notasalida, $this->controlador);
    if ($this->Controlador_model->delete_by_id($id, 'notasalidadetalle')) {
      $data['monto'] = $compra->monto - $detalle->subtotal;
      $this->Controlador_model->update(array('id' => $detalle->notasalida), $data, $this->controlador);
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_updateventa()
  {
    $compra = $this->Controlador_model->get($this->notasalida, $this->controlador);
    $empresa = $this->Controlador_model->get($compra->empresa, 'empresa');
    $usuario = $this->Controlador_model->get($compra->usuario, 'usuario');
    $contador = $this->Controlador_model->contador($this->notasalida);
    $data['empresa'] = $compra->empresa;
    $nombreempresa = $empresa->tipo == '0' ? $empresa->nombre : $empresa->razonsocial;
    $data['nombreempresa'] = $empresa->ruc . ' | ' . $nombreempresa;
    $data['usuario'] = $compra->usuario;
    $data['estado'] = $compra->estado;
    $data['contador'] = $contador;
    $data['tiposalida'] = $compra->tiposalida;
    $data['nombreusuario'] = $usuario->documento . ' | ' . $usuario->nombre . ' ' . $usuario->apellido;
    $data['codigo'] = $compra->codigo;
    $data['montototal'] = $compra->monto;
    $data['empresaAlmacen'] = $this->db->where("empresa", $compra->empresa)->get("almacen")->result();
    echo json_encode($data);
  }

  public function ajax_updatecantidad()
  {
    $detalle = $this->Controlador_model->get($this->input->post('detalle'), 'notasalidadetalle');
    $ventas = $this->Controlador_model->get($detalle->notasalida, $this->controlador);
    $data['cantidad'] = $this->input->post('cantidad');
    $data['subtotal'] = $this->input->post('cantidad') * $detalle->precio;
    if ($this->Controlador_model->update(array('id' => $this->input->post('detalle')), $data, 'notasalidadetalle')) {
      $venta['montototal'] = $ventas->montototal - $detalle->subtotal + ($this->input->post('cantidad') * $detalle->precio);
      $this->Controlador_model->update(array('id' => $detalle->notasalida), $venta, $this->controlador);
      echo json_encode(array("status" => TRUE));
    }
  }

  private function _validatprocesar()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    $contador = $this->Controlador_model->contador($this->notasalida);

    if ($contador == 0) {
      $data['inputerror'][] = 'totales';
      $data['error_string'][] = 'Debe agregar productos para poder procesar.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('tiposalida') == 'TRASLADO DE ALMACEN' and $this->input->post('destinotraslado') == '0') {
      $data['inputerror'][] = 'destinotraslado';
      $data['error_string'][] = 'Seleccione el destino del traslado';
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
    $notasalida = $this->Controlador_model->get($this->notasalida, 'notasalida');
    $productos = $this->Controlador_model->getDetalle($this->notasalida, 'notasalidadetalle');
    foreach ($productos as $value) {

      $cantidad = $this->Controlador_model->getStockAlmacen($value->producto, $value->almacen, $value->lote, $notasalida->empresa);
      $movimiento['empresa'] = $notasalida->empresa;
      $movimiento['usuario'] = $this->usuario;
      $movimiento['notasalida'] = $this->notasalida;
      $movimiento['tipooperacion'] = "NS";
      $movimiento['producto'] = $value->producto;
      $movimiento['almacen'] = $value->almacen;
      $movimiento['lote'] = $value->lote ? $value->lote : NULL;
      $movimiento['medida'] = $value->medida;
      $movimiento['medidacantidad'] = $value->medidacantidad;
      $movimiento['cantidad'] = $value->cantidad; //? LO QUE REGISTRA
      $movimiento['cantidaditem'] = $value->cantidaditem;
      $movimiento['totalitemoperacion'] = $value->cantidaditem;
      $movimiento['stockanterior'] = $cantidad ? $cantidad->cantidad : 0;
      $movimiento['tipo'] = $this->input->post('tiposalida') == 'TRASLADO DE ALMACEN' ? 'SALIDA TRASLADO' : 'SALIDA';
      $movimiento['stockactual'] = ($cantidad ? $cantidad->cantidad : 0) - $value->cantidaditem;
      //$movimiento['costopromedio'] = $cantidad ? $cantidad->costopromedio : $producto->preciocompra;
      $movimiento['created'] = date('Y-m-d');
      $movimiento['hora'] = date("H:i:s");
      $this->Controlador_model->save('movimiento', $movimiento);

      if ($this->input->post('tiposalida') == 'TRASLADO DE ALMACEN') {
        $cantidadIngreso = $this->Controlador_model->getStockAlmacen($value->producto,$this->input->post('destinotraslado'), $value->lote, $notasalida->empresa);
        $movimientoIngreso['empresa'] = $notasalida->empresa;
        $movimientoIngreso['usuario'] = $this->usuario;
        $movimientoIngreso['notasalida'] = $this->notasalida;
        $movimientoIngreso['tipooperacion'] = "NS";
        $movimientoIngreso['producto'] = $value->producto;
        $movimientoIngreso['almacen'] = $this->input->post('destinotraslado');
        $movimientoIngreso['lote'] = $value->lote ? $value->lote : NULL;
        $movimientoIngreso['medida'] = $value->medida;
        $movimientoIngreso['medidacantidad'] = $value->medidacantidad;
        $movimientoIngreso['cantidad'] = $value->cantidad; //? LO QUE REGISTRA
        $movimientoIngreso['cantidaditem'] = $value->cantidaditem;
        $movimientoIngreso['totalitemoperacion'] = $value->cantidaditem;
        $movimientoIngreso['stockanterior'] = $cantidadIngreso ? $cantidadIngreso->cantidad : 0;
        $movimientoIngreso['tipo'] =  'INGRESO TRASLADO';
        $movimientoIngreso['stockactual'] = ($cantidadIngreso ? $cantidadIngreso->cantidad : 0) + $value->cantidaditem;
        //$movimientoIngreso['costopromedio'] = $cantidad ? $cantidad->costopromedio : $producto->preciocompra;
        $movimientoIngreso['created'] = date('Y-m-d');
        $movimientoIngreso['hora'] = date("H:i:s");
        $this->Controlador_model->save('movimiento', $movimientoIngreso);
      }


      //Descontamos el stock del almacen
      $stockDescontar['cantidad'] = $cantidad->cantidad - $value->cantidaditem;
      $this->db->where('id', $cantidad->id)->update('stock', $stockDescontar);

      if ($this->input->post('tiposalida') == 'TRASLADO DE ALMACEN') {
        //? consultamos si donde se va trasladar el stock ya tiene registro
        $cantidadRegistrar = $this->Controlador_model->getStockAlmacen($value->producto, $this->input->post('destinotraslado'), $value->lote, $this->input->post('empresatraslado'));
        if ($cantidadRegistrar) {
          $stockUpdate['cantidad'] = $cantidadRegistrar->cantidad + $value->cantidaditem;
          $this->db->where('id', $cantidadRegistrar->id)->update('stock', $stockUpdate);
        } else {
          $dataStock['cantidad'] =  $value->cantidaditem;
          $dataStock['producto'] =  $value->producto;
          $dataStock['lote'] =  $value->lote ? $value->lote : NULL;
          $dataStock['almacen'] =  $this->input->post('destinotraslado');
          $dataStock['empresa'] =  $this->input->post('empresatraslado');
          $this->Controlador_model->save('stock', $dataStock);
        }
      }
    }
    $data['created'] = date('Y-m-d');
    $data['tiposalida'] = $this->input->post('tiposalida');
    $data['almacendestino'] = $this->input->post('destinotraslado') <> 0 ? $this->input->post('destinotraslado') : NULL;
    $data['empleado'] = $this->input->post('empleado') ? $this->input->post('empleado') : NULL;
    $data['comentario'] = $this->input->post('comentario') ? $this->input->post('comentario') : NULL;
    $data['estado'] = '1';
    if ($this->db->where('id', $this->notasalida)->update($this->controlador, $data)) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function excel()
  {
    $metodo = $this->input->post('metodo1') == null ? $this->input->post('metodo2') : $this->input->post('metodo1');
    $tipo = $this->input->post('tipo1') == null ? $this->input->post('tipo2') : $this->input->post('tipo1');
    $estado = $this->input->post('estado1') == null ? $this->input->post('estado2') : $this->input->post('estado1');
    $fecha = $this->input->post('fecha');
    $mes = $this->input->post('mes');
    $usuario = $this->input->post('usuario');
    $ano = date('Y');
    if ($fecha) {
      $pedidos = $this->Controlador_model->generarpedidoD($metodo, $tipo, $estado, $fecha, $usuario);
    } else if ($mes) {
      $pedidos = $this->Controlador_model->generarpedidoM($metodo, $tipo, $estado, $mes, $ano, $usuario);
    }
    // Propiedades del archivo excel
    $this->phpexcel->getProperties()->setTitle("Lista de Utilidad")->setDescription("Lista de Compra");
    // Setiar la solapa que queda actia al abrir el excel
    $this->phpexcel->setActiveSheetIndex(0);
    // Solapa excel para trabajar con PHP
    $sheet = $this->phpexcel->getActiveSheet();
    $sheet->setTitle("Lista de Compra");
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $sheet->getColumnDimension('H')->setAutoSize(true);
    $sheet->getColumnDimension('I')->setAutoSize(true);
    $sheet->getColumnDimension('J')->setAutoSize(true);
    $style_header = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7')));
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $sheet->getStyle('A1:J1')->applyFromArray($style_header);
    $sheet->getStyle("A1:J1")->applyFromArray($style);
    $sheet->setCellValue('A1', 'NRO');
    $sheet->setCellValue('B1', 'COMPRA');
    $sheet->setCellValue('C1', 'TIPO');
    $sheet->setCellValue('D1', 'MOVIMIENTO');
    $sheet->setCellValue('E1', 'PROVEEDOR');
    $sheet->setCellValue('F1', 'FECHA');
    $sheet->setCellValue('G1', 'ESTADO');
    $sheet->setCellValue('H1', 'MONTO');
    $sheet->setCellValue('I1', 'IGV');
    $sheet->setCellValue('J1', 'TOTAL');
    $i = 1;
    foreach ($pedidos as $value) {
      $cliente = $this->Controlador_model->get($value->proveedor, 'proveedor');
      $igvtotal = $value->montototal * ($value->igv / 100);
      $subtotal = $value->montototal - $igvtotal;
      $i++;
      $sheet->setCellValue('A' . $i, $i - 1);
      $sheet->setCellValue('B' . $i, $value->serie . '-' . $value->numero);
      $sheet->setCellValue('C' . $i, $value->tipocompra);
      $sheet->setCellValue('D' . $i, $value->movimiento);
      $sheet->setCellValue('E' . $i, $cliente->nombre);
      $sheet->setCellValue('F' . $i, $value->created);
      if ($value->estado == '0') {
        $estado = "Pendiente";
      }
      if ($value->estado == '1') {
        $estado = "Cancelado";
      }
      $sheet->setCellValue('G' . $i, $estado);
      $sheet->setCellValue('H' . $i, $subtotal);
      $sheet->setCellValue('I' . $i, $igvtotal);
      $sheet->setCellValue('J' . $i, $value->montototal);
    }
    // Salida
    header("Content-Type: application/vnd.ms-excel");
    $nombreArchivo = 'compras_' . date('YmdHis');
    header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
    header("Cache-Control: max-age=0");
    // Genera Excel
    $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, "Excel5");
    // Escribir
    $writer->save('php://output');
    exit;
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

  function ajax_empresaAlmacen($idempresa)
  {
    $numero = $this->Controlador_model->codigos($this->controlador, $idempresa);
    $numeros = $numero ? $numero->correlativo + 1 : 1;
    $cadena = "";
    for ($i = 0; $i < 4 - strlen($numeros); $i++) {
      $cadena = $cadena . '0';
    }
    $dataUpdate['codigo'] = "NS" . $cadena . $numeros;
    $dataUpdate['correlativo'] = $numeros;
    $dataUpdate["empresa"] = $idempresa;

    $this->Controlador_model->update(["id" => $this->notasalida], $dataUpdate, "notasalida");
    $dataNotaSalidad = $this->Controlador_model->get($this->notasalida, "notasalida");
    $dataAlmacen = $this->db->where("empresa", $idempresa)->get("almacen")->result();
    echo json_encode(["dataAlmacen" => $dataAlmacen, "codigoActualizado" =>  $dataNotaSalidad->codigo]);
  }

  function ajax_dataEmpresaAlmacen($idempresa)
  {
    $dataAlmacen = $this->db->where("empresa", $idempresa)->get("almacen")->result();
    echo json_encode($dataAlmacen);
  }
}

/* End of file compra.php */
/* Location: ./system/application/controllers/compra.php */
