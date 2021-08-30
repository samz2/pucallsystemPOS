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
    $query = $this->Controlador_model->getProductos($q);
    $dataProductos = [];
    foreach ($query as $value) {
      $dataProductos[] = ['id' => $value->id, 'text' => $value->nombre];
    }
    
    if(empty($dataProductos)){
      $dataResult = array(array("text" => "SIN RESULTADOS DE BUSQUEDA"));
    }else{
      $dataResult = array(array("text" => "RESULTADOS DE BUSQUEDA", "children" => $dataProductos));
    }
  
    echo json_encode($dataResult);
  }

  public function kardexFecha()
  {
    $producto = $this->input->post('producto');
    $fechainicio = $this->input->post('fechainicio');
    $fechafinal = $this->input->post('fechafinal');
    $tipofiltrado = $this->input->post('tipofiltrado');
    $movimientos = $this->Controlador_model->kardexFecha($producto, $fechainicio, $fechafinal, $tipofiltrado);
    $ticket = '';
    $ticket .= '
    <table id="tabla_kardex" class="table table-bordered table-striped" >
    <thead>
      <tr>
        <th>#</th>
        <th>Usuario responsable</th>
        <th>Producto</th>
        <th>Medida</th>
        <th>Tipo</th>
        <th>Codigo Operacion</th>
        <th>Almacen</th>
        <th>Fecha / Hora</th>
        <th>Total items de Operacion</th>
        <th>Stock Anterior</th>
        <th>Stock Actual</th>
      </tr>
    </thead>
    <tbody>';
    foreach ($movimientos as $indice => $data) {
      $dataproducto = $this->Controlador_model->get($data->producto, 'producto');
      $usuarioresponsable = $this->Controlador_model->get($data->usuario, 'usuario');
      $almacen = $this->Controlador_model->get($data->almacen, 'almacen');
      if ($data->tipooperacion == "NI") {
        $dataTipo = $data->tipo;
        $notaingreso = $this->Controlador_model->get($data->notaingreso, 'notaingreso');
        $codigoDocumento = $notaingreso->codigo;
      } else if ($data->tipooperacion == "VENTA") {
        $venta = $this->Controlador_model->get($data->venta, 'venta');
        $codigoDocumento =  $venta->serie . '-' . $venta->numero;
        if($data->productocombo){
          $queryproductocombo = $this->Controlador_model->get($data->productocombo, "producto");
          $dataTipo = $data->tipo.": ".$queryproductocombo->nombre;
        }else{
          $dataTipo = $data->tipo;
        }
      } else if ($data->tipooperacion == "NS") {
        $dataTipo = $data->tipo;
        $notasalida = $this->Controlador_model->get($data->notasalida, 'notasalida');
        $codigoDocumento = $notasalida->codigo;
      } else if ($data->tipooperacion == "COMPRA") {
        $dataTipo = $data->tipo;
        $notaingreso = $this->Controlador_model->get($data->notaingreso, 'notaingreso');
        $compra = $this->Controlador_model->get($data->compra, 'compra');
        $codigoDocumento = $notaingreso->codigo." / ".$compra->serie."-".$compra->numero;
      }

      $ticket .= '
      <tr>
        <td>' . ($indice + 1) . '</td>
        <td>' . $usuarioresponsable->nombre . " " . $usuarioresponsable->apellido . " " . $usuarioresponsable->documento . '</td>
        <td>' . $dataproducto->nombre . '</td>
        <td>' . $data->medida . '</td>
        <td>' . $dataTipo . '</td>
        <td>' . $codigoDocumento . '</td>
        <td>'.$almacen->nombre.'</td>
        <td>' . $data->created . " " . $data->hora . '</td>
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
