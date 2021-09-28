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
      $queryAdicionales = $this->db->select_sum("monto")->where("compra", $value->id)->get('compracostosadicionales')->row();
      $montoAdicional = $queryAdicionales->monto == NULL ? 0 : $queryAdicionales->monto;
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
        "S/ " . $value->montototal,
        "S/ " . number_format($montoAdicional, 2),
        "S/ " . number_format(($value->montototal + $montoAdicional), 2),
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

  public function detalleexcel($finicio = FALSE, $factual = FALSE, $empresa = FALSE)
  {
    $query = $this->Controlador_model->dataCompra($this->controlador, '0', $empresa, $finicio, $factual);
    // Propiedades del archivo excel
    $this->phpexcel->getProperties()->setTitle("Lista de Facturas")->setDescription("Lista de Compra");
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
    $sheet->getStyle('G')->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('H')->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('I')->getNumberFormat()->setFormatCode('#,##0.00');
    $style_header = array(
      'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
      'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7'))
    );
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $sheet->getStyle('A1:J1')->applyFromArray($style_header);
    $sheet->getStyle("A1:J1")->applyFromArray($style);
    $sheet->setCellValue('A1', 'CODIGO');
    $sheet->setCellValue('B1', 'EMPRESA');
    $sheet->setCellValue('C1', 'TIPO');
    $sheet->setCellValue('D1', 'N° DOCUMENTO');
    $sheet->setCellValue('E1', 'PROVEEDOR');
    $sheet->setCellValue('F1', 'ESTADO');
    $sheet->setCellValue('G1', 'MONTO COMPRA');
    $sheet->setCellValue('H1', 'MONTO FLETE');
    $sheet->setCellValue('I1', 'TOTAL');
    $sheet->setCellValue('J1', 'FECHA');
    $i = 1;
    foreach ($query as $key => $value) {
      $usuario = $this->Controlador_model->get($value->usuario, 'usuario');
      $proveedor = $this->Controlador_model->get($value->proveedor, 'proveedor');
      $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
      if ($value->estado == '1') {
        $estado = 'APROBADO';
      }
      if ($value->estado == '2') {
        $estado = 'GENERADO';
      }
      if ($value->estado == '3') {
        $estado = 'ANULADO';
      }
      $queryAdicionales = $this->db->select_sum("monto")->where("compra", $value->id)->get('compracostosadicionales')->row();
      $montoAdicional = $queryAdicionales->monto == NULL ? 0 : $queryAdicionales->monto;
      $prov = $proveedor ? $proveedor->nombre : '';
      $i++;
      $sheet->setCellValue('A' . $i, $value->codigo);
      $sheet->setCellValue('B' . $i, $empresa->ruc . " | " . $empresa->nombre . " | " . $empresa->serie);
      $sheet->setCellValue('C' . $i, $value->movimiento);
      $sheet->setCellValue('D' . $i, $value->serie . ' ' . $value->numero);
      $sheet->setCellValue('E' . $i, $prov);
      $sheet->setCellValue('F' . $i, $estado);
      $sheet->setCellValue('G' . $i, "S/ " . $value->montototal);
      $sheet->setCellValue('H' . $i, "S/ " . $montoAdicional);
      $sheet->setCellValue('I' . $i, "S/ " . ($value->montototal + $montoAdicional));
      $sheet->setCellValue('J' . $i, $value->created);
    }

    // Salida
    header("Content-Type: application/vnd.ms-excel");
    $nombreArchivo = 'compras_' . date('Y-m-d');
    header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
    header("Cache-Control: max-age=0");
    // Genera Excel
    $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, "Excel5");
    // Escribir
    ob_end_clean();
    $writer->save('php://output');
    exit();
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
      $queryAdicionales = $this->db->select_sum("monto")->where("compra", $value->id)->get('compracostosadicionales')->row();
      $montoAdicional = $queryAdicionales->monto == NULL ? 0 : $queryAdicionales->monto;
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
        "S/ " . $value->montototal,
        "S/ " . number_format($montoAdicional, 2),
        "S/ " . number_format(($value->montototal + $montoAdicional), 2),
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
        $row .= '<a onclick="imprimir(' . $data->id . ')" class="btn btn-danger" data-toggle="tooltip"><i class="fa fa-print"></i>  <span class="hidden-xs">IMPRIMIR</span></a> ';
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
    <div class="row"><div class="col-md-12">
    <div class="table-responsive">
    <table id="tabla-compra" class="table table-bordered table-striped">
    <thead>
    <tr>
      <th>#</th>
      <th>Codigo</th>
      <th>Destino</th>
      <th>Producto</th>
      <th>Tipo</th>
      <th>Cantidad</th>
      <th>Regalo</th>
      <th>Total Items</th>
      <th>Precio</th>
      <th>Precio <label class="label label-success">+ FLETE</label></th>
      <th>Sub-total</th>
      <th>Sub-total <label class="label label-success">+ FLETE</label></th>
    </tr>
    </thead>
    <tbody>';
    $totalsinflete = 0;
    $totalconflete = 0;
    $i = 0;
    $queryAdicionales = $this->db->select_sum("monto")->where("compra", $id)->get('compracostosadicionales')->row();
    $montoAdicional = $queryAdicionales->monto == NULL ? 0 : $queryAdicionales->monto;
    foreach ($compradetalle as $value) {
      $i++;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $marca = $this->Controlador_model->get($producto->categoria, 'productocategoria');
      $almacen = $this->Controlador_model->get($value->almacen, 'almacen');
      $marcanombre = $marca ? $marca->nombre : '';
      $totalsinflete += $value->subtotal;
      $totalconflete += $value->subtotal_flete;
      $ticket .= '
      <tr>
        <td>' . $i . '</td>
        <td>' . $producto->codigo . '</td>
        <td>' . $almacen->nombre . '</td>
        <td>' . $producto->nombre . ' ' . $marcanombre . '</td>
        <td>' . $value->medida . '</td>
        <td>' . $value->cantidad . '</td>
        <td>' . $value->cantidaditemregalo . '</td>
        <td>' . $value->totalitem . '</td>
        <td>' . number_format($value->precioneto, 2) . '</td>
        <td>' . number_format($value->precioneto_flete, 2) . '</td>
        <td>' . number_format($value->subtotal, 2) . '</td>
        <td>' . number_format($value->subtotal_flete, 2) . '</td>
      </tr>';
    }

    $ticket .= '
    </tbody>
    <tfoot>
    <tr>
      <td colspan="10" align="right">
      <strong>Totales:</strong>
      </td>
      <td align="right">
      <strong>' . number_format($totalsinflete, 2) . '</strong>
      </td>
      <td align="right">
      <strong>' . number_format($totalconflete, 2) . '</strong>
      </td>
     </tr>
  </tfoot>
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

  public function completarproveedoresfletes()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->completarproveedoresfletes($q);
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
        $value->cantidaditemregalo,
        $value->cantidad,
        $value->totalitem,
        "S/. " . number_format($value->preciopaquete_sinigv, 2),
        "S/. " . number_format($value->preciopaquete_conigv_sinflete, 2),
        "S/. " . number_format($value->preciounitario, 2),
        "S/. " . number_format($value->precioneto, 2),
        "S/. " . number_format($value->precioneto_flete, 2),
        "S/. " . number_format($value->subtotal, 2),
        "S/. " . number_format($value->subtotal_flete, 2),
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
      $medidacantidad =  $this->input->post('paquete');
      $totalCantidad = $this->input->post('paquete') * $this->input->post('cantidad');
      $preciocomprapaquete_conigv = $this->input->post("preciocomprapaquete"); //? con igv
      $preciocomprapaquete_sinigv = $this->input->post("preciocomprapaquete") / (1 + ($compras->igv / 100));
      $preciounitario_sinigv = $preciocomprapaquete_sinigv / $medidacantidad;
      $preciounitario_conigv_sinflete = $preciocomprapaquete_conigv / $medidacantidad;
      $totalIngreso =  $preciocomprapaquete_conigv * $this->input->post('cantidad');
      $data['preciopaquete_sinigv'] =  $preciocomprapaquete_sinigv;
      $data['preciopaquete_conigv_sinflete'] =  $preciocomprapaquete_conigv;
      $data['preciopaquete_conigv_conflete'] = $totalIngreso;
    } else {
      $totalCantidad = $this->input->post('cantidad');
      $preciounitario_sinigv = $this->input->post("preciocompra") / (1 + ($compras->igv / 100));
      $preciounitario_conigv_sinflete = $this->input->post("preciocompra");
      $data['preciopaquete_sinigv'] =  0.0000;
      $data['preciopaquete_conigv_sinflete'] = 0.0000;
      $data['preciopaquete_conigv_conflete'] = 0.0000;
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
    $data['precioneto_flete'] = $preciounitario_conigv_sinflete;
    $data['precioneto'] = $preciounitario_conigv_sinflete; //? solo unitario
    $data['preciounitario'] = $preciounitario_sinigv; //? solo unitario
    $data['cantidad'] = $this->input->post('cantidad');
    $data['almacen'] = $this->input->post('almacen');
    $data['subtotal'] = $totalIngreso;
    $data['subtotal_flete'] = $totalIngreso;
    $data['lote'] =  $producto->status_lote == '1' ? $this->input->post('lote') : NULL;
    if ($this->Controlador_model->save('compradetalle', $data)) {
      $compra['montototal'] = $compras->montototal + $totalIngreso;
      $compra['montoactual'] = $compras->montototal + $totalIngreso;
      $this->Controlador_model->update(array('id' => $this->compra), $compra, 'compra');
      if ($this->updateFlete()) {
        echo json_encode(array("status" => TRUE));
      }
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
      if ($this->updateFlete()) {
        echo json_encode(array("status" => TRUE));
      }
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
    $queryCosto = $this->db->select_sum("monto")->where("compra", $this->compra)->get("compracostosadicionales")->row();
    $data['costoAdicional'] = $queryCosto;
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
        $movimiento['modalidad'] = "ENTRADA";
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

    if ($this->input->post('formapago') == 'CONTADO') {
      $egreso['tipo'] = "EMPRESA";
      $data['montoactual'] = 0;
      $egreso['modalidad'] = "COMPRA";
      $egreso['empresa'] = $compra->empresa;
      $egreso['usuario'] = $compra->usuario;
      $egreso['concepto'] = '4';
      $egreso['compra'] = $this->compra;
      $egreso['montototal'] = $compra->montototal;
      $egreso['tipopago'] = $this->input->post('metodopago');
      $egreso['tipotarjeta'] = $this->input->post('tipotarjeta');
      $egreso['operacion'] = $this->input->post('metodopago') <> "EFECTIVO" ? $this->input->post('operacion') : NULL;
      $egreso['observacion'] = 'CANCELAR COMPRA DE LA SERIE: ' . $compra->serie . ' CON NUMERO: ' . $compra->numero . 'Y CON CODIGO INTERNO: ' . $compra->codigo;
      $egreso['created'] = date('Y-m-d');
      $egreso['hora'] = date('H:i:s');
      $this->Controlador_model->save('egreso', $egreso);
    }

    $data['estado_pago'] = $this->input->post('formapago') == "CONTADO" ? "1" : "0";
    $data['formapago'] = $this->input->post('formapago');
    $data['movimiento'] = $this->input->post('movimiento');
    $data['estado'] = '1';
    $data['serie'] = $this->input->post('serie');
    $data['numero'] = $this->input->post('numero');
    $data['created'] = date('Y-m-d');
    $data['hora'] = date('H:i:s');
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

  // public function excel()
  // {
  //   $finicio = $this->uri->segment(3);
  //   $factual = $this->uri->segment(4);
  //   $usuario = $this->perfil == 1 ? FALSE : $this->usuario;
  //   $pedidos = $this->Controlador_model->listar($finicio, $factual, $usuario, $this->controlador);
  //   // Propiedades del archivo excel
  //   $this->phpexcel->getProperties()->setTitle("Lista de OC")->setDescription("Lista de OC por Proveedor");
  //   // Setiar la solapa que queda actia al abrir el excel
  //   $this->phpexcel->setActiveSheetIndex(0);
  //   // Solapa excel para trabajar con PHP
  //   $sheet = $this->phpexcel->getActiveSheet();
  //   $sheet->setTitle("Lista de OC - Proveedor");
  //   $sheet->getColumnDimension('A')->setAutoSize(true);
  //   $sheet->getColumnDimension('B')->setAutoSize(true);
  //   $sheet->getColumnDimension('C')->setAutoSize(true);
  //   $sheet->getColumnDimension('D')->setAutoSize(true);
  //   $sheet->getColumnDimension('E')->setAutoSize(true);
  //   $sheet->getColumnDimension('F')->setAutoSize(true);
  //   $sheet->getColumnDimension('G')->setAutoSize(true);
  //   $sheet->getColumnDimension('H')->setAutoSize(true);
  //   $style_header = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7')));
  //   $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
  //   $sheet->getStyle('A1:H1')->applyFromArray($style_header);
  //   $sheet->getStyle("A1:H1")->applyFromArray($style);
  //   $sheet->setCellValue('A1', 'ORDEN');
  //   $sheet->setCellValue('B1', 'O.C.');
  //   $sheet->setCellValue('C1', 'TIPO');
  //   $sheet->setCellValue('D1', 'Nº DOC.');
  //   $sheet->setCellValue('E1', 'RAZON SOCIAL');
  //   $sheet->setCellValue('F1', 'FECHA EMI.');
  //   $sheet->setCellValue('G1', 'ESTADO');
  //   $sheet->setCellValue('H1', 'MONTO');
  //   $i = 1;
  //   foreach ($pedidos as $value) {
  //     $i++;
  //     $proveedor = $this->Controlador_model->get($value->proveedor, 'proveedor');
  //     $sheet->setCellValue('A' . $i, $i - 1);
  //     $sheet->setCellValue('B' . $i, $value->codigo);
  //     $sheet->setCellValue('C' . $i, $value->movimiento);
  //     $sheet->setCellValue('D' . $i, $value->serie . '-' . $value->numero);
  //     $sheet->setCellValue('E' . $i, $proveedor ? $proveedor->nombre : "");
  //     $sheet->setCellValue('F' . $i, $value->created);
  //     $sheet->setCellValue('G' . $i, $value->estado);
  //     $sheet->setCellValue('H' . $i, $value->montototal);
  //   }
  //   // Salida
  //   header("Content-Type: application/vnd.ms-excel");
  //   $nombreArchivo = 'compra_' . date('YmdHis');
  //   header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
  //   header("Cache-Control: max-age=0");
  //   // Genera Excel
  //   $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, "Excel5");
  //   // Escribir
  //   $writer->save('php://output');
  //   exit;
  // }

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

  public function ajax_cosotoadicionales()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where("compra", $this->compra)->get('compracostosadicionales')->result();
    $data = [];
    foreach ($query as $key => $value) {
      $boton = '';
      $proveedor = $this->Controlador_model->get($value->proveedor, 'proveedor');
      //$boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="edit_costoadicional(' . $value->id . ')"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar_cosotoadicional(' . $value->id . ')"><i class="fa fa-trash"></i></a>';
      $data[] = array(
        $key + 1,
        ($proveedor ? $proveedor->ruc . " " . $proveedor->nombre : "SIN DATOS"),
        $value->serie_documento . " " . $value->numero_documento,
        $value->formapago,
        $value->descripcion,
        $value->monto,
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

  private function _validate_compraadicional()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    if ($this->input->post('costo_adicional') == '') {
      $data['inputerror'][] = 'costo_adicional';
      $data['error_string'][] = '';
      $data['status'] = FALSE;
    }

    if ($this->input->post('serie_documento') == '') {
      $data['inputerror'][] = 'serie_documento';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('numero_documento') == '') {
      $data['inputerror'][] = 'numero_documento';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('proveedorflete') == '') {
      $data['inputerror'][] = 'proveedoresfletes';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }


    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_add_costoadicional()
  {
    $this->_validate_compraadicional();
    $dataCompra = $this->Controlador_model->get($this->compra, "compra");
    $data['compra'] = $this->compra;
    $data['tipo'] = "FLETE";
    $data['tienda'] =  $dataCompra->empresa;
    $data['proveedor'] =  $this->input->post('proveedorflete');
    $data['serie_documento'] =  $this->input->post('serie_documento');
    $data['numero_documento'] =  $this->input->post('numero_documento');
    $data['formapago'] =  $this->input->post('formapago_adicional');
    $data['descripcion'] = $this->input->post('descripcion_adicional');
    $data['monto'] = $this->input->post('costo_adicional');
    $data['montoactual'] = $this->input->post('formapago_adicional') == "CONTADO" ? 0.00 : $this->input->post('costo_adicional');
    $data['estado_pago'] = $this->input->post('formapago_adicional') == "CONTADO" ? "1" : "0";
    $data['created'] = date("Y-m-d");
    $data['hora'] = date("H:i:s");
    $insert = $this->Controlador_model->save('compracostosadicionales', $data);
    if ($insert) {
      if ($this->input->post('formapago_adicional') == "CONTADO") {
        $egreso['tipo'] = "EMPRESA";
        $egreso['modalidad'] = "FLETE";
        $egreso['empresa'] =  $dataCompra->empresa;
        $egreso['usuario'] = $dataCompra->usuario;
        $egreso['concepto'] = '7';
        $egreso['flete'] = $insert;
        $egreso['tipopago'] = $this->input->post('metodopago_flete');
        $egreso['tipotarjeta'] = $this->input->post('metodopago_flete') == "TARJETA" ? $this->input->post('tipotarjeta_flete') : NULL;
        $egreso['montototal'] = $this->input->post('costo_adicional');
        $egreso['observacion'] = 'CANCELAR FLETE CON SERIE:' . $this->input->post('serie_documento') . " Y NUMERO: " . $this->input->post('numero_documento');
        $egreso['created'] = date('Y-m-d');
        $egreso['hora'] = date('H:i:s');
        $this->Controlador_model->save('egreso', $egreso);
      }
      if ($this->updateFlete()) {
        echo json_encode(array("status" => TRUE));
      }
    }
  }

  private function updateFlete()
  {
    $queryTotalFlete = $this->db->select_sum("monto")->where("compra", $this->compra)->get("compracostosadicionales")->row();
    $queryTotalCantidaCompra = $this->db->select_sum("cantidaditem")->where("compra", $this->compra)->get("compradetalle")->row();
    $ItemsCompraTotal = is_null($queryTotalCantidaCompra->cantidaditem) ? 0 : $queryTotalCantidaCompra->cantidaditem; //? Total de items que se compra
    $FleteTotal = is_null($queryTotalFlete->monto) ? 0 : $queryTotalFlete->monto;
    if ($ItemsCompraTotal > 0) {
      $CostoFletePorItem = $FleteTotal / $ItemsCompraTotal;
    } else {
      $CostoFletePorItem = 0;
    }
    $dataCompraDetalle = $this->db->where("compra", $this->compra)->get("compradetalle")->result();
    foreach ($dataCompraDetalle as $value) {
      $FleteUnitarioAsignar = $CostoFletePorItem;
      $precioneto_flete = $value->precioneto +  $FleteUnitarioAsignar; //? costo unitario con flete
      $totalSubConFlete = $precioneto_flete * $value->cantidaditem; //? costo total con flete
      $totalSubSinFlete = $value->subtotal;  //? costo total sin flete
      $TotalConFlete =  $totalSubConFlete - $totalSubSinFlete;
      $subtotal_flete = $value->subtotal + $TotalConFlete;
      $dataUpdate["precioneto_flete"] = $precioneto_flete;
      $dataUpdate["subtotal_flete"] = $subtotal_flete;
      if ($value->medida == "PAQUETE") {
        $totalfletepaquete = $value->cantidaditem * $CostoFletePorItem;
        $dataUpdate["preciopaquete_conigv_conflete"] = $value->preciopaquete_conigv_conflete + $totalfletepaquete;
      } else {
        $dataUpdate["preciopaquete_conigv_conflete"] = 0.0000;
      }
      $this->Controlador_model->update(["id" => $value->id], $dataUpdate, "compradetalle");
    }
    return true;
  }

  public function ajax_deletecostoadicional($id)
  {
    $dataFlete = $this->Controlador_model->get($id, "compracostosadicionales");
    if ($dataFlete->formapago == 'CONTADO') {
      $this->db->where('flete', $id)->delete("egreso");
    }
    if ($this->Controlador_model->delete_by_id($id, 'compracostosadicionales')) {
      if ($this->updateFlete()) {
        echo json_encode(array("status" => TRUE));
      }
    } else {
      echo json_encode(array("status" => FALSE));
    }
  }

  function ajax_updateigv()
  {
    $dataupdateigv["igv"] = $this->input->post("igv");
    $this->Controlador_model->update(["id" => $this->compra], $dataupdateigv, "compra");
    echo json_encode(["status" => TRUE]);
  }
}
