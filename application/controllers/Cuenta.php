<?php

//Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
require __DIR__ . '/ticket/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Cuenta extends CI_Controller
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
  }

  public function index()
  {
    $data = array(
      'contenido' => $this->vista,
      'datas' => $this->Controlador_model->getCuenta()
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function ShowTicket($id)
  {
    $empresa = $this->Controlador_model->get($this->session->userdata('empresa'), 'empresa');
    $sale = $this->Controlador_model->get($id, 'venta');
    $cliente = $this->Controlador_model->get($sale->cliente, 'cliente');
    $ticket = '<div class="col-md-12"><div class="text-center">' . $empresa->receiptheader . '</div><div style="clear:both;"><h4 class="text-center">Venta Núm:.: ' . $sale->serie . '-' . $sale->numero . '</h4> <div style="clear:both;"></div><span class="float-left">Fecha: ' . date('d-m-Y', strtotime($sale->created)) . '</span><div style="clear:both;"><span class="float-left">Clientes: ' . ($sale->cliente ? $cliente->nombre : 'Cliente sin registrar') . '</span><div style="clear:both;"></div><table class="table" cellspacing="0" border="0"><thead><tr><th><em>#</em></th><th>Producto</th><th>Cantidad</th><th>SubTotal</th></tr></thead><tbody>';
    $i = 1;
    $ventadetalle = $this->Controlador_model->comanda($id);
    foreach ($ventadetalle as $posale) {
      $suma = $this->Controlador_model->sumacomanda($id, $posale->producto);
      $producto = $this->Controlador_model->get($posale->producto, 'producto');
      $ticket .= '<tr><td style="text-align:center; width:30px;">' . $i . '</td><td style="text-align:left; width:180px;">' . $producto->nombre . '</td><td style="text-align:center; width:50px;">' . $suma->cantidad . '</td><td style="text-align:right; width:70px; ">' . number_format(($suma->cantidad * $posale->precio), 2, '.', '') . ' Soles</td></tr>';
      $i++;
    }
    // barcode codding type
    $bcs = 'code128';
    $height = 20;
    $width = 3;
    $ticket .= '</tbody></table><table class="table" cellspacing="0" border="0" style="margin-bottom:8px;"><tbody><tr><td style="text-align:left;">Total Items</td><td style="text-align:right; padding-right:1.5%;">' . $sale->totalitems . '</td><td style="text-align:left; padding-left:1.5%;">Total</td><td style="text-align:right;font-weight:bold;">' . number_format($sale->total, 2, '.', '') . ' Soles</td></tr>';
    $ticket .= '<tr><td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Grand Total</td><td colspan="2" style="border-top:1px dashed #000; padding-top:5px; text-align:right; font-weight:bold;">' . number_format($sale->total, 2, '.', '') . ' Soles</td></tr><tr>';
    $ticket .= '<td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Pagado (Recepción)</td><td colspan="2" style="padding-top:5px; text-align:right; font-weight:bold;">' . number_format($sale->pago, 2, '.', '') . ' Soles</td></tr>';
    $payements = $this->Controlador_model->getSale($id, 'pago');
    if ($payements) {
      foreach ($payements as $pay) {
        $ticket .= '<tr><td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Pago (' . $pay->metodopago . ')</td><td colspan="2" style="padding-top:5px; text-align:right; font-weight:bold;">' . number_format($pay->pago, 2, '.', '') . ' Soles</td></tr>';
      }
    }
    $vuelto = ($sale->pago < $sale->total) ? 0 : ($sale->pago - $sale->total);
    $ticket .= '<tr><td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Vuelto</td><td colspan="2" style="padding-top:5px; text-align:right; font-weight:bold;">' . number_format(($sale->formapago == 'CONTADO' ? $vuelto : 0), 2, '.', '') . ' Soles</td></tr></tbody></table>';
    $ticket .= '<input type="hidden" name="venta" id="venta" value="' . $id . '" /><div style="border-top:1px solid #000; padding-top:10px;"><span class="float-left">' . $empresa->razonsocial . '</span><span class="float-right">Tel. ' . $empresa->telefono . '</span><div style="clear:both;"><center><img style="margin-top:30px" src="' . site_url('inicio/GenerateBarcode/' . $sale->numero . '/' . $bcs . '/' . $height . '/' . $width) . '" alt="' . $sale->id . '" /></center><div class="text-center" style="background-color:#000;padding:5px;width:85%;color:#fff;margin:0 auto;border-radius:3px;margin-top:40px;">' . $empresa->receiptfooter . '</div></div>';

    echo $ticket;
  }

  public function imprimir($id)
  {
    $empresa = $this->Controlador_model->get($this->session->userdata('empresa'), 'empresa');
    $sale = $this->Controlador_model->get($id, 'venta');
    //$ventadetalle = $this->Controlador_model->getVD($id);
    $usuario = $this->Controlador_model->get($sale->usuario, 'usuario');
    $cliente = $this->Controlador_model->get($sale->cliente, 'cliente');
    $mesa = $this->Controlador_model->get($sale->mesa, 'mesa');
    $nombre_impresora = "POS-80";
    $connector = new WindowsPrintConnector($nombre_impresora);
    $printer = new Printer($connector);
    # Mando un numero de respuesta para saber que se conecto correctamente.
    # Vamos a alinear al centro lo próximo que imprimamos
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    /* Intentaremos cargar e imprimir el logo */
    try {
      $url = 'files/Setting/logo.png';
      $logo = EscposImage::load($url, false);
      $printer->bitImage($logo);
    } catch (Exception $e) {
      /*No hacemos nada si hay error*/
    }
    /* Ahora vamos a imprimir un encabezado */
    $printer->text("\n" . $empresa->razonsocial . "\n");
    $printer->text('Direccion: ' . $empresa->direccion . "\n");
    $printer->text("DELIVERY: " . $empresa->telefono . "\n");
    #La fecha también
    date_default_timezone_set("America/Lima");
    $printer->text(date("Y-m-d H:i:s") . "\n");
    $printer->text("----------------------------------------" . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("Tipo Venta: " . $sale->tipoventa . "\n");
    $printer->text("Comprobante: " . $sale->serie . '-' . $sale->numero . "\n");
    $printer->text("----------------------------------------" . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("Mesa: " . $mesa->nombre . "\n");
    $printer->text("Camarera: " . $usuario->nombre . ' ' . $usuario->apellido . "\n");
    $printer->text("Cliente: " .  isset($cliente->nombre) ? $cliente->nombre : 'Cliente sin Registrar' . "\n"); //cambio 22-08-2021
    // $printer->text("Cliente: " . ($pedido->cliente ? $cliente->nombre : 'Cliente sin Registrar') . "\n");
    $printer->text("------------------------------------------" . "\n");
    $printer->text("CANT  DESCRIPCION    PRECIO U.   TOTAL\n");
    $printer->text("------------------------------------------" . "\n");
    /* Ahora vamos a imprimir los productos. Alinear a la izquierda para la cantidad y el nombre */
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $total = 0;
    $ventadetalle = $this->Controlador_model->comanda($id);
    foreach ($ventadetalle as $value) {
      $suma = $this->Controlador_model->sumacomanda($id, $value->producto);
      $total += $value->precio * $suma->cantidad;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $printer->text($producto->nombre . "\n");
      $printer->text($suma->cantidad . " UND.                " . $value->precio . "     " . number_format($value->precio * $suma->cantidad, 2) . " \n");
    }
    /* Terminamos de imprimir los productos, ahora va el total */
    $printer->text("------------------------------------------" . "\n");
    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    $printer->text("TOTAL: S/      " . number_format($total, 2) . "\n");
    /* Podemos poner también un pie de página */
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("comprobante sin valor\n");
    $printer->text("Muchas gracias por su compra\n");
    /* Alimentamos el papel 3 veces */
    $printer->feed(3);
    /* Cortamos el papel. Si nuestra impresora no tiene soporte para ello, no generará ningún error */
    $printer->cut();
    /* Por medio de la impresora mandamos un pulso. Esto es útil cuando la tenemos conectada por ejemplo a un cajón */
    $printer->pulse();
    /* Para imprimir realmente, tenemos que "cerrar" la conexión con la impresora. Recuerda incluir esto al final de todos los archivos */
    $printer->close();
  }

  public function cobrar()
  {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista . 'cobrar',
      'empresas' => $this->Controlador_model->getAll('empresa'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }


  public function ajax_list_generadocobro()
  {
    $finicio = $this->input->post('inicio');
    $factual = $this->input->post('final');
    $tienda = $this->input->post("tienda");
    $usuario = $this->perfil == 1 || $this->perfil == 2 ? FALSE : $this->usuario;
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $creditos = $this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'")->where('empresa', $tienda)->where('formapago', 'CREDITO')->where('estado', '1')->order_by('numero', 'desc')->get('venta');
    $faltantes = $this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'")->where('empresa', $tienda)->where('concepto', 17)->order_by('id', 'desc')->get('egreso');
    $data = [];
    $no = 0;
    $hoy = strtotime(date('Y-m-d'));
    foreach ($creditos->result() as $value) {
      $no++;
      $cliente = $this->Controlador_model->get($value->cliente, 'cliente');
      $boton1 = '';
      $boton2 = '';
      $estado = '';
      //add html fodr action
      $boton1 = '<a class="btn btn-sm btn-primary" title="Cobrar" onclick="verIngreso(' . $value->id . ', 1)"><i class="fa fa-credit-card"></i></a> ';
      $boton2 = '<a class="btn btn-sm btn-default" title="Cobrar" onclick="verPagos(' . $value->id . ', 1)"><i class="fa fa-eye"></i></a> ';
      if ($value->montoactual == 0) {
        $estado = '<td><span class="label label-success">CANCELADO</span></td>';
      }
      if ($value->montoactual > 0 && strtotime($value->created) < $hoy) {
        $estado = '<td><span class="label label-danger">VENCIDO</span></td>';
      }
      if ($value->montoactual > 0 && strtotime($value->created) >= $hoy) {
        $estado = '<td><span class="label label-warning" style="background:#ffc107; color:#212529">PENDIENTE</span></td>';
      }
      $data[] = array(
        $no,
        $value->serie . '-' . $value->numero,
        substr($cliente->nombre . ' ' . $cliente->apellido, 0, 30),
        $value->created,
        $value->vence,
        $estado,
        $value->montototal,
        $value->montoactual,
        $boton1 . $boton2
      );
    }
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $creditos->num_rows(),
      "recordsFiltered" => $creditos->num_rows(),
      "data" => $data
    );
    // echo "la".$empresa;
    echo json_encode($result);
  }

  public function ajax_list_pendientecobro()
  {
    $empresa = $this->uri->segment(3);
    $usuario = $this->perfil == 1 || $this->perfil == 2 ? FALSE : $this->usuario;
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->where('empresa', $empresa)->where('montoactual >', 0)->where('formapago', 'CREDITO')->where('estado', '1')->order_by('numero', 'desc')->get('venta');

    $faltantes = $this->db->where('empresa', $empresa)->where('montoactual >', 0)->order_by('id', 'desc')->get('egreso');
    $data = [];
    $no = 0;
    $hoy = strtotime(date('Y-m-d'));

    /*
    foreach ($faltantes->result() as $value) {
      $no++;
      $usuario = $this->Controlador_model->get($value->usuario, 'usuario');
      $empleado = $this->Controlador_model->get($value->empleado, 'usuario');
      $concepto = $this->Controlador_model->get($value->concepto, 'concepto');
      //add variables for action
      $boton1 = '';
      $boton2 = '';
      $estado = '';
      //add html fodr action
      $boton1 = '<a class="btn btn-sm btn-primary" title="Cobrar" onclick="verIngreso(' . $value->id . ', 0)"><i class="fa fa-credit-card"></i></a> ';
      $boton2 = '<a class="btn btn-sm btn-default" title="Cobrar" onclick="verPagos(' . $value->id . ', 0)"><i class="fa fa-eye"></i></a> ';
      if ($value->montoactual == 0) {
        $estado = '<td><span class="label label-success">CANCELADO</span></td>';
      }
      if ($value->montoactual > 0 && strtotime($value->created) < $hoy) {
        $estado = '<td><span class="label label-danger">VENCIDO</span></td>';
      }
      if ($value->montoactual > 0 && strtotime($value->created) >= $hoy) {
        $estado = '<td><span class="label label-warning">PENDIENTE</span></td>';
      }
      $data[] = array(
        $no,
        $concepto->concepto,
        $empleado ? $empleado->nombre : $usuario->nombre,
        $value->created,
        $estado,
        $value->montototal,
        $value->montoactual,
        $boton1 . $boton2
      );
    }
    */

    foreach ($query->result() as $value) {
      if (strtotime($value->created) < $hoy) {
        $no++;
        $cliente = $this->Controlador_model->get($value->cliente, 'cliente');
        //add variables for action
        $boton1 = '';
        $boton2 = '';
        $estado = '';
        //add html fodr action
        $boton1 = '<a class="btn btn-sm btn-primary" title="Cobrar" onclick="verIngreso(' . $value->id . ', 1)"><i class="fa fa-credit-card"></i></a> ';
        $boton2 = '<a class="btn btn-sm btn-default" title="Cobrar" onclick="verPagos(' . $value->id . ', 1)"><i class="fa fa-eye"></i></a> ';
        if ($value->montoactual == 0) {
          $estado = '<td><span class="label label-success">CANCELADO</span></td>';
        }
        if ($value->montoactual > 0 && strtotime($value->created) < $hoy) {
          $estado = '<td><span class="label label-danger">VENCIDO</span></td>';
        }
        if ($value->montoactual > 0 && strtotime($value->created) >= $hoy) {
          $estado = '<td><span class="label label-warning" style="background:#ffc107; color:#212529">PENDIENTE</span></td>';
        }
        $data[] = array(
          $no,
          $value->serie . '-' . $value->numero,
          substr($cliente->nombre . ' ' . $cliente->apellido, 0, 30),
          $value->created,
          $value->vence,
          $estado,
          $value->montototal,
          $value->montoactual,
          $boton1 . $boton2
        );
      }
    }

    $result = array(
      "draw" => $draw,
      "recordsTotal" => $query->num_rows(),
      "recordsFiltered" => $query->num_rows(),
      "data" => $data
    );
    //output to json format
    echo json_encode($result);
  }

  public function ajax_updatecobro()
  {
    $this->_validatecobro();
    $dataventaquery = $this->Controlador_model->get($this->input->post('id'), "venta");
    $ingreso['tipo'] = $this->input->post("pagorealizado");
    $ingreso['modalidad'] = "CREDITO DE VENTAS";
    $ingreso['metodopago'] = $this->input->post('metodopago');
    $ingreso['tipotarjeta'] = $this->input->post('metodopago') == 'TARJETA' ? $this->input->post('tipotarjeta') : NULL;
    $ingreso['operacion'] = $this->input->post('metodopago') <> 'EFECTIVO' ? $this->input->post('operacion') : NULL;
    $ingreso['monto'] = $this->input->post('pago');
    $montoAcutal = $this->input->post('monto') - $this->input->post('pago');
    $data['montoactual'] = $montoAcutal;
    $pagoTotal = $dataventaquery->pago + $this->input->post('pago');
    $data['pago'] = $pagoTotal;
    $venta = $this->Controlador_model->get($this->input->post('id'), 'venta');
    if ($this->input->post('metodopago') == 'DESCUENTO PLANILLA') {
      $egreso = $this->Controlador_model->get($this->input->post('id'), 'egreso');
      if ($egreso) {
        $concepto = $this->Controlador_model->get($egreso->concepto, 'concepto');
        $ingreso['observacion'] = 'CANCELAR ' . $concepto->concepto . ' ' . $egreso->observacion;
        $ingreso['egreso'] = $this->input->post('id');
        $this->Controlador_model->update(array('id' => $this->input->post('id')), $data, 'egreso');
      } else {
        $ingreso['observacion'] = 'CANCELAR ' . $venta->serie . '-' . $venta->numero;
        $ingreso['venta'] = $this->input->post('id');
        $this->Controlador_model->update(array('id' => $this->input->post('id')), $data, 'venta');
      }
    } else {
      $ingreso['observacion'] = 'CANCELAR ' . $venta->serie . '-' . $venta->numero;
      $ingreso['venta'] = $this->input->post('id');
      $this->Controlador_model->update(array('id' => $this->input->post('id')), $data, 'venta');
    }
    $ingreso['usuario'] = $this->usuario;
    $ingreso['empresa'] = $dataventaquery->empresa;
    $ingreso['caja'] = $this->input->post("pagorealizado") == "CAJA" ? $this->input->post('caja') : NULL;
    $ingreso['concepto'] = 8;
    $ingreso['created'] = date('Y-m-d');
    $ingreso['hora'] = date('H:i:s');
    $insertIngreso = $this->Controlador_model->save('ingreso', $ingreso);
    if ($insertIngreso) {
      $ventaactualizada = $this->Controlador_model->get($this->input->post('id'), 'venta');
      if ($ventaactualizada->montoactual > 0) {
        //? Si aun tiene deuda registramos en ingreso cuanto de deuda aun le queda
        $dataingreso = [
          'restaventa' =>  $ventaactualizada->montoactual
        ];
        $this->Controlador_model->update(array('id' => $insertIngreso), $dataingreso, 'ingreso');
      }
      echo json_encode(array("status" => TRUE));
    }
  }

  private function _validatecobro()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    $dataCaja = $this->Controlador_model->queryCaja($this->input->post('caja'));
    if ($dataCaja->num_rows() == 0 && $this->input->post('pagorealizado') == "CAJA") {
      $data['inputerror'][] = 'caja';
      $data['error_string'][] = 'ಠ_ಠ La caja esta cerrada';
      $data['status'] = FALSE;
    }

    if ($this->input->post('caja') == '0' && $this->input->post('pagorealizado') == "CAJA") {
      $data['inputerror'][] = 'caja';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('pago') == '') {
      $data['inputerror'][] = 'pago';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('pago') > $this->input->post('monto')) {
      $data['inputerror'][] = 'pago';
      $data['error_string'][] = 'El pago no debe ser mayor a la deuda.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }



  public function ingresocredito($id)
  {
    $data = $this->Controlador_model->get_by_id($id, 'venta');
    $data->cajas = $this->db->where("tienda", $data->empresa)->get("cajaprincipal")->result();
    echo json_encode($data);
  }

  public function ajax_list_creditos($id)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->where('venta', $id)->order_by('id', 'desc')->get('ingreso');
    $data = [];
    $no = 0;
    foreach ($query->result() as $value) {
      $no++;
      $botones = '';
      $botones .= '<a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Imprimir" onclick="imprimircomprobante(' . $value->id . ', ' . $value->venta . ')"><i class="fa fa-print"></i></a>';
      $botones .= ' <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="borrarpagos(' . "'" . $value->id . "'" . ')"><i class="fa fa-trash"></i></a>';
      if ($value->tipo == "CAJA") {
        $caja = $this->Controlador_model->get($value->caja, "caja");
        $tipocobro = $caja->descripcion;
      } else {
        $tipocobro = $value->tipo;
      }
      $data[] = array(
        $no,
        $tipocobro,
        $value->metodopago,
        $value->created . " / " . $value->hora,
        $value->monto,
        $value->restaventa,
        $value->observacion,
        $botones
      );
    }
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $query->num_rows(),
      "recordsFiltered" => $query->num_rows(),
      "data" => $data
    );
    //output to json format
    echo json_encode($result);
  }

  public function imprimircomprobante($id, $venta)
  {
    $data = array(
      'ingreso' => $ingreso = $this->Controlador_model->get($id, 'ingreso'),
      'empresa' => $this->Controlador_model->get($this->empresa, 'empresa'),
      'venta' => $venta = $this->Controlador_model->get($venta, 'venta'),
      'cliente' => $this->Controlador_model->get($venta->cliente, 'cliente'),
      'usuario' => $this->Controlador_model->get($venta->usuario_proceso, 'usuario'),
    );

    $this->load->view('imprimircomprobante2', $data);
  }

  public function ajax_deletecobro($id)
  {
    $ingreso = $this->Controlador_model->get($id, 'ingreso');
    if ($ingreso->modalidad == "CUENTAXCOBRAR") {
      $venta = $this->Controlador_model->get($ingreso->venta, 'venta');
      $data['montoactual'] = $venta->montoactual + $ingreso->monto;
      $data['pago'] = $venta->pago - $ingreso->monto;
      $this->Controlador_model->update(array('id' => $ingreso->venta), $data, 'venta');
    }
    $this->Controlador_model->delete_by_id($id, 'ingreso');
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_list_faltantes($id)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->where('egreso', $id)->order_by('id', 'desc')->get('ingreso');
    $data = [];
    $no = 0;
    foreach ($query->result() as $value) {
      $no++;
      //add variables for action
      $boton1 = '';
      //add html for action
      $boton1 = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="borrarpagos(' . "'" . $value->id . "'" . ')"><i class="fa fa-trash"></i></a>';
      $data[] = array(
        $no,
        $value->created,
        $value->monto,
        $value->observacion,
        $boton1
      );
    }
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $query->num_rows(),
      "recordsFiltered" => $query->num_rows(),
      "data" => $data
    );
    //output to json format
    echo json_encode($result);
  }


  public function ingresocobro()
  {
    $empresa = $this->Controlador_model->get($this->session->userdata('empresa'), 'empresa');
    $ventas = $this->Controlador_model->get($this->input->post('venta'), 'venta');
    $numero = $this->Controlador_model->codigos('OTROS', $empresa->serie, 'venta');
    $numeros = (isset($numero->consecutivo) ? $numero->consecutivo : 0) + 1;
    $cadena = "";
    for ($i = 0; $i < 6 - strlen($numeros); $i++) {
      $cadena = $cadena . '0';
    }
    $data['datas'] = $ventas;
    $data['numero'] = $cadena . $numeros;
    $data['venta'] = $this->input->post('venta');
    $this->load->view('/' . $this->controlador . 'cobrar', $data);
  }

  public function cobro()
  {
    $registros = $this->Controlador_model->get($this->input->post('registro'), 'registro');
    $registro = $this->Controlador_model->aperturado($registros->tienda);
    if ($registro) {
      if ($this->form_validation->run($this->controlador)) {
        if ($this->input->post('pago') <= 0 || $this->input->post('pago') > $this->input->post('monto')) {
          mensaje_alerta('error', 'monto');
        } else {
          if ($this->Controlador_model->cobro($registro->id)) {
            mensaje_alerta('hecho', 'crear');
          } else {
            mensaje_alerta('error', 'crear');
          }
        }
        echo json_encode(array("status" => TRUE));
      } else {
        mensaje_alerta('error', 'crear');
        echo json_encode(array("status" => FALSE));
      }
    } else {
      mensaje_alerta('error', 'registro');
      echo json_encode(array("status" => FALSE));
    }
  }

  public function buscarCliente()
  {
    $q = $this->input->get("q");
    $clientesDni = $this->Controlador_model->getClienteDni($q);
    $clientesRuc = $this->Controlador_model->getClienteRuc($q);;
    // var_dump($this->db->last_query());
    $clienteDniJson = [];
    $clienteRucJson = [];
    foreach ($clientesDni as $cliente) {
      $clienteDniJson[] = ['id' => $cliente->id, 'text' => $cliente->documento . ' | ' . $cliente->nombre . ' ' . $cliente->apellido];
    }
    foreach ($clientesRuc as $cliente) {
      $clienteRucJson[] = ['id' => $cliente->id, 'text' => $cliente->documento . ' | ' . $cliente->nombre . ' ' . $cliente->apellido];
    }
    echo json_encode(array(array("text" => "DNI", "children" => $clienteDniJson), array("text" => "RUC", "children" => $clienteRucJson)));
  }

  public function ajax_list_generadocobrocliente()
  {
    $tienda = $this->input->post("tienda");
    $cliente = $this->input->post("cliente");
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    //$creditos = $this->db->query("select vd.*, v.serie, v.numero,c.nombre as clientenombre,v.cliente,c.documento, v.created from venta v inner join ventadetalle vd on vd.venta = v.id  join cliente c on v.cliente = c.id where v.empresa = $empresa and v.formapago = 'CREDITO' and v.cliente = $cliente and v.estado = '1'");
    $creditos = $this->Controlador_model->getCreditos($cliente, $tienda);
    $data = [];
    $no = "";
    $hoy = strtotime(date('Y-m-d'));
    foreach ($creditos as $key =>  $value) {
      /* $nombre_cliente = str_replace(" ", "_", $value->clientenombre);
      $nombre_producto = str_replace(" ", "_", $value->nombre); */
      /* $no = "<input type='checkbox' id='chk_$value->id' class='form-control'  onclick=agregarPago('$value->cliente','$value->id',$value->cantidad,$value->subtotal,'$nombre_cliente','$value->documento','$nombre_producto')>"; */
      // $boton1 = '<a class="btn btn-sm btn-primary" title="seleccionar" onclick="Seleccionar(' . $value->id . ', 1)"><i class="fa fa-credit-card"></i></a> ';
      $estado = '';
      if ($value->estado == "0") {
        $estado = "<label class='label label-danger'>DEUDA</label>";
      } else {
        $estado = "<label class='label label-success'>CANCELADO</label>";
      }
      $cliente = $this->Controlador_model->get($value->cliente, "cliente");
      $botones = '';
      $botones .= '<a class="btn btn-sm btn-default" title="PRODUCTOS" onclick="verProductos(' . $value->id . ')"><i class="fa fa-shopping-cart"></i></a> ';
      if ($value->estado == "0") {
        $botones .= '<button id="btn-cobrar-' . $value->id . '" class="btn btn-sm btn-success" title="COBRAR" onclick="pagarcredito(' . $value->id . ')"><i class="fa fa-money"></i></button> ';
      }
      $tienda = $this->Controlador_model->get($value->tienda, "empresa");
      $comprobamte = $this->Controlador_model->get($value->ventafinal, "venta");
      $data[] = array(
        $no,
        $tienda->ruc." SERIE :".$tienda->serie." | ".$tienda->nombre,
        $value->codigo,
        $cliente->tipodocumento . " | " . $cliente->nombre . " " . $cliente->apellido,
        $value->inicio,
        $value->final <> "0000-00-00" ? $value->final : "",
        $comprobamte ? $comprobamte->serie . "-" . $comprobamte->numero : "",
        $value->montototal,
        /* $value->totalpedido,
        $value->totalitems, */
        $estado,
        $botones
      );
    }
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $start,
      "recordsFiltered" => $length,
      "data" => $data,
    );
    echo json_encode($result);
  }
  public function pagos()
  {
    $datos["empresa"]         = $this->input->post("empresa");
    $datos["usuario_creador"] = $this->usuario;
    $datos["caja"]            = $this->session->userdata('caja');
    $datos["cliente"]         = $this->input->post("cliente");
    $datos["referencia"]      = "";
    $datos["tipoventa"]       = $this->input->post("tipoventa");
    // nueva lógica
    $comprobante = $this->db->where("tipo", $this->input->post("tipoventa"))->where("empresa", $this->input->post("empresa"))->get("comprobante")->row();
    if (isset($comprobante)) {
      $datos["serie"]           = $comprobante->serie;
      $datos["numero"]          = $this->Controlador_model->addLeadingZeros($comprobante->correlativo);
      $datos["consecutivo"]     = (int)$comprobante->correlativo + 1;
      $z["correlativo"]         = $datos["consecutivo"];
      $this->Controlador_model->update(array('id' => $comprobante->id), $z, 'comprobante');
    }

    // fin lógica
    $datos["formapago"]       = "CONTADO";
    $datos["montototal"]      = $this->input->post("subtotal");
    $datos["descuento"]       = $this->input->post("descuento");
    $datos["deudatotal"]      = floatval($this->input->post("subtotal")) - floatval($this->input->post("descuento"));
    $datos["montoactual"]     = 0;
    $datos["pago"]            = $this->input->post("pago");
    $datos["vuelto"]          = $this->input->post("vuelto");
    $cant = 0;
    foreach ($this->input->post("datos") as $fila => $val) {
      $cant += $val["cantidad"];
    }
    $datos["totalitems"]      = $cant;
    $datos["emision"]         = "";
    $datos["hash"]            = "";
    $datos["estado"]          = "1";
    $datos["consumo"]         = "0";
    $datos["dcuenta"]         = "0"; //1 es para cuentas divididas
    // $datos["sound"]           = "1";
    $datos["atender"]         = "1"; //atendido
    $datos["modificar"]       = "0"; // 1 NC
    $datos["hora"]            = date("H:i:s");
    $datos["created"]         = date("Y-m-d");
    $datos["hf_procesado"]    = "";
    $datos["vence"]           = date("Y-m-d");
    $datos["anular_motivo"]    = "";
    try {
      $idVenta = $this->Controlador_model->save("venta", $datos);
      if ($idVenta != 0) {
        foreach ($this->input->post("datos") as $fila => $val) {
          $detVenta["venta"]        = $idVenta;
          $detVenta["tipo"]         = "0";
          $detVenta["producto"]     = $idVenta;
          $detVenta["nombre"]       = $val["nombre"];
          $detVenta["preciocompra"] = "0";
          $detVenta["precio"]       = $val["SubTotal"];
          $detVenta["cantidad"]     = $val["cantidad"];
          $detVenta["subtotal"]     = $val["SubTotal"];
          $detVenta["opcion"]       = "";
          $detVenta["time"]         = $datos["hora"];
          $detVenta["estado"]       = "0";
          $detVenta["estadopago"]   = "1";
          $this->Controlador_model->save("ventadetalle", $detVenta);
        }
        echo "SUCCESS";
      }
    } catch (\Throwable $th) {
      echo $th;
    }
  }

  function pagar()
  {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista . 'pagar',
      'cajas' => $this->Controlador_model->getcaja(),
      'empresas' => $this->Controlador_model->getAll('empresa'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  function ajax_list_generadopago($finicio, $ffin, $tipodeuda, $tienda)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $queryDeudas = $this->Controlador_model->cuentasporpagar($finicio, $ffin, $tipodeuda, $tienda);
    $data = [];
    $no = 0;
    foreach ($queryDeudas as $value) {
      $no++;
      $proveedor = $this->Controlador_model->get($value->proveedor, 'proveedor');
      $botones = '';
      $botones .= '<a class="btn btn-sm btn-primary" title="Cobrar" onclick="pagardeuda(' . $value->id . ', \'' . $value->tipo . '\')"><i class="fa fa-credit-card"></i></a> ';
      $botones .= '<a class="btn btn-sm btn-default" title="Ver Pagos" onclick="verPagos(' . $value->id . ', \'' . $value->tipo . '\')"><i class="fa fa-eye"></i></a> ';
      if ($value->estado_pago == "1") {
        $estado = '<td><span class="label label-success">CANCELADO</span></td>';
      } else if ($value->estado_pago == "0") {
        $estado = '<td><span class="label label-danger" style="background:#ffc107; color:#212529">PENDIENTE</span></td>';
      } else {
        $estado = '<td><span class="label label-default">SIN DATOS</span></td>';
      }
      if ($value->tipo == "FLETE") {
        $pagototal = $value->monto;
        $saldo = $value->montoactual;
        $documento = $value->serie_documento . "-" . $value->numero_documento;
        $dataTienda = $this->Controlador_model->get($value->tienda, 'empresa');
        $tienda = $dataTienda->ruc." | SERIE ".$dataTienda->serie." ".$dataTienda->nombre;
      } else if ($value->tipo == "COMPRA") {
        $documento = $value->serie . "-" . $value->numero;
        $pagototal = $value->montototal;
        $saldo = $value->montoactual;
        $dataTienda = $this->Controlador_model->get($value->empresa, 'empresa');
        $tienda = $dataTienda->ruc." | SERIE ".$dataTienda->serie." ".$dataTienda->nombre;
      } else {
        $documento = "SIN DATOS";
        $pagototal = "SIN DATOS";
        $saldo = "SIN DATOS";
        $tienda = "SIN DATOS";
      }
      $data[] = array(
        $no,
        $tienda,
        $value->tipo,
        $proveedor ? $proveedor->ruc . " | " . $proveedor->nombre : "SIN DATOS",
        $documento,
        $pagototal,
        $saldo,
        $estado,
        $botones
      );
    }
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $start,
      "recordsFiltered" =>  $length,
      "data" => $data
    );
    echo json_encode($result);
  }

  public function ajax_list_pagos_entregados($id, $tipodeuda)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->where('compra', $id)->order_by('id', 'desc')->get('egreso');
    $data = [];
    $no = 0;
    foreach ($query->result() as $value) {
      $no++;
      $boton1 = '';
      $boton1 = "<a class='btn btn-sm btn-danger' href='javascript:void(0)' title='Borrar' onclick='borrarpagos($value->id, $tipodeuda)'><i class='fa fa-trash'></i></a>";
      $data[] = array(
        $no,
        $value->created,
        $value->montototal,
        $value->observacion,
        $boton1
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

  public function ajax_list_pagos($id, $tipodeuda)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->Controlador_model->pagosGenerados($id, $tipodeuda);
    $data = [];
    foreach ($query as $key => $value) {
      $cajaresponsaje = $this->Controlador_model->get($value->caja, 'caja');
      $usuarioresponsaje = $this->Controlador_model->get($value->usuario, 'usuario');
      $botones = '';
      $botones .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="borrarpagos(' . "'" . $value->id . "'" . ')"><i class="fa fa-trash"></i></a>';
      $data[] = array(
        $key + 1,
        ($value->tipo == "CAJA" ? $cajaresponsaje->descripcion : $value->tipo),
        $usuarioresponsaje ? $usuarioresponsaje->usuario : "SIN DATOS",
        $value->tipopago,
        $value->observacion,
        $value->created . " / " . $value->hora,
        $value->montototal,
        $botones
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

  public function ingresopago($id, $tipodeuda, $tienda)
  {
    if ($tipodeuda == "FLETE") {
      $data = $this->Controlador_model->get_by_id($id, 'compracostosadicionales');
    } else {
      $data = $this->Controlador_model->get_by_id($id, 'compra');
    }
    $data->cajas = $this->db->where("tienda", $tienda)->get("cajaprincipal")->result();
    echo json_encode($data);
  }

  private function _validatepago()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    if ($this->input->post('pago') == '') {
      $data['inputerror'][] = 'pago';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('pago') == 0) {
      $data['inputerror'][] = 'pago';
      $data['error_string'][] = 'El pago no debe ser mayor a cero.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('pago') > $this->input->post('monto')) {
      $data['inputerror'][] = 'pago';
      $data['error_string'][] = 'El pago no debe ser mayor a la deuda.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('pagorealizado') == "CAJA" and $this->input->post('caja') == "0") {
      $data['inputerror'][] = 'caja';
      $data['error_string'][] = 'Debe seleccionar una caja pasa registrar el egreso';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_updatepago()
  {
    $this->_validatepago();
    $tipodeudaformulario = $this->input->post('tipodeudaformulario');
    if ($tipodeudaformulario == "FLETE") {
      $datatipodeuda = $this->Controlador_model->get($this->input->post('id'), 'compracostosadicionales');
      $operacionpago = $datatipodeuda->montoactual - $this->input->post('pago');
      $operacionpago == 0 ? $dataflete['estado_pago'] = "1" : $dataflete['estado_pago'] = "0";
      $dataflete['montoactual'] = $operacionpago;
      $this->Controlador_model->update(array('id' => $this->input->post('id')), $dataflete, 'compracostosadicionales');
      $egreso['modalidad'] = "FLETE";
      $egreso['observacion'] = 'CANCELAR FLETE DE LA SERIE: ' . $datatipodeuda->serie_documento . ' CON NUMERO: ' . $datatipodeuda->numero_documento;
      $egreso['flete'] = $this->input->post('id');
      $egreso['empresa'] = $datatipodeuda->tienda;
    } else {
      $datatipodeuda = $this->Controlador_model->get($this->input->post('id'), 'compra');
      $operacionpago = $datatipodeuda->montoactual - $this->input->post('pago');
      $operacionpago == 0 ? $dataCompra['estado_pago'] = "1" : $dataCompra['estado_pago'] = "0";
      $dataCompra['montoactual'] = $operacionpago;
      $this->Controlador_model->update(array('id' => $this->input->post('id')), $dataCompra, 'compra');
      $egreso['modalidad'] = "COMPRA";
      $egreso['observacion'] = 'CANCELAR COMPRA DE LA SERIE: ' . $datatipodeuda->serie . ' CON NUMERO: ' . $datatipodeuda->numero . " Y CON CODIGO INTERNO: " . $datatipodeuda->codigo;
      $egreso['compra'] = $this->input->post('id');
      $egreso['empresa'] = $datatipodeuda->empresa;
    }
    $egreso['tipopago'] = $this->input->post('metodopago');
    $egreso['tipo'] = $this->input->post('pagorealizado');
    $egreso['caja'] = $this->input->post('pagorealizado') == "CAJA" ? $this->input->post('caja') : NULL;
    $egreso['tipotarjeta'] = $this->input->post('metodopago') <> 'EFECTIVO' ? $this->input->post('tipotarjeta') : NULL;
    $egreso['operacion'] = $this->input->post('metodopago') <> 'EFECTIVO' ? $this->input->post('operacion') : NULL;
    $egreso['montototal'] = $this->input->post('pago');
    $egreso['montoactual'] = $operacionpago; // deuda restante para pagar
    $egreso['usuario'] = $this->usuario;
    $egreso['concepto'] = 7;
    $egreso['created'] = date('Y-m-d');
    $egreso['hora'] = date('H:i:s');
    $insert = $this->Controlador_model->save('egreso', $egreso);
    if ($insert) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_deletepagos($id)
  {
    $egreso = $this->Controlador_model->get($id, 'egreso');
    if ($egreso->modalidad == "FLETE") {
      $compracostosadicionales = $this->Controlador_model->get($egreso->flete, 'compracostosadicionales');
      $operacionActualizar = $compracostosadicionales->montoactual + $egreso->montototal;
      $data['montoactual'] = $operacionActualizar;
      $data['estado_pago'] = "0";
      $this->Controlador_model->update(array('id' => $compracostosadicionales->id), $data, 'compracostosadicionales');
    } else {
      $compra = $this->Controlador_model->get($egreso->compra, 'compra');
      $operacionActualizarCompra = $compra->montoactual + $egreso->montototal;
      $dataCompra['montoactual'] = $operacionActualizarCompra;
      $dataCompra['estado_pago'] = "0";
      $this->Controlador_model->update(array('id' => $compra->id), $dataCompra, 'compra');
    }
    $this->Controlador_model->delete_by_id($id, 'egreso');
    echo json_encode(array("status" => TRUE));
  }

  function ajax_list_pendientepago($tienda,$tipodeuda)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $querypedientes = $this->Controlador_model->getPendientes($tipodeuda, $tienda);
    $data = [];
    foreach ($querypedientes as $key => $value) {
      $proveedor = $this->Controlador_model->get($value->proveedor, 'proveedor');
      $botones = '';
      $estado = '';
      $botones .= "<a class='btn btn-sm btn-primary' title='PAGAR' onclick='pagardeuda($value->id, \"$value->tipo\")'><i class='fa fa-credit-card'></i></a> ";
      $botones .= "<a class='btn btn-sm btn-default' title='VER PAGOS' onclick='verPagos($value->id, \"$value->tipo\")'><i class='fa fa-eye'></i></a> ";
      if ($value->estado_pago == "1") {
        $estado = '<td><span class="label label-success">CANCELADO</span></td>';
      } else if ($value->estado_pago == "0") {
        $estado = '<td><span class="label label-danger">PENDIENTE</span></td>';
      } else {
        $estado = '<td><span class="label label-default">SIN DATOS</span></td>';
      }
      if ($value->tipo == "FLETE") {
        $pagototal = $value->monto;
        $saldo = $value->montoactual;
        $documento = $value->serie_documento . "-" . $value->numero_documento;
        $dataTienda = $this->Controlador_model->get($value->tienda, 'empresa');
        $tienda = $dataTienda->ruc." | SERIE ".$dataTienda->serie." ".$dataTienda->nombre;
      } else if ($value->tipo == "COMPRA") {
        $documento = $value->serie . "-" . $value->numero;
        $pagototal = $value->montototal;
        $saldo = $value->montoactual;
        $dataTienda = $this->Controlador_model->get($value->empresa, 'empresa');
        $tienda = $dataTienda->ruc." | SERIE ".$dataTienda->serie." ".$dataTienda->nombre;
      } else {
        $documento = "SIN DATOS";
        $pagototal = "SIN DATOS";
        $saldo = "SIN DATOS";
        $tienda = "";
      }
      $data[] = array(
        $key + 1,
        $tienda,
        $value->tipo,
        $proveedor ? $proveedor->ruc . " | " . $proveedor->nombre : "SIN DATOS",
        $documento,
        $pagototal,
        $saldo,
        $estado,
        $botones
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
  function ajax_detallesproductos($idcredito)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $detalleCredito = $this->db->order_by("id", "DESC")->where("credito", $idcredito)->get("ventadetalle")->result();
    $dataCredito = $this->Controlador_model->get($idcredito, "credito");
    $data = [];
    foreach ($detalleCredito as $key => $value) {
      $botones = '';
      if ($dataCredito->estado == "0") {
        $botones .= "<a class='btn btn-danger btn-sm' title='ELIMINAR' onclick='eliminarItemCredito($value->id)'><i class='fa fa-trash'></i></a>";
      }
      $usuario = $this->Controlador_model->get($value->usuario, "usuario");
      $data[] = array(
        $key + 1,
        $value->created . " / " . $value->time,
        $usuario ? $usuario->usuario : "SIN DATOS",
        $value->tipoprecio,
        $value->nombre,
        $value->precio,
        $value->cantidad,
        $value->subtotal,
        $botones
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

  function ajax_delete_item($idventadetalle)
  {
    $ventaDetalle = $this->Controlador_model->get($idventadetalle, "ventadetalle");
    $creditoData = $this->Controlador_model->get($ventaDetalle->credito, "credito");
    if ($ventaDetalle) {
      //todo: REGISTRO DEL MOVIMIENTO Y ACTUALIZACION DEL STOCK
      if ($ventaDetalle->tipo == "0") {
        $dataProducto = $this->Controlador_model->get($ventaDetalle->producto, 'producto');
        $producto = $ventaDetalle->producto;
        $dataTienda = $this->Controlador_model->get($ventaDetalle->tienda, "empresa");
        if ($dataProducto->tipo == '0') {
          $cantidad = $this->Controlador_model->getStockAlmacen($producto, $dataTienda->almacen, $ventaDetalle->lote, $dataTienda->id);
          $movimiento['empresa'] = $this->empresa;
          $movimiento['modalidad'] = "ENTRADA";
          $movimiento['usuario'] = $this->usuario;
          $movimiento['credito'] = $ventaDetalle->credito;
          $movimiento['tipooperacion'] = "VENTA DE CREDITO AL CLIENTE";
          $movimiento['producto'] = $producto;
          $movimiento['almacen'] = $dataTienda->almacen;
          $movimiento['lote'] =  ($ventaDetalle->lote ? $ventaDetalle->lote : NULL);
          if ($ventaDetalle->variante) {
            $dataVariante = $this->Controlador_model->get($ventaDetalle->variante, "productovariante");
            $totalRestablecer = $dataVariante->cantidad * $ventaDetalle->cantidad;
            $movimiento['medida'] =  $dataVariante->nombre;
            $movimiento['medidacantidad'] = $dataVariante->cantidad;
            $movimiento['cantidaditem'] = $dataVariante->cantidad * $ventaDetalle->cantidad;
            $movimiento['totalitemoperacion'] = $dataVariante->cantidad * $ventaDetalle->cantidad;
          } else {
            $totalRestablecer = $ventaDetalle->cantidad;
            $movimiento['medida'] =  "UNIDAD";
            $movimiento['medidacantidad'] = 1;
            $movimiento['cantidaditem'] = $ventaDetalle->cantidad;
            $movimiento['totalitemoperacion'] = $ventaDetalle->cantidad;
          }
          $movimiento['cantidad'] = $ventaDetalle->cantidad; //? LO QUE REGISTRA
          $movimiento['stockanterior'] = $cantidad ? $cantidad->cantidad : 0;
          $movimiento['tipo'] = 'INGRESO POR ELIMINAR EL PRODUCTO DEL CREDITO AL CLIENTE';
          $movimiento['stockactual'] = ($cantidad ? $cantidad->cantidad : 0) + $totalRestablecer;
          $movimiento['created'] = date('Y-m-d');
          $movimiento['hora'] = date("H:i:s");
          $this->Controlador_model->save('movimiento', $movimiento);
          //todo: ACTUALIZACION DEL STOCK
          $dataRestablecer['cantidad'] = ($cantidad ? $cantidad->cantidad : 0) + $totalRestablecer;;
          $this->Controlador_model->update(array('id' => $cantidad->id), $dataRestablecer, 'stock');
        } else if ($dataProducto->tipo == '2') {
          $combos = $this->db->where('producto',  $producto)->get('combo')->result();
          foreach ($combos as $combo) {
            $stock = $this->Controlador_model->getStockAlmacen($combo->item_id, $dataTienda->almacen, NULL, $dataTienda->id);
            $movimientoCombo['modalidad'] = "ENTRADA";
            $movimientoCombo['empresa'] = $this->empresa;
            $movimientoCombo['usuario'] = $this->usuario;
            $movimientoCombo['credito'] = $ventaDetalle->credito;
            $movimientoCombo['tipooperacion'] = "VENTA DE CREDITO AL CLIENTE";
            $movimientoCombo['producto'] = $combo->item_id;
            $movimientoCombo['productocombo'] = $producto;
            $movimientoCombo['almacen'] = $dataTienda->almacen;
            $movimientoCombo['lote'] =  ($ventaDetalle->lote ? $ventaDetalle->lote : NULL);
            $movimientoCombo['medida'] =  "COMBO";
            $movimientoCombo['medidacantidad'] = $combo->cantidad;
            $movimientoCombo['cantidad'] = $ventaDetalle->cantidad; //? LO QUE REGISTRA
            $movimientoCombo['cantidaditem'] = $combo->cantidad * $ventaDetalle->cantidad;
            $movimientoCombo['totalitemoperacion'] = $combo->cantidad * $ventaDetalle->cantidad;
            $movimientoCombo['stockanterior'] = $stock ? $stock->cantidad : 0;
            $movimientoCombo['tipo'] = 'INGRESO POR ELIMINAR EL PRODUCTO DEL CREDITO AL CLIENTE';
            $movimientoCombo['stockactual'] = ($stock ? $stock->cantidad : 0) + ($combo->cantidad * $ventaDetalle->cantidad);
            $movimientoCombo['created'] = date('Y-m-d');
            $movimientoCombo['hora'] = date("H:i:s");
            $this->Controlador_model->save('movimiento', $movimientoCombo);
            //todo: ACTUALIZACION DEL STOCK
            $dataRestablecerCombo['cantidad'] = ($stock ? $stock->cantidad : 0) + ($combo->cantidad * $ventaDetalle->cantidad);
            $this->Controlador_model->update(array('id' => $stock->id), $dataRestablecerCombo, 'stock');
          }
        }
      }
      //TODO: ACTUALIZACION DEL CREDITO
      $updateCredito["totalitems"] = $creditoData->totalitems - $ventaDetalle->cantidad;
      $updateCredito["totalpedido"] = $creditoData->totalpedido - 1;
      $updateCredito["montototal"] = $creditoData->montototal - $ventaDetalle->subtotal;
      $updateCredito["montoactual"] = $creditoData->montoactual - $ventaDetalle->subtotal;
      $this->Controlador_model->update(["id" => $creditoData->id], $updateCredito, "credito");
      $this->Controlador_model->delete_by_id($idventadetalle, "ventadetalle");
      $repuesta = ["status" => TRUE];
    } else {
      $repuesta = ["status" => FALSE];
    }
    echo json_encode($repuesta);
  }

  private function validateCredito($idcredito)
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    $dataCreditovalidate = $this->Controlador_model->get($idcredito, "credito");
    $totalpagar = $dataCreditovalidate->montoactual - $this->input->post("descuentocredito");
    $dataCliente = $this->Controlador_model->get($dataCreditovalidate->cliente, "cliente");
    if ($this->input->post('cajacredito') == '0') {
      $data['inputerror'][] = 'cajacredito';
      $data['error_string'][] = 'Debe seleccionar una caja para procesar el cobro';
      $data['status'] = FALSE;
    } else {
      $dataCaja = $this->Controlador_model->queryCaja($this->input->post('cajacredito'));
      if ($dataCaja->num_rows() == 0) {
        $data['inputerror'][] = 'cajacredito';
        $data['error_string'][] = 'ಠ_ಠ La caja esta cerrada';
        $data['status'] = FALSE;
      }
    }

    if ($this->input->post('pagocredito') === 0 || $this->input->post('pagocredito') === "0.00") {
      $data['inputerror'][] = 'pagocredito';
      $data['error_string'][] = 'El pago debe ser mayor a cero.';
      $data['status'] = FALSE;
    } else if ($this->input->post('pagocredito') == "") {
      $data['inputerror'][] = 'pagocredito';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('pagocredito') < $totalpagar) {
      $data['inputerror'][] = 'pagocredito';
      $data['error_string'][] = 'El pago debe ser mayor a la deuda.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('tcomprobantecredito') == "FACTURA" && $dataCliente->tipodocumento != "RUC") {
      $data['inputerror'][] = 'tcomprobantecredito';
      $data['error_string'][] = 'No puede usar esta opcion porque el cliente no es RUC';
      $data['status'] = FALSE;
    }



    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  function ajax_procesarCredito($idcredito)
  {
    $this->validateCredito($idcredito);
    $dataCreditoVenta = $this->Controlador_model->get($idcredito, "credito");
    $tienda = $this->Controlador_model->get($this->input->post("tienda_pagar"), "empresa");
    $horaproceso = date("H:i:s");
    $fechaproceso = date("Y-m-d");
    $horafechaproceso = date("Y-m-d H:i:s");
    $serie = substr($this->input->post("tcomprobantecredito"), 0, 1) . substr($tienda->serie, 1, 3);
    $numero = $this->Controlador_model->codigos($this->input->post("tcomprobantecredito"), $serie);
    $numeros = $numero ? $numero->consecutivo + 1 : 1;
    $cadena = "";
    for ($i = 0; $i < 6 - strlen($numeros); $i++) {
      $cadena = $cadena . '0';
    }
    $deudaventa = $dataCreditoVenta->montoactual - $this->input->post("descuentocredito");
    $vuelto = $this->input->post("pagocredito") - $deudaventa;
    $dataVenta["empresa"] = $this->input->post("tienda_pagar");
    $dataVenta["usuario_creador"] = $this->usuario;
    $dataVenta["usuario_proceso"] = $this->usuario;
    $dataVenta["usuario_anulado"] = NULL;
    $dataVenta["caja"] = $this->input->post("cajacredito");
    $dataVenta["cliente"] = $dataCreditoVenta->cliente;
    $dataVenta["tipoventa"] = $this->input->post("tcomprobantecredito");
    $dataVenta['serie'] = $serie;
    $dataVenta['numero'] = $cadena . $numeros;
    $dataVenta['consecutivo'] = $numeros;
    $dataVenta['formapago'] = "CONTADO";
    $dataVenta['montototal'] = $dataCreditoVenta->montoactual;
    $dataVenta['descuento'] = $this->input->post("descuentocredito");
    $dataVenta['deudatotal'] = $deudaventa;
    $dataVenta['montoactual'] = 0;
    $dataVenta['pago'] = $this->input->post("pagocredito");
    $dataVenta['vuelto'] = $vuelto;
    $dataVenta['totalitems'] = $dataCreditoVenta->totalitems;
    $dataVenta['estado'] = "1";
    $dataVenta['atender'] = "1";
    $dataVenta['sound'] = "1";
    $dataVenta['hora'] = $horaproceso;
    $dataVenta['created'] = $fechaproceso;
    $dataVenta['hf_procesado'] = $horafechaproceso;
    $dataVenta['vence'] = NULL;
    $insert = $this->Controlador_model->save("venta", $dataVenta);
    if ($insert) {
      //? REGISTRAMOS EL INGRESO
      $dataIngreso["empresa"] = $this->input->post("tienda_pagar");
      $dataIngreso["usuario"] = $this->usuario;
      $dataIngreso["tipo"] = "CAJA";
      $dataIngreso["modalidad"] = "CREDITO AL CLIENTE";
      $dataIngreso["concepto"] = 8; //? El 8 es cuenta por cobrar
      $dataIngreso["caja"] = $this->input->post("cajacredito");
      $dataIngreso["venta"] = $insert;
      $dataIngreso["metodopago"] =  $this->input->post("metodopagocredito");
      $dataIngreso["tipotarjeta"] =  $this->input->post("metodopagocredito") == "TARJETA" ? $this->input->post("tipotarjetacredito") : NULL;
      $dataIngreso["operacion"] =  $this->input->post("metodopagocredito") <> "EFECTIVO" ? $this->input->post("n_operacioncredito") : NULL;
      $dataIngreso["monto"] = $deudaventa;
      $dataIngreso["observacion"] = "CANCELACION DE CREDITO AL CLIENTE";
      $dataIngreso["created"] = $fechaproceso;
      $dataIngreso["hora"] = $horaproceso;
      $this->Controlador_model->save("ingreso", $dataIngreso);
      //? ACTUALIZAMOS EL CREDITO
      $updateCredito["usuario_proceso"] = $this->usuario;
      $updateCredito["ventafinal"] = $insert;
      $updateCredito["montoactual"] = 0;
      $updateCredito["final"] = $fechaproceso;
      $updateCredito["estado"] = "1";
      $this->db->where("id", $idcredito)->update("credito", $updateCredito);
      //? ACTUALIZAMOS LA VENTADETALLE CON ID DE LA VENTA QUE SE CREO
      $updateVentaDetalle["venta"] = $insert;
      $this->db->where("credito", $idcredito)->update("ventadetalle", $updateVentaDetalle);
      echo json_encode(["status" => TRUE]);
    }
  }

  function ajax_datapagarcredito($idcredito, $tienda)
  {
    $dataCredito = $this->Controlador_model->get($idcredito, "credito");
    $dataCredito->cajas = $this->db->where("tienda", $tienda)->get("cajaprincipal")->result();
    echo json_encode($dataCredito);
  }

  function ajax_cajatiendapagar($tienda){
    $data = $this->db->where("tienda", $tienda)->get("cajaprincipal")->result();
    echo json_encode($data);
  }
}
