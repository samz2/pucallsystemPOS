<?php

include "validaciondedatos.php";
include "procesarcomprobante.php";
//Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
require __DIR__ . '/ticket/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Guiaremision extends CI_Controller
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
    $this->guiaremision = $this->session->userdata('guiaremision') ? $this->session->userdata('guiaremision') : FALSE;
  }

  public function index()
  {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista,
      'usuarios' => $this->Controlador_model->getAll('usuario'),
      'empresas' => $this->Controlador_model->getAll('empresa'),
      'departamentos' => $this->Controlador_model->getAll('departamento'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  function actualizacionubigeo()
  {
    //? ACTUALIZACION DE CODIGO DE PROVINCIAS
    /*  $departamentos = $this->db->order_by("codigo", "ASC")->get("departamento")->result();
    foreach($departamentos as $departamento){
      $queryProvincia = $this->db->where("departamento", $departamento->id)->order_by('id', 'ASC')->get("provincia");
      foreach($queryProvincia->result() as $key => $provincia){
        $codigo = $key + 1;
        $updateCodigo["codigo"] = $codigo <= 9 ? "0".$codigo : $codigo;
        $this->Controlador_model->update(["id" => $provincia->id], $updateCodigo, "provincia");
      }
    } */
    //? ACTUALIZACION CODIGO DE DISTRITOS
    /* set_time_limit(300000);
    $provincias = $this->db->order_by("codigo", "ASC")->get("provincia")->result();
    foreach($provincias as $provincia){
      $queryDistrito = $this->db->where("provincia", $provincia->id)->order_by('id', 'ASC')->get("distrito")->result();
      foreach($queryDistrito as $key => $provincia){
        $codigo = $key + 1;
        $updateCodigo["codigo"] = $codigo <= 9 ? "0".$codigo : $codigo;
        $this->Controlador_model->update(["id" => $provincia->id], $updateCodigo, "distrito");
      }
    }  */
  }

  public function crear()
  {
    $data['empresa'] = $this->empresa;
    $data['usuario'] = $this->usuario;
    $data['modalidadtraslado'] = '02';
    $data['created'] = date('Y-m-d');
    $ultimo = $this->Controlador_model->save($this->controlador, $data);
    $CI = &get_instance();
    $CI->session->set_userdata('guiaremision', $ultimo);
    redirect($this->url);
  }

  public function actualizar($id)
  {
    $CI = &get_instance();
    $CI->session->set_userdata('guiaremision', $id);
    redirect($this->url);
  }

  public function volver()
  {
    $CI = &get_instance();
    $CI->session->set_userdata('guiaremision', NULL);
    redirect($this->url);
  }

  public function ajax_generar()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $finicio = $this->input->get('finicio');
    $factual = $this->input->get('factual');
    $empresa = $this->input->get('empresa');
    $query = $this->db->order_by('id', 'desc')->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'")->where('empresa', $empresa)->where('estado', '1')->get($this->controlador)->result();
    $data = [];
    $estadoRsgister = ["0" => '<span class="label label-default">PENDIENTE</span>', "1" => '<span class="label label-info">GENERADO</span>'];
    foreach ($query as $key => $value) {
      $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
      $usuario = $this->Controlador_model->get($value->usuario, 'usuario');
      $venta = $this->Controlador_model->get($value->venta, 'venta');
      $cliente = $venta ? $this->Controlador_model->get($venta->cliente, 'cliente') : '';
      //add variables for action
      $boton = '';
      $estado = '<td>&nbsp;</td>';
      //add html fodr action
      if ($value->estado == "0") {
        $boton .= '<a class="btn btn-info btn-sm" href="' . $this->url . '/actualizar/' . $value->id . '" title="Actualizar"><i class="fa fa-pencil"></i></a> ';
        $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar(' . $value->id . ')"><i class="fa fa-trash"></i></a> ';
      } else {
        $boton .= '<a onclick="visualizar(' . $value->id . ')" class="btn btn-default btn-sm" title="Visualizar"><i class="fa fa-eye"></i></a> ';
        $boton .= '<a onclick="imprimirguiaremision(' . $value->id . ')" class="btn btn-danger btn-sm" title="Generar"><i class="fa fa-print"></i></a> ';
        $boton .= '<a class="btn btn-info btn-sm" onclick="procesar_documento_electronico(' . $value->id . ')" title="Emitir"><i class="fa fa-upload"></i></a> ';
        $archivo = $empresa->ruc . '-' . '09' . '-' . $value->serie . '-' . $value->numero;
        if ($value->emision === 'soap-env:Client.1032') {
          $boton .= '<a class="btn btn-danger btn-sm" onclick="anular(' . $value->id . ')" title="Anular"><i class="fa fa-buysellads"></i></a> ';
        } else {
          $boton .= '<a target="_blank" class="btn btn-success btn-sm" href="archivos_xml_sunat/cpe_xml/' . ($empresa->tipoproceso == '1' ? 'produccion' : 'beta') . '/' . $empresa->ruc . '/R-' . $archivo . '.XML" title="CDR"><i class="fa fa-briefcase"></i></a> ';
        }
        if ($value->emision === '0' || $value->emision === 'soap-env:Client.1033') {
          $estado = '<td><span class="label label-info">ACEPTADO</span></td>';
        }
        if ($value->emision === 'soap-env:Client.1032') {
          $estado = '<td><span class="label label-danger">RECHAZADO</span></td>';
        }
        if ($value->emision === '' || $value->emision === '0000' || $value->emision === 'soap-env:Client.0130') {
          $estado = '<td>&nbsp;</td>';
        }
      }

      $data[] = array(
        $key + 1,
        $empresa->ruc,
        $venta ? $cliente->nombre : '',
        $value->serie . '-' . $value->numero,
        $venta ? $venta->serie . '-' . $venta->numero : '',
        $estadoRsgister[$value->estado],
        $estado,
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

  public function ajax_pendiente()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $empresa = $this->input->get('empresa');
    $query = $this->db->order_by('id', 'desc')->where('empresa', $empresa)->where('estado', '0')->get($this->controlador)->result();
    $data = [];
    foreach ($query as $key => $value) {
      $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
      $usuario = $this->Controlador_model->get($value->usuario, 'usuario');
      $venta = $this->Controlador_model->get($value->venta, 'venta');
      $cliente = $venta ? $this->Controlador_model->get($venta->cliente, 'cliente') : '';
      //add variables for action
      $boton = '';
      //add html fodr action
      $boton .= '<a class="btn btn-info btn-sm" href="' . $this->url . '/actualizar/' . $value->id . '" title="Actualizar"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar(' . $value->id . ')"><i class="fa fa-trash"></i></a> ';
      $data[] = array(
        $key + 1,
        $empresa->ruc,
        $venta ? $cliente->nombre : '',
        $value->serie . '-' . $value->numero,
        $venta ? $venta->serie . '-' . $venta->numero : '',
        '<span class="label label-default">PENDIENTE</span>',
        "S/D",
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

  public function visualizar($id = FALSE)
  {
    $data = $this->Controlador_model->get($id, $this->controlador);
    $empresa = $this->Controlador_model->get($data->empresa, 'empresa');
    $cliente = $this->Controlador_model->get($data->usuario, 'usuario');
    $notasalidadetalle = $this->Controlador_model->getDetalle($id, 'compradetalle');
    $ticket = '';
    $ticket .= '<div class="clearfix"><div class="pull-left"><h4 class="text-left"><span>' . $empresa->razonsocial . '</span></h4>
    <span><b>RUC:</b> ' . $empresa->ruc . '</br><b>Direccion:</b> ' . $empresa->direccion . '</span></br>
    <span><b>Telefono:</b> ' . $empresa->telefono . ' <b>Celular:</b> ' . $empresa->celular . '</span></div><div class="pull-right">
    <h4><br># ' . $data->codigo . '<br></h4></div></div><hr><div class="row"><div class="col-md-12">
    <div class="pull-left m-t-30"><span><h4>SOLICITANTE</h4><span><span><b>Nombre: </b>' . $cliente->nombre . ' ' . $cliente->apellido . '</span><br/>
    <span><b>DNI/RUC: </b>' . ($cliente ? $cliente->dni : '') . '</span><br/><span><b>DIRECCION: </b>' . $cliente->direccion . '</span><br/>
    <span><b>TELEFONO: </b>' . $cliente->telefono . '</span></div><div class="pull-right m-t-30"></div></div></div><div class="m-h-50"></div>
    <div class="row"><div class="col-md-12"><div class="table-responsive"><table class="table table-bordered table-condensed"><thead>
    <tr><th>#</th><th>Codigo</th><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Sub-total</th></tr></thead><tbody>';
    $total = 0;
    $i = 0;
    foreach ($notasalidadetalle as $value) {
      $i++;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $marca = $this->Controlador_model->get($producto->marca, 'marca');
      $total += $value->subtotal;
      $ticket .= '<tr><td>' . $i . '</td><td>' . $producto->codigo . '</td><td>' . $producto->nombre . ' ' . ($marca ? $marca->nombre : '') . '</td>
      <td>' . $value->preciounitario . '</td><td>' . $value->cantidad . '</td><td class="right">' . number_format($value->subtotal, 2) . '</td></tr>';
    }
    $ticket .= '<tr><td colspan="5" align="right"><strong>Total:</strong></td><td align="right">
    <strong>' . number_format($total, 2) . '</strong></td></tr></tbody></table></div></div></div>';
    echo $ticket;
  }

  public function botonpedido()
  {
    $data = $this->Controlador_model->get($this->guiaremision, $this->controlador);
    $row = '';
    if ($data) {
      if ($data->estado == '0') {
        $row .= '<a onclick="salvardatos(' . $data->id . ')" class="btn btn-warning">SALVAR DATOS <i class="fa  fa-pencil-square-o"></i></a> ';
        $row .= '<a onclick="generarGuiaRemision(' . $data->id . ')" class="btn btn-success" data-toggle="tooltip">GENERAR <i class="fa fa-check-circle"></i></a> ';
      } else {
        $row .= '<a onclick="imprimir(' . $data->id . ')" class="btn btn-danger" data-toggle="tooltip"><span class="hidden-xs">IMPRIMIR</span> <i class="fa fa-print"></i></a> ';
        $row .= '<a href="' . $this->url . '/crear" class="btn btn-warning" data-toggle="tooltip">NUEVO <i class="fa fa-plus"></i></a> ';
      }
    }
    echo $row;
  }

  public function completarproducto()
  {
    $data = strtoupper($this->input->post("term"));
    $this->Controlador_model->completarproducto($data, $this->input->post("empresa"));
  }

  public function completarventa()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completarventa($q);
    }
  }

  public function completar_conductores()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completar_conductores($q);
    }
  }

  public function completar_clientesdestinos()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completar_clientesdestinos($q);
    }
  }

  public function completar_vehiculo()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completar_vehiculo($q);
    }
  }

  public function completar_transportistas()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completar_transportistas($q);
    }
  }

  public function completarT()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completarT($q);
    }
  }

  public function ajax_list_detalle()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where('guiaremision', $this->guiaremision)->get('guiaremisiondetalle')->result();
    $datas = $this->Controlador_model->get($this->guiaremision, $this->controlador);
    $data = [];
    foreach ($query as $key => $value) {
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $marca = $this->Controlador_model->get($producto->marca, 'marca');
      $nombremarca = $marca ? $marca->nombre : '';
      //add variables for action
      $boton1 = '';
      $campo1 = '';
      //add html fodr action
      if ($datas->estado == '0') {
        $boton1 = '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrardetalle(' . "'" . $value->id . "'" . ')"><i class="fa fa-trash"></i></a>';
        $campo1 = '<input type="text" size="2" class="form-control text-center money" id="cantidad' . ($key + 1) . '" name="cantidad"
       onkeydown="if(event.keyCode == 13) { cambiarcantidad(' . ($key + 1) . ') }" value="' . $value->cantidad . '" autocomplete="off">';
      } else {
        $campo1 = $value->cantidad;
      }
      $data[] = array(
        ($key + 1),
        $producto->codigo,
        $producto->nombre,
        $value->medida . " [$value->medidacantidad]",
        $value->cantidad,
        $value->cantidaditem,
        $boton1
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
    $dataGuiaremision = $this->Controlador_model->get($this->guiaremision, "guiaremision");
    $stock = $this->Controlador_model->getStock($this->input->post('producto'), $this->input->post("almacen"), $this->input->post("lote"), $dataGuiaremision->empresa);
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

    if ($this->input->post('cantidad') == '') {
      $data['inputerror'][] = 'cantidad';
      $data['error_string'][] = 'Este campo es obligatorio.';
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

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_adddetalle()
  {
    $this->_validatedetalle();
    $producto = $this->Controlador_model->get($this->input->post('producto'), 'producto');
    $quiaremision = $this->Controlador_model->get($this->guiaremision, $this->controlador);
    if ($this->input->post('tipocantidad') == "PAQUETE") {
      $totalCantidad = $this->input->post('paquete') * $this->input->post('cantidad');
      $preciocompra = $producto->preciocompra;
      $totalIngreso =  $preciocompra * $this->input->post('cantidad');
      $medidacantidad =  $this->input->post('paquete');
    } else {
      $totalCantidad = $this->input->post('cantidad');
      $preciocompra = $producto->preciocomprapaquete;
      $totalIngreso =  $preciocompra * $totalCantidad;
      $medidacantidad =  1;
    }
    $data['producto'] = $this->input->post('producto');
    $data['guiaremision'] = $this->guiaremision;
    $data['nombre'] = $producto->nombre;
    $data['medida'] = $this->input->post('tipocantidad');
    $data['medidacantidad'] =  $medidacantidad;
    $data['cantidaditem'] = $totalCantidad;
    $data['almacen'] = $this->input->post('almacen');
    $data['precio'] = $preciocompra;
    $data['cantidad'] = $this->input->post('cantidad');
    $data['subtotal'] = $totalIngreso;
    if ($this->Controlador_model->save('guiaremisiondetalle', $data)) {
      $dataguiaremision['monto'] = $quiaremision->monto + $totalIngreso;
      $this->Controlador_model->update(array('id' => $this->guiaremision), $dataguiaremision, 'guiaremision');
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_deletedetalle($id)
  {
    if ($this->Controlador_model->delete_by_id($id, 'guiaremisiondetalle')) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_updateventa()
  {
    $datas = $this->Controlador_model->get($this->guiaremision, $this->controlador);
    $contador = $this->Controlador_model->contador($this->guiaremision);
    $empresa = $this->Controlador_model->get($datas->empresa, 'empresa');
    $venta = $this->Controlador_model->get($datas->venta, 'venta');
    $transportepublico = $this->Controlador_model->get($datas->transportistapublico, 'transportepublico');
    $vehiculo = $this->Controlador_model->get($datas->vehiculo_transporteprivado, 'transporteprivado');
    $conductor = $this->Controlador_model->get($datas->conductor_transporteprivado, 'transporteprivado');
    $clientedestino = $this->Controlador_model->get($datas->destino_cliente, 'cliente');
    $cliente = $venta ? $this->Controlador_model->get($venta->cliente, 'cliente') : '';
    $usuario = $this->Controlador_model->get($datas->usuario, 'usuario');
    $data['empresa'] = $datas->empresa;
    $data['motivostraslado'] = $datas->motivostraslado;
    $data['usuarios'] = $usuario ? $usuario->nombre . " | " . $usuario->apellido : "SIN DATOS";
    $data['modalidadtraslado'] = $datas->modalidadtraslado;
    $data['direccionsalida'] = $empresa->direccion;
    $data['numeracion'] = $datas->serie . '-' . $datas->numero;
    $data['estado'] = $datas->estado;
    $data['perfil'] = $this->perfil;
    $data['contador'] = $contador;
    $data['razonsocial'] = $empresa->ruc . ' | ' . $empresa->razonsocial . ' ' . $empresa->nombre;
    $data['nombrecliente'] = $venta ? $cliente->documento . ' | ' . $cliente->nombre . ' ' . $cliente->apellido : '';
    $data['venta'] = $venta ? $venta->id : '';
    $data['nombrecompleto'] = $venta ? $venta->tipoventa . ' | ' . $venta->serie . '-' . $venta->numero . ' | ' . $venta->created : '';
    $data['transportista'] = $transportepublico ? $transportepublico->id : '';
    $data['transportistas'] = $transportepublico ? $transportepublico->documento . ' | ' . $transportepublico->razonsocial : '';
    $data['vehiculo'] =  $vehiculo ? $vehiculo->id  : '';
    $data['vehiculos'] = $vehiculo ? $vehiculo->tipodocumento . ' | ' . $vehiculo->documento : '';
    $data['conductor'] =  $conductor ? $conductor->id  : '';
    $data['conductores'] = $conductor ? $conductor->tipodocumento . ' | ' . $conductor->documento : '';
    $data['clientedestino'] =  $clientedestino ? $clientedestino->id  : '';
    $data['clientesdestinos'] = $clientedestino ? $clientedestino->tipodocumento . ' | ' . $clientedestino->documento . ' | ' . $clientedestino->nombre . " " . $clientedestino->apellido : '';
    $data['conductores'] = $conductor ? $conductor->tipodocumento . ' | ' . $conductor->documento : '';
    $data['destino_departamento'] = $datas->destino_departamento;
    $data['destino_distrito'] = $datas->destino_distrito;
    $data['destino_provincia'] = $datas->destino_provincia;
    $data['destino_direccion'] = $datas->destino_direccion;
    $data['pesobrutobienes'] = $datas->pesobrutobienes;
    $data['pesobrutobienes'] = $datas->pesobrutobienes;
    $data['empresaAlmacen'] = $this->db->where("empresa", $datas->empresa)->get("almacen")->result();
    echo json_encode($data);
  }

  private function _validate()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    $totalRegistros = $this->Controlador_model->contador($this->guiaremision);
    if ($totalRegistros == 0) {
      if ($this->input->post('empresa') == '') {
        $data['inputerror'][] = 'empresas';
        $data['error_string'][] = 'Este campo es obligatorio.';
        $data['status'] = FALSE;
      }
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_update()
  {
    $this->_validate();
    $totalRegistros = $this->Controlador_model->contador($this->guiaremision);
    if ($totalRegistros == 0) {
      $data['empresa'] = $this->input->post('empresa');
    }

    $data['motivostraslado'] = $this->input->post('motivostraslado');
    $data['modalidadtraslado'] = $this->input->post('modalidadtraslado');
    $data['transportistapublico'] = $this->input->post('transportista') == "" ? NULL : $this->input->post('transportista');
    $data['vehiculo_transporteprivado'] = $this->input->post('vehiculo') == "" ? NULL : $this->input->post('vehiculo');
    $data['conductor_transporteprivado'] = $this->input->post('conductor') == "" ? NULL : $this->input->post('conductor');
    $data['destino_cliente'] = $this->input->post('clientedestino') == "" ? NULL : $this->input->post('clientedestino');
    // $data['direccion'] = $this->input->post('direccion');
    $this->Controlador_model->update(array('id' => $this->guiaremision), $data, $this->controlador);
    echo json_encode(array("status" => TRUE));
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
    $contador = $this->Controlador_model->contador($this->guiaremision);

    if ($contador == 0) {
      $data['inputerror'][] = 'ubigeodestino';
      $data['error_string'][] = 'Debe agregar productos para poder procesar.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('fechatraslado') == '') {
      $data['inputerror'][] = 'fechatraslado';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    } else {
      if ($this->input->post('fechatraslado') < date("Y-m-d")) {
        $data['inputerror'][] = 'fechatraslado';
        $data['error_string'][] = 'Debesa agregar una fecha mayor o igual a la de hoy 🤨';
        $data['status'] = FALSE;
      }
    }

    if ($this->input->post('destino_provincia') == '0') {
      $data['inputerror'][] = 'destino_provincia';
      $data['error_string'][] = 'Este campor es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('destino_departamento') == '0') {
      $data['inputerror'][] = 'destino_departamento';
      $data['error_string'][] = 'Este campor es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('destino_distrito') == '0') {
      $data['inputerror'][] = 'destino_distrito';
      $data['error_string'][] = 'Este campor es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('destino_direccion') == '') {
      $data['inputerror'][] = 'destino_direccion';
      $data['error_string'][] = 'Este campor es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('clientedestino') == '') {
      $data['inputerror'][] = 'clientesdestinos';
      $data['error_string'][] = '';
      $data['status'] = FALSE;
    }

    if ($this->input->post('modalidadtraslado') == "01") {
      //? PUBLICO
      if ($this->input->post('transportista') == '') {
        $data['inputerror'][] = 'transportistas';
        $data['error_string'][] = '';
        $data['status'] = FALSE;
      }
    } else {
      //? PRIVADO
      if ($this->input->post('vehiculo') == "") {
        $data['inputerror'][] = 'vehiculos';
        $data['error_string'][] = 'Este campo es obligatorio.';
        $data['status'] = FALSE;
      }
      if ($this->input->post('conductor') == "") {
        $data['inputerror'][] = 'conductores';
        $data['error_string'][] = 'Este campo es obligatorio.';
        $data['status'] = FALSE;
      }
    }


    if ($this->input->post('pesobrutobienes') == '' or $this->input->post('pesobrutobienes') == 0) {
      $data['inputerror'][] = 'pesobrutobienes';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('pesobrutobienes') == 0) {
      $data['inputerror'][] = 'pesobrutobienes';
      $data['error_string'][] = 'El peso tiene que ser mayor que 0 🤨';
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
    $guiaremision = $this->Controlador_model->get($this->guiaremision, $this->controlador);
    $empresa = $this->Controlador_model->get($guiaremision->empresa, 'empresa');
    $departamento = $this->Controlador_model->get($this->input->post('destino_departamento'), 'depatarmento');
    $provincia = $this->Controlador_model->get($this->input->post('destino_provincia'), 'provincia');
    $distrito = $this->Controlador_model->get($this->input->post('destino_distrito'), 'distrito');
    $numero = $this->Controlador_model->ultimoguiaremision();
    $numeros = $numero ? $numero->consecutivo + 1 : 1;
    $cadena = "";
    for ($i = 0; $i < 6 - strlen($numeros); $i++) {
      $cadena .= '0';
    }
    $data['estado'] = '1';
    $data['serie'] = 'T' . substr($empresa->serie, 1, 3);
    $data['numero'] = $cadena . $numeros;
    $data['consecutivo'] = $numeros;
    $data['destino_cliente'] = $this->input->post('clientedestino');
    $data['destino_direccion'] = $this->input->post('destino_direccion');
    $data['destino_departamento'] = $this->input->post('destino_departamento');
    $data['destino_provincia'] = $this->input->post('destino_provincia');
    $data['destino_distrito'] = $this->input->post('destino_distrito');
    $data['destino_ubigeo'] = $departamento->codigo . $provincia->codigo . $distrito->codigo;
    $data['pesobrutobienes'] = $this->input->post('pesobrutobienes');
    $data['fechatraslado'] = $this->input->post('fechatraslado');
    $data['modalidadtraslado'] = $this->input->post('modalidadtraslado');
    $data['motivostraslado'] = $this->input->post('motivostraslado');
    if ($this->input->post('modalidadtraslado') == "01") {
      //? PUBLICO
      $data['vehiculo_transporteprivado'] = NULL;
      $data['conductor_transporteprivado'] = NULL;
      $data['transportistapublico'] = $this->input->post("transportista");
    } else {
      //? PRIVADO
      $data['vehiculo_transporteprivado'] = $this->input->post('vehiculo');
      $data['conductor_transporteprivado'] = $this->input->post('conductor');
      $data['transportistapublico'] = NULL;
    }
    $data['created'] = date("Y-m-d");
    if ($this->Controlador_model->update(array('id' => $this->guiaremision), $data, $this->controlador)) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_addtransportista()
  {
    $data['modalidadtraslado'] = $this->input->post('modalidadtransporte');
    $data['transportista'] = $this->input->post('datoT');
    $data['vehiculo'] = $this->input->post('vehiculo');
    if ($this->Controlador_model->update(array('id' => $this->guiaremision), $data, $this->controlador)) {
      echo json_encode(array("status" => TRUE, "transportista" => $this->input->post('datosT')));
    }
  }

  public function ajax_delete($id)
  {
    $this->Controlador_model->delete_by_id($id, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }

  public function imprimirguiaremision($id)
  {
    $ticket = '<embed src="' . $this->url . '/guiaremisionpdf/' . $id . '" type="application/pdf" width="100%" height="400"></embed>';
    echo $ticket;
  }

  public function guiaremisionpdf($id)
  {
    $guiaremision = $this->Controlador_model->get($id, $this->controlador);
    $venta = $this->Controlador_model->get($guiaremision->venta, 'venta');
    $modalidadtraslado["01"] = "TRANSPORTE PUBLICO";
    $modalidadtraslado["02"] = "TRANSPORTE PRIVADO";
    $motivoTraslado["01"] = 'VENTA';
    $motivoTraslado["02"] = 'COMPRA';
    $motivoTraslado["04"] = 'TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA';
    $motivoTraslado["08"] = 'IMPORTACION';
    $motivoTraslado["09"] = 'EXPORTACION';
    $resultMotivoTraslado = $guiaremision->motivostraslado == "" ? "OCURRIO UN PROBLEMA A GENERAR LA GUIA" : $motivoTraslado[$guiaremision->motivostraslado];
    $data = array(
      'venta' => $venta,
      'data' => $guiaremision,
      'motivostraslado' => $resultMotivoTraslado,
      'modalidadtraslado' => $modalidadtraslado[$guiaremision->modalidadtraslado],
      'empresa' => $this->Controlador_model->get($guiaremision->empresa, 'empresa'),
      'detalle' => $this->Controlador_model->getDetalle($id, 'guiaremisiondetalle'),
    );
    $this->load->view('/pdf' . $this->controlador, $data);
    $html = $this->output->get_output();
    $this->dompdf->loadHtml($html);
    $this->dompdf->setPaper('A4', 'portrait');
    $this->dompdf->render();
    $this->dompdf->stream("guiaremision.pdf", array("Attachment" => 0));
  }

  public function emitir($id)
  {
    $guia = $this->Controlador_model->get($id, $this->controlador);
    $empresa = $this->Controlador_model->get($guia->empresa, 'empresa');
    $guiadetalle = $this->Controlador_model->getDetalle($id, 'guiaremisiondetalle');
    $ruc_emisor = $empresa->ruc;
    $tipodeproceso = $empresa->tipoproceso;
    $emisor['ruc'] = $ruc_emisor;
    $emisor['tipo_doc'] = "6";
    $emisor['nom_comercial'] = $empresa->nombre;
    $emisor['razon_social'] = $empresa->razonsocial;
    $emisor['codigo_ubigeo'] = $empresa->ubigeo;
    $emisor['direccion'] = $empresa->direccion;
    $emisor['direccion_departamento'] = $empresa->departamento;
    $emisor['direccion_provincia'] = $empresa->provincia;
    $emisor['direccion_distrito'] = $empresa->distrito;
    $emisor['direccion_codigopais'] = 'PE';
    $emisor['usuariosol'] = $tipodeproceso == '1' ? $empresa->usuariosol : 'MODDATOS';
    $emisor['clavesol'] = $tipodeproceso == '1' ? $empresa->clavesol : 'moddatos';
    $emisor['tipoproceso'] = $tipodeproceso;

    $url_base = 'archivos_xml_sunat/';
    $content_folder_xml = 'cpe_xml/';
    $content_firmas = 'certificados/';
    // $tipo_comprobante = $venta->tipoventa == 'FACTURA' ? "01" : "03";
    $tipo_comprobante = "09";
    $archivo = $ruc_emisor . '-' . $tipo_comprobante . '-' . $guia->serie . '-' . $guia->numero;

    if ($tipodeproceso == '1') {
      $ruta = $url_base . $content_folder_xml . 'produccion/' . $ruc_emisor . "/" . $archivo;
      $ruta_cdr = $url_base . $content_folder_xml . 'produccion/' . $ruc_emisor . "/";
      $ruta_firma = $url_base . $content_firmas . 'produccion/' . $ruc_emisor . '.pfx';
      $ruta_ws = 'https://e-guiaremision.sunat.gob.pe/ol-ti-itemision-guia-gem/billService';
      // $ruta_ws = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
      $pass_firma = 'Alienhackgr33n';
    }

    if ($tipodeproceso == '2') {
      $ruta = $url_base . $content_folder_xml . 'homologacion/' . $ruc_emisor . "/" . $archivo;
      $ruta_cdr = $url_base . $content_folder_xml . 'homologacion/' . $ruc_emisor . "/";
      $ruta_firma = $url_base . $content_firmas . 'homologacion/' . $ruc_emisor . '.pfx';
      $ruta_ws = 'https://www.sunat.gob.pe/ol-ti-itcpgem-sqa/billService';
    }

    if ($tipodeproceso == '3') {
      $ruta = $url_base . $content_folder_xml . 'beta/' . $ruc_emisor . "/" . $archivo;
      $ruta_cdr = $url_base . $content_folder_xml . 'beta/' . $ruc_emisor . "/";
      if (file_exists('beta/' . $ruc_emisor . '.pfx')) {
        $ruta_firma = $url_base . $content_firmas . '/' . $ruc_emisor . '.pfx';
      } else {
        $ruta_firma = $url_base . $content_firmas . 'beta/firmabeta.pfx';
        $pass_firma = '123456';
      }
      $ruta_ws = 'https://e-beta.sunat.gob.pe/ol-ti-itemision-guia-gem-beta/billService';
    }

    $rutas = array();
    $rutas['nombre_archivo'] = $archivo;
    $rutas['ruta_xml'] = $ruta;
    $rutas['ruta_cdr'] = $ruta_cdr;
    $rutas['ruta_firma'] = $ruta_firma;
    $rutas['pass_firma'] = $pass_firma;
    $rutas['ruta_ws'] = $ruta_ws;

    $data_comprobante = $this->crear_cabecera($emisor, $guia);

    $procesarcomprobante = new Procesarcomprobante();

    $resp = $procesarcomprobante->procesar_guia_de_remision($data_comprobante, $guiadetalle, $rutas);
    $ventas['id'] = $id;
    $ventas['emision'] = isset($resp['cod_sunat']) ? $resp['cod_sunat'] : NULL;
    $ventas['hash'] = isset($resp['hash_cpe']) ? $resp['hash_cpe'] : NULL;
    $this->Controlador_model->updateguia($ventas);
    echo json_encode($resp);
    exit();
  }

  private function _validatecliente()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    $detallelist = $this->Controlador_model->check($this->input->post('documento'));

    if ($this->input->post('documento') == '') {
      $data['inputerror'][] = 'documento';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('nombre') == '') {
      $data['inputerror'][] = 'nombre';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($detallelist) {
      $data['inputerror'][] = 'documento';
      $data['error_string'][] = 'Este campo ya existe en la base de datos.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_addcliente()
  {
    $this->_validatecliente();
    $data['tipo'] = $this->input->post('tipo');
    $data['documento'] = $this->input->post('documento');
    $data['nombre'] = $this->input->post('nombre');
    $data['apellido'] = $this->input->post('apellido');
    $data['direccion'] = $this->input->post('direccion');
    $data['celular'] = $this->input->post('celular');
    if ($this->Controlador_model->save('cliente', $data)) {
      $ultimo = $this->Controlador_model->ultimo('cliente');
      $venta['transportista'] = $ultimo->id;
      $this->Controlador_model->update(array('id' => $this->guiaremision), $venta, 'guiaremision');
      echo json_encode(array("status" => TRUE));
    }
  }

  function crear_cabecera($emisor, $data)
  {
    // $tipo_comprobante = $data->tipoventa == 'FACTURA' ? "01" : "03";
    $tipo_comprobante = "09";
    $referencia = $this->Controlador_model->get($data->venta, 'venta');
    $codigo_referencia = $referencia->tipoventa == 'FACTURA' ? "01" : "03";
    $fecha = date('Y-m-d');
    $date1 = new DateTime($data->created);
    $date2 = new DateTime($fecha);
    $diff = $date1->diff($date2);
    if ($diff->days > 5) {
      $fechas = date('Y-m-d', strtotime($data->created . '+ ' . ($diff->days - 5) . ' days'));
    } else {
      $fechas = $data->created;
    }

    $cliente = $this->Controlador_model->get($referencia->cliente, 'cliente');
    $transportista = $this->Controlador_model->get($data->transportista, 'cliente');
    $guiaremisiondetalle = $this->Controlador_model->numPaquetes($data->id, 'guiaremisiondetalle');
    $tipo = $cliente->tipo == 'RUC' ? 6 : 1;
    $tipotransportista = $transportista->tipo == 'RUC' ? 6 : 1;
    $cabecera = array(
      'SERIE' => $data->serie,
      'NUMERO' => $data->numero,
      //'FECHA_DOCUMENTO' => $data->created,
      'CODIGO' => $tipo_comprobante,
      'NOTA' => $referencia->serie . '-' . $referencia->numero,
      'SERIE_REFERENCIA' => $referencia->serie,
      'NUMERO_REFERENCIA' => $referencia->numero,
      'CODIGO_REFERENCIA' => $codigo_referencia,
      'CODMOTIVO_TRASLADO' => $data->motivostraslado,
      'MOTIVO_TRASLADO' => "", //? Descripcion del motivo del traslado
      'PESO' => $data->pesobruto, //? peso bruto total de los bienes
      'NUMERO_PAQUETES' => $guiaremisiondetalle->cantidad, //? "no es obligatorio" numero de bultos o pallets
      'TIPO_DOCUMENTO_TRANSPORTE' => ($data->modalidadtraslado == "01") ? $tipotransportista : "",
      'NRO_DOCUMENTO_TRANSPORTE' => ($data->modalidadtraslado == "01") ? $transportista->documento : "",
      'RAZON_SOCIAL_TRANSPORTE' => ($data->modalidadtraslado == "01") ? $transportista->nombre : "", //? apellidos y nombres o denominacion o razon social del transportista
      'FECHA_DOCUMENTO' => $data->fechatraslado, //? fecha de inicio del traslado
      'CODTIPO_TRANSPORTISTA' => $data->modalidadtraslado, //? Modalidad del traslado
      'PLACA' => $data->vehiculo,
      'DNI_CONDUCTOR' => ($data->modalidadtraslado == "02") ? $transportista->documento : "",
      'UBIGEO_DESTINO' => $data->ubigeodestino,
      'DIR_DESTINO' => $data->direcciondestino,
      'UBIGEO_PARTIDA' => $emisor['codigo_ubigeo'],
      'DIR_PARTIDA' => $emisor['direccion'],
      //==============================================
      'NRO_GUIA_REMISION' => "",
      'COD_GUIA_REMISION' => "",
      'NRO_OTR_COMPROBANTE' => "",
      'COD_OTR_COMPROBANTE' => "",
      //==================================================
      'NRO_DOCUMENTO_CLIENTE' => $cliente->documento,
      'RAZON_SOCIAL_CLIENTE' => $cliente->nombre . ' ' . $cliente->apellido,
      //RUC
      'TIPO_DOCUMENTO_CLIENTE' => $tipo,
      'DIRECCION_CLIENTE' => $cliente->direccion,

      'COD_UBIGEO_CLIENTE' => "",
      'DEPARTAMENTO_CLIENTE' => "",
      'PROVINCIA_CLIENTE' => "",
      'DISTRITO_CLIENTE' => "",

      'CIUDAD_CLIENTE' => 'Pucallpa',
      'COD_PAIS_CLIENTE' => 'PE',
      //===============================================
      'NRO_DOCUMENTO_EMPRESA' => $emisor['ruc'],
      'TIPO_DOCUMENTO_EMPRESA' => $emisor['tipo_doc'], //RUC
      'NOMBRE_COMERCIAL_EMPRESA' => $emisor['nom_comercial'],
      'CODIGO_UBIGEO_EMPRESA' => $emisor['codigo_ubigeo'],
      'DIRECCION_EMPRESA' => $emisor['direccion'],
      'DEPARTAMENTO_EMPRESA' => $emisor['direccion_departamento'],
      'PROVINCIA_EMPRESA' => $emisor['direccion_provincia'],
      'DISTRITO_EMPRESA' => $emisor['direccion_distrito'],
      'CODIGO_PAIS_EMPRESA' => $emisor['direccion_codigopais'],
      'RAZON_SOCIAL_EMPRESA' => $emisor['razon_social'],
      'CONTACTO_EMPRESA' => "",
      //===================CLAVES SOL EMISOR====================//
      'EMISOR_RUC' => $emisor['ruc'],
      'EMISOR_USUARIO_SOL' => $emisor['usuariosol'],
      'EMISOR_PASS_SOL' => $emisor['clavesol']
    );

    return $cabecera;
  }

  function ajax_empresaAlmacen($idempresa)
  {
    $dataUpdate["empresa"] = $idempresa;
    $this->Controlador_model->update(["id" => $this->guiaremision], $dataUpdate, "guiaremision");
    $dataAlmacen = $this->db->where("empresa", $idempresa)->get("almacen")->result();
    echo json_encode(["dataAlmacen" => $dataAlmacen]);
  }

  function ajax_salvardatos()
  {
    $data['pesobrutobienes'] = $this->input->post('pesobrutobienes');
    $data['destino_departamento'] = $this->input->post('destino_departamento');
    $data['destino_provincia'] = $this->input->post('destino_provincia');
    $data['destino_distrito'] = $this->input->post('destino_distrito');
    $data['destino_direccion'] = $this->input->post('destino_direccion');
    $data['destino_direccion'] = $this->input->post('destino_direccion');
    $data['fechatraslado'] = $this->input->post('fechatraslado');
    $this->Controlador_model->update(array('id' => $this->guiaremision), $data, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }
  function ajax_departamento_provincia()
  {
    $dataProvincias = $this->db->where("departamento", $this->input->post("destino_departamento"))->order_by("nombre", "ASC")->get("provincia")->result();
    echo json_encode($dataProvincias);
  }
  function ajax_provincia_distrito()
  {
    $dataProvincias = $this->db->where("provincia", $this->input->post("destino_provincia"))->order_by("nombre", "ASC")->get("distrito")->result();
    echo json_encode($dataProvincias);
  }
}
