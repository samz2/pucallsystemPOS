<?php

//Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
require __DIR__ . '/ticket/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Caja extends CI_Controller
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
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista,
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function reporte()
  {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => '/reporte' . $this->controlador,
      'empresas' => $this->Controlador_model->getAll('empresa'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function restaurar()
  {
    $idcaja = $this->input->post("idcaja");
    $empresa = $this->input->post("empresa");
    $queryValidate = $this->db->where("empresa", $empresa)->where("estado", '0')->get("caja")->row();
    if ($queryValidate) {
      echo json_encode(["status" => FALSE]);
    } else {
      $this->Controlador_model->restaurar($idcaja, $this->controlador);
      echo json_encode(["status" => TRUE]);
    }
  }

  public function general($finicio, $factual, $empresa)
  {
    $ingresos = $this->Controlador_model->generalingreso($finicio, $factual);
    $egresos = $this->Controlador_model->generalegreso($finicio, $factual);
    $total = 0;
    $ingreso = 0;
    $egreso = 0;
    $mostrar = '';
    $mostrar .= '<table id="example1" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Codigo</th><th>Concepto</th>
    <th></th><th>Ingreso (S/.)</th><th>Salida (S/.)</th><th>Total (S/.)</th></tr></thead><tbody>';
    $i = 0;
    foreach ($ingresos as $data) {
      $i++;
      $concepto = $this->Controlador_model->get($data->concepto, 'concepto');
      if ($data->concepto == '3') {
        $monto = $this->Controlador_model->ingresoefectivo($finicio, $factual);
      } else {
        $monto = $this->Controlador_model->getIngreso($finicio, $factual, $data->concepto);
      }
      $ingreso += $monto->monto;
      $total += $monto->monto;
      $mostrar .= '<tr><td>' . $i . '</td><td>' . $concepto->codigo . '</td><td>' . $concepto->concepto . '</td><td class="text-center">';
      $mostrar .= '<a class="btn btn-warning btn-sm" onclick="verGenerar(' . $data->concepto . ')" title="MOSTRAR"><i class="fa fa-eye"></i></a> ';
      $mostrar .= '<a class="btn btn-danger btn-sm" onclick="verDetalle(' . $data->concepto . ')" title="IMPRIMIR"><i class="fa fa-print"></i></a> ';
      $mostrar .= '</td><td align="right">' . number_format($monto->monto, 2) . '</td><td align="right">' . number_format(0, 2) . '</td>
      <td align="right">' . number_format($monto->monto, 2) . '</td></tr>';
    }
    foreach ($egresos as $data) {
      $i++;
      $concepto = $this->Controlador_model->get($data->concepto, 'concepto');
      $monto = $this->Controlador_model->getEgreso($finicio, $factual, $data->concepto);
      $total = $total - $monto->montototal;
      $egreso = $egreso + $monto->montototal;
      $mostrar .= '<tr><td>' . $i . '</td><td>' . $concepto->codigo . '</td><td>' . $concepto->concepto . '</td><td align="center">';
      $mostrar .= '<a class="btn btn-warning btn-sm" onclick="verGenerar(' . $data->concepto . ')" title="MOSTRAR"><i class="fa fa-eye"></i></a> ';
      $mostrar .= '<a class="btn btn-danger btn-sm" onclick="verDetalle(' . $data->concepto . ')" title="IMPRIMIR"><i class="fa fa-print"></i></a> ';
      $mostrar .= '</td><td align="right">' . number_format(0, 2) . '</td><td align="right">' . number_format($monto->montototal, 2) . '</td>
      <td align="right">' . number_format(0 - $monto->montototal, 2) . '</td></tr>';
    }
    $mostrar .= '</tbody><tfoot><tr><td colspan="3"></td><td align="center"><b>Total:</b></td>
    <td align="right">' . number_format($ingreso, 2) . '</td><td align="right">' . number_format($egreso, 2) . '</td>
    <td align="right">' . number_format($total, 2) . '</td></tr></tfoot></table>';
    echo $mostrar;
  }

  public function generaldetalle()
  {
    $concepto = $this->input->post('concepto');
    $finicio = $this->input->post('finicio');
    $factual = $this->input->post('factual');
    $ingresos = $this->Controlador_model->generaldetalle($finicio, $factual, $concepto, 'ingreso');
    $egresos = $this->Controlador_model->generaldetalle($finicio, $factual, $concepto, 'egreso');
    $mostrar = '';
    $mostrar .= '<table id="example2" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Tipo</th>
    <th>Documento</th><th>Descripcion</th><th>Monto</th><th>Fecha</th></tr></thead><tbody>';
    $i = 0;
    foreach ($ingresos as $data) {
      $i++;
      $venta = $this->Controlador_model->get($data->venta, 'venta');
      $mostrar .= '<tr><td>' . $i . '</td><td>' . ($venta ? $venta->tipoventa : '') . '</td>
      <td>' . ($venta ? $venta->serie . '-' . $venta->numero : '') . '</td><td>' . $data->observacion . '</td>
      <td>' . $data->monto . '</td><td>' . $data->created . '</td></tr>';
    }
    foreach ($egresos as $data) {
      $i++;
      $compra = $this->Controlador_model->get($data->compra, 'compra');
      $mostrar .= '<tr><td>' . $i . '</td><td>' . ($compra ? $compra->movimiento : '') . '</td><td>' . ($compra ? $compra->codigo : '') . '</td>
      <td>' . $data->observacion . '</td><td>' . $data->montototal . '</td><td>' . $data->created . '</td></tr>';
    }
    $mostrar .= '</tbody></table>';
    echo $mostrar;
  }

  public function especifico($finicio, $factual, $empresa)
  {
    $empleado = $this->perfil == 1 ? FALSE : $this->usuario;
    $datas = $this->Controlador_model->getCajas($empleado, $finicio, $factual, $empresa);
    $totalEfectivoVentas = 0;
    $totalAbonoCaja = 0;
    $totalSaldoInicial = 0;
    $totalGasto = 0;
    $totalCaja = 0;
    $totalCajaTodos = 0;
    $mostrar = '';
    $mostrar .= '<table id="example1" class="table table-bordered table-striped">
    <thead>
    <tr>
      <th><b>#</b></th>
      <th><b>Empresa</b></th>
      <th><b>Descripcion</b></th>
      <th><b>Colaborador</b></th>
      <th><b>Fecha</b></th>
      <th><b>Estado</b></th>
      <th><b>Operaciones</b></th>
      <th><b> <label class="label label-success"> + S.I.</label></b></th>
      <th><b><label class="label label-success"> + Efectivo Ventas</label></b></th>
      <th><b><label class="label label-success"> + Adicionales</label></b></b></th>
      <th><b><label class="label label-danger"> - Gastos</label></b></th>
      <th><b>Total en Caja</b></th>
    </tr>
    </thead>
    <tbody>';
    $i = 0;
    foreach ($datas as $data) {
      $i++;
      $AbonoCaja = $this->Controlador_model->getTotalAbono($data->id);
      $empresa = $this->Controlador_model->get($data->empresa, 'empresa');
      $usuario = $this->Controlador_model->get($data->usuario, 'usuario');
      $totalSaldoInicial += $data->saldoinicial;
      $totalEfectivoVentas += $data->efectivocontado;
      $resultAbono = !is_null($AbonoCaja->monto) ? $AbonoCaja->monto : 0;
      $totalAbonoCaja += $resultAbono;
      $GastoCaja = $this->Controlador_model->getTotalGasto($data->id);
      $resultGasto = !is_null($GastoCaja->montototal) ? $GastoCaja->montototal : 0;
      $totalGasto += $resultGasto;
      $totalCaja = ($data->saldoinicial + $data->efectivocontado + $resultAbono) - $resultGasto;
      $totalCajaTodos += $totalCaja;
      if ($data->estado == "1") {
        $estado = "<label class='label label-danger'>CERRADO</label>";
      } else {
        $estado = "<label class='label label-success'>ABIERTO</label>";
      }
      $mostrar .= '
      <tr>
      <td>' . $i . '</td>
      <td>' . $empresa->ruc . " | " . $empresa->nombre . " | " . "SERIE: " . $empresa->serie . '</td>
      <td>' . $data->descripcion . '</td>
      <td>' . $usuario->nombre . '</td>
      <td>' . $data->created . '</td>
      <td>' . $estado . '</td>
      <td align="center">';
      //? Desabilitado remporalmente para luego agregar a este boton como la vista de CORTE DE CAJA "Donde podra ver el detalle de caja en tiempo real sin que cierrer caja"
      //$mostrar .= '<a class="btn btn-default btn-sm" onclick="showTicket(' . $data->id . ')" title="Detalle"><i class="fa fa-ticket"></i></a> ';
      if ($data->estado == "1") {
        if ($this->perfil == 1 || $this->perfil == 2) {
          $mostrar .= '<button class="btn btn-danger btn-sm restaurar" onclick="restaurarcaja(' . $data->id . ', ' . $data->empresa . ')"  title="Restaurar Caja"><i class="fa fa-repeat fa-spin"></i></button> ';
        }
        $mostrar .= '<a class="btn btn-warning btn-sm" onclick="imprimir(' . $data->id . ', ' . $empresa->tipoimpresora . ')" title="Imprimir"><i class="fa fa-print"></i></a> ';
      }
      $mostrar .= '<button class="btn btn-info btn-sm" onclick="stockcaja(' . $data->id . ')"  title="Stock Caja"><i class="fa fa-cubes"></i></button> ';

      $mostrar .= '
      </td>
      <td align="right">' . $data->saldoinicial . '</td>
      <td align="right">' . $data->efectivocontado . '</td>
      <td align="right">' . $resultAbono . '</td>
      <td align="right">' . $resultGasto . '</td>
      <td align="right">' . $totalCaja . '</td>
      </tr>';
    }
    $mostrar .= '</tbody>
    <tfoot>
    <tr>
      <td colspan="6"></td>
      <td align="center"><b>Total:</b></td>
      <td align="right">' . number_format($totalSaldoInicial, 2) . '</td>
      <td align="right">' . number_format($totalEfectivoVentas, 2) . '</td>
      <td align="right">' . number_format($totalAbonoCaja, 2) . '</td>
      <td align="right">' . number_format($totalGasto, 2) . '</td>
      <td align="right">' . number_format($totalCajaTodos, 2) . '</td>
    </tr>
    </tfoot>
    </table>';
    echo $mostrar;
  }

  public function generalpdf()
  {
    $finicio = $this->input->post('finicio');
    $factual = $this->input->post('factual');
    $ticket = '<embed src="' . $this->url . '/generalimprimir/' . $finicio . '/' . $factual . '" type="application/pdf" width="100%" height="400"></embed>';
    echo $ticket;
  }

  public function generalimprimir()
  {
    $finicio = $this->uri->segment(3);
    $factual = $this->uri->segment(4);
    $data['finicio'] = $finicio;
    $data['factual'] = $factual;
    $data['ingresos'] = $this->Controlador_model->generalingreso($finicio, $factual);
    $data['egresos'] = $this->Controlador_model->generalegreso($finicio, $factual);
    $this->load->view('/pdfgeneral', $data);
    $html = $this->output->get_output();
    $this->dompdf->loadHtml($html);
    $this->dompdf->setPaper('A4', 'portrait');
    $this->dompdf->render();
    $this->dompdf->stream("general.pdf", array("Attachment" => 0));
  }

  public function generaldetallepdf()
  {
    $finicio = $this->input->post('finicio');
    $factual = $this->input->post('factual');
    $concepto = $this->input->post('concepto');
    $ticket = '<embed src="' . $this->url . '/generaldetalleimprimir/' . $finicio . '/' . $factual . '/' . $concepto . '" type="application/pdf" width="100%" height="400"></embed>';
    echo $ticket;
  }

  public function generaldetalleimprimir()
  {
    $finicio = $this->uri->segment(3);
    $factual = $this->uri->segment(4);
    $concepto = $this->uri->segment(5);
    $data['concepto'] = $concepto;
    $data['ingresos'] = $this->Controlador_model->generaldetalle($finicio, $factual, $concepto, 'ingreso');
    $data['egresos'] = $this->Controlador_model->generaldetalle($finicio, $factual, $concepto, 'egreso');
    $this->load->view('/pdfgeneraldetalle', $data);
    $html = $this->output->get_output();
    $this->dompdf->loadHtml($html);
    $this->dompdf->setPaper('A4', 'portrait');
    $this->dompdf->render();
    $this->dompdf->stream("detalle.pdf", array("Attachment" => 0));
  }

  public function especificopdf()
  {
    $finicio = $this->input->post('finicio');
    $factual = $this->input->post('factual');
    $empleado = $this->input->post('empleado');
    $ticket = '<embed src="' . $this->url . '/especificoimprimir/' . $finicio . '/' . $factual . '/' . $empleado . '" type="application/pdf" width="100%" height="400"></embed>';
    echo $ticket;
  }

  public function especificoimprimir()
  {
    $finicio = $this->uri->segment(3);
    $factual = $this->uri->segment(4);
    $empleado = $this->uri->segment(5);
    $data['factual'] = $factual;
    $data['finicio'] = $finicio;
    $data['datas'] = $this->Controlador_model->especifico($empleado, $finicio, $factual, $this->controlador);
    $this->load->view('/pdfespecifico', $data);
    // Get output html
    $html = $this->output->get_output();
    // Load pdf library
    $this->load->library('pdf');
    // Load HTML content
    $this->dompdf->loadHtml($html);
    // (Optional) Setup the paper size and orientation
    $this->dompdf->setPaper('A4', 'portrait');
    // Render the HTML as PDF
    $this->dompdf->render();
    // Output the generated PDF (1 = download and 0 = preview)
    $this->dompdf->stream("especifico.pdf", array("Attachment" => 0));
  }

  public function ShowTicket($id)
  {
    $caja = $this->Controlador_model->get($id, 'caja');
    $usuario = $this->Controlador_model->get($caja->usuario, 'usuario');
    $empresa = $this->Controlador_model->get($caja->empresa, 'empresa');
    $ticket = '<div class="col-md-12"><div class="text-center">' . $empresa->razonsocial . '</div>
    <div class="text-center">RUC: ' . $empresa->ruc . '</div><div style="clear:both;"></div>
    <h4 class="text-center">CIERRE DE CAJA</h4><hr/>
    <span class="float-left">' . strtoupper($caja->descripcion) . '</span><div style="clear:both;"></div>
    <span class="float-left">FECHA: ' . $caja->apertura . '</span><div style="clear:both;"></div>
    <span class="float-left">ENCARGADO: ' . $usuario->nombre . '</span><div style="clear:both;"></div>
    <span class="float-left">SALDO EFECTIVO: ' . number_format($caja->saldoinicial + $caja->efectivo - $caja->gasto, 2) . '</span><hr/>
    <table class="table table-hover"><tbody><thead><tr><th>CONCEPTOS</th><th style="text-align:right;">MONTOS</th></tr></thead>
    <tr><td>SALDO INICIAL</td><td style="text-align:right;font-weight:bold;">' . $caja->saldoinicial . ' Soles</td></tr>
    <tr><td>CONTADO</td><td style="text-align:right;font-weight:bold;">' . $caja->contado . ' Soles</td></tr>
    <tr><td>CREDITO</td><td style="text-align:right;font-weight:bold;">' . $caja->credito . ' Soles</td></tr>
    <tr><td>EFECTIVO</td><td style="text-align:right;font-weight:bold;">' . $caja->efectivo . ' Soles</td></tr>
    <tr><td>TARJETA</td><td style="text-align:right;font-weight:bold;">' . $caja->tarjeta . ' Soles</td></tr>
    <tr><td>GASTO</td><td style="text-align:right;font-weight:bold;">' . $caja->gasto . ' Soles</td></tr>
    <tr><td style="text-align:left; font-weight:bold; padding-top:5px;">VENTA TOTAL</td>
    <td style="border-top:1px dashed #000; padding-top:5px; text-align:right; font-weight:bold;">' . number_format($caja->saldoinicial + $caja->contado - $caja->gasto, 2) . ' Soles</td></tr></tbody></table>';
    echo $ticket;
  }

  public function getimprimir($id)
  {
    $caja = $this->Controlador_model->get($id, 'caja');
    $empresas = $this->Controlador_model->getAll('empresa');
    $i = 0;
    foreach ($empresas as $value) {
      $ventas = $this->Controlador_model->ventacaja($id, $value->id);
      $ingresos = $this->Controlador_model->ingresocaja($id, $value->id);
      if ($ingresos) {
        $contado = 0;
        $credito = 0;
        $efectivo = 0;
        $tarjeta = 0;
        foreach ($ventas as $venta) {
          if ($venta->formapago == 'CONTADO') {
            $contado += $venta->montototal;
          } else {
            $credito += $venta->montototal;
          }
        }
        foreach ($ingresos as $ingreso) {
          if ($ingreso->metodopago == 'EFECTIVO') {
            $efectivo += $ingreso->monto;
          } else {
            $tarjeta += $ingreso->monto;
          }
        }
        $empresa[$i] = array(
          'tipo' => $value->tipo,
          'nombre' => $value->nombre,
          'razonsocial' => $value->razonsocial,
          'departamento' => $value->departamento,
          'provincia' => $value->provincia,
          'distrito' => $value->distrito,
          'ruc' => $value->ruc,
          'contado' => number_format($contado, 2),
          'credito' => number_format($credito, 2),
          'efectivo' => number_format($efectivo, 2),
          'tarjeta' => number_format($tarjeta, 2)
        );
        $i++;
      }
    }
    $data = array(
      'caja' => $caja,
      'empresas' => $empresa,
      'saldoefectivo' => number_format($caja->saldoinicial + $caja->efectivo, 2),
      'empresa' => $this->Controlador_model->get($this->empresa, 'empresa'),
      'usuario' => $this->Controlador_model->get($caja->usuario, 'usuario')
    );
    echo json_encode($data);
  }

  public function imprimir($id)
  {
    $caja = $this->Controlador_model->get($id, 'caja');
    $empresa = $this->Controlador_model->get($caja->empresa, 'empresa');
    $usuario = $this->Controlador_model->get($caja->usuario, 'usuario');
    $monedero = $this->db->where('caja', $id)->get('monedero')->row();
    $diezcentimos = $monedero->diezcentimos * 0.10;
    $veintecentimos = $monedero->veintecentimos * 0.20;
    $cincuentacentimos = $monedero->cincuentacentimos * 0.50;
    $unsol = $monedero->unsol * 1;
    $dossoles = $monedero->dossoles * 2;
    $cincosoles = $monedero->cincosoles * 5;
    $diezsoles = $monedero->diezsoles * 10;
    $veintesoles = $monedero->veintesoles * 20;
    $cincuentasoles = $monedero->cincuentasoles * 50;
    $ciensoles = $monedero->ciensoles * 100;
    $doscientossoles = $monedero->doscientossoles * 200;
    $montototal = $diezcentimos + $veintecentimos + $cincuentacentimos + $unsol + $dossoles + $cincosoles + $diezsoles + $veintesoles + $cincuentasoles + $ciensoles + $doscientossoles;
    $nombre_impresora = $empresa->nombreimpresora;
    $connector = new WindowsPrintConnector($nombre_impresora);
    $printer = new Printer($connector);
    # Mando un numero de respuesta para saber que se conecto correctamente.
    # Vamos a alinear al centro lo próximo que imprimamos
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    if ($empresa->tipo == 0) {
      $printer->text($empresa->nombre . "\n");
      $printer->text($empresa->razonsocial . "\n");
    } else {
      $printer->text($empresa->razonsocial . "\n");
    }
    $printer->text($empresa->departamento . ' ' . $empresa->provincia . ' ' . $empresa->distrito . "\n");
    $printer->text("RUC: " . $empresa->ruc . "\n");
    $printer->text("Telf: " . $empresa->telefono . "\n");
    /* Ahora vamos a imprimir un encabezado */
    $printer->text("CIERRRE CAJA DIARIO \n");
    $printer->text("------------------------------------------------" . "\n");
    #La fecha también
    $printer->text(strtoupper($caja->descripcion) . "\n");
    $printer->text("FECHA DE APERTURA: " . $caja->apertura . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("ENCARGADO: " . $usuario->nombre . "\n");
    $printer->text("SALDO INICIAL: " . $caja->saldoinicial . "\n");
    $printer->text("------------------------------------------------" . "\n");
    $printer->text("TIPO DE MONEDA                CANTIDAD DE MONEDA\n");
    $printer->text("------------------------------------------------" . "\n");
    /* Ahora vamos a imprimir los productos. Alinear a la izquierda para la cantidad y el nombre */
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("0.10                                   " . $monedero->diezcentimos . "\n");
    $printer->text("0.20                                   " . $monedero->veintecentimos . "\n");
    $printer->text("0.50                                   " . $monedero->cincuentacentimos . "\n");
    $printer->text("1.00                                   " . $monedero->unsol . "\n");
    $printer->text("2.00                                   " . $monedero->dossoles . "\n");
    $printer->text("5.00                                   " . $monedero->cincosoles . "\n");
    $printer->text("10.00                                  " . $monedero->diezsoles . "\n");
    $printer->text("20.00                                  " . $monedero->veintesoles . "\n");
    $printer->text("50.00                                  " . $monedero->cincuentasoles . "\n");
    $printer->text("100.00                                 " . $monedero->ciensoles . "\n");
    $printer->text("200.00                                 " . $monedero->doscientossoles . "\n");
    /* Terminamos de imprimir los productos, ahora va el total */
    $printer->text("------------------------------------------------" . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("MONTO TOTAL:                          " . number_format($montototal, 2) . "\n");
    /* Podemos poner también un pie de página */
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("------------------------------------------------" . "\n");
    $printer->text("RESUMEN DE CAJA\n");
    $ingresos = $this->Controlador_model->getControl($id, 'ingreso');
    $efectivo = 0;
    $tarjeta = 0;
    foreach ($ingresos as $ingreso) {
      if ($ingreso->metodopago == 'EFECTIVO') {
        $efectivo += $ingreso->monto;
      } else {
        $tarjeta += $ingreso->monto;
      }
    }
    $totalcaja = $caja->saldoinicial + $caja->efectivo - $caja->gasto;
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("SALDO INICIAL: \n");
    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    $printer->text($caja->saldoinicial . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("EFECTIVO: \n");
    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    $printer->text($caja->efectivo . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("TARJETA: \n");
    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    $printer->text($caja->tarjeta . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("GASTO: \n");
    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    $printer->text($caja->gasto . "\n");
    /* Terminamos de imprimir los productos, ahora va el total */
    $printer->text("------------------------------------------------" . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("MONTO TOTAL: \n");
    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    $printer->text(number_format($totalcaja, 2) . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("DIFERENCIA: \n");
    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    $printer->text(number_format($montototal - $totalcaja, 2) . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    /* Podemos poner también un pie de página */
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("------------------------------------------------" . "\n");
    $printer->text("PRODUCTOS VENDIDOS\n");
    $posales = $this->Controlador_model->resumenventa($id);
    $totalventas = 0;
    foreach ($posales as $value) {
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $sumacantidad = $this->Controlador_model->ventaresumencantidad($id, $value->producto);
      $sumasubtotal = $this->Controlador_model->ventaresumensubtotal($id, $value->producto);
      $totalventas += $sumasubtotal->subtotal;
      $printer->setJustification(Printer::JUSTIFY_LEFT);
      $printer->text(ucwords(strtolower($producto->nombre)) . "\n");
      $printer->setJustification(Printer::JUSTIFY_RIGHT);
      $printer->text($sumacantidad->cantidad . " UND\n");
    }
    /* Podemos poner también un pie de página */
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("------------------------------------------------" . "\n");
    /* Podemos poner también un pie de página */
    $printer->text("El mundo es de quienes se atraven\n");
    /* Alimentamos el papel 3 veces */
    $printer->feed(3);
    /* Cortamos el papel. Si nuestra impresora no tiene soporte para ello, no generará ningún error */
    $printer->cut();
    /* Por medio de la impresora mandamos un pulso. Esto es útil cuando la tenemos conectada por ejemplo a un cajón */
    $printer->pulse();
    /* Para imprimir realmente, tenemos que "cerrar" la conexión con la impresora. Recuerda incluir esto al final de todos los archivos */
    $printer->close();
  }

  public function cpepdf($id)
  {
    $monedero = $this->db->where('caja', $id)->get('monedero')->row();
    $data = array(
      'monedero' => $monedero,
      'montoCerrarCaja' => $monedero->montototal,
      'caja' => $caja = $this->Controlador_model->get($id, 'caja'),
      'usuario' => $this->Controlador_model->get($caja->usuario, 'usuario'),
      'empresa' => $this->Controlador_model->get($caja->empresa, 'empresa'),
      'posales' => $this->Controlador_model->resumenventa($id)
    );
    $this->load->view('imprimircierre', $data);
  }

  public function ajax_stockcaja($idcaja)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->where('caja', $idcaja)->get("cajastock")->result();
    $data = [];
    foreach ($query as $key => $value) {
      $producto = $this->Controlador_model->get($value->producto, "producto");
      $categoria = $this->Controlador_model->get($value->categoria, "productocategoria");
      $caja = $this->Controlador_model->get($value->caja, "caja");
      $final_stock=0;
      if($caja->estado==0){
        if($value->final_stock==0){ 
          $final_stock="";     
      }else{
      $final_stock=$value->final_stock; 
      }}else{
      $final_stock=$value->final_stock;
      }
      $data[] = array(
        $key + 1,
        $producto ? $producto->codigo : "SIN DATOS",
        $value->nombre,
        $categoria ? $categoria->nombre : "SIN DATOS",
        $value->inicio_stock,
        $final_stock,
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

  public function ajax_descargarStockCaja($idcaja)
  {
    $data['idcaja'] =$idcaja;
    $this->load->view('/pdfcajastock', $data);
    // Get output html
    $html = $this->output->get_output();
    // Load pdf library
    $this->load->library('pdf');
    // Load HTML content
    $this->dompdf->loadHtml($html);
    // (Optional) Setup the paper size and orientation
    $this->dompdf->setPaper('A4', 'portrait');
    // Render the HTML as PDF
    $this->dompdf->render();
    // Output the generated PDF (1 = download and 0 = preview)
    $this->dompdf->stream("cotrolstock.pdf", array("Attachment" => 0));
  }
  public function ajax_miniStockCaja($idcaja)
  {
    $data['idcaja'] =$idcaja;
    $this->load->view('/caja_stock', $data);
    
  }
}
