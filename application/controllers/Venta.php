<?php

include "validaciondedatos.php";
include "procesarcomprobante.php";
//Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
require __DIR__ . '/ticket/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Venta extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model(modelo(), 'Controlador_model');
    $this->controlador = controlador();
    $this->load->library('Phpmailer_lib');
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
      'empresas' => $this->Controlador_model->getAll('empresa'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $ruta = "theme/adminlte/template";
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function visualizar($id = FALSE)
  {
    if ($id) {
      $data = array(
        'titulo' => 'Actualizar ' . $this->titulo_controlador,
        'contenido' => $this->vista . 'ver',
        'data' => $this->Controlador_model->get($id, $this->controlador),
        'ventadetalle' => $this->Controlador_model->getDetalle($id, 'ventadetalle'),
        'breads' => array(array('ruta' => $this->url, 'titulo' => $this->titulo_controlador), array('ruta' => 'javascript:;', 'titulo' => 'Visualizar'))
      );
      $this->load->view(THEME . TEMPLATE, $data);
    } else {
      show_404();
    }
  }

  public function verventas($finicio, $factual, $empresa)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $usuario = $this->perfil == 1 || $this->perfil == 2 || $this->perfil == 6 ? FALSE : $this->usuario;
    $generados = $this->Controlador_model->ventas($finicio, $factual, $empresa, $usuario, '1');
    $anulados = $this->Controlador_model->ventas($finicio, $factual, $empresa, $usuario, '3');
    $data = [];
    $no = 0;
    $estado_sunat[''] = '';
    $estado_sunat['0000'] = '';
    $estado_sunat['soap-env:Client.0130'] = '';
    $estado_sunat['soap-env:Client.0111'] = '';
    $estado_sunat['soap-env:Client.0151'] = '';
    $estado_sunat['soap-env:Client.1032'] = '<span class="label label-danger">RECHAZADO</span>';
    $estado_sunat['soap-env:Client.2638'] = '<span class="label label-danger">RECHAZADO</span>';
    $estado_sunat['soap-env:Client.1033'] = '<span class="label label-info">ACEPTADO</span>';
    $estado_sunat['0'] = '<span class="label label-info">ACEPTADO</span>';
    foreach ($generados as $key => $value) {
      $no++;
      $caja =  $this->Controlador_model->get($value->caja, 'caja');
      $cliente = $this->Controlador_model->get($value->cliente, 'cliente');
      $usuario = $this->Controlador_model->get($value->usuario_creador, 'usuario');
      $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
      //add variables for action
      $boton = '';
      $boton .= '<a class="btn btn-default btn-sm" onclick="showTicket(' . $value->id . ')" title="Detalle"><i class="fa fa-ticket"></i></a> ';
      $boton .= '<a class="btn btn-warning btn-sm" onclick="imprimir(' . $value->id . ', ' . $empresa->tipoimpresora . ')" title="Imprimir"><i class="fa fa-print"></i></a> ';
      if ($value->emision === '0' || $value->emision === 'soap-env:Client.1032' || $value->emision === 'soap-env:Client.1033') {
        $tipo_comprobante = $value->tipoventa == 'FACTURA' ? "01" : "03";
        $archivo = $empresa->ruc . '-' . $tipo_comprobante . '-' . $value->serie . '-' . $value->numero;
        if ($value->emision === 'soap-env:Client.1032') {
          //? El comprobante ya esta informado y se encuenta con estado anulado o rechazado
          if ($this->perfil == 1 || $this->perfil == 2 || $this->perfil == 6) {
            $boton .= '<a class="btn btn-danger btn-sm" onclick="anular(' . $value->id . ', ' . $value->empresa . ')" title="Anular"><i class="fa fa-buysellads"></i></a> ';
          }
        } else {
          //? soap-env:Client.1033: EL comprobante fue registrdo previamente con otros datos
          if ($value->tipoventa <> 'OTROS') {
            $carpeta = $empresa->tipoproceso == '1' ? 'produccion' : 'beta';
            $boton .= '<a target="_blank" class="btn btn-success btn-sm" href="archivos_xml_sunat/cpe_xml/' . $carpeta . '/' . $empresa->ruc . '/R-' . $archivo . '.XML" title="CDR"><i class="fa fa-briefcase"></i></a> ';
          }
        }
      } else {
        if ($value->tipoventa <> 'OTROS') {
          $boton .= '<a class="btn btn-info btn-sm" onclick="procesar_documento_electronico(' . $value->id . ')" title="Emitir"><i class="fa fa-upload"></i></a> ';
        }
        if ($this->perfil == 1 || $this->perfil == 2 || $this->perfil == 6) {
          $boton .= '<a class="btn btn-danger btn-sm" onclick="anular(' . $value->id . ', ' . $value->empresa . ')" title="Anular"><i class="fa fa-buysellads"></i></a> ';
        }
      }
      $data[] = array(
        $no,
        $caja->descripcion,
        $value->formapago,
        $value->tipoventa,
        $value->serie . '-' . $value->numero,
        $cliente->nombre,
        '<span class="label label-success">GENERADO</span>',
        $estado_sunat[$value->emision],
        $value->deudatotal,
        $value->created,
        $boton
      );
    }
    foreach ($anulados as $key => $value) {
      $no++;
      $cliente = $this->Controlador_model->get($value->cliente, 'cliente');
      $usuario = $this->Controlador_model->get($value->usuario_creador, 'usuario');
      $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
      $caja =  $this->Controlador_model->get($value->caja, 'caja');
      //add variables for action
      $boton = '';
      //add html fodr action
      $boton .= '<a class="btn btn-default btn-sm" onclick="showTicket(' . $value->id . ')" title="Detalle"><i class="fa fa-ticket"></i></a> ';
      $boton .= '<a class="btn btn-warning btn-sm" onclick="PrintTicket(' . $value->id . ', ' . $empresa->tipoimpresora . ')" title="Imprimir"><i class="fa fa-print"></i></a> ';
      $data[] = array(
        $no,
        $caja->descripcion,
        $value->formapago,
        $value->tipoventa,
        $value->serie . '-' . $value->numero,
        $cliente->nombre,
        '<span class="label label-danger">ANULADO</span>',
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

  public function ajax_anular($idventa, $empresa)
  {
    $motivo = $this->input->post("result");
    $dataEmpresa = $this->Controlador_model->get($empresa, 'empresa');
    $detalle = $this->Controlador_model->getDetalle($idventa, 'ventadetalle');
    foreach ($detalle as $value) {

      $productos = $this->Controlador_model->get($value->producto, 'producto');
      if ($productos->tipo == '0') {
        $cantidad = $this->Controlador_model->getStockAlmacen($value->producto, $dataEmpresa->almacen, $value->lote, $empresa);
        $movimiento['empresa'] = $empresa;
        $movimiento['usuario'] = $this->usuario;
        $movimiento['venta'] = $idventa;
        $movimiento['tipooperacion'] = "VENTA";
        $movimiento['producto'] = $value->producto;
        $movimiento['almacen'] = $dataEmpresa->almacen;
        $movimiento['lote'] =  ($value->lote ? $value->lote : NULL);
        if ($value->variante) {
          $dataVariante = $this->Controlador_model->get($value->variante, "productovariante");
          $totalRestablecer = $dataVariante->cantidad * $value->cantidad;
          $movimiento['medida'] =  $dataVariante->nombre;
          $movimiento['medidacantidad'] = $dataVariante->cantidad;
          $movimiento['cantidaditem'] = $dataVariante->cantidad * $value->cantidad;
          $movimiento['totalitemoperacion'] = $dataVariante->cantidad * $value->cantidad;
        } else {
          $totalRestablecer = $value->cantidad;
          $movimiento['medida'] =  "UNIDAD";
          $movimiento['medidacantidad'] = 1;
          $movimiento['cantidaditem'] = $value->cantidad;
          $movimiento['totalitemoperacion'] = $value->cantidad;
        }
        $movimiento['cantidad'] = $value->cantidad; //? LO QUE REGISTRA
        $movimiento['stockanterior'] = $cantidad ? $cantidad->cantidad : 0;
        $movimiento['tipo'] =  'ENTRADA ANULACION VENTA';
        $movimiento['stockactual'] = ($cantidad ? $cantidad->cantidad : 0) + $totalRestablecer;
        $movimiento['created'] = date('Y-m-d');
        $movimiento['hora'] = date("H:i:s");
        $this->Controlador_model->save('movimiento', $movimiento);

        $stock = $this->Controlador_model->getStock($value->producto, $dataEmpresa->almacen);
        $producto['cantidad'] = $stock->cantidad + $totalRestablecer;
        $this->Controlador_model->update(array('id' => $stock->id), $producto, 'stock');
      } else if ($productos->tipo == '2') {
        $combos = $this->Controlador_model->getCombo($value->producto);
        foreach ($combos as $combo) {
          $stockCombo = $this->Controlador_model->getStockProceso($combo->item_id, $dataEmpresa->almacen, NULL, $empresa);
          $movimientoCombo['empresa'] = $empresa;
          $movimientoCombo['usuario'] = $this->usuario;
          $movimientoCombo['venta'] = $idventa;
          $movimientoCombo['tipooperacion'] = "VENTA";
          $movimientoCombo['producto'] = $combo->item_id;
          $movimientoCombo['productocombo'] = $value->producto;
          $movimientoCombo['almacen'] = $dataEmpresa->almacen;
          $movimientoCombo['lote'] =  ($value->lote ? $value->lote : NULL);
          $movimientoCombo['medida'] =  "COMBO";
          $movimientoCombo['medidacantidad'] = $combo->cantidad;
          $movimientoCombo['cantidad'] = $value->cantidad; //? LO QUE REGISTRA
          $movimientoCombo['cantidaditem'] = $combo->cantidad * $value->cantidad;
          $movimientoCombo['totalitemoperacion'] = $combo->cantidad * $value->cantidad;
          $movimientoCombo['stockanterior'] = $stockCombo ? $stockCombo->cantidad : 0;
          $movimientoCombo['tipo'] =  'ENTRADA ANULACION VENTA';
          $movimientoCombo['stockactual'] = ($stockCombo ? $stockCombo->cantidad : 0) + ($combo->cantidad * $value->cantidad);
          $movimientoCombo['created'] = date('Y-m-d');
          $movimientoCombo['hora'] = date("H:i:s");
          $this->Controlador_model->save('movimiento', $movimientoCombo);
          $cantidad2 = $value->cantidad * $combo->cantidad;
          $stockC = $this->Controlador_model->getStock($combo->producto, $dataEmpresa->almacen);
          if ($stockC) {
            $productoCombo['cantidad'] = $stockC->cantidad + $cantidad2;
            $this->Controlador_model->update(array('id' => $stockC->id), $productoCombo, 'stock');
          }
        }
      }
    }
    $this->Controlador_model->delete_by_venta($idventa, 'ingreso');
    $venta['usuario_anulado'] = $this->usuario;
    $venta['estado'] = '3';
    $venta['anular_motivo'] = $motivo;
    $this->Controlador_model->update(array('id' => $idventa), $venta, $this->controlador);
    echo json_encode(array("status" => TRUE));
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

  public function resumen($finicio, $factual, $empresa)
  {
    $generados = $this->Controlador_model->resumen($finicio, $factual, $empresa, '1');
    $anulados = $this->Controlador_model->resumen($finicio, $factual, $empresa, '3');
    $mostrar = '';
    $mostrar .= '<table id="example1" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Venta</th>
    <th>Cliente</th><th>Colaborador</th><th>Estado</th><th>Condicion</th><th>Sunat</th><th>Fecha</th>
    <th>Monto</th></tr></thead><tbody>';
    $totalactual = 0;
    $i = 0;
    foreach ($generados as $data) {
      $i++;
      $totalactual += $data->montototal;
      $usuario = $this->Controlador_model->get($data->usuario_creador, 'usuario');
      $cliente = $this->Controlador_model->get($data->cliente, 'cliente');
      $sunat = "";
      if ($data->emision === '0') {
        $sunat = "ACEPTADO";
      }
      if ($data->emision === 'soap-env:Client.1032') {
        $sunat = "COMUNICACION DE BAJA";
      }
      if ($data->emision === '0000' || $data->emision == NULL) {
        $sunat = "SIN EMITIR";
      }
      $mostrar .= '<tr><td>' . $i . '</td><td>' . $data->serie . '-' . $data->numero . '</td><td>' . $cliente->nombre . '</td>
      <td>' . $usuario->nombre . '</td><td>GENERADO</td><td>' . $data->formapago . '</td><td>' . $sunat . '</td>
      <td>' . $data->created . '</td><td align="right">' . $data->montototal . '</td></tr>';
    }
    foreach ($anulados as $data) {
      $i++;
      $totalactual = $totalactual + $data->montototal;
      $usuario = $this->Controlador_model->get($data->usuario_creador, 'usuario');
      $cliente = $this->Controlador_model->get($data->cliente, 'cliente');
      $mostrar .= '<tr><td>' . $i . '</td><td>' . $data->serie . '-' . $data->numero . '</td><td>' . $cliente->nombre . '</td>
      <td>' . $usuario->nombre . '</td><td>ANULADO</td><td>' . $data->formapago . '</td><td>' . $sunat . '</td>
      <td>' . $data->created . '</td><td align="right">' . $data->montototal . '</td></tr>';
    }
    $mostrar .= '</tbody><tfoot><tr><td colspan="7"></td><td align="center"><b>Total:</b></td>
    <td ALIGN="right">' . number_format($totalactual, 2) . '</td></tr></tfoot></table>';
    echo $mostrar;
  }

  public function detalle($finicio, $factual, $empresa)
  {
    $generados = $this->Controlador_model->detalle($finicio, $factual, $empresa, '1');
    $anulados = $this->Controlador_model->detalle($finicio, $factual, $empresa, '3');
    $mostrar = '';
    $mostrar .= '
    <table id="example1" class="table table-bordered table-striped">
    <thead>
    <tr>
      <th>#</th>
      <th>Venta</th>
      <th>Colaborador</th>
      <th>Fecha / Hora</th>
      <th>Concepto</th>
      <th>P. Compra</th>
      <th>P. Venta</th>
      <th>Utilidad por venta</th>
      <th>Cantidad</th>
      <th>Utilidad</th>
      <th>Monto</th>
    </tr>
    </thead>
    <tbody>';
    $totalactual = 0;
    $totalutilidad = 0;
    $i = 0;
    foreach ($generados as $data) {
      $i++;
      $venta = $this->Controlador_model->get($data->id, 'venta');
      $usuario = $this->Controlador_model->get($venta->usuario_creador, 'usuario');
      $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
      $utilidadporventa = $data->precioventa - $data->preciocompra;
      $utilidad = $data->subtotal - ($data->cantidad * $data->preciocompra);
      $totalactual += $data->subtotal;
      $totalutilidad += $utilidad;
      $mostrar .= '
      <tr>
      <td>' . $i . '</td>
      <td>' . $venta->serie . '-' . $venta->numero . '</td>
      <td>' . $usuario->nombre . '</td>
      <td>' . $venta->created . " / " . $venta->hora . '</td>';
      if ($data->tipo == '0') {
        $producto = $this->Controlador_model->get($data->producto, 'producto');
        $categoria = $this->Controlador_model->get($producto->categoria, 'productocategoria');
        $mostrar .= '<td>' . $producto->nombre . ' ' . ($categoria ? $categoria->nombre : '') . '</td>';
      } else {
        $mostrar .= '<td>' . $data->nombre . '</td>';
      }
      $mostrar .= '<td align="right">S/ ' . $data->preciocompra . '</td>
      <td align="right">S/ ' . $data->precioventa . '</td>
      <td align="right"> S/ ' . number_format($utilidadporventa, 2) . '</td>
      <td align="right">' . $data->cantidad . '</td>
      <td align="right">' . number_format($utilidad, 2) . '</td>
      <td align="right">' . $data->subtotal . '</td>
      </tr>';
    }

    $mostrar .= '</tbody>
    <tfoot>
    <tr>
    <td colspan="7">
    </td><td align="center" colspan="2"><b>Total:</b></td>
    <td align="right">' . number_format($totalutilidad, 2) . '</td>
    <td align="right">' . number_format($totalactual, 2) . '</td>
    </tr>
    </tfoot>
    </table>';
    echo $mostrar;
  }

  public function resumenexcel($finicio = FALSE, $factual = FALSE, $empresa = FALSE)
  {
    $generados = $this->Controlador_model->resumen($finicio, $factual, $empresa, '1');
    $anulados = $this->Controlador_model->resumen($finicio, $factual, $empresa, '3');
    // Propiedades del archivo excel
    $this->phpexcel->getProperties()->setTitle("Lista de Utilidad")->setDescription("Lista de Venta");
    // Setiar la solapa que queda actia al abrir el excel
    $this->phpexcel->setActiveSheetIndex(0);
    // Solapa excel para trabajar con PHP
    $sheet = $this->phpexcel->getActiveSheet();
    $sheet->setTitle("Lista de Venta");
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
    $sheet->getStyle('J')->getNumberFormat()->setFormatCode('#,##0.00');
    $style_header = array(
      'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
      'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7'))
    );
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $sheet->getStyle('A1:J1')->applyFromArray($style_header);
    $sheet->getStyle("A1:J1")->applyFromArray($style);
    $sheet->setCellValue('A1', 'NRO');
    $sheet->setCellValue('B1', 'TIPO');
    $sheet->setCellValue('C1', 'VENTA');
    $sheet->setCellValue('D1', 'DOCUMENTO');
    $sheet->setCellValue('E1', 'CLIENTE');
    $sheet->setCellValue('F1', 'VENDEDOR');
    $sheet->setCellValue('G1', 'FECHA');
    $sheet->setCellValue('H1', 'ESTADO');
    $sheet->setCellValue('I1', 'SUNAT');
    $sheet->setCellValue('J1', 'MONTO');
    $i = 1;
    foreach ($generados as $value) {
      $cliente = $this->Controlador_model->get($value->cliente, 'cliente');
      $vendedor = $this->Controlador_model->get($value->usuario_creador, 'usuario');
      if ($value->emision === '0') {
        $sunat = "ACEPTADO";
      }
      if ($value->emision === 'soap-env:Client.1032') {
        $sunat = "COMUNICACION DE BAJA";
      }
      if ($value->emision === '0000' || $value->emision == NULL) {
        $sunat = "SIN EMITIR";
      }
      $i++;
      $sheet->setCellValue('A' . $i, $i - 1);
      $sheet->setCellValue('B' . $i, $value->tipoventa);
      $sheet->setCellValue('C' . $i, $value->serie . '-' . $value->numero);
      $sheet->setCellValue('D' . $i, $cliente->documento);
      $sheet->setCellValue('E' . $i, $cliente->nombre . ' ' . $cliente->apellido);
      $sheet->setCellValue('F' . $i, $vendedor->nombre);
      $sheet->setCellValue('G' . $i, $value->created);
      $sheet->setCellValue('H' . $i, 'GENERADO');
      $sheet->setCellValue('I' . $i, $sunat);
      $sheet->setCellValue('J' . $i, $value->montototal);
    }
    foreach ($anulados as $value) {
      $cliente = $this->Controlador_model->get($value->cliente, 'cliente');
      $vendedor = $this->Controlador_model->get($value->usuario_creador, 'usuario');
      if ($value->emision === '0') {
        $sunat = "ACEPTADO";
      }
      if ($value->emision === 'soap-env:Client.1032') {
        $sunat = "COMUNICACION DE BAJA";
      }
      if ($value->emision === '0000' || $value->emision == NULL) {
        $sunat = "SIN EMITIR";
      }
      $i++;
      $sheet->setCellValue('A' . $i, $i - 1);
      $sheet->setCellValue('B' . $i, $value->tipoventa);
      $sheet->setCellValue('C' . $i, $value->serie . '-' . $value->numero);
      $sheet->setCellValue('D' . $i, $cliente->documento);
      $sheet->setCellValue('E' . $i, $cliente->nombre . ' ' . $cliente->apellido);
      $sheet->setCellValue('F' . $i, $vendedor->nombre);
      $sheet->setCellValue('G' . $i, $value->created);
      $sheet->setCellValue('H' . $i, 'ANULADO');
      $sheet->setCellValue('I' . $i, $sunat);
      $sheet->setCellValue('J' . $i, $value->montototal);
    }
    // Salida
    header("Content-Type: application/vnd.ms-excel");
    $nombreArchivo = 'ventas_' . date('YmdHis');
    header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
    header("Cache-Control: max-age=0");
    // Genera Excel
    $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, "Excel5");
    // Escribir
    $writer->save('php://output');
    exit;
  }

  public function contador($finicio = FALSE, $factual = FALSE, $empresa = FALSE)
  {
    $generados = $this->Controlador_model->generarcontador($finicio, $factual, $empresa, '1');
    $anulados = $this->Controlador_model->generarcontador($finicio, $factual, $empresa, '3');
    // Propiedades del archivo excel
    $this->phpexcel->getProperties()->setTitle("Lista de Utilidad")->setDescription("Lista de Venta");
    // Setiar la solapa que queda actia al abrir el excel
    $this->phpexcel->setActiveSheetIndex(0);
    // Solapa excel para trabajar con PHP
    $sheet = $this->phpexcel->getActiveSheet();
    $empresas = $this->Controlador_model->get($empresa, 'empresa');
    if ($empresas->tipo == '0') {
      $nombre = $empresas->nombre;
    }
    if ($empresas->tipo == '1') {
      $nombre = $empresas->razonsocial;
    }
    $sheet->setTitle("$nombre");
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
    $sheet->getStyle('J')->getNumberFormat()->setFormatCode('#,##0.00');
    $style_header = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7')));
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $sheet->getStyle('A1:J1')->applyFromArray($style_header);
    $sheet->getStyle("A1:J1")->applyFromArray($style);
    $sheet->setCellValue('A1', 'NRO');
    $sheet->setCellValue('B1', 'TIPO');
    $sheet->setCellValue('C1', 'VENTA');
    $sheet->setCellValue('D1', 'DOCUMENTO');
    $sheet->setCellValue('E1', 'CLIENTE');
    $sheet->setCellValue('F1', 'VENDEDOR');
    $sheet->setCellValue('G1', 'FECHA');
    $sheet->setCellValue('H1', 'ESTADO');
    $sheet->setCellValue('I1', 'SUNAT');
    $sheet->setCellValue('J1', 'MONTO');
    $i = 1;
    foreach ($generados as $value) {
      if ($value->tipoventa <> 'OTROS') {
        $cliente = $this->Controlador_model->get($value->cliente, 'cliente');
        $vendedor = $this->Controlador_model->get($value->usuario_creador, 'usuario');
        if ($value->emision === '0') {
          $sunat = "ACEPTADO";
        }
        if ($value->emision === 'soap-env:Client.1032') {
          $sunat = "COMUNICACION DE BAJA";
        }
        if ($value->emision === '0000' || $value->emision == NULL) {
          $sunat = "SIN EMITIR";
        }
        $i++;
        $sheet->setCellValue('A' . $i, $i - 1);
        $sheet->setCellValue('B' . $i, $value->tipoventa);
        $sheet->setCellValue('C' . $i, $value->serie . '-' . $value->numero);
        $sheet->setCellValue('D' . $i, $cliente->documento);
        $sheet->setCellValue('E' . $i, $cliente->nombre . ' ' . $cliente->apellido);
        $sheet->setCellValue('F' . $i, $vendedor->nombre);
        $sheet->setCellValue('G' . $i, $value->created);
        $sheet->setCellValue('H' . $i, 'GENERADO');
        $sheet->setCellValue('I' . $i, $sunat);
        $sheet->setCellValue('J' . $i, $value->montototal);
      }
    }
    foreach ($anulados as $value) {
      if ($value->tipoventa <> 'OTROS') {
        $cliente = $this->Controlador_model->get($value->cliente, 'cliente');
        $vendedor = $this->Controlador_model->get($value->usuario_creador, 'usuario');
        if ($value->emision === '0') {
          $sunat = "ACEPTADO";
        }
        if ($value->emision === 'soap-env:Client.1032') {
          $sunat = "COMUNICACION DE BAJA";
        }
        if ($value->emision === '0000' || $value->emision == NULL) {
          $sunat = "SIN EMITIR";
        }
        $i++;
        $sheet->setCellValue('A' . $i, $i - 1);
        $sheet->setCellValue('B' . $i, $value->tipoventa);
        $sheet->setCellValue('C' . $i, $value->serie . '-' . $value->numero);
        $sheet->setCellValue('D' . $i, $cliente->documento);
        $sheet->setCellValue('E' . $i, $cliente->nombre . ' ' . $cliente->apellido);
        $sheet->setCellValue('F' . $i, $vendedor->nombre);
        $sheet->setCellValue('G' . $i, $value->created);
        $sheet->setCellValue('H' . $i, 'ANULADO');
        $sheet->setCellValue('I' . $i, $sunat);
        $sheet->setCellValue('J' . $i, $value->montototal);
      }
    }
    // Salida
    header("Content-Type: application/vnd.ms-excel");
    $nombreArchivo = 'ventas_' . date('YmdHis');
    header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
    header("Cache-Control: max-age=0");
    // Genera Excel
    $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, "Excel5");
    // Escribir
    $writer->save('php://output');
    exit;
  }

  public function detalleexcel($finicio = FALSE, $factual = FALSE, $empresa = FALSE)
  {
    $generados = $this->Controlador_model->detalle($finicio, $factual, $empresa, '1');
    $anulados = $this->Controlador_model->detalle($finicio, $factual, $empresa, '3');
    // Propiedades del archivo excel
    $this->phpexcel->getProperties()->setTitle("Lista de Utilidad")->setDescription("Lista de Venta");
    // Setiar la solapa que queda actia al abrir el excel
    $this->phpexcel->setActiveSheetIndex(0);
    // Solapa excel para trabajar con PHP
    $sheet = $this->phpexcel->getActiveSheet();
    $sheet->setTitle("Lista de Venta");
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
    $sheet->getColumnDimension('K')->setAutoSize(true);
    $sheet->getColumnDimension('L')->setAutoSize(true);
    $sheet->getColumnDimension('M')->setAutoSize(true);
    $sheet->getStyle('I')->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('J')->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('K')->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('L')->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('M')->getNumberFormat()->setFormatCode('#,##0.00');
    $style_header = array(
      'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
      'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7'))
    );
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $sheet->getStyle('A1:M1')->applyFromArray($style_header);
    $sheet->getStyle("A1:M1")->applyFromArray($style);
    $sheet->setCellValue('A1', 'NRO');
    $sheet->setCellValue('B1', 'FECHA');
    $sheet->setCellValue('C1', 'HORA');
    $sheet->setCellValue('D1', 'ESTADO');
    $sheet->setCellValue('E1', 'VENTA');
    $sheet->setCellValue('F1', 'USUARIO');
    $sheet->setCellValue('G1', 'PRODUCTO');
    $sheet->setCellValue('H1', 'COMPRA');
    $sheet->setCellValue('I1', 'VENTA');
    $sheet->setCellValue('J1', 'UTILIDAD POR VENTA');
    $sheet->setCellValue('K1', 'CANTIDAD VENDIDO');
    $sheet->setCellValue('L1', 'UTILIDAD TOTAL');
    $sheet->setCellValue('M1', 'MONTO DE VENTA');
    $i = 1;
    foreach ($generados as $key =>  $value) {
      $venta = $this->Controlador_model->get($value->id, 'venta');
      $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
      $vendedor = $this->Controlador_model->get($venta->usuario_creador, 'usuario');

      $i++;
      $utilidadventa = $value->precioventa - $value->preciocompra;
      $utilidadTotal = ($value->precioventa * $value->cantidad) - ($value->preciocompra * $value->cantidad);
      $sheet->setCellValue('A' . $i, $key + 1);
      $sheet->setCellValue('B' . $i, $venta->created);
      $sheet->setCellValue('C' . $i, $venta->hora);
      $sheet->setCellValue('D' . $i, "GENERADO");
      $sheet->setCellValue('E' . $i, $venta->serie . '-' . $venta->numero);
      $sheet->setCellValue('F' . $i, $vendedor->usuario);
      if ($value->tipo == '0') {
        $producto = $this->Controlador_model->get($value->producto, 'producto');
        $sheet->setCellValue('G' . $i, $producto->nombre);
      } else {
        $sheet->setCellValue('G' . $i, $value->nombre);
      }
      $sheet->setCellValue('H' . $i, $value->preciocompra);
      $sheet->setCellValue('I' . $i, $value->precioventa);
      $sheet->setCellValue('J' . $i, $utilidadventa);
      $sheet->setCellValue('K' . $i, $value->cantidad);
      $sheet->setCellValue('L' . $i, $utilidadTotal);
      $sheet->setCellValue('M' . $i, $value->subtotal);
    }
    foreach ($anulados as $value) {
      $venta = $this->Controlador_model->get($value->id, 'venta');
      $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
      $vendedor = $this->Controlador_model->get($venta->usuario_creador, 'usuario');
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $i++;
      $utilidadventa =  $value->precioventa - $value->preciocompra;
      $utilidadTotal = ($value->precioventa * $value->cantidad) - ($value->preciocompra * $value->cantidad);
      $sheet->setCellValue('A' . $i, $key + 1);
      $sheet->setCellValue('B' . $i, $venta->created);
      $sheet->setCellValue('C' . $i, $venta->hora);
      $sheet->setCellValue('D' . $i, "GENERADO");
      $sheet->setCellValue('E' . $i, $venta->serie . '-' . $venta->numero);
      $sheet->setCellValue('F' . $i, $vendedor->usuario);
      $sheet->setCellValue('G' . $i, $producto->nombre);
      $sheet->setCellValue('H' . $i, $value->preciocompra);
      $sheet->setCellValue('I' . $i, $value->precioventa);
      $sheet->setCellValue('J' . $i, $utilidadventa);
      $sheet->setCellValue('K' . $i, $value->cantidad);
      $sheet->setCellValue('L' . $i, $utilidadTotal);
      $sheet->setCellValue('M' . $i, $value->subtotal);
    }
    // Salida
    header("Content-Type: application/vnd.ms-excel");
    $nombreArchivo = 'ventasdetalle_' . date('YmdHis');
    header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
    header("Cache-Control: max-age=0");
    // Genera Excel
    $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, "Excel5");
    // Escribir
    $writer->save('php://output');
    exit;
  }

  public function emitir($id)
  {
    $venta = $this->Controlador_model->get($id, $this->controlador);
    $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
    $ventadetalle = $this->Controlador_model->getDetalle($id, 'ventadetalle');
    $ruc_emisor = $empresa->ruc;
    $tipo_comprobante = $venta->tipoventa == 'FACTURA' ? "01" : "03";
    $tipodeproceso = $empresa->tipoproceso;
    $emisor['ruc'] = $ruc_emisor;
    $emisor['tipo_doc'] = "6";
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
    $archivo = $ruc_emisor . '-' . $tipo_comprobante . '-' . $venta->serie . '-' . $venta->numero;

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

    $data_comprobante = $this->crear_cabecera($emisor, $venta);
    $procesarcomprobante = new Procesarcomprobante();
    $resp = $procesarcomprobante->procesar_factura($data_comprobante, $ventadetalle, $rutas);
    $ventas['id'] = $id;
    $ventas['emision'] = isset($resp['cod_sunat']) ? $resp['cod_sunat'] : NULL;
    $ventas['hash'] = isset($resp['hash_cpe']) ? $resp['hash_cpe'] : NULL;
    $this->Controlador_model->updateventa($ventas);
    echo json_encode($resp);
    exit();
  }

  public function enviomasivo($finicio, $factual, $empresa)
  {
    $procesado = 0;
    $noprocesado = 0;
    $usuario = $this->perfil == 1 ? FALSE : $this->usuario;
    $validar = $this->Controlador_model->validar($finicio, $factual, $empresa);
    if ($validar) {
      foreach ($validar as $value) {
        if ($this->masivo($value->id, $empresa)) {
          $procesado++;
        } else {
          $noprocesado++;
        }
      }
      $data = array('respuesta' => 'ok', 'procesado' => $noprocesado, 'noprocesado' => $procesado);
    } else {
      $data = array('respuesta' => 'not');
    }
    echo json_encode($data);
  }

  public function masivo($id, $empresa)
  {
    $empresa = $this->Controlador_model->get($empresa, 'empresa');
    $venta = $this->Controlador_model->get($id, $this->controlador);
    $ventadetalle = $this->Controlador_model->getDetalle($id, 'ventadetalle');
    $ruc_emisor = $empresa->ruc;
    $tipo_comprobante = $venta->tipoventa == 'FACTURA' ? "01" : "03";
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
    $archivo = $ruc_emisor . '-' . $tipo_comprobante . '-' . $venta->serie . '-' . $venta->numero;

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

    $data_comprobante = $this->crear_cabecera($emisor, $venta);
    $procesarcomprobante = new Procesarcomprobante();
    $resp = $procesarcomprobante->procesar_factura($data_comprobante, $ventadetalle, $rutas);
    $ventas['id'] = $id;
    $ventas['emision'] = isset($resp['cod_sunat']) ? $resp['cod_sunat'] : NULL;
    $ventas['hash'] = isset($resp['hash_cpe']) ? $resp['hash_cpe'] : NULL;
    $this->Controlador_model->updateventa($ventas);
  }

  function crear_cabecera($emisor, $data)
  {
    $tipo_comprobante = $data->tipoventa == 'FACTURA' ? "01" : "03";
    $fecha = date('Y-m-d');
    $date1 = new DateTime($data->created);
    $date2 = new DateTime($fecha);
    $diff = $date1->diff($date2);
    if ($diff->days > 5) {
      $fechas = date('Y-m-d', strtotime($data->created . '+ ' . ($diff->days - 5) . ' days'));
    } else {
      $fechas = $data->created;
    }
    $cliente = $this->Controlador_model->get($data->cliente, 'cliente');
    $tipo = $data->tipoventa == 'FACTURA' ? 6 : 1;
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
      'POR_IGV' => "0.01",
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
      'TIPO_COMPROBANTE_MODIFICA' => "",
      'NRO_DOCUMENTO_MODIFICA' => "",
      'COD_TIPO_MOTIVO' => "",
      'DESCRIPCION_MOTIVO' => "",
      //===============================================
      'NRO_COMPROBANTE' => $data->serie . '-' . $data->numero,
      'FECHA_DOCUMENTO' => $fechas,
      //pag. 31 //fecha de vencimiento
      'FECHA_VTO' => $fechas,
      'COD_TIPO_DOCUMENTO' => $tipo_comprobante,
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
      'EMISOR_RUC' => $emisor['ruc'],
      'EMISOR_USUARIO_SOL' => $emisor['usuariosol'],
      'EMISOR_PASS_SOL' => $emisor['clavesol']
    );
    return $cabecera;
  }

  public function printfcomprobante($idventa)
  {
    $venta = $this->Controlador_model->get($idventa, 'venta');
    $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
    $ingreso = $this->db->where("venta", $idventa)->get('ingreso')->row();
    $htmlComprobante = "
       <h4 class='text-center'> $venta->serie - $venta->numero</h4>
          <span class='float-left'>Fecha: $venta->created</span><br>
          <span class='float-left'>Cliente: " . ($cliente ? $cliente->nombre : 'SIN DATOS') . "</span>
        <table class='table'>
        <thead>
          <tr>
            <th class='text-center'>#</th>
            <th class='text-center'>Descripcion</th>
            <th class='text-center'>Cant</th>
            <th class='text-right;' style='width:100px'>Importe S/</th>
          </tr>
        </thead>
        <tbody>";
    if ($venta->consumo == '1') {
      $htmlComprobante .=
        "<tr>
          <td class='text-center'>1</td>
          <td class='text-center'>Por consumo</td>
          <td class='text-center'>1</td>
          <td class='text-right'>" . number_format($venta->deudatotal, 2) . "</td>
        </tr>";
    } else {
      $ventadetalle = $this->Controlador_model->pedidodetalle($idventa);
      foreach ($ventadetalle as $key => $value) {
        $htmlComprobante .=
          "<tr>
            <td class='text-center'>" . ($key + 1) . "</td>
            <td class='text-center'> $value->nombre [" . ($value->variante ? ($value->cantidadvariante * $value->cantidad) : $value->cantidad) . "]</td>
            <td class='text-center'>$value->cantidad</td>
            <td class='text-right'>" . number_format($value->cantidad * $value->precio, 2) . "</td>
            </tr>";
      }
    }

    $htmlComprobante .= "
    </tbody>
    <tfoot>
      <tr>
          <td colspan='3' style='text-align:right; font-weight:bold;'>Total S/</td>
          <td style='text-align:right; font-weight:bold;'> $venta->montototal</td>
      </tr>
      <tr>
        <td colspan='3' style='text-align:right; font-weight:bold; border:none'>Descuento S/</td>
        <td style='text-align:right; font-weight:bold; border:none'> $venta->descuento</td>
      </tr>
      <tr>
        <td colspan='3' style='text-align:right; font-weight:bold; border:none; color:#36a229''>Pagado(" . ($ingreso ? $ingreso->metodopago : 'SIN DATOS') . ") S/</td>
        <td style='text-align:right; font-weight:bold; border:none; color:#36a229'> $venta->deudatotal</td>
      </tr>
      <tr>
        <td colspan='3' style='text-align:right; font-weight:bold; border:none;'>Recibido S/</td>
        <td style='text-align:right; font-weight:bold; border:none;'>$venta->pago</td>
      </tr>
      <tr>
        <td colspan='3' style='text-align:right; font-weight:bold; border:none'>Vuelto S/</td>
        <td style='text-align:right; font-weight:bold; border:none'>$venta->vuelto</td>
      </tr>
    </tfoot>
    </table>";

    $telefono = $cliente->telefono != "" ? $cliente->telefono : "";
    $email = $cliente->correo != "" ? $cliente->correo : "";
    $htmlFotter = "
    <div class='row' style='margin-bottom:10px'>
      <div class='col-md-6'>
        <div class='input-group input-group-sm'>
          <input type='email' id='correo' class='form-control' placeholder='correo@pucallsystem.com' value='$email'>
          <span class='input-group-btn' id='span-print'>
            <button id='enviarcorreo' class='btn btn-success' onclick='sendMail($idventa)' type='button'>
            <i class='fa fa-paper-plane' aria-hidden='true'></i>
            </button>
          </span>
        </div>
      </div>
      <div class='col-md-6'>
        <div class='input-group input-group-sm'>
        <span class='input-group-addon' id='sizing-addon3'>+51</span>
        <input type='text' id='telefonoWP' class='form-control' placeholder='999999999' value='$telefono' autocomplete='off'>
        <span class='input-group-btn' id='span-print'>
          <button class='btn btn-success' onclick='sentTicketWA($idventa)' type='button'><i class='fa fa-whatsapp' aria-hidden='true'></i></button>
        </span> 
        </div>
      </div>
    </div>
   <button data-dismiss='modal' class='btn btn-default hiddenpr'>Cerrar</button>
   <button class='btn btn-add' onclick='imprimircomprobante($empresa->tipoimpresora , $idventa)' id='btncomprobante'>Imprimir</button>";

    echo json_encode(["htmlComprobante" => $htmlComprobante, "htmlFotter" => $htmlFotter]);
  }

  public function imprimir($id)
  {
    $venta = $this->Controlador_model->get($id, 'venta');
    $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
    $mesa = $this->Controlador_model->get($venta->mesa, 'mesa');
    $usuario = $this->Controlador_model->get($venta->usuario_creador, 'usuario');
    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
    $nombre_impresora = $empresa->nombreimpresora;
    $connector = new WindowsPrintConnector($nombre_impresora);
    $printer = new Printer($connector);
    $comprobante = $venta->serie . '|' . $venta->numero;
    $numerocomprobante = $venta->serie . '-' . $venta->numero;
    $tipocom = $venta->tipoventa == 'FACTURA' ? "01" : "03";
    $tipo = $venta->tipoventa == 'FACTURA' ? 6 : 1;
    $url_base = 'archivos_xml_sunat/imgqr/';
    $encriptado = password_hash($venta->numero, PASSWORD_BCRYPT);
    $encriptadohash = substr(str_replace("$2y$10$", "", $encriptado), 0, 27) . '=';
    $codigohash = $venta->hash ? $venta->hash : $encriptadohash;
    $params['data'] = $empresa->ruc . '|' . $tipocom . '|' . $comprobante . '|0.00|' . $venta->montototal . '|' . date('d/m/Y', strtotime($venta->created)) . '|' . $tipo . '|' . $cliente->documento . '|' . $codigohash . '|';
    $params['level'] = 'H';
    $params['size'] = 3;
    $params['savename'] = $url_base . $numerocomprobante . '.png';
    $this->ciqrcode->generate($params);
    # Mando un numero de respuesta para saber que se conecto correctamente.
    # Vamos a alinear al centro lo próximo que imprimamos
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    /* Intentaremos cargar e imprimir el logo */
    try {
      $url = 'iles/Setting/logo.png';
      $logo = EscposImage::load($url, false);
      $printer->bitImage($logo);
    } catch (Exception $e) {
      /*No hacemos nada si hay error*/
    }
    /* Ahora vamos a imprimir un encabezado */
    if ($venta->tipoventa == 'OTROS') {
      $printer->text("\n" . "TICKET DE VENTA" . "\n");
    } else {
      if ($empresa->tipo == 1) {
        $printer->text("\n" . $empresa->razonsocial . "\n");
      } else {
        $printer->text("\n" . $empresa->nombre . "\n");
        $printer->text($empresa->razonsocial . "\n");
      }
      $printer->text($empresa->direccion . "\n");
      $printer->text($empresa->departamento . ' ' . $empresa->provincia . ' ' . $empresa->distrito . "\n");
      $printer->text("RUC: " . $empresa->ruc . "\n");
      $printer->text("DELIVERY " . $empresa->telefono . "\n");
      #La fecha también
      $printer->text("------------------------------------------------" . "\n");
      $printer->text($venta->tipoventa . " DE VENTA ELECTRONICA" . "\n");
      $printer->text($venta->serie . "-" . $venta->numero . "\n");
    }
    $printer->text("------------------------------------------------" . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text($cliente->tipodocumento . ": " . $cliente->documento . "\n");
    $printer->text("CLIENTE: " . $cliente->nombre . ' ' . $cliente->apellido . "\n");
    $printer->text("DIRECCION: " . $cliente->direccion . "\n");
    $printer->text("FECHA EMISION: " . $venta->created . " " . $venta->hora . "\n");
    $printer->text("FECHA VENCIMIENTO: " . $venta->created . "\n");
    $printer->text("MODEDA: SOLES\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("------------------------------------------------\n");
    $printer->text("[CANT.]    DESCRIPCION          P/U       TOTAL\n");
    $printer->text("------------------------------------------------" . "\n");
    /* Ahora vamos a imprimir los productos. Alinear a la izquierda para la cantidad y el nombre */
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $total = 0;
    $ventadetalle = $this->Controlador_model->pedidodetalle($id);
    foreach ($ventadetalle as $value) {
      $total += $value->precio * $value->cantidad;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $printer->text($producto->codigo . " " . $value->nombre . "\n");
      $printer->text("[" . $value->cantidad . "]                    " . $value->precio . "    " . number_format($value->precio * $value->cantidad, 2) . " \n");
    }
    /* Terminamos de imprimir los productos, ahora va el total */
    $printer->text("------------------------------------------------" . "\n");
    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    if ($venta->tipoventa == 'OTROS') {
      $printer->text("TOTAL:   S/   " . number_format($total, 2) . "\n");
    } else {
      $printer->text("OP. EXONERADA:   S/   " . number_format($total, 2) . "\n");
      $printer->text("IGV:   S/   " . number_format(0, 2) . "\n");
      $printer->text("TOTAL:   S/   " . number_format($total, 2) . "\n");
    }
    $pago = 0;
    $vuelto = 0;
    $pagos = $this->Controlador_model->getDetalle($id, 'ingreso');
    foreach ($pagos as $value) {
      if ($value->metodopago == 'EFECTIVO') {
        $vuelto = $venta->pago - $value->monto;
      }
      $printer->text("PAGO ($value->metodopago):   S/   " . number_format($value->monto, 2) . "\n");
    }
    $printer->text("RECIBIO:   S/   " . number_format($venta->pago, 2) . "\n");
    $printer->text("VUELTO:   S/   " . number_format($vuelto, 2) . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("------------------------------------------------" . "\n");
    $printer->text("SON: " . num_to_letras($total) . "\n");
    $printer->text("VENDEDOR: " . $usuario->usuario . "\n");
    $printer->text("MESA: " . $mesa->nombre . "\n");
    $printer->text("------------------------------------------------" . "\n");
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    if ($venta->tipoventa == 'OTROS') {
      $printer->text("GRACIAS POR SU COMPRA" . "\n");
    } else {
      $printer->text($codigohash . "\n");
      $printer->text("------------------------------------------------" . "\n");
      /* Intentaremos cargar e imprimir el logo */
      try {
        $codeurl = $url_base . $numerocomprobante . '.png';
        $codeqr = EscposImage::load($codeurl, false);
        $printer->bitImage($codeqr);
      } catch (Exception $e) {
        /*No hacemos nada si hay error*/
      }
      $printer->text("NO SE ACEPTAN DEVOLUCIONES Y/O\n");
      $printer->text("CAMBIO DESPUES DE LAS 24 HORAS\n");
      $printer->text("------------------------------------------------\n");
      $printer->text("REPRESENTACION IMPRESA DE\n");
      $printer->text("COMPROBANTE ELECTRONICO\n");
      $printer->text("AUTORIZADO MEDIANTE LA RESOLUCION DE\n");
      $printer->text("INTENDENCIA N°. 034-005-0005315\n");
    }
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
    $venta = $this->Controlador_model->get($id, 'venta');
    $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
    $comprobante = $venta->serie . '|' . $venta->numero;
    $numerocomprobante = $venta->serie . '-' . $venta->numero;
    $tipo = $venta->tipoventa == 'FACTURA' ? 6 : 1;
    $tipocom = $venta->tipoventa == 'FACTURA' ? "01" : "03";
    $url_base = 'archivos_xml_sunat/imgqr/';
    $encriptado = password_hash($venta->numero, PASSWORD_BCRYPT);
    $encriptadohash = substr(str_replace("$2y$10$", "", $encriptado), 0, 27) . '=';
    $codigohash = $venta->hash ? $venta->hash : $encriptadohash;
    $params['data'] = $empresa->ruc . '|' . $comprobante . '|' . $venta->numero . '|0.00|' . $venta->montototal . '|' . date('d/m/Y', strtotime($venta->created)) . '|' . $tipo . '|' . $cliente->documento . '|' . $codigohash . '|';
    $params['level'] = 'H';
    $params['size'] = 5;
    $params['savename'] = $url_base . $numerocomprobante . '.png';
    $this->ciqrcode->generate($params);
    $data = array(
      'venta' => $venta,
      'empresa' => $empresa,
      'cliente' => $cliente,
      'codigohash' => $codigohash,
      'qrcode' => '../../' . $url_base . $numerocomprobante . '.png',
      'mesa' => $this->Controlador_model->get($venta->mesa, 'mesa'),
      'usuario' => $this->Controlador_model->get($venta->usuario_creador, 'usuario'),
      'ingresos' => $this->Controlador_model->getDetalle($id, 'ingreso'),
      'ventadetalle' => $this->Controlador_model->comanda($id)
    );
    $this->load->view('imprimircomprobante', $data);
  }

  public function getimprimir($id)
  {
    $venta = $this->Controlador_model->get($id, 'venta');
    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
    $empresa = $this->Controlador_model->get($this->empresa, 'empresa');
    $comprobante = $venta->serie . '|' . $venta->numero;
    $numerocomprobante = $venta->serie . '-' . $venta->numero;
    $tipo = $venta->tipoventa == 'FACTURA' ? 6 : 1;
    $tipocom = $venta->tipoventa == 'FACTURA' ? "01" : "03";
    $data = array(
      'ventas' => $venta,
      'empresa' => $empresa,
      'cliente' => $cliente,
      'usuario' => $this->Controlador_model->get($venta->usuario_creador, 'usuario'),
      'vuelto' => number_format($venta->pago - $venta->total, 2),
      'codigoqr' => $empresa->ruc . '|' . $tipocom . '|' . $comprobante . '|0.00|' . $venta->montototal . '|' . date('d/m/Y', strtotime($venta->created)) . '|' . $tipo . '|' . $cliente->documento . '|' . $venta->hash . '|',
      'importeletra' => num_to_letras($venta->total),
      'pagos' => $this->Controlador_model->getDetalle($id, 'ingreso'),
      'ventadetalle' => $this->Controlador_model->getDetalle($id, 'ventadetalle')
    );
    echo json_encode($data);
  }

  public function notas($id)
  {
    $sale = $this->Controlador_model->get($id, 'venta');
    $cliente = $this->Controlador_model->get($sale->cliente, 'cliente');
    $empresa = $this->Controlador_model->get($sale->empresa, 'empresa');
    $posales = $this->Controlador_model->getDetalle($id, 'ventadetalle');
    $ticket = '<input class="form-control" id="id" type="hidden" name="id" value="' . $id . '">';
    $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">' . $sale->tipoventa . '</label>
    <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $sale->serie . '-' . $sale->numero . '"></div></div>';
    $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">' . $cliente->tipo . '</label>
    <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $cliente->documento . '"></div></div>';
    $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">CLIENTE</label>
    <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $cliente->nombre . '"></div></div>';
    $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">FECHA</label>
    <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $sale->created . '"></div></div>';
    $ticket .= '<div class="col-md-12">
    <table class="table table-bordered table-striped">
    <thead><tr><th><em>#</em></th><th>Descripcion</th><th>Cant</th><th>Precio</th><th>SubTotal</th></tr></thead><tbody>';
    $i = 1;
    foreach ($posales as $posale) {
      $producto = $this->Controlador_model->get($posale->producto, 'producto');
      $ticket .= '<tr><td align="center">' . $i . '</td><td align="left">' . $posale->nombre . '</td>
      <td align="center">' . $posale->cantidad . '</td><td align="right">' . number_format($posale->precio, 2) . '</td>
      <td align="right">' . number_format($posale->cantidad * $posale->precio, 2) . '</td></tr>';
      $i++;
    }
    // barcode codding type
    $ticket .= '</tbody></table><table class="table table-bordered table-striped" style="margin-bottom:8px;">
    <tbody><tr><td colspan="2" style="text-align:left;">TOTAL</td><td colspan="2" style="text-align:right;">' . number_format($sale->montototal, 2) . ' Soles</td></tr>';
    $ticket .= '</tbody></table>';
    echo $ticket;
  }

  public function crearnotas()
  {
    $id = $this->input->post('id');
    if ($id) {
      $venta = $this->Controlador_model->get($id, 'venta');
      if ($venta->montototal <= 0) {
        mensaje_alerta('error', 'crear');
      } else {
        if ($this->Controlador_model->crearnotas($id)) {
          mensaje_alerta('hecho', 'crear');
        } else {
          mensaje_alerta('error', 'crear');
        }
      }
      redirect($this->url);
    } else {
      show_404();
    }
  }

  public function vernotas($id)
  {
    $sale = $this->Controlador_model->get($id, 'venta');
    $notacredito = $this->Controlador_model->getnotas($id, 'notacredito');
    $notadebito = $this->Controlador_model->getnotas($id, 'notadebito');
    $cliente = $this->Controlador_model->get($sale->cliente, 'cliente');
    $empresa = $this->Controlador_model->get($sale->empresa, 'empresa');
    $posales = $this->Controlador_model->getDetalle($id, 'ventadetalle');
    $ticket = '';
    if ($notacredito) {
      if ($notacredito->motivo == '01') {
        $notacredito_descripcion = 'ANULACION DE LA OPERACION';
      }
      if ($notacredito->motivo == '02') {
        $notacredito_descripcion = 'ANULACION POR ERROR EN EL RUC';
      }
      if ($notacredito->motivo == '03') {
        $notacredito_descripcion = 'CORRECION POR ERROR EN LA DESCRIPCION';
      }
      if ($notacredito->motivo == '04') {
        $notacredito_descripcion = 'DESCUENTO GLOBAL';
      }
      if ($notacredito->motivo == '05') {
        $notacredito_descripcion = 'DESCUENTO POR ITEM';
      }
      if ($notacredito->motivo == '06') {
        $notacredito_descripcion = 'DEVOLUCION TOTAL';
      }
      if ($notacredito->motivo == '07') {
        $notacredito_descripcion = 'DEVOLUCION POR ITEM';
      }
      if ($notacredito->motivo == '08') {
        $notacredito_descripcion = 'BONIFICACION';
      }
      if ($notacredito->motivo == '09') {
        $notacredito_descripcion = 'DISMINUCION EN EL VALOR';
      }
      $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">FECHA</label>
       <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $notacredito->created . '"></div></div>';
      $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">NOTA CREDITO</label>
       <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $notacredito->serie . '-' . $notacredito->numero . '"></div></div>';
      $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">MOTIVO</label>
       <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $notacredito_descripcion . '"></div></div>';
      $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">DESCRIPCION</label>
       <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $notacredito->descripcion . '"></div></div>';
    }
    if ($notadebito) {
      if ($notadebito->motivo == '01') {
        $notadebito_descripcion = 'INTERES POR MORA';
      }
      if ($notadebito->motivo == '02') {
        $notadebito_descripcion = 'AUMENTO EN EL VALOR';
      }
      if ($notadebito->motivo == '03') {
        $notadebito_descripcion = 'PENALIDADES';
      }
      $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">FECHA</label>
       <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $notadebito->created . '"></div></div>';
      $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">NOTA DEBITO</label>
       <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $notadebito->serie . '-' . $notadebito->numero . '"></div></div>';
      $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">MOTIVO</label>
       <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $notadebito_descripcion . '"></div></div>';
      $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">DESCRIPCION</label>
       <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $notadebito->descripcion . '"></div></div>';
    }
    $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">DOC. REFERENCIA</label>
     <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $sale->serie . '-' . $sale->numero . '"></div></div>';
    $ticket .= '<div class="form-group"><label class="col-sm-2 control-label" for="nombre">CLIENTE</label>
     <div class="col-sm-10"><input disabled class="form-control" type="text" value="' . $cliente->nombre . '"></div></div>';
    $ticket .= '<div class="col-md-12 text-center" style="margin-bottom:8px;">';
    if ($notacredito) {
      $archivo = $empresa->ruc . '-07-' . $notacredito->serie . '-' . $notacredito->numero;
      if ($notacredito->emision == '0') {
        $ticket .= '<a target="_blank" class="btn btn-sm btn-success" href="archivos_xml_sunat/cpe_xml/produccion/' . $empresa->ruc . '/R-' . $archivo . '.XML" title="CDR"><i class="fa fa-briefcase"></i></a>';
      } else {
        $ticket .= '<a class="btn btn-sm btn-primary" title="Emitir" onclick="procesar_documento_electronico(' . "'" . $id . "'" . ')"><i class="fa fa-upload"></i></a>';
      }
    }
    if ($notadebito) {
      $archivo = $empresa->ruc . '-08-' . $notadebito->serie . '-' . $notadebito->numero;
      if ($notadebito->emision == '0') {
        $ticket .= '<a target="_blank" class="btn btn-sm btn-success" href="archivos_xml_sunat/cpe_xml/produccion/' . $empresa->ruc . '/R-' . $archivo . '.XML" title="CDR"><i class="fa fa-briefcase"></i></a>';
      } else {
        $ticket .= '<a class="btn btn-sm btn-primary" title="Emitir" onclick="procesar_documento_electronico(' . "'" . $id . "'" . ')"><i class="fa fa-upload"></i></a>';
      }
    }
    $ticket .= '<a class="btn btn-sm btn-danger" title="Imprimir" onclick="PrintTicket(' . "'" . $id . "'" . ')"><i class="fa fa-print"></i></a>
     </div><br>';
    $ticket .= '<div class="col-md-12">
     <table class="table table-bordered table-striped" style="margin-bottom:8px;">
     <thead><tr><th><em>#</em></th><th>Descripcion</th><th>Cant</th><th>Precio</th><th>SubTotal</th></tr></thead><tbody>';
    $i = 1;
    foreach ($posales as $posale) {
      $producto = $this->Controlador_model->get($posale->producto, 'producto');
      $ticket .= '<tr><td align="center">' . $i . '</td><td align="left">' . $posale->nombre . '</td>
       <td align="center">' . $posale->cantidad . '</td><td align="right">' . number_format($posale->precio, 2) . '</td>
       <td align="right">' . number_format($posale->cantidad * $posale->precio, 2) . '</td></tr>';
      $i++;
    }
    // barcode codding type
    $ticket .= '</tbody></table><table class="table table-bordered table-striped" style="margin-bottom:8px;">
     <tbody><tr><td colspan="2" style="text-align:left;">TOTAL</td><td colspan="2" style="text-align:right;">' . number_format($sale->montototal, 2) . ' Soles</td></tr>';
    $ticket .= '</tbody></table></div>';
    echo $ticket;
  }

  public function sentTicketWA($phone, $venta)
  {
    $url = "CPETemp";
    if (!file_exists($url)) {
      mkdir($url, 0777, true);
      // var_dump("entro");
    }
    $this->showcomprobanteSaved($url, $phone, $venta);
  }

  public function showcomprobanteSaved($url, $phone, $idventa)
  {
    $venta = $this->Controlador_model->get($idventa, 'venta');
    $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
    $comprobante = $venta->serie . '|' . $venta->numero;
    $numerocomprobante = $venta->serie . '-' . $venta->numero;
    $tipo = $venta->tipoventa == 'FACTURA' ? 6 : 1;
    $tipocom = $venta->tipoventa == 'FACTURA' ? "01" : "03";
    $url_base = 'archivos_xml_sunat/imgqr/';
    $encriptado = password_hash($venta->numero, PASSWORD_BCRYPT);
    $encriptadohash = substr(str_replace("$2y$10$", "", $encriptado), 0, 27) . '=';
    $codigohash = $venta->hash ? $venta->hash : $encriptadohash;
    $params['data'] = $empresa->ruc . '|' . $tipocom . '|' . $comprobante . '|0.00|' . $venta->montototal . '|' . date('d/m/Y', strtotime($venta->created)) . '|' . $tipo . '|' . $cliente->documento . '|' . $codigohash . '|';
    $params['level'] = 'H';
    $params['size'] = 5;
    $params['savename'] = $url_base . $numerocomprobante . '.png';
    $this->ciqrcode->generate($params);
    $data = array(
      'venta' => $venta,
      'empresa' => $empresa,
      'cliente' => $cliente,
      'codigohash' => $codigohash,
      'qrcode' => base_url() . $url_base . $numerocomprobante . '.png',
      'mesa' => $this->Controlador_model->get($venta->mesa, 'mesa'),
      'usuario' => $this->Controlador_model->get($venta->usuario_creador, 'usuario'),
      'ingresos' => $this->Controlador_model->getDetalle($idventa, 'ingreso'),
      'ventadetalle' => $this->Controlador_model->comanda($idventa)
    );
    $this->load->view('imprimircomprobante', $data);
    $filename = $url . "/" . $venta->serie . "-" . $venta->numero . "-" . $cliente->documento . "-" . date('Y-m-d') . ".pdf";
    $html = $this->output->get_output();
    $this->dompdf->loadHtml($html);
    $this->dompdf->setPaper('A4', 'portrait');
    // $this->dompdf->setPaper(array(0, 0, 100, 100));
    $this->dompdf->render();
    $output = $this->dompdf->output();
    file_put_contents($filename, $output);
    $this->sendCPToNumber($filename, $phone);
    //$this->switshtable();
    // $this->dompdf->stream($filename, array("Attachment"=>1));
  }

  public function sendCPToNumber($uri, $phone)
  {
    $texto = "Hola, gracias por tu consumo. Puedes descargar tu Comprobante Electronico en el siguiente enlace! " . urlencode(base_url() . $uri);
    redirect("https://wa.me/51" . $phone . "?text=" . $texto);
  }

  function sendemail()
  {
    $idventa = $this->input->post('idventa');
    $this->crearPDF($idventa);
    $deta = $this->Controlador_model->get($idventa, 'venta');
    $cliente = $this->Controlador_model->get($deta->cliente, 'cliente');
    $nombre_pdf = $deta->serie . "-" . $deta->numero . "-" . $cliente->documento . "-" . $deta->created . ".pdf";

    $correoTo = $this->input->post('correo');
    $path = base_url() . "CPETemp/" . $nombre_pdf;
    $asunto = "Documento electronico";
    $correo = "fel@pucallsystem.com";
    $password = "=ACn*SH^QYh6";
    $name = "PucallSystem";
    $mail = $this->phpmailer_lib->load();
    //$mail->isSMTP();                      // Establecer el correo electrónico para utilizar SMTP
    $mail->Host = 'mail.pucallsystem.com';  // Especificar el servidor de correo a utilizar 
    $mail->SMTPAuth = true;                 // Habilitar la autenticacion con SMTP
    $mail->Username = $correo;              // Correo electronico saliente ejemplo: tucorreo@gmail.com
    $mail->Password = $password;            // Tu contraseña de gmail
    $mail->SMTPSecure = 'tls';              // Habilitar encriptacion, `ssl` es aceptada
    $mail->Port = 465;                      // Puerto TCP  para conectarse 
    $mail->setFrom($correo, $name);         //Introduzca la dirección de la que debe aparecer el correo electrónico. Puede utilizar cualquier dirección que el servidor SMTP acepte como válida. El segundo parámetro opcional para esta función es el nombre que se mostrará como el remitente en lugar de la dirección de correo electrónico en sí.
    $mail->addReplyTo($correo, "HOLA");         //Introduzca la dirección de la que debe responder. El segundo parámetro opcional para esta función es el nombre que se mostrará para responder
    $mail->addAddress($correoTo);   // Agregar quien recibe el e-mail enviado
    /* $message = file_get_contents($template);
    $message = str_replace('{{first_name}}', $name, $message);
    $message = str_replace('{{message}}', $txt_message, $message);
    $message = str_replace('{{customer_email}}', $correo, $message); */

    $mail->isHTML(true);  // Establecer el formato de correo electrónico en HTML
    $template = '<!doctype html>
    <html lang="es">
    <head>
    <meta charset="UTF-8">
    <title>Aloha!</title>
    <style type="text/css">
        * {
            font-family: Verdana, Arial, sans-serif;
        }
    </style>
    </head>
    <body>
    <p>Hola!</p>
    <p>Adjuntamos el comprobante electrónico de su compra.</p>
    </body>
    </html>';
    $mail->Subject = $asunto;
    $mail->msgHTML($template);
    $archivo = 'comprobante.pdf';
    //if (!$mail->AddAttachment($path, $archivo)) {
    $url = $path;
    $fichero = file_get_contents($url);
    $mail->addStringAttachment($fichero, $archivo);
    // }

    if (!$mail->Send()) {
      echo json_encode(array('status' => FALSE));
      exit();
    } else {
      echo json_encode(array('status' => TRUE));
      exit();
    }
  }

  public function crearPDF($idventa)
  {
    $url = "CPETemp";
    if (!file_exists($url)) {
      mkdir($url, 0777, true);
      // var_dump("entro");
    }
    $venta = $this->Controlador_model->get($idventa, 'venta');
    $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
    $comprobante = $venta->serie . '|' . $venta->numero;
    $numerocomprobante = $venta->serie . '-' . $venta->numero;
    $tipo = $venta->tipoventa == 'FACTURA' ? 6 : 1;
    $tipocom = $venta->tipoventa == 'FACTURA' ? "01" : "03";
    $url_base = 'archivos_xml_sunat/imgqr/';
    $encriptado = password_hash($venta->numero, PASSWORD_BCRYPT);
    $encriptadohash = substr(str_replace("$2y$10$", "", $encriptado), 0, 27) . '=';
    $codigohash = $venta->hash ? $venta->hash : $encriptadohash;
    $params['data'] = $empresa->ruc . '|' . $tipocom . '|' . $comprobante . '|0.00|' . $venta->montototal . '|' . date('d/m/Y', strtotime($venta->created)) . '|' . $tipo . '|' . $cliente->documento . '|' . $codigohash . '|';
    $params['level'] = 'H';
    $params['size'] = 5;
    $params['savename'] = $url_base . $numerocomprobante . '.png';
    $this->ciqrcode->generate($params);
    $data = array(
      'venta' => $venta,
      'empresa' => $empresa,
      'cliente' => $cliente,
      'codigohash' => $codigohash,
      'qrcode' => base_url() . $url_base . $numerocomprobante . '.png',
      'mesa' => $this->Controlador_model->get($venta->mesa, 'mesa'),
      'usuario' => $this->Controlador_model->get($venta->usuario_creador, 'usuario'),
      'ingresos' => $this->Controlador_model->getDetalle($idventa, 'ingreso'),
      'ventadetalle' => $this->Controlador_model->comanda($idventa)
    );
    $this->load->view('imprimircomprobante', $data);
    $filename = $url . "/" . $venta->serie . "-" . $venta->numero . "-" . $cliente->documento . "-" . $venta->created . ".pdf";
    $html = $this->output->get_output();
    $this->dompdf->loadHtml($html);
    $this->dompdf->setPaper('A4', 'portrait');
    // $this->dompdf->setPaper(array(0, 0, 100, 100));
    $this->dompdf->render();
    $output = $this->dompdf->output();
    file_put_contents($filename, $output);
    //$this->switshtable();
    // $this->dompdf->stream($filename, array("Attachment"=>1));

  }
}
