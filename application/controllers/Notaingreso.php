<?php

class Notaingreso extends CI_Controller
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
    $this->notaingreso = $this->session->userdata('notaingreso') ? $this->session->userdata('notaingreso') : FALSE;
  }

  public function index()
  {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista,
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
    $query = $this->Controlador_model->dataNotaIngreso($this->controlador, '0', $empresa ,$finicio, $factual);
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
      $boton = '';
      $boton .= '<a onclick="visualizar(' . $value->id . ')" class="btn btn-default btn-sm" title="Visualizar"><i class="fa fa-eye"></i></a> ';
      $boton .= '<a onclick="imprimir(' . $value->id . ')" class="btn btn-danger btn-sm" title="Imprimir"><i class="fa fa-print"></i></a> ';

      if ($value->tipoingreso == 'COMPRA') {
        $datacompra = $this->Controlador_model->get($value->compra, "compra");
        $dataTipoIngreso = $value->tipoingreso . ": " . $datacompra->serie . " - " . $datacompra->numero;
      } else {
        $dataTipoIngreso = $value->tipoingreso;
      }

      $data[] = array(
        $key + 1,
        $value->codigo,
        $empresa->ruc." | ".$empresa->serie." | ".$empresa->nombre,
        $dataTipoIngreso,
        $usuario ? $usuario->nombre : '',
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
      $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
      //add variables for action
      $boton = '';
      //add html fodr action
      $boton .= '<a class="btn btn-info btn-sm" href="' . $this->url . '/actualizar/' . $value->id . '" title="Modificar"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-danger btn-sm" onclick="borrar(' . $value->id . ')" title="Borrar"><i class="fa fa-trash"></i></a> ';
      $data[] = array(
        $key + 1,
        $value->codigo,
        $empresa ? $empresa->ruc." | ".$empresa->serie." | ".$empresa->nombre : "SIN DATOS",
        $value->tipoingreso,
        $usuario ? $usuario->nombre : '',
        '<span class="label label-warning" style="background:#ffc107; color:#212529">PENDIENTE</span>',
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
    $data['codigo'] = "NI" . $cadena . $numeros;
    $data['correlativo'] = $numeros;
    $data['tipoingreso'] = 'AJUSTE STOCK';
    $data['usuario'] = $this->usuario;
    $data['empresa'] = $this->empresa;
    $data['created'] = date('Y-m-d');
    $insert = $this->Controlador_model->save($this->controlador, $data);
    $CI = &get_instance();
    $CI->session->set_userdata('notaingreso', $insert);
    redirect($this->url);
  }

  public function volver()
  {
    $CI = &get_instance();
    $CI->session->set_userdata('notaingreso', NULL);
    redirect($this->url);
  }

  public function actualizar($id)
  {
    $CI = &get_instance();
    $CI->session->set_userdata('notaingreso', $id);
    redirect($this->url);
  }

  public function botonpedido()
  {
    $data = $this->Controlador_model->get($this->notaingreso, $this->controlador);
    $row = '';
    if ($data) {
      if ($data->estado == '0') {
        $row .= '<a onclick="grabar(' . $data->id . ')" class="btn btn-success" data-toggle="tooltip">GENERAR</a> ';
      } else {
        $row .= '<a onclick="imprimir(' . $data->id . ')" class="btn btn-danger" data-toggle="tooltip"><i class="fa fa-print"></i>  <span class="hidden-xs">IMPRIMIR</span> </a> ';
        $row .= '<a href="' . $this->url . '/crear" class="btn btn-warning" data-toggle="tooltip"><i class="fa fa-plus"></i>  <span class="hidden-xs">NUEVO</span></a> ';
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
    $notasalidadetalle = $this->Controlador_model->getDetalle($id, 'notaingresodetalle');
    if ($data->tipoingreso == 'COMPRA') {
      $datacompra = $this->Controlador_model->get($data->compra, "compra");
      $dataTipoIngreso = $data->tipoingreso . ": " . $datacompra->serie . "-" . $datacompra->numero;
    } else {
      $dataTipoIngreso = $data->tipoingreso;
    }
    $ticket = '';
    $ticket .= '
    <div class="row">
      <div class="col-lg-6">
          <h4 class="text-left">'
      . $empresa->razonsocial . '
          </h4>
          <b>NOMBRE COMERCIAL:</b> ' . $empresa->nombre . '</br>
          <b>RUC:</b> ' . $empresa->ruc . '</br>
          <b>SERIE:</b> ' . $empresa->serie . '</br>
          <b>DIRECCION:</b> ' . $empresa->direccion . '</br>
          <b>TELEFONO:</b> ' . $empresa->telefono . '
      </div>
      <div class="col-lg-6">
        <h4 class="text-right">' . $dataTipoIngreso . ' <br># ' . $data->codigo . '</h4>
      </div>
    </div>

    <div class="row" style="margin-top:15px; margin-bottom:15px">
    <div class="col-md-12">
    <h4>SOLICITANTE</h4>
     <b>Nombre: </b>' . $cliente->nombre . ' ' . $cliente->apellido . '
    </div>
    </div>

    <div class="row">
    <div class="col-md-12">
    <div class="table-responsive">
    <table id="tabla-ingreso" class="table table-bordered table-striped">
    <thead>
    <tr>
    <th>#</th>
    <th>Codigo</th>
    <th>Destino</th>
    <th>Producto</th>
    <th>Precio</th>
    <th>Total items</th>
    <th>Sub-total</th>
    </tr>
    </thead>
    <tbody>';
    $total = 0;
    $i = 0;
    foreach ($notasalidadetalle as $value) {
      $i++;
      $producto  = $this->Controlador_model->get($value->producto, 'producto');
      $categoria = $this->Controlador_model->get($producto->categoria, 'productocategoria');
      $destino   = $this->Controlador_model->get($value->almacen, 'almacen');
      $total += $value->subtotal;
      $ticket .= '
      <tr>
        <td>' . $i . '</td>
        <td>' . $producto->codigo . '</td>
        <td>' . $destino->nombre . '</td>
        <td>' . $producto->nombre . ' ' . ($categoria ? $categoria->nombre : '') . '</td>
        <td>' . $value->precio . '</td>
        <td>' . $value->cantidaditem . '</td>
        <td class="right">' . number_format($value->subtotal, 2) . '</td>
      </tr>';
    }
    $ticket .= '
    <tfoot>
      <tr>
        <td colspan="6" align="right">
          <strong>Total:</strong>
          </td>
          <td align="right">
          <strong>' . number_format($total, 2) . '</strong>
        </td>
      </tr>
    </tfoot>
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

  public function imprimir($id)
  {
    $ticket = '<embed src="' . $this->url . '/notaingresopdf/' . $id . '" type="application/pdf" width="100%" height="400"></embed>';
    echo $ticket;
  }

  public function notaingresopdf($id)
  {
    $notaingreso = $this->Controlador_model->get($id, $this->controlador);
    $notaingresodetalle = $this->Controlador_model->getDetalle($id, 'notaingresodetalle');
    $data = array(
      'data' => $notaingreso,
      'letras' => num_to_letras($notaingreso->montototal),
      'datas' => $notaingresodetalle,
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
    
    $totalRegistros = $this->Controlador_model->contador($this->notaingreso);
    if($totalRegistros == 0){
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

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_update()
  {
    $this->_validate();
    $data['usuario'] = $this->input->post('usuario');
    $totalRegistros = $this->Controlador_model->contador($this->notaingreso);
    if($totalRegistros == 0){
      $data['empresa'] = $this->input->post('empresa');
    }
    $this->Controlador_model->update(array('id' => $this->notaingreso), $data, $this->controlador);
    echo json_encode(array("status" => TRUE));
    
  }

  public function ajax_list_detalle()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where('notaingreso', $this->notaingreso)->get('notaingresodetalle')->result();
    $compra = $this->Controlador_model->get($this->notaingreso, $this->controlador);
    $data = [];

    foreach ($query as $key => $value) {
      $no = $key + 1;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $destino = $this->Controlador_model->get($value->almacen, 'almacen');
      //$marca = $this->Controlador_model->get($producto->categoria, 'productocategoria');
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

      if ($value->medida == "PAQUETE") {
        $medidacantidad = " : " . $value->medidacantidad;
      } else {
        $medidacantidad = "";
      }

      if ($value->lote) {
        $querylote = $this->Controlador_model->get($value->lote, "lote");
        $dataLote = $querylote->lote;
      } else {
        $dataLote = "S/L";
      }

      $data[] = array(
        $no . $campohidden,
        $destino->nombre,
        $producto->codigo,
        $value->nombre,
        $dataLote,
        $value->medida . $medidacantidad,
        $value->precio,
        $value->cantidad,
        $value->totalitem,
        $value->subtotal,
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
    //$detallelist = $this->Controlador_model->detalleduplicado($this->notaingreso, $this->input->post('producto'));
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
    $compras = $this->Controlador_model->get($this->notaingreso, $this->controlador);

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
    $data['notaingreso'] = $this->notaingreso;
    $data['nombre'] = $producto->nombre;
    $data['medida'] = $this->input->post('tipocantidad');
    $data['medidacantidad'] =  $medidacantidad;
    $data['cantidaditem'] = $totalCantidad;
    $data['cantidaditemregalo'] = 0;
    $data['totalitem'] = 0 + $totalCantidad;
    $data['almacen'] = $this->input->post('almacen');
    $data['lote'] = $this->input->post('lote') ? $this->input->post('lote') : NULL;

    $data['precio'] = $preciocompra;
    $data['cantidad'] = $this->input->post('cantidad');
    $data['subtotal'] = $totalIngreso;


    if ($this->Controlador_model->save('notaingresodetalle', $data)) {
      $compra['montototal'] = $compras->montototal + $totalIngreso;
      $this->Controlador_model->update(array('id' => $this->notaingreso), $compra, 'notaingreso');
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_deletedetalle($id)
  {
    $detalle = $this->Controlador_model->get($id, 'notaingresodetalle');
    $compra = $this->Controlador_model->get($detalle->notaingreso, $this->controlador);
    if ($this->Controlador_model->delete_by_id($id, 'notaingresodetalle')) {
      $data['montototal'] = $compra->montototal - $detalle->subtotal;
      $this->Controlador_model->update(array('id' => $detalle->notaingreso), $data, $this->controlador);
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_updateventa()
  {
    $compra = $this->Controlador_model->get($this->notaingreso, $this->controlador);
    $empresa = $this->Controlador_model->get($compra->empresa, 'empresa');
    $usuario = $this->Controlador_model->get($compra->usuario, 'usuario');
    $contador = $this->Controlador_model->contador($this->notaingreso);
    $data['empresa'] = $compra->empresa;
    $nombreempresa = $empresa->tipo == '0' ? $empresa->nombre : $empresa->razonsocial;
    $data['nombreempresa'] = $empresa->ruc . ' | ' . $nombreempresa;
    $data['usuario'] = $compra->usuario;
    $data['estado'] = $compra->estado;
    $data['contador'] = $contador;
    $data['tipoingreso'] = $compra->tipoingreso;
    $data['nombreusuario'] = $usuario->documento . ' | ' . $usuario->nombre . ' ' . $usuario->apellido;
    $data['codigo'] = $compra->codigo;
    $data['montototal'] = $compra->montototal;
    $data['empresaAlmacen'] = $this->db->where("empresa", $compra->empresa)->get("almacen")->result();
    echo json_encode($data);
  }

  public function ajax_updatecantidad()
  {
    $detalle = $this->Controlador_model->get($this->input->post('detalle'), 'notaingresodetalle');
    $ventas = $this->Controlador_model->get($detalle->notaingreso, $this->controlador);
    $data['cantidad'] = $this->input->post('cantidad');
    $data['subtotal'] = $this->input->post('cantidad') * $detalle->precio;
    if ($this->Controlador_model->update(array('id' => $this->input->post('detalle')), $data, 'notaingresodetalle')) {
      $venta['montototal'] = $ventas->montototal - $detalle->subtotal + ($this->input->post('cantidad') * $detalle->precio);
      $this->Controlador_model->update(array('id' => $detalle->notaingreso), $venta, $this->controlador);
      echo json_encode(array("status" => TRUE));
    }
  }

  private function _validatprocesar()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    $contador = $this->Controlador_model->contador($this->notaingreso);

    if ($contador == 0) {
      $data['inputerror'][] = 'totales';
      $data['error_string'][] = 'Debe agregar productos para poder procesar.';
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
    $notaingreso = $this->Controlador_model->get($this->notaingreso, 'notaingreso');
    $productos = $this->Controlador_model->getDetalle($this->notaingreso, 'notaingresodetalle');
    $hora = date('H:i:s');
    $creado = date('Y-m-d');
    foreach ($productos as $value) {
      $cantidad = $this->Controlador_model->getStock($value->producto, $value->almacen, $value->lote, $notaingreso->empresa);
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $movimiento['empresa'] = $notaingreso->empresa;
      $movimiento['usuario'] = $this->usuario;
      $movimiento['modalidad'] = "ENTRADA";
      $movimiento['tipooperacion'] = "NI";
      $movimiento['notaingreso'] = $this->notaingreso;
      $movimiento['tipo'] = 'ENTRADA';
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
      $movimiento['created'] = $creado;
      $movimiento['hora'] = $hora;
      $this->Controlador_model->save('movimiento', $movimiento);
      
      if ($cantidad) {
        $stockUpdate['cantidad'] = $cantidad->cantidad + $value->cantidaditem;
        $this->db->where('id', $cantidad->id)->update('stock', $stockUpdate);
      } else {
        $stockRegister['producto'] = $value->producto;
        $stockRegister['empresa'] = $notaingreso->empresa;
        $stockRegister['cantidad'] = $value->cantidaditem;
        $stockRegister['almacen'] = $value->almacen;
        $stockRegister['lote'] = $value->lote ? $value->lote : NULL;
        $stockRegister['costopromedio'] = $producto->preciocompra;
        $this->Controlador_model->save('stock', $stockRegister);
      }

    }
    $data['created'] = $creado;
    $data['hora'] = $hora;
    $data['estado'] = '1';
    if ($this->db->where('id', $this->notaingreso)->update($this->controlador, $data)) {
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
      $sheet->setCellValue('E' . $i, isset($cliente->nombre) ? $cliente->nombre : '');
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

  public function completarlote($producto)
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completarlote($q, $producto);
    }
  }

  function ajax_preciosUpdate()
  {
    $query = $this->Controlador_model->get($this->input->post('idproducto'), "producto");
    echo json_encode($query);
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
    $dataUpdate['codigo'] = "NI" . $cadena . $numeros;
    $dataUpdate['correlativo'] = $numeros;
    $dataUpdate["empresa"] = $idempresa;

    $this->Controlador_model->update(["id" => $this->notaingreso],$dataUpdate, "notaingreso");
    $dataNotaIngreso = $this->Controlador_model->get($this->notaingreso, "notaingreso");
    $dataAlmacen = $this->db->where("empresa", $idempresa)->get("almacen")->result();
    echo json_encode(["dataAlmacen" => $dataAlmacen, "codigoActualizado" =>  $dataNotaIngreso->codigo ]);
  }

  
}
