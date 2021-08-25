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
    $empresa = $this->input->post("empresa");
    $usuario = $this->perfil == 1 || $this->perfil == 2 ? FALSE : $this->usuario;
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $creditos = $this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'")->where('empresa', $empresa)->where('formapago', 'CREDITO')->where('estado', '1')->order_by('numero', 'desc')->get('venta');
    $faltantes = $this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'")->where('empresa', $empresa)->where('concepto', 17)->order_by('id', 'desc')->get('egreso');
    $data = [];
    $no = 0;
    $hoy = strtotime(date('Y-m-d'));
    foreach ($creditos->result() as $value) {
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
        $estado = '<td><span class="label label-warning">PENDIENTE</span></td>';
      }
      $data[] = array(
        $no,
        $value->serie . '-' . $value->numero,
        substr($cliente->nombre . ' ' . $cliente->apellido, 0, 30),
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
        $estado = '<td><span class="label label-warning">PENDIENTE</span></td>';
      }
      $data[] = array(
        $no,
        $value->serie . '-' . $value->numero,
        substr($cliente->nombre . ' ' . $cliente->apellido, 0, 30),
        $value->vence,
        $estado,
        $value->montototal,
        $value->montoactual,
        $boton1 . $boton2
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

  public function ajax_updatecobro()
  {
    $this->_validatecobro();
    $dataventaquery = $this->Controlador_model->get($this->input->post('id'), "venta");
    $ingreso['metodopago'] = $this->input->post('metodopago');

    $ingreso['tipotarjeta'] = $this->input->post('metodopago') <> 'EFECTIVO' ? $this->input->post('tipotarjeta') : NULL;
    $ingreso['operacion'] = $this->input->post('metodopago') <> 'EFECTIVO' ? $this->input->post('operacion') : NULL;

    $ingreso['monto'] = $this->input->post('pago');

    $data['montoactual'] = $this->input->post('monto') - $this->input->post('pago');
    $data['pago'] = $dataventaquery->pago + $this->input->post('pago');

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
    $ingreso['empresa'] = $this->empresa;
    $ingreso['caja'] = $this->session->userdata('caja') ? $this->session->userdata('caja') : NULL;
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
      //add variables for action
      $botones = '';
      //add html for action
      $botones .= '<a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Imprimir" onclick="imprimircomprobante(' . $value->id . ', ' . $value->venta . ')"><i class="fa fa-print"></i></a>';
      $botones .= ' <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="borrarpagos(' . "'" . $value->id . "'" . ')"><i class="fa fa-trash"></i></a>';
      $data[] = array(
        $no,
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
      'usuario' => $this->Controlador_model->get($venta->usuario, 'usuario'),
    );

    $this->load->view('imprimircomprobante2', $data);
  }

  public function ajax_deletecobro($id)
  {
    $ingreso = $this->Controlador_model->get($id, 'ingreso');
    $venta = $this->Controlador_model->get($ingreso->venta, 'venta');
    if ($venta) {
      $data['montoactual'] = $venta->montoactual + $ingreso->monto;
      $data['pago'] = $venta->pago - $ingreso->monto;
      $this->Controlador_model->update(array('id' => $ingreso->venta), $data, 'venta');
    } else {
      $egreso = $this->Controlador_model->get($ingreso->egreso, 'egreso');
      $data['montoactual'] = $egreso->montoactual + $ingreso->monto;
      $this->Controlador_model->update(array('id' => $ingreso->egreso), $data, 'egreso');
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

    $empresa = $this->input->post("empresa");
    $cliente = $this->input->post("cliente");
    $usuario = $this->perfil == 1 || $this->perfil == 2 ? FALSE : $this->usuario;
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $creditos = $this->db->query("select vd.*, v.serie, v.numero,c.nombre as clientenombre,c.documento, v.created from venta v inner join ventadetalle vd on vd.venta = v.id  join cliente c on v.cliente = c.id where v.empresa = $empresa and v.formapago = 'CREDITO' and v.cliente = $cliente and v.estado = '1'");
    $data = [];
    $no = "";
    $hoy = strtotime(date('Y-m-d'));
    foreach ($creditos->result() as $value) {
      $nombre_cliente = str_replace(" ", "_", $value->clientenombre);
      $nombre_producto = str_replace(" ","_",$value->nombre);
      $no = "<input type='checkbox' id='chk_$value->id' class='form-control'  onclick=agregarPago('$value->id',$value->cantidad,$value->subtotal,'$nombre_cliente','$value->documento','$nombre_producto')>";
      // $cliente = $this->Controlador_model->get($value->cliente, 'cliente');
      //add variables for action
      $boton1 = '';
      $boton2 = '';
      $estado = '';
      //add html fodr action
      // $boton1 = '<a class="btn btn-sm btn-primary" title="seleccionar" onclick="Seleccionar(' . $value->id . ', 1)"><i class="fa fa-credit-card"></i></a> ';
      $boton2 = '<a class="btn btn-sm btn-default" title="Cobrar" onclick="verPagos(' . $value->id . ', 1)"><i class="fa fa-eye"></i></a> ';
      // if ($value->montoactual == 0) {
      //   $estado = '<td><span class="label label-success">CANCELADO</span></td>';
      // }
      // if ($value->montoactual > 0 && strtotime($value->created) < $hoy) {
      //   $estado = '<td><span class="label label-danger">VENCIDO</span></td>';
      // }
      // if ($value->montoactual > 0 && strtotime($value->created) >= $hoy) {
      //   $estado = '<td><span class="label label-warning">PENDIENTE</span></td>';
      // }
      $data[] = array(
        $no,
        $value->serie . '-' . $value->numero,
        $value->created,
        $value->nombre,
        $value->cantidad,
        $value->precio,
        $value->subtotal,
        $boton1 . $boton2
      );
    }
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $creditos->num_rows(),
      "recordsFiltered" => $creditos->num_rows(),
      "data" => $data,
    );
    echo json_encode($result);
  }
  public function pagos()
  {
    $datos["empresa"]         = $this->input->post("empresa");
    $datos["usuario_creador"] = $this->usuario;
    $datos["caja"]            = $this->session->userdata('caja');
    $datos["cliente"]         = $this->input->post("idCliente");
    $datos["referencia"]      = "";
    $datos["tipoventa"]       = $this->input->post("tipoventa");
    // nueva lógica
    $comprobante = $this->db->where("tipo",$this->input->post("tipoventa"))->where("empresa",$this->input->post("empresa"))->get("comprobante")->row();
    if(isset($comprobante))
    {
      $datos["serie"]           = $comprobante->serie;;
      $datos["numero"]          = $this->Controlador_model->addLeadingZeros($comprobante->correlativo);
      $datos["consecutivo"]     = $comprobante->correlativo;
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
    foreach($this->input->post("datos") as $fila => $val)
    {
      $cant += $val["cantidad"];
    }
    $datos["totalitems"]      = $cant;
    $datos["emision"]         = "";
    $datos["hash"]            = "";
    $datos["estado"]          = "0";
    $datos["consumo"]         = "0";
    $datos["dcuenta"]         = "0"; //1 es para cuentas divididas
    // $datos["sound"]           = "1";
    $datos["atender"]         = "1"; //atendido
    $datos["modificar"]       = "0"; // 1 NC
    $datos["hora"]            = date("H:i:s");
    $datos["created"]         = date("Y-m-d");
    $datos["hf_procesado"]    = "";
    $datos["vence"]           = date("Y-m-d");
    $datos["anularmotivo"]    = "";
    // $result = array(
    //   "prueba" => $prueba,
    // );
    echo json_encode($datos);
  }
}
