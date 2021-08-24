<?php

class Kardex extends CI_Controller
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
      'datas' => $this->Controlador_model->getAll('empresa'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function autocompletar()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->autocompletar($q);
    }
  }

  function buscarProducto()
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

  public function kardexFecha()
  {
    $producto = $this->input->post('producto');
    $fechainicio = $this->input->post('fechainicio');
    $fechafinal = $this->input->post('fechafinal');
    $tipofiltrado = $this->input->post('tipofiltrado');
    $movimientos = $this->Controlador_model->kardexFecha($producto, $fechainicio, $fechafinal, $tipofiltrado);
    $saldoinicial = $this->Controlador_model->saldoinicialxFecha($producto, $fechainicio, $fechafinal);
    $ticket = '';
    $ticket .= '
    <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>Usuario responsable</th>
        <th>Producto</th>
        <th>Medida</th>
        <th>Tipo</th>
        <th>Codigo</th>
        <th>Fecha / Hora</th>
        <th>Detalle</th>
        <th>Total items de Operacion</th>
        <th>Stock Anterior</th>
        <th>Stock Actual</th>
      </tr>
    </thead>
    <tbody>';
    foreach ($movimientos as $indice => $data) {
      $dataproducto = $this->Controlador_model->get($data->producto, 'producto');
      $usuarioresponsable = $this->Controlador_model->get($data->usuario, 'usuario');
      if ($data->tipooperacion == "NI") {
        $notaingreso = $this->Controlador_model->get($data->notaingreso, 'notaingreso');
        $compra = $this->Controlador_model->get($notaingreso->compra, 'compra');
        if (isset($compra->proveedor)) {
          $proveedor = $this->Controlador_model->get($compra->proveedor, 'proveedor');
        }
        $razon = isset($compra->proveedor) ? $proveedor->nombre : '';
        $fecha = $notaingreso->created;
        $NID = $this->Controlador_model->getNIDetalle($data->producto, $data->notaingreso);
        $precio = $NID->precio;
        $codigoDocumento = $notaingreso->codigo;
      } else if ($data->tipooperacion == "VENTA") {
        $venta = $this->Controlador_model->get($data->venta, 'venta');
        $codigoDocumento =  $venta->serie . '-' . $venta->numero;
        $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
        $razon = $cliente->nombre . ' ' . $cliente->apellido;
        $fecha = $venta->created;
        $VD = $this->Controlador_model->getVDetalle($data->producto, $data->venta);
        $precio = $VD->precio;
      } else if ($data->tipooperacion == "NS") {
        $notasalida = $this->Controlador_model->get($data->notasalida, 'notasalida');
        $codigoDocumento = $notasalida->codigo;
        $usuario = $this->Controlador_model->get($notasalida->usuario, 'usuario');
        $razon = $usuario->nombre . ' ' . $usuario->apellido;
        $NSD = $this->Controlador_model->getNSDetalle($data->producto, $data->notasalida);
        $precio = $NSD->precio;
      }

      $ticket .= '
      <tr>
      <td>' . ($indice + 1) . '</td>
      <td>' . $usuarioresponsable->nombre . " " . $usuarioresponsable->apellido . " " . $usuarioresponsable->documento . '</td>
      <td>' . $dataproducto->nombre . '</td>
      <td>' . $data->medida . '</td>
      <td>' . $data->tipo . '</td>
      <td>' . $codigoDocumento . '</td>
      <td>' . $data->created . " " . $data->hora . '</td>
      <td>' . $data->detalle . '</td>
      <td>' . $data->totalitemoperacion . '</td>
      <td>' . $data->stockanterior . '</td>
      <td>' . $data->stockactual . '</td>
      </tr>';
    }
    // barcode codding type
    $ticket .= '
    </tbody>
    </table>';

    echo $ticket;
  }
}

/* End of file producto.php */
/* Location: ./system/application/controllers/producto.php */
