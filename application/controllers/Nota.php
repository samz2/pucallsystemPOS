<?php

include "validaciondedatos.php";
include "procesarcomprobante.php";
//Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
require __DIR__ . '/ticket/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Nota extends CI_Controller
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
    $this->nota = $this->session->userdata('nota') ? $this->session->userdata('nota') : FALSE;
    $this->caja = $this->session->userdata('caja') ? $this->session->userdata('caja') : FALSE;
  }

  public function index()
  {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista,
      'empresas' => $this->Controlador_model->getAll('empresa'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function ajax_list_generado($finicio, $factual, $empresa)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'")->where('empresa', $empresa);
    $query = $this->db->where('estado <>', '0')->order_by('id', 'desc')->get('nota')->result();
    $data = [];
    $tipo_nota['07'] = 'NOTA DE CREDITO';
    $tipo_nota['08'] = 'NOTA DE DEBITO';
    $estado_comprobante['0'] = '<span class="label label-default">PENDIENTE</span>';
    $estado_comprobante['1'] = '<span class="label label-success">GENERADO</span>';
    $estado_comprobante['2'] = '<span class="label label-danger">ANULADO</span>';
    $estado_sunat[''] = '';
    $estado_sunat['0000'] = '';
    $estado_sunat['soap-env:Client.0130'] = '';
    $estado_sunat['soap-env:Client.0151'] = '';
    $estado_sunat['soap-env:Client.1032'] = '<span class="label label-danger">RECHAZADO</span>';
    $estado_sunat['soap-env:Client.2638'] = '<span class="label label-danger">RECHAZADO</span>';
    $estado_sunat['soap-env:Client.1033'] = '<span class="label label-info">ACEPTADO</span>';
    $estado_sunat['0'] = '<span class="label label-info">ACEPTADO</span>';
    foreach ($query as $key => $value) {
      $venta = $this->Controlador_model->get($value->venta, 'venta');
      $usuario = $this->Controlador_model->get($value->usuario, 'usuario');
      $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
      //add variables for action
      $boton = '';
      $sunat = '';
      //add html fodr action
      $boton .= '<a class="btn btn-default btn-sm" onclick="vernotas(' . $value->id . ')" title="Detalle"><i class="fa fa-ticket"></i></a> ';
      if ($value->estado == '0') {
        $boton .= '<a class="btn btn-warning btn-sm" onclick="imprimir(' . $value->id . ', ' . $empresa->tipoimpresora . ')" title="Imprimir"><i class="fa fa-print"></i></a> ';
      }
      if ($value->emision === '0' || $value->emision === 'soap-env:Client.2638' || $value->emision === 'soap-env:Client.1033') {
        if ($value->emision === 'soap-env:Client.2638') {
          $boton .= '<a class="btn btn-danger btn-sm" onclick="anular(' . $value->id . ')" title="Anular"><i class="fa fa-buysellads"></i></a> ';
        } else {
          $archivo = $empresa->ruc . '-' . $value->tiponota . '-' . $value->serie . '-' . $value->numero;
          $carpeta = $empresa->tipoproceso == '1' ? 'produccion' : 'beta';
          $boton .= '<a target="_blank" class="btn btn-success btn-sm" href="archivos_xml_sunat/cpe_xml/' . $carpeta . '/' . $empresa->ruc . '/R-' . $archivo . '.XML" title="CDR"><i class="fa fa-briefcase"></i></a> ';
        }
      } else {
        $boton .= '<a class="btn btn-info btn-sm" onclick="procesar_documento_electronico(' . $value->id . ')" title="Emitir"><i class="fa fa-upload"></i></a> ';
        $boton .= '<a class="btn btn-danger btn-sm" onclick="anular(' . $value->id . ')" title="Anular"><i class="fa fa-buysellads"></i></a> ';
      }
      $data[] = array(
        $key + 1,
        $tipo_nota[$value->tiponota],
        $value->tipoventa,
        $value->serie . '-' . $value->numero,
        $estado_comprobante[$value->estado],
        $estado_sunat[$value->emision],
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
    //output to json format
    echo json_encode($result);
  }

  public function ajax_list_pendiente($empresa)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->where('empresa', $empresa)->where('estado', '0')->order_by('id', 'desc')->get('nota')->result();
    $data = [];
    $tipo_nota['07'] = 'NOTA DE CREDITO';
    $tipo_nota['08'] = 'NOTA DE DEBITO';
    $estado_comprobante['0'] = '<span class="label label-default">PENDIENTE</span>';
    $estado_comprobante['1'] = '<span class="label label-success">GENERADO</span>';
    $estado_comprobante['2'] = '<span class="label label-danger">ANULADO</span>';
    $estado_sunat[''] = '';
    $estado_sunat['0000'] = '';
    $estado_sunat['soap-env:Client.0130'] = '';
    $estado_sunat['soap-env:Client.0151'] = '';
    $estado_sunat['soap-env:Client.1032'] = '<span class="label label-danger">RECHAZADO</span>';
    $estado_sunat['soap-env:Client.2638'] = '<span class="label label-danger">RECHAZADO</span>';
    $estado_sunat['soap-env:Client.1033'] = '<span class="label label-info">ACEPTADO</span>';
    $estado_sunat['0'] = '<span class="label label-info">ACEPTADO</span>';
    foreach ($query as $key => $value) {
      $venta = $this->Controlador_model->get($value->venta, 'venta');
      $usuario = $this->Controlador_model->get($value->usuario, 'usuario');
      $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
      //add variables for action
      $boton = '';
      $sunat = '';
      //add html fodr action
      $boton .= '<a class="btn btn-info btn-sm" href="' . $this->url . '/actualizar/' . $value->id . '" title="Editar"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-danger btn-sm" onclick="borrar(' . $value->id . ')" title="Detalle"><i class="fa fa-trash"></i></a> ';
      $data[] = array(
        $key + 1,
        $tipo_nota[$value->tiponota],
        $value->tipoventa,
        $value->serie . '-' . $value->numero,
        $estado_comprobante[$value->estado],
        $estado_sunat[$value->emision],
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
    //output to json format
    echo json_encode($result);
  }

  public function crear()
  {
    $data['empresa'] = $this->empresa;
    $data['usuario'] = $this->usuario;
    $data['motivo'] = '01';
    $insert = $this->Controlador_model->save('nota', $data);
    $CI = &get_instance();
    $CI->session->set_userdata('nota', $insert);
    redirect($this->url);
  }

  public function actualizar($id)
  {
    $CI = &get_instance();
    $CI->session->set_userdata('nota', $id);
    redirect($this->url);
  }

  public function volver()
  {
    $CI = &get_instance();
    $CI->session->set_userdata('nota', NULL);
    redirect($this->url);
  }

  public function botonpedido()
  {
    $data = $this->Controlador_model->get($this->nota, 'nota');
    $empresa = $this->Controlador_model->get($data->empresa, 'empresa');
    $row = '';
    if ($data) {
      if ($data->estado == 0) {
        $row .= '<a onclick="grabar()" class="btn btn-success" data-toggle="tooltip" title="GRABAR"><i class="fa fa-apple"></i></a> ';
      }
      if ($data->estado == 1) {
        $row .= '<a href="' . $this->url . '/crear" class="btn btn-warning" data-toggle="tooltip" title="NUEVO"><i class="fa fa-leaf"></i></a> ';
        $row .= '<a onclick="imprimir(' . $data->id . ', ' . $empresa->tipoimpresora . ')" class="btn btn-danger" data-toggle="tooltip" title="IMPRIMIR"><i class="fa fa-print"></i></a> ';
      }
    }
    $row .= '<a onclick="location.reload()" class="btn btn-openid" data-toggle="tooltip" title="RECARGAR"><i class="fa fa-repeat"></i></a> ';
    $row .= '<a href="' . $this->url . '/volver" class="btn btn-default" data-toggle="tooltip" title="VOLVER"><i class="fa fa-arrow-left"></i></a> ';
    echo $row;
  }

  private function _validate()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    /*
    if ($this->input->post('venta') == '') {
      $data['inputerror'][] = 'ventas';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('usuario') == '') {
      $data['inputerror'][] = 'usuarios';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }
    */
    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_update()
  {
    $this->_validate();
    $nota = $this->Controlador_model->get($this->nota, 'nota');
    if ($nota->estado == 1) {
      redirect($this->url);
    }
    $data['empresa'] = $this->input->post('empresa');
    $data['usuario'] = $this->input->post('usuario') == '' ? NULL : $this->input->post('usuario');
    $data['venta'] = $this->input->post('venta') == '' ? NULL : $this->input->post('venta');
    $data['tiponota'] = $this->input->post('tiponota');
    if ($this->input->post('tiponota') == '07') {
      //? Nota de CREDITO
      $data['motivo'] = $this->input->post('motivocredito');
    } else {
      //? Nota de DEBITO
      $data['motivo'] = $this->input->post('motivodebito');
    }
    $this->Controlador_model->update(array('id' => $this->nota), $data, 'nota');
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_add_detalle()
  {
    $nota = $this->Controlador_model->get($this->nota, 'nota');
    if ($nota->estado == 1) {
      redirect($this->url);
    }
    $detalles = $this->Controlador_model->detalleventa($nota->venta);
    $montototal = 0;
    foreach ($detalles as $key => $value) {
      $montototal = $montototal + $value->subtotal;
      $detalle['nota'] = $this->nota;
      $detalle['producto'] = $value->producto;
      $detalle['nombre'] = $value->nombre;
      $detalle['precioventa'] = $value->precio;
      $detalle['cantidad'] = $value->cantidad;
      $this->Controlador_model->save('notadetalle', $detalle);
    }
    $data['montototal'] = $montototal;
    $this->Controlador_model->update(array('id' => $this->nota), $data, 'nota');
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_delete_detalle()
  {
    $nota = $this->Controlador_model->get($this->nota, 'nota');
    if ($nota->estado == 1) {
      redirect($this->url);
    }
    $detalles = $this->Controlador_model->detallenota($this->nota);
    $montototal = 0;
    foreach ($detalles as $key => $value) {
      $montototal = $montototal + ($value->precioventa * $value->cantidad);
      $this->Controlador_model->delete_by_id($value->id, 'notadetalle');
    }
    $data['montototal'] = $nota->montototal - $montototal;
    $this->Controlador_model->update(array('id' => $this->nota), $data, 'nota');
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_list_detalle()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where('nota', $this->nota)->get('notadetalle')->result();
    $venta = $this->Controlador_model->get($this->nota, 'nota');
    $data = [];
    foreach ($query as $key => $value) {
      $no = $key + 1;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $marca = $this->Controlador_model->get($producto->marca, 'marca');
      $descripcion = $producto->nombre . ' ' . ($marca ? $marca->nombre : '') . ' ' . $producto->unidad;
      if ($producto->categoria == 1) {
        $combo = $this->Controlador_model->getCombo($value->producto);
        if ($combo) {
          $descripcion .= '<br><b>DETALLE:</b>';
          foreach ($combo as $values) {
            $productos = $this->Controlador_model->get($values->producto, 'producto');
            $marcas = $this->Controlador_model->get($productos->marca, 'marca');
            $descripcion .= '<br> ' . $productos->nombre . ' ' . ($marcas ? $marcas->nombre : '') . ' ' . $productos->unidad;
          }
        }
      }
      //add variables for action
      $boton = '';
      $campo1 = '';
      $campo2 = '';
      $campohidden = '';
      //add html fodr action

      if ($venta->estado == '0') {
        $campohidden = '<input type="hidden" class="form-control" id="detalle' . $no . '" name="detalle" value="' . $value->id . '">';
        $boton = '<a class="btn btn-sm btn-danger" title="BORRAR" onclick="borrardetalle(' . $value->id . ')"><i class="fa fa-trash"></i></a> ';
        $campo1 = '<input type="text" size="2" class="form-control text-center money" id="cantidad' . $no . '" name="cantidad" onkeydown="if(event.keyCode == 13) { cambiarcantidad(' . $no . ') }" value="' . $value->cantidad . '" autocomplete="off">';
        $campo2 = '<input type="text" size="2" class="form-control text-center money" id="precio' . $no . '" name="precio" onkeydown="if(event.keyCode == 13) { cambiarsubtotal(' . $no . ') }" value="' . $value->precioventa . '" autocomplete="off">';
      }
      if ($venta->estado == '1') {
        $campo1 = $value->cantidad;
        $campo2 = $value->precioventa;
      }
      $data[] = array(
        $no,
        $descripcion,
        $campohidden . $campo2,
        $campo1,
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

  public function ajax_updateventa()
  {
    $datas = $this->Controlador_model->get($this->nota, 'nota');
    $venta = $this->Controlador_model->get($datas->venta, 'venta');
    $contador = $this->Controlador_model->contador($this->nota);
    $empresa = $this->Controlador_model->get($datas->empresa, 'empresa');
    $usuario = $this->Controlador_model->get($datas->usuario, 'usuario');
    $datausuario = $usuario ? $usuario->documento . ' | ' . $usuario->nombre . ' ' . $usuario->apellido : '';
    $datosdeventa = $datas->venta == NULL ? '' : $venta->tipoventa . ' | ' . $venta->serie . '-' . $venta->numero . ' | ' . $venta->created;
    $data['tiponota'] = $datas->tiponota;
    $data['motivo'] = $datas->motivo;
    $data['numeracion'] = $datas->serie . '-' . $datas->numero;
    $data['montototal'] = $datas->montototal;
    $data['estado'] = $datas->estado;
    $data['contador'] = $contador;
    $data['empresa'] = $datas->empresa;
    $data['razonsocial'] = $empresa->ruc . ' | ' . $empresa->razonsocial . ' ' . $empresa->nombre;
    $data['usuario'] = $datas->usuario;
    $data['nombreusuario'] = $datausuario;
    $data['venta'] = $datas->venta;
    $data['nombreventa'] = $datosdeventa;
    echo json_encode($data);
  }

  public function ajax_updatecantidad()
  {
    $detalle = $this->Controlador_model->get($this->input->post('detalle'), 'notadetalle');
    $venta = $this->Controlador_model->get($detalle->nota, 'nota');
    if ($venta->estado == 1) {
      redirect($this->url);
    }
    $data['cantidad'] = $this->input->post('cantidad');
    $subtotal = $this->input->post('cantidad') * $detalle->precioventa;
    if ($this->Controlador_model->update(array('id' => $this->input->post('detalle')), $data, 'notadetalle')) {
      $ventas['montototal'] = $venta->montototal - ($detalle->precioventa * $detalle->cantidad) + $subtotal;
      $this->Controlador_model->update(array('id' => $detalle->nota), $ventas, 'nota');
      echo json_encode(array("status" => TRUE));
    }
  }

  private function _validatenota()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    if ($this->input->post('venta') == '') {
      $data['inputerror'][] = 'ventas';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('descripcion') == '') {
      $data['inputerror'][] = 'descripcion';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_updatesubtotal()
  {
    $detalle = $this->Controlador_model->get($this->input->post('detalle'), 'notadetalle');
    $venta = $this->Controlador_model->get($detalle->nota, 'nota');
    if ($venta->estado == 1) {
      redirect($this->url);
    }
    $data['precioventa'] = $this->input->post('precio');
    $subtotal = $this->input->post('precio') * $detalle->cantidad;
    if ($this->Controlador_model->update(array('id' => $this->input->post('detalle')), $data, 'notadetalle')) {
      $ventas['montototal'] = $venta->montototal - ($detalle->precioventa * $detalle->cantidad) + $subtotal;
      $this->Controlador_model->update(array('id' => $detalle->nota), $ventas, 'nota');
      echo json_encode(array("status" => TRUE));
    }
  }

  /*
  public function ajax_addnota()
  {
    $this->_validatenota();
    $venta = $this->Controlador_model->get($this->input->post('venta'), 'venta');
    $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
    if ($this->input->post('tiponota') == '07') {
      $serie = substr($venta->tipoventa, 0, 1) . 'C' . substr($empresa->serie, 2, 2);
      $numero = $this->Controlador_model->codigos($this->input->post('tiponota'), $venta->tipoventa, $venta->empresa, $serie, 'nota');
      $numeros = $numero ? $numero->consecutivo + 1 : 1;
      $cadena = "";
      for ($i = 0; $i < 6 - strlen($numeros); $i++) {
        $cadena = $cadena . '0';
      }
      $detalle = $this->Controlador_model->getDetalle($this->input->post('venta'), 'ventadetalle');
      foreach ($detalle as $value) {
        $productos = $this->Controlador_model->get($value->producto, 'producto');
        if ($productos->categoria == '1') {
          $combo = $this->Controlador_model->getCombo($value->producto);
          foreach ($combo as $values) {
            $cantidad2 = $value->cantidad * $values->cantidad;
            $stock = $this->Controlador_model->getStock($values->producto, $venta->empresa);
            $producto['cantidad'] = $stock->cantidad + $cantidad2;
            $this->Controlador_model->update(array('id' => $stock->id), $producto, 'stock');
          }
        }
        if ($productos->categoria == '0') {
          $stock = $this->Controlador_model->getStock($value->producto, $venta->empresa);
          $producto['cantidad'] = $stock->cantidad + $value->cantidad;
          $this->Controlador_model->update(array('id' => $stock->id), $producto, 'stock');
        }
      }
      $this->Controlador_model->delete_by_id($this->input->post('venta'), 'movimiento');
      $egreso['empresa'] = $venta->empresa;
      $egreso['usuario'] = $this->usuario;
      $egreso['concepto'] = 5;
      $egreso['venta'] = $this->input->post('venta');
      $egreso['caja'] = $this->session->userdata('caja');
      $egreso['monto'] = $venta->montototal;
      $egreso['observacion'] = 'NOTA DE CREDITO ' . $serie . '-' . $cadena . $numeros;
      $egreso['created'] = date('Y-m-d');
      $this->Controlador_model->save('egreso', $egreso);
      $data['motivo'] = $this->input->post('motivocredito');
      $data['montototal'] = $venta->montototal;
    } else {
      $serie = substr($venta->tipoventa, 0, 1) . 'D' . substr($empresa->serie, 2, 2);
      $numero = $this->Controlador_model->codigos($this->input->post('tiponota'), $venta->tipoventa, $venta->empresa, $serie, 'nota');
      $numeros = $numero ? $numero->consecutivo + 1 : 1;
      $cadena = "";
      for ($i = 0; $i < 6 - strlen($numeros); $i++) {
        $cadena = $cadena . '0';
      }
      $ingreso['empresa'] = $venta->empresa;
      $ingreso['usuario'] = $this->usuario;
      $ingreso['concepto'] = 3;
      $ingreso['venta'] = $this->input->post('venta');
      $ingreso['metodopago'] = $this->input->post('metodopago');
      $ingreso['tipotarjeta'] = $this->input->post('tipotarjeta') ? $this->input->post('tipotarjeta') : '';
      $ingreso['operacion'] = $this->input->post('operacion') ? $this->input->post('operacion') : '';
      $ingreso['monto'] = $venta->montototal;
      $ingreso['observacion'] = 'NOTA DE DEBITO ' . $serie . '-' . $cadena . $numeros;
      $ingreso['created'] = date('Y-m-d');
      $this->Controlador_model->save('ingreso', $ingreso);
      $data['motivo'] = $this->input->post('motivodebito');
      $data['montototal'] = $this->input->post('monto');
    }
    $ventas['modificar'] = '1';
    $this->Controlador_model->update(array('id' => $this->input->post('venta')), $ventas, 'venta');
    $data['empresa'] = $venta->empresa;
    $data['usuario'] = $this->usuario;
    $data['venta'] = $this->input->post('venta');
    $data['tiponota'] = $this->input->post('tiponota');
    $data['tipoventa'] = $venta->tipoventa;
    $data['serie'] = $serie;
    $data['numero'] = $cadena . $numeros;
    $data['consecutivo'] = $numeros;
    $data['descripcion'] = $this->input->post('descripcion');
    $data['created'] = date('Y-m-d');
    $this->Controlador_model->save('nota', $data);
    echo json_encode(array("status" => TRUE));
  }
  */

  public function completarventa()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completarventa($q);
    }
  }

  public function ajax_edit($id)
  {
    $data = $this->Controlador_model->get_by_id($id, $this->controlador);
    echo json_encode($data);
  }

  public function ajax_delete($id)
  {
    if ($this->Controlador_model->delete_by_id($id, $this->controlador)) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function vernotas($id)
  {
    $nota = $this->Controlador_model->get($id, 'nota');
    $sale = $this->Controlador_model->get($nota->venta, 'venta');
    $cliente = $this->Controlador_model->get($sale->cliente, 'cliente');
    $empresa = $this->Controlador_model->get($sale->empresa, 'empresa');
    $posales = $this->Controlador_model->getDetalle($nota->venta, 'ventadetalle');
    $ticket = '';
    $tipo_nota['07'] = 'NOTA DE CREDITO';
    $tipo_nota['08'] = 'NOTA DE DEBITO';
    $notacredito_descripcion['01'] = 'ANULACION DE LA OPERACION';
    $notacredito_descripcion['02'] = 'ANULACION POR ERROR EN EL RUC';
    $notacredito_descripcion['03'] = 'CORRECION POR ERROR EN LA DESCRIPCION';
    $notacredito_descripcion['04'] = 'DESCUENTO GLOBAL';
    $notacredito_descripcion['05'] = 'DESCUENTO POR ITEM';
    $notacredito_descripcion['06'] = 'DEVOLUCION TOTAL';
    $notacredito_descripcion['07'] = 'DEVOLUCION POR ITEM';
    $notacredito_descripcion['08'] = 'BONIFICACION';
    $notacredito_descripcion['09'] = 'DISMINUCION EN EL VALOR';
    $notadebito_descripcion['01'] = 'INTERES POR MORA';
    $notadebito_descripcion['02'] = 'AUMENTO EN EL VALOR';
    $notadebito_descripcion['03'] = 'PENALIDADES';
    $ticket .= '
    <div class="form-group">
    <label class="col-sm-2 control-label" for="nombre">FECHA</label>
    <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $nota->created . '"></div></div>';
    $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">' . $tipo_nota[$nota->tiponota] . '</label>
    <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $nota->serie . '-' . $nota->numero . '"></div></div>';
    $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">MOTIVO</label>
    <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . ($nota->tiponota == '07' ? $notacredito_descripcion[$nota->motivo] : $notadebito_descripcion[$nota->motivo]) . '"></div></div>';
    $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">DESCRIPCION</label>
    <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $nota->descripcion . '"></div></div>';
    $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">DOC. REFERENCIA</label>
    <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $sale->serie . '-' . $sale->numero . '"></div></div>';
    $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">CLIENTE</label>
    <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $cliente->nombre . '"></div></div>';
    $ticket .= '<div class="col-md-12 text-center" style="margin-bottom:8px;">';
    $ticket .= '</div><br>';
    $ticket .= '<div class="col-md-12">
    <table class="table table-bordered table-striped" style="margin-bottom:8px;">
    <thead><tr><th><em>#</em></th><th>Descripcion</th><th>Cant</th><th>Precio</th><th>SubTotal</th></tr></thead><tbody>';
    $i = 1;
    foreach ($posales as $posale) {
      $producto = $this->Controlador_model->get($posale->producto, 'producto');
      $ticket .= '<tr><td align="center">' . $i . '</td><td align="left">' . $posale->nombre . '</td><td align="center">' . $posale->cantidad . '</td><td align="right">' . number_format($posale->precio, 2) . '</td><td align="right">' . number_format($posale->cantidad * $posale->precio, 2) . '</td></tr>';
      $i++;
    }
    // barcode codding type
    $ticket .= '</tbody>
    </table>
    <table class="table table-bordered table-striped" style="margin-bottom:8px;">
    <tbody>
    <tr>
    <td colspan="2" style="text-align:left;">TOTAL</td>
    <td colspan="2" style="text-align:right;">' . number_format($sale->montototal, 2) . ' Soles</td>
    </tr>';
    $ticket .= '</tbody></table></div>';
    echo $ticket;
  }

  public function emitir($id)
  {
    $nota = $this->Controlador_model->get($id, 'nota');
    $empresa = $this->Controlador_model->get($nota->empresa, 'empresa');
    $ventadetalle = $this->Controlador_model->getDetalle($nota->venta, 'ventadetalle');
    $ruc_emisor = $empresa->ruc;
    $archivo = $ruc_emisor . '-' . $nota->tiponota . '-' . $nota->serie . '-' . $nota->numero;
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

    if ($tipodeproceso == '1') {
      $ruta = $url_base . $content_folder_xml . 'produccion/' . $ruc_emisor . "/" . $archivo;
      $ruta_cdr = $url_base . $content_folder_xml . 'produccion/' . $ruc_emisor . "/";
      $ruta_firma = $url_base . $content_firmas . 'produccion/' . $ruc_emisor . '.pfx';
      $ruta_ws = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
      $pass_firma = 'Alienhackgr33n';
    }

    if ($tipodeproceso == '3') {
      $ruta = $url_base . $content_folder_xml . 'beta/' . $ruc_emisor . "/" . $archivo;
      $ruta_cdr = $url_base . $content_folder_xml . 'beta/' . $ruc_emisor . "/";
      $ruta_firma = $url_base . $content_firmas . 'beta/firmabeta.pfx';
      $pass_firma = '123456';
      $ruta_ws = 'https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService';
    }

    $rutas = array();
    $rutas['nombre_archivo'] = $archivo;
    $rutas['ruta_xml'] = $ruta;
    $rutas['ruta_cdr'] = $ruta_cdr;
    $rutas['ruta_firma'] = $ruta_firma;
    $rutas['pass_firma'] = $pass_firma;
    $rutas['ruta_ws'] = $ruta_ws;

    $data_comprobante = $this->crear_cabecera($emisor, $nota);

    $procesarcomprobante = new Procesarcomprobante();

    if ($nota->tiponota == "07") {
      $resp = $procesarcomprobante->procesar_nota_de_credito($data_comprobante, $ventadetalle, $rutas);
    } else {
      $resp = $procesarcomprobante->procesar_nota_de_debito($data_comprobante, $ventadetalle, $rutas);
    }
    $notas['id'] = $id;
    $notas['emision'] = isset($resp['cod_sunat']) ? $resp['cod_sunat'] : '';
    $notas['hash'] = isset($resp['hash_cpe']) ? $resp['hash_cpe'] : '';
    $this->Controlador_model->updatenotas($notas);
    echo json_encode($resp);
    exit();
  }

  function crear_cabecera($emisor, $data)
  {
    $notadebito_descripcion['01'] = 'INTERES POR MORA';
    $notadebito_descripcion['02'] = 'AUMENTO EN EL VALOR';
    $notadebito_descripcion['03'] = 'PENALIDADES';

    $notacredito_descripcion['01'] = 'ANULACION DE LA OPERACION';
    $notacredito_descripcion['02'] = 'ANULACION POR ERROR EN EL RUC';
    $notacredito_descripcion['03'] = 'CORRECION POR ERROR EN LA DESCRIPCION';
    $notacredito_descripcion['04'] = 'DESCUENTO GLOBAL';
    $notacredito_descripcion['05'] = 'DESCUENTO POR ITEM';
    $notacredito_descripcion['06'] = 'DEVOLUCION TOTAL';
    $notacredito_descripcion['07'] = 'DEVOLUCION POR ITEM';
    $notacredito_descripcion['08'] = 'BONIFICACION';
    $notacredito_descripcion['09'] = 'DISMINUCION EN EL VALOR';

    $venta = $this->Controlador_model->get($data->venta, 'venta');
    $numerocomprobante = $venta->serie . '-' . $venta->numero;
    $comprobante = $data->serie . '-' . $data->numero;
    $tipo = $data->tipoventa == 'FACTURA' ? 6 : 1;
    $tipocomprobante = $data->tipoventa == 'FACTURA' ? "01" : "03";

    if ($data->tiponota == '07') { //Nota de Crédito
      $codigo_motivo_modifica = $data->motivo;
      $descripcion_motivo_modifica = $notacredito_descripcion[$data->motivo];
    }
    if ($data->tiponota == '08') { //Nota de Débito
      $codigo_motivo_modifica = $data->motivo;
      $descripcion_motivo_modifica = $notadebito_descripcion[$data->motivo];
    }

    $fecha = date('Y-m-d');
    $date1 = new DateTime($data->created);
    $date2 = new DateTime($fecha);
    $diff = $date1->diff($date2);
    if ($diff->days > 5) {
      $fechas = date('Y-m-d', strtotime($data->created . '+ ' . ($diff->days - 5) . ' days'));
    } else {
      $fechas = $data->created;
    }

    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
    $cabecera = array(
      //pag. 28
      'TIPO_OPERACION' => '0101',
      'TOTAL_GRAVADAS' => "0",
      'TOTAL_INAFECTA' => "0",
      'TOTAL_EXONERADAS' => $data->montototal,
      'TOTAL_GRATUITAS' => "0",
      'TOTAL_PERCEPCIONES' => "0",
      'TOTAL_RETENCIONES' => "0",
      'TOTAL_DETRACCIONES' => "0",
      'TOTAL_BONIFICACIONES' => "0",
      'TOTAL_EXPORTACION' => "0",
      'TOTAL_DESCUENTO' => "0",
      'SUB_TOTAL' => $data->montototal,
      //Porcentaje del impuesto
      'POR_IGV' => "0.00",
      'TOTAL_IGV' => "0",
      'TOTAL_ISC' => "0",
      'TOTAL_OTR_IMP' => "0",
      'TOTAL' => $data->montototal,
      'TOTAL_LETRAS' => num_to_letras($data->montototal),
      //==============================================
      'NRO_GUIA_REMISION' => "",
      'COD_GUIA_REMISION' => "",
      'NRO_OTR_COMPROBANTE' => "",
      'COD_OTR_COMPROBANTE' => "",
      //==============================================
      'TIPO_COMPROBANTE_MODIFICA' => $tipocomprobante,
      'NRO_DOCUMENTO_MODIFICA' => $numerocomprobante,
      'COD_TIPO_MOTIVO' => $codigo_motivo_modifica,
      'DESCRIPCION_MOTIVO' => $descripcion_motivo_modifica,
      //===============================================
      'NRO_COMPROBANTE' => $comprobante,
      'FECHA_DOCUMENTO' => $fechas,
      //'FECHA_DOCUMENTO' => $data->created,
      //pag. 31 //fecha de vencimiento
      'FECHA_VTO' => $fechas,
      //'FECHA_VTO' => $data->created,
      'COD_TIPO_DOCUMENTO' => $data->tiponota,
      'COD_MONEDA' => 'PEN',
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
      //====================INFORMACION PARA ANTICIPO=====================//
      'FLG_ANTICIPO' => "0",
      //====================REGULAR ANTICIPO=====================//
      'FLG_REGU_ANTICIPO' => "0",
      'NRO_COMPROBANTE_REF_ANT' => "",
      'MONEDA_REGU_ANTICIPO' => "",
      'MONTO_REGU_ANTICIPO' => "0",
      'TIPO_DOCUMENTO_EMP_REGU_ANT' => "",
      'NRO_DOCUMENTO_EMP_REGU_ANT' => "",
      //===================CLAVES SOL EMISOR====================//
      'DESCRIPCION_DETALLE' => $data->descripcion,
      'EMISOR_RUC' => $emisor['ruc'],
      'EMISOR_USUARIO_SOL' => $emisor['usuariosol'],
      'EMISOR_PASS_SOL' => $emisor['clavesol']
    );

    return $cabecera;
  }

  public function pdfimprimir($id)
  {
    $ticket = '<embed src="' . $this->url . '/imprimirpdf/' . $id . '" type="application/pdf" width="100%" height="400"></embed>';
    echo $ticket;
  }

  public function imprimirpdf($id)
  {
    $nota = $this->Controlador_model->get($id, 'nota');
    $tipo = $nota->tipoventa == 'BOLETA' ? 1 : 6;
    $comprobante = $nota->serie . '|' . $nota->numero;
    $numerocomprobante = $nota->serie . '-' . $nota->numero;
    $tipocomprobante = $nota->tipoventa == 'FACTURA' ? "01" : "03";
    $venta = $this->Controlador_model->get($nota->venta, 'venta');
    $empresa = $this->Controlador_model->get($nota->empresa, 'empresa');
    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
    $url_base = 'archivos_xml_sunat/imgqr/';
    $encriptado = password_hash($nota->numero, PASSWORD_BCRYPT);
    $encriptadohash = substr(str_replace("$2y$10$", "", $encriptado), 0, 27) . '=';
    $codigohash = $nota->hash ? $nota->hash : $encriptadohash;
    $params['data'] = $empresa->ruc . '|' . $nota->tiponota . '|' . $comprobante . '|0.00|' . $nota->montototal . '|' . date('d/m/Y', strtotime($nota->created)) . '|' . $tipo . '|' . $cliente->documento . '|' . $codigohash . '|';
    $params['level'] = 'H';
    $params['size'] = 10;
    $params['savename'] = $url_base . $numerocomprobante . '.png';
    $this->ciqrcode->generate($params);
    $data = array(
      'nota' => $nota,
      'venta' => $venta,
      'cliente' => $cliente,
      'empresa' => $empresa,
      'codigohash' => $codigohash,
      'qrcode' => $url_base . $numerocomprobante . '.png',
      'contador' => $this->Controlador_model->contador($nota->venta),
      'ventadetalle' => $this->Controlador_model->getDetalle($nota->venta, 'ventadetalle')
    );
    $this->load->view('pdfnotas', $data);
    $html = $this->output->get_output();
    $this->dompdf->loadHtml($html);
    $this->dompdf->setPaper('A4', 'portrait');
    $this->dompdf->render();
    $this->dompdf->stream("comprobante.pdf", array("Attachment" => 0));
  }

  public function cpepdf($id)
  {
    $nota = $this->Controlador_model->get($id, 'nota');
    $tipo = $nota->tipoventa == 'BOLETA' ? 1 : 6;
    $comprobante = $nota->serie . '|' . $nota->numero;
    $numerocomprobante = $nota->serie . '-' . $nota->numero;
    $tipocomprobante = $nota->tipoventa == 'FACTURA' ? "01" : "03";
    $venta = $this->Controlador_model->get($nota->venta, 'venta');
    $empresa = $this->Controlador_model->get($nota->empresa, 'empresa');
    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
    $url_base = 'archivos_xml_sunat/imgqr/';
    $encriptado = password_hash($nota->numero, PASSWORD_BCRYPT);
    $encriptadohash = substr(str_replace("$2y$10$", "", $encriptado), 0, 27) . '=';
    $codigohash = $nota->hash ? $nota->hash : $encriptadohash;
    $params['data'] = $empresa->ruc . '|' . $nota->tiponota . '|' . $comprobante . '|0.00|' . $nota->montototal . '|' . date('d/m/Y', strtotime($nota->created)) . '|' . $tipo . '|' . $cliente->documento . '|' . $codigohash . '|';
    $params['level'] = 'H';
    $params['size'] = 5;
    $params['savename'] = $url_base . $numerocomprobante . '.png';
    $this->ciqrcode->generate($params);
    $data = array(
      'nota' => $nota,
      'venta' => $venta,
      'cliente' => $cliente,
      'empresa' => $empresa,
      'codigohash' => $codigohash,
      'qrcode' => '../../' . $url_base . $numerocomprobante . '.png'
    );
    $this->load->view('imprimirnota', $data);
  }

  function autocompleteusuarios()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->autocompleteusuarios($q);
    }
  }


  public function ajax_addprocesar()
  {
    $this->_validatenota();
    $nota = $this->Controlador_model->get($this->nota, 'nota');
    $venta = $this->Controlador_model->get($nota->venta, 'venta');
    $empresa = $this->Controlador_model->get($nota->empresa, 'empresa');
    if ($nota->tiponota == '07') {
      $serie = substr($venta->tipoventa, 0, 1) . 'C' . substr($empresa->serie, 2, 2);
      $numero = $this->Controlador_model->codigos($nota->tiponota, $venta->tipoventa, $nota->empresa, $serie, 'nota');
      $numeros = $numero ? $numero->consecutivo + 1 : 1;
      $cadena = "";
      for ($i = 0; $i < 6 - strlen($numeros); $i++) {
        $cadena = $cadena . '0';
      }
      $detalle = $this->Controlador_model->detallenota($this->nota);
      foreach ($detalle as $value) {
        $this->ajax_descontar_stock($value->producto, $value->cantidad);
      }
      $this->Controlador_model->delete_by_id($nota->venta, 'movimiento');
      $egreso['empresa'] = $nota->empresa;
      $egreso['usuario'] = $this->usuario;
      $egreso['concepto'] = 5;
      $egreso['caja'] = $this->caja ? $this->caja : NULL;
      $egreso['venta'] = $nota->venta;
      $egreso['montototal'] = $nota->montototal;
      $egreso['montoactual'] = $nota->montototal;
      $egreso['observacion'] = 'NOTA DE CREDITO ' . $serie . '-' . $cadena . $numeros;
      $egreso['created'] = date('Y-m-d');
      $this->Controlador_model->save('egreso', $egreso);
    } else {
      $serie = substr($venta->tipoventa, 0, 1) . 'D' . substr($empresa->serie, 2, 2);
      $numero = $this->Controlador_model->codigos($nota->tiponota, $venta->tipoventa, $nota->empresa, $serie, 'nota');
      $numeros = $numero ? $numero->consecutivo + 1 : 1;
      $cadena = "";
      for ($i = 0; $i < 6 - strlen($numeros); $i++) {
        $cadena = $cadena . '0';
      }
      $ingreso['empresa'] = $nota->empresa;
      $ingreso['usuario'] = $this->usuario;
      $ingreso['concepto'] = 3;
      $ingreso['caja'] = $this->caja;
      $ingreso['venta'] = $nota->venta;
      $ingreso['metodopago'] = $this->input->post('metodopago');
      $ingreso['tipotarjeta'] = $this->input->post('tipotarjeta');
      $ingreso['operacion'] = $this->input->post('operacion');
      $ingreso['monto'] = $nota->montototal;
      $ingreso['observacion'] = 'NOTA DE DEBITO ' . $serie . '-' . $cadena . $numeros;
      $ingreso['created'] = date('Y-m-d');
      $this->Controlador_model->save('ingreso', $ingreso);
    }
    $ventas['modificar'] = '1';
    $this->Controlador_model->update(array('id' => $nota->venta), $ventas, 'venta');
    $data['caja'] = $this->caja;
    $data['tipoventa'] = $venta->tipoventa;
    $data['serie'] = $serie;
    $data['numero'] = $cadena . $numeros;
    $data['consecutivo'] = $numeros;
    $data['descripcion'] = $this->input->post('descripcion');
    $data['estado'] = '1';
    $data['created'] = $this->input->post('fecha');
    $this->Controlador_model->update(array('id' => $this->nota), $data, 'nota');
    echo json_encode(array("status" => TRUE));
  }

  //? Procesos de descontar stock



  public function ajax_descontar_stock($idproducto, $cantidad)
  {
    $dataEmpresa = $this->Controlador_model->get($this->empresa, 'empresa');
    //todo: hacemos el aumento del stock
    $producto = $this->Controlador_model->get($idproducto, 'producto');
    if ($producto->tipo == 0) {
      $stock = $this->Controlador_model->getStockProceso($producto->id, $dataEmpresa->almacen);
      $updateStock = [
        'cantidad' => ($stock->cantidad + $cantidad)
      ];
      $this->Controlador_model->update(['id' => $stock->id], $updateStock, 'stock');
    } else if ($producto->tipo == 2) {
      $combo = $this->Controlador_model->ProductoCombo($producto->id);
      foreach ($combo as $value) {
        $stock = $this->Controlador_model->getStockProceso($value->item_id, $dataEmpresa->almacen);
        $updateStock = [
          'cantidad' => $stock->cantidad + ($value->cantidad * $cantidad)
        ];
        $this->Controlador_model->update(['id' => $stock->id], $updateStock, 'stock');
      }
    } else {
      // el producto no maneja el stock
    }
  }
}
