<?php
// CODIGO RECUPERADO DE MAVILA
class Producto extends CI_Controller
{

  public function __construct()
  {
    $this->folder = 'files/';
    parent::__construct();
    $this->load->model(modelo(), 'Controlador_model');
    $this->controlador = controlador();
    $this->titulo_controlador = humanize($this->controlador);
    $this->url = base_url() . $this->controlador;
    $this->vista = $this->controlador;
    $this->perfil = $this->session->userdata('perfil') ? $this->session->userdata('perfil') : FALSE;
    $this->empresa = $this->session->userdata('empresa') ? $this->session->userdata('empresa') : FALSE;
    $this->usuario = $this->session->userdata('usuario') ? $this->session->userdata('usuario') : FALSE;
  }

  public function index()
  {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista,
      'categories' => $this->db->order_by('nombre', 'asc')->where("estado", "0")->get('productocategoria')->result(),
      'marcas' => $this->db->order_by("nombre", "ASC")->get('marca')->result(),
      'empresas' => $this->Controlador_model->getAll('empresa'),
      'almacenes' => $this->Controlador_model->getAll('almacen'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function ajax_list($empresa)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where("categoria <>", 14)->get($this->controlador)->result();
    $data = [];
    $estado['0'] = '<span class="label label-success">ACTIVADO</span>';
    $estado['1'] = '<span class="label label-danger">DESACTIVADO</span>';
    foreach ($query as $key => $value) {
      $stock = $this->totalStock($value->id, $empresa);

      if ($value->tipo == "1") {
        $stocks = "<label class=\"label label-warning\" style=\"background:#ffc107; color:#212529\">SERVICIO</label>";
      } else {
        $stocks = "<label class=\"label label-default\">" . $stock . "</label>";
      }
      $categoria = $this->Controlador_model->get($value->categoria, 'productocategoria');
      //add variables for action
      $boton = '';
      //add html for action
      $boton .= '<button id="boton-detalles-' . $value->id . '" class="btn btn-sm btn-default" title="Detalle" onclick="detalle(' . $value->id . ', '.$empresa.')"><i class="fa fa-th"></i></button> ';

      if ($this->perfil == 1 || $this->perfil == 2) {
        $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="edit(' . $value->id . ')"><i class="fa fa-pencil"></i></a> ';
        if ($value->variante == 1) {
          $boton .= '<a class="btn btn-sm btn-info" id="variante-' . $value->id . '" title="Variantes" onclick="variante(' . $value->id . ', ' . $value->tipo . ')"><i class="fa fa-th"></i></a> ';
        }
        if ($value->tipo === '2') {
          $boton .= '<a class="btn btn-sm btn-success" title="Combos" onclick="combo(' . $value->id . ')"><i class="fa fa-briefcase"></i></a> ';
        }

        if ($value->estado == '1') {
          $boton .= '<a class="btn btn-sm btn-default" title="Activar" onclick="activar(' . $value->id . ')"><i class="fa fa-check"></i></a> ';
        } else {
          $boton .= '<a class="btn btn-sm btn-warning" title="Desactivar" onclick="desactivar(' . $value->id . ')"><i class="fa fa-power-off"></i></a> ';
        }
        $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar(' . $value->id . ')"><i class="fa fa-trash"></i></a> ';

        if ($value->status_lote == '1') {
          $queryLote = $this->db->where("producto", $value->id)->get("lote")->num_rows();
          $lotes = "
          <div class='input-group'>
            <input type='number' class='form-control' readonly value='" . $queryLote . "'>
            <span class='input-group-btn'>
              <button class='btn waves-effect waves-light btn-default' id='button-lote-" . $value->id . "' onclick = 'verLotesAlmacen(" . $value->id . ", ".$empresa.")'>
                <i class='fa fa-bar-chart-o'></i>
              </button>
            </span>
          </div>
          ";
        } else {
          $lotes = "S/L";
        }
      }

      $data[] = array(
        $key + 1,
        $value->codigo,
        substr($value->nombre, 0, 50),
        $value->codigoBarra,
        $lotes,
        $categoria ? $categoria->nombre : '',
        $stocks,
        $value->precioventa,
        $estado[$value->estado],
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

  public function detallesproductos($empresa)
  {
    $detallesproductos = $this->db->order_by('id', 'desc')->where("categoria <>", 14)->where("empresa", $empresa)->get($this->controlador)->result();
    $mostrar = '';
    $mostrar .= '
    <table id="tablamain" class="table table-bordered table-striped">
    <thead>
    <tr>
      <th>#</th>
      <th>Codigo</th>
      <th>Nombre</th>
      <th>C.Barra</th>
      <th>Lotes</th>
      <th>Categoria</th>
      <th>Stock total</th>
      <th>Precio</th>
      <th>Estado</th>
      <th>Acciones BTN</th>
    </tr>
    </thead>
    <tbody>';
    $estado['0'] = '<span class="label label-success">ACTIVADO</span>';
    $estado['1'] = '<span class="label label-danger">DESACTIVADO</span>';
    $i = 0;
    foreach ($detallesproductos as $value) {
      $i++;
      $stock = $this->totalStock($value->id, $empresa);
      if ($value->tipo == "1") {
        $stocks = "<label class=\"label label-warning\" style=\"background:#ffc107; color:#212529\">SERVICIO</label>";
      } else {
        $stocks = "<label class=\"label label-default\">" . $stock . "</label>";
      }
      $categoria = $this->Controlador_model->get($value->categoria, 'productocategoria');
      //add variables for action
      $boton = '';
      //add html for action
      $boton .= '<button id="boton-detalles-' . $value->id . '" class="btn btn-sm btn-default" title="Detalle" onclick="detalle(' . $value->id . ')"><i class="fa fa-th"></i></button> ';

      if ($this->perfil == 1 || $this->perfil == 2) {
        $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="edit(' . $value->id . ')"><i class="fa fa-pencil"></i></a> ';
        if ($value->variante == 1) {
          $boton .= '<a class="btn btn-sm btn-info" id="variante-' . $value->id . '" title="Variantes" onclick="variante(' . $value->id . ', ' . $value->tipo . ')"><i class="fa fa-th"></i></a> ';
        }
        if ($value->tipo === '2') {
          $boton .= '<a class="btn btn-sm btn-success" title="Combos" onclick="combo(' . $value->id . ')"><i class="fa fa-briefcase"></i></a> ';
        }
        if ($value->estado == '1') {
          $boton .= '<a class="btn btn-sm btn-default" title="Activar" onclick="activar(' . $value->id . ')"><i class="fa fa-check"></i></a> ';
        } else {
          $boton .= '<a class="btn btn-sm btn-warning" title="Desactivar" onclick="desactivar(' . $value->id . ')"><i class="fa fa-power-off"></i></a> ';
        }
        $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar(' . $value->id . ')"><i class="fa fa-trash"></i></a> ';

        if ($value->status_lote == '1') {
          $queryLote = $this->db->where("producto", $value->id)->get("lote")->num_rows();
          $lotes = "
          <div class='input-group'>
            <input type='number' class='form-control' readonly value='" . $queryLote . "'>
            <span class='input-group-btn'>
              <button class='btn waves-effect waves-light btn-default' id='button-lote-" . $value->id . "' onclick = 'verLotesAlmacen(" . $value->id . ")'>
                <i class='fa fa-bar-chart-o'></i>
              </button>
            </span>
          </div>
          ";
        } else {
          $lotes = "S/L";
        }
      }

      $mostrar .= "
      <tr>
        <td> $i</td>
        <td> $value->codigo</td>
        <td> ".substr($value->nombre, 0, 50)."</td>
        <td>$value->codigoBarra</td>
        <td>$lotes</td>
        <td>".($categoria ? $categoria->nombre : "" )."</td>
        <td>$stocks</td>
        <td>$value->precioventa</td>
        <td>".$estado[$value->estado]."</td>
        <td>$boton</td>
      </tr>";
    }
    $mostrar .= "</tbody></table>";

    echo $mostrar;
  }


  public function totalStock($idproducto, $empresa)
  {
    //? contamos para cuantas selecciones de ese producto le resta
    $queryproducto = $this->Controlador_model->get($idproducto, "producto");
    $cantidadRestante = [];

    if ($queryproducto->tipo == '0') {
      //todo: El tipo de producto 0 es estandar
      $stok_D = $this->db->select_sum("cantidad")->where("empresa", $empresa)->where('producto', $queryproducto->id)->get('stock')->row();
      $cantidadRestante[] = $stok_D->cantidad  == '' ? 0 : $stok_D->cantidad;
      /*
      $existenciaStock = $this->db->where('producto',  $queryproducto->id)->where("empresa", $empresa)->get('stock')->row(); //todo: verificamos si el producto esta registra en stock
      if ($existenciaStock) {
        $stok_D = $this->db->select_sum("cantidad")->where("empresa", $empresa)->where('producto', $queryproducto->id)->get('stock')->row();
        $cantidadRestante[] = $stok_D->cantidad;
      } else {
        $cantidadRestante[] =" no tiene";
      }
      */
    } else if ($queryproducto->tipo == '2') {

      //todo: El tipo de producto 2 es combo
      $combo = $this->db->where('producto',  $queryproducto->id)->get('combo')->result(); //todo: verificamos si el tiene registro en la tabal combo
      if ($combo) {
        foreach ($combo as $key => $value2) {
          $existenciaStockCombo = $this->db->where('producto',  $value2->item_id)->where("empresa", $empresa)->get('stock')->row(); //todo: verificamos si el producto esta registra en stock
          if ($existenciaStockCombo) {
            $totalCantidad = $this->db->select_sum("cantidad")->where('producto',  $value2->item_id)->where("empresa", $empresa)->get("stock")->row();
            if ($totalCantidad->cantidad >= $value2->cantidad) {
              $cantidadTotalStock =  $totalCantidad->cantidad;
              $cantidadCombo = $value2->cantidad;
              $totalSelecciones = ($cantidadTotalStock /  $cantidadCombo);
              $cantidadRestante[] = $totalSelecciones;
            } else {
              $cantidadRestante[] = 0;
              break;
            }
          } else {
            $cantidadRestante[] = 0;
          }
        }
      } else {
        $cantidadRestante[] = 0;
      }
    } else {
      $cantidadRestante[] = "Servicio";
    }
    if($queryproducto->tipo == '2'){
      $dataReturn = min($cantidadRestante);
    }else{
      $dataReturn = $cantidadRestante[0];
    }
    return $dataReturn;
  }

  private function _validate()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    $producto = $this->Controlador_model->check($this->input->post('id'), $this->input->post('nombre'));

    if ($producto) {
      $data['inputerror'][] = 'nombre';
      $data['error_string'][] = 'Este nombre ya se encuentra registrado.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('nombre') == '') {
      $data['inputerror'][] = 'nombre';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('precioventa') == '') {
      $data['inputerror'][] = 'precioventa';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('tipo') == '0') {
      if ($this->input->post('preciocompra') == '') {
        $data['inputerror'][] = 'preciocompra';
        $data['error_string'][] = 'Este campo es obligatorio.';
        $data['status'] = FALSE;
      }

      if ($this->input->post('unidad') == '') {
        $data['inputerror'][] = 'unidad';
        $data['error_string'][] = 'Este campo es obligatorio.';
        $data['status'] = FALSE;
      }

      if ($this->input->post('alertqt') == '') {
        $data['inputerror'][] = 'alertqt';
        $data['error_string'][] = 'Este campo es obligatorio.';
        $data['status'] = FALSE;
      }
    }

    if ($this->input->post('categoria') == '0') {
      $data['inputerror'][] = 'categoria';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_add()
  {
    $this->_validate();
    $variante = 0;
    if (!is_null($this->input->post('chkVariante'))) {
      $variante = $this->input->post('chkVariante');
    }
    $config['upload_path'] = './files/products/';
    $config['encrypt_name'] = TRUE;
    $config['allowed_types'] = 'gif|jpg|jpeg|png';
    $config['max_width'] = '10000';
    $config['max_height'] = '10000';
    $this->load->library('upload', $config);
    $this->load->library('image_lib');
    if ($this->upload->do_upload('foto')) {
      $datas = array('upload_data' => $this->upload->data());
      $config2['image_library'] = 'gd2';
      $config2['source_image'] = $datas['upload_data']['full_path'];
      $config2['create_thumb'] = TRUE;
      $config2['maintain_ratio'] = TRUE;
      $config2['width'] = 120;
      $config2['height'] = 120;
      $this->image_lib->clear();
      $this->image_lib->initialize($config2);
      $this->image_lib->resize();
      $image = $datas['upload_data']['file_name'];
      $image_thumb = $datas['upload_data']['raw_name'] . '_thumb' . $datas['upload_data']['file_ext'];
      $data['photo'] = $image;
      $data['photothumb'] = $image_thumb;
    }


    $data['variante'] = $variante;
    $data['status_lote'] = $this->input->post('chkLotes')  ? $this->input->post('chkLotes') : "0";
    $data['empresa'] = $this->perfil == 1 || $this->perfil == 2 ? $this->input->post('empresa') : $this->empresa;
    $data['categoria'] = $this->input->post('categoria');
    $data['tipo'] = $this->input->post('tipo');
    $data['codigo'] = $this->input->post('codigo');
    $data['marca'] = $this->input->post('marca') == '0' ? NULL : $this->input->post('marca');
    $data['codigoBarra'] = $this->input->post('codigoBarra');
    $data['numero'] = $this->input->post('numero');
    $data['nombre'] = $this->input->post('nombre');
    $data['descripcion'] = $this->input->post('descripcion');
    $data['color'] = $this->input->post('color');
    $data['alertqt'] = $this->input->post('alertqt');
    $data['preciocompra'] = $this->input->post('preciocompra');

    $data['preciocomprapaquete'] = $this->input->post('preciocomprapaquetes');

    $data['precioventa'] = $this->input->post('precioventa');
    $data["preciodistribuidor"] = $this->input->post('preciodistribuidor');
    $data["preciomayorista"] = $this->input->post('preciomayorista');
    $data["precioespecial"] = $this->input->post('precioespecial');
    $data['fechacaducidad'] = $this->input->post('fecha_caducidad');
    $data['cantidadpaquete'] =  $this->input->post('cantidadpaquete');
    $data['unidad'] = $this->input->post('unidad');


    if ($this->Controlador_model->save($this->controlador, $data)) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_edit($id)
  {
    $data = $this->Controlador_model->get($id, $this->controlador);
    echo json_encode($data);
  }

  public function ajax_detalle($id, $empresa)
  {
    
    $datas = $this->Controlador_model->get($id, $this->controlador);
    $almacenes = $this->db->where('empresa', $empresa)->get('almacen');
    if ($almacenes->num_rows() > 0) {
      $dataHtml = '';
      foreach ($almacenes->result() as $key => $value) {
        $stockProducto = $this->db->select_sum("cantidad")->where('producto', $id)->where('empresa', $empresa)->where('almacen', $value->id)->get('stock')->row();
        if ($key == 0) {
          $dataHtml .= '
          <tr class="fila-stock">
          <th rowspan="' . $almacenes->num_rows() . '" style="vertical-align:middle">STOCKS</th>
          <td>' . $value->nombre . ': ' . ( $stockProducto->cantidad == '' ? 0 : $stockProducto->cantidad) . '</td>
          </tr>';
        } else {
          $dataHtml .= '
          <tr class="fila-stock">
          <td>' . $value->nombre . ': ' . ( $stockProducto->cantidad == '' ? 0 : $stockProducto->cantidad) . '</td>
          </tr>';
        }
      }
    }else{
      $dataHtml = "<tr class='fila-stock'><td colspan='2' style='text-align:center; font-weight:bold'>NO SE ENCONTRARON ALMACENES EN ESTA EMPRESA</td></tr>";
    }
    if ($datas->precioventa == 0) {
      $margen = "0.00%";
    } else {
      $margen = number_format((1 - ($datas->preciocompra / $datas->precioventa)) * 100, 2);
    }
    $categorias = $this->Controlador_model->get($datas->categoria, 'productocategoria');
    $mostrar = '';
    $tipo['0'] = 'ESTANDAR';
    $tipo['1'] = 'SERVICIO';
    $tipo['2'] = 'COMBINACION';
    $data = array(
      'tipo' => $tipo[$datas->tipo],
      'codigo' => $datas->codigo,
      'nombre' => $datas->nombre,
      'alertqt' => $datas->alertqt,
      'costo' => $datas->preciocompra,
      'precio' => $datas->precioventa,
      'descripcion' => $datas->descripcion,
      'margen_producto' => $margen . "%", //agregado dc
      'unidad' => $datas->unidad,
      'photo' => $datas->photo,
      'categoria' => $categorias ? $categorias->nombre : '',
      'stock' => $mostrar,
      'dataAlmacenes' => $dataHtml
    );

    echo json_encode($data);
  }

  public function ajax_update()
  {
    $variante = 0;
    if (!is_null($this->input->post('chkVariante'))) {
      $variante = $this->input->post('chkVariante');
    }

    $this->_validate();
    $config['upload_path'] = './files/products/';
    $config['encrypt_name'] = TRUE;
    $config['allowed_types'] = 'gif|jpg|jpeg|png';
    $config['max_width'] = '10000';
    $config['max_height'] = '10000';
    $this->load->library('upload', $config);
    $this->load->library('image_lib');
    if ($this->upload->do_upload('foto')) {
      $datas = array('upload_data' => $this->upload->data());
      $config2['image_library'] = 'gd2';
      $config2['source_image'] = $datas['upload_data']['full_path'];
      $config2['create_thumb'] = TRUE;
      $config2['maintain_ratio'] = TRUE;
      $config2['width'] = 120;
      $config2['height'] = 120;
      $this->image_lib->clear();
      $this->image_lib->initialize($config2);
      $this->image_lib->resize();
      $image = $datas['upload_data']['file_name'];
      $image_thumb = $datas['upload_data']['raw_name'] . '_thumb' . $datas['upload_data']['file_ext'];
      $data['photo'] = $image;
      $data['photothumb'] = $image_thumb;
    }


    $data['variante'] = $variante;
    $data['status_lote'] = $this->input->post('chkLotes')  ? $this->input->post('chkLotes') : "0";
    $data['empresa'] = $this->perfil == 1 || $this->perfil == 2 ? $this->input->post('empresa') : $this->empresa;
    $data['categoria'] = $this->input->post('categoria');
    $data['tipo'] = $this->input->post('tipo');
    $data['codigo'] = $this->input->post('codigo');
    $data['marca'] = $this->input->post('marca') == '0' ? NULL : $this->input->post('marca');
    $data['codigoBarra'] = $this->input->post('codigoBarra');
    $data['numero'] = $this->input->post('numero');
    $data['nombre'] = $this->input->post('nombre');
    $data['descripcion'] = $this->input->post('descripcion');
    $data['color'] = $this->input->post('color');
    $data['alertqt'] = $this->input->post('alertqt');
    $data['preciocompra'] = $this->input->post('preciocompra');

    $data['preciocomprapaquete'] = $this->input->post('preciocomprapaquetes');

    $data['precioventa'] = $this->input->post('precioventa');
    $data["preciodistribuidor"] = $this->input->post('preciodistribuidor');
    $data["preciomayorista"] = $this->input->post('preciomayorista');
    $data['fechacaducidad'] = $this->input->post('fecha_caducidad');
    $data["precioespecial"] = $this->input->post('precioespecial');
    $data['cantidadpaquete'] =  $this->input->post('cantidadpaquete');
    $data['unidad'] = $this->input->post('unidad');

    $this->Controlador_model->update(["id" => $this->input->post("id")], $data, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_delete($id)
  {
    if ($this->Controlador_model->delete_by_id($id, $this->controlador)) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_list_combo($id)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where('producto', $id)->get('combo');
    $data = [];
    $no = 0;
    foreach ($query->result() as $value) {
      $producto = $this->Controlador_model->get($value->item_id, 'producto');
      $categoria = $this->Controlador_model->get($producto->categoria, 'productocategoria');
      $no++;
      //add variables for action
      $boton1 = '';
      //add html for action
      $boton1 = '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrarcombo(' . $value->id . ')"><i class="fa fa-trash"></i></a>';
      $data[] = array(
        $no,
        $producto->codigo,
        $producto->nombre,
        $categoria ? $categoria->nombre : '',
        $value->cantidad,
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

  public function autocompletar()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->autocompletar($q);
    }
  }

  public function autocompletarlotes()
  {
    $q = strtoupper($this->input->post("term"));
    $idproducto = $this->input->post("idproducto");
    $this->Controlador_model->autocompletarlotes($q, $idproducto);
  }

  private function _validatecombo()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    $combolist = $this->Controlador_model->getDup($this->input->post('combo'), $this->input->post('producto'));

    if ($this->input->post('producto') == '') {
      $data['inputerror'][] = 'producto';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('cantidad') == '') {
      $data['inputerror'][] = 'cantidad';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($combolist) {
      $data['inputerror'][] = 'producto';
      $data['error_string'][] = 'Este campo ya existe en la base de datos.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_addcombo()
  {
    $this->_validatecombo();
    $data = array(
      'producto' => $this->input->post('combo'),
      'item_id' => $this->input->post('producto'),
      'cantidad' => $this->input->post('cantidad'),
    );
    $insert = $this->Controlador_model->save('combo', $data);
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_deletecombo($id)
  {
    if ($this->Controlador_model->delete_by_id($id, 'combo')) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_desactivar($id)
  {
    $this->Controlador_model->desactivar_by_id($id, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_activar($id)
  {
    $this->Controlador_model->activar_by_id($id, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }

  public function delete($id)
  {
    $product = $this->Controlador_model->get($id, 'producto');
    if ($product->photo !== '') {
      unlink('./files/products/' . $product->photo);
      unlink('./files/products/' . $product->photothumb);
    }
    $this->Controlador_model->delete($id, $this->controlador);
    redirect($this->url);
  }

  public function downloads($name)
  {
    $data = file_get_contents($this->folder . $name);
    force_download($name, $data);
  }

  public function codigo()
  {
    if ($this->input->is_ajax_request()) {
      $tipo = $this->input->post('tipo');
      $categoria = $this->input->post('categoria');
      $numero = $this->Controlador_model->codigos($tipo, $categoria);
      $numeros = $numero ? $numero->numero + 1 : 1;
      $cadena = "";
      for ($i = 0; $i < 3 - strlen($numeros); $i++) {
        $cadena = $cadena . '0';
      }
      $categorias = $this->Controlador_model->get($categoria, 'productocategoria');
      $codigo1 = $categorias ? $categorias->id : '00';
      $codigo = substr($categorias->nombre, 0, 1) . $tipo . $codigo1 . $cadena . $numeros;
      $output = array("codigo" => $codigo, "numero" => $numeros);
      $this->output->set_content_type('application/json')->set_output(json_encode($output));
    } else {
      show_404();
    }
  }

  

  function insertarNew()
  {
    $dataInsertar = json_decode($this->input->post('data'), true);
    foreach ($dataInsertar as $value) {
      $queryProducto = $this->db->where('nombre', trim($value[2]))->get("producto")->row();
      $cantidad = $value[10] == '' ? 0 : $value[10];
      if (!$queryProducto) {
        $tipoproducto['ESTANDAR'] = '0';
        $tipoproducto['SERVICIO'] = '1';
        $tipoproducto['COMBINACION'] = '2';
        $tipo = $value[0] == '' ? '0' : $value[0];

        $categoria = trim($value[1]);
        $categorias = $this->db->where('nombre', $categoria)->get('productocategoria')->row();
        if (!$categorias) {
          //Si no encuentra la categoria lo registramos
          $numero = $this->Controlador_model->codigo('productocategoria');
          $numeros = $numero ? $numero->numero + 1 : 1;
          $cadena = "";
          for ($i = 0; $i < 2 - strlen($numeros); $i++) {
            $cadena = $cadena . '0';
          }
          $datacategoria['empresa'] = $this->empresa;
          $datacategoria['nombre'] = $categoria;
          $datacategoria['codigo'] = $cadena . $numeros;
          $datacategoria['numero'] = $numeros;
          $insert = $this->Controlador_model->save('productocategoria', $datacategoria);
        }

        $marca = trim($value[8]);
        if ($marca != '') {
          $marcas = $this->db->where('nombre', $marca)->get('marca')->row();
          if (!$marcas) {
            $numeroM = $this->Controlador_model->codigo('marca');
            $numerosM = $numeroM ? $numeroM->numero + 1 : 1;
            $cadenaM = "";
            for ($i = 0; $i < 2 - strlen($numerosM); $i++) {
              $cadenaM = $cadenaM . '0';
            }
            $datamarca['empresa'] = $this->empresa;
            $datamarca['nombre'] = $marca;
            $datamarca['codigo'] = $cadenaM . $numerosM;
            $datamarca['numero'] = $numerosM;
            $insertmarca = $this->Controlador_model->save('marca', $datamarca);
          }
          $idmarca = $marcas ? $marcas->id : $insertmarca;
        } else {
          $idmarca = NULL;
        }

        $almacen = trim($value[9]);
        $almacenes = $this->db->where('nombre', $almacen)->get('almacen')->row();
        if (!$almacenes) {
          $numeroA = $this->Controlador_model->codigo('almacen');
          $numerosA = $numeroA ? $numeroA->numero + 1 : 1;
          $cadenaA = "";
          for ($i = 0; $i < 2 - strlen($numerosA); $i++) {
            $cadenaA = $cadenaA . '0';
          }
          $dataAlmacen['empresa'] = $this->empresa;
          $dataAlmacen['nombre'] = $almacen;
          $dataAlmacen['codigo'] = $cadenaA . $numerosA;
          $dataAlmacen['numero'] = $numerosA;
          $insertAlmacen = $this->Controlador_model->save('almacen', $dataAlmacen);
        }

        $idcategoria = $categorias ? $categorias->id : $insert;
        $idalmacen = $almacenes ? $almacenes->id : $insertAlmacen;

        //? Registro del producto
        $numero = $this->Controlador_model->codigos($tipoproducto[$tipo], $idcategoria);
        $numeros = $numero ? $numero->numero + 1 : 1;
        $cadena = "";
        for ($i = 0; $i < 3 - strlen($numeros); $i++) {
          $cadena = $cadena . '0';
        }
        $categorias = $this->Controlador_model->get($idcategoria, 'productocategoria');
        $codigo1 = $categorias ? $categorias->id : '00';
        $codigo = substr($categorias->nombre, 0, 1) . $tipoproducto[$tipo] . $codigo1 . $cadena . $numeros;

        $data['empresa'] = $this->empresa;
        $data['categoria'] = $idcategoria;
        $data['marca'] = $idmarca;
        $data['tipo'] = $tipoproducto[$tipo];
        $data['codigo'] = $codigo;
        $data['numero'] = $numeros;
        $data['nombre'] = $value[2];
        $data['preciocompra'] = $value[3];
        $data['precioventa'] = $value[4];
        $data['unidad'] = $value[5];
        $data['fechacaducidad'] = $value[6] != "" ? $value[6] : '0000-00-00';
        $data['codigoBarra'] = $value[7] != "" ? $value[7] : "";
        $data['color'] = 'color01';
        $insertproducto = $this->Controlador_model->save('producto', $data);

        //?aumento del stock del producto
        $queryStock = $this->db->where('almacen', $idalmacen)->where('producto', $insertproducto)->get("stock")->row();
        if ($queryStock) {
          $dataUpdateStock = [
            'cantidad' => $queryStock->cantidad + $cantidad
          ];
          $this->Controlador_model->update(['id' => $queryStock->id], $dataUpdateStock, 'stock');
        } else {
          $dataInsertStock = [
            'empresa' => $this->empresa,
            'producto' => $insertproducto,
            'almacen' => $idalmacen,
            'cantidad' => $cantidad,
          ];
          $this->Controlador_model->save('stock', $dataInsertStock);
        }
      } else {
        $almacen = trim($value[9]);
        $almacenes = $this->db->where('nombre', $almacen)->get('almacen')->row();
        if (!$almacenes) {
          $numeroA = $this->Controlador_model->codigo('almacen');
          $numerosA = $numeroA ? $numeroA->numero + 1 : 1;
          $cadenaA = "";
          for ($i = 0; $i < 2 - strlen($numerosA); $i++) {
            $cadenaA = $cadenaA . '0';
          }
          $dataAlmacen['empresa'] = $this->empresa;
          $dataAlmacen['nombre'] = $almacen;
          $dataAlmacen['codigo'] = $cadenaA . $numerosA;
          $dataAlmacen['numero'] = $numerosA;
          $insertAlmacen = $this->Controlador_model->save('almacen', $dataAlmacen);
        }
        $idalmacen = $almacenes ? $almacenes->id : $insertAlmacen;
        //?aumento del stock del producto
        $queryStock = $this->db->where('almacen', $idalmacen)->where('producto', $queryProducto->id)->get("stock")->row();
        if ($queryStock) {
          $dataUpdateStock = [
            'cantidad' => $queryStock->cantidad + $cantidad
          ];
          $this->Controlador_model->update(['id' => $queryStock->id], $dataUpdateStock, 'stock');
        } else {
          $dataInsertStock = [
            'empresa' => $this->empresa,
            'producto' => $queryProducto->id,
            'almacen' => $idalmacen,
            'cantidad' => $cantidad,
          ];
          $this->Controlador_model->save('stock', $dataInsertStock);
        }
      }
    }

    echo json_encode(["status" => TRUE]);
  }

  public function reporte()
  {
    $date = date("Y-m-d");
    $year = date("Y");
    $mes = date("m");
    // $VentaDiario = 0;
    // $TodaySales = $this->Controlador_model->getAll('venta');
    // foreach ($TodaySales as $TodaySale) {
    //   if ($TodaySale->created == $date) {
    //     $VentaDiario += $TodaySale->montototal;
    //   }   
    // }
    $TodaySales = $this->db->query("select sum(montototal) AS sum FROM venta where created = '$date'")->row();
    $Top5product = $this->Controlador_model->productoTop($mes);
    $monthlySales = $this->db->query("SELECT SUM(IF(MONTH = 1, numRecords, 0)) AS 'january', SUM(IF(MONTH = 2, numRecords, 0)) AS 'feburary',SUM(IF(MONTH = 3, numRecords, 0)) AS 'march',SUM(IF(MONTH = 4, numRecords, 0)) AS 'april',SUM(IF(MONTH = 5, numRecords, 0)) AS 'may',SUM(IF(MONTH = 6, numRecords, 0)) AS 'june',SUM(IF(MONTH = 7, numRecords, 0)) AS 'july',SUM(IF(MONTH = 8, numRecords, 0)) AS 'august',SUM(IF(MONTH = 9, numRecords, 0)) AS 'september',SUM(IF(MONTH = 10, numRecords, 0)) AS 'october',SUM(IF(MONTH = 11, numRecords, 0)) AS 'november',SUM(IF(MONTH = 12, numRecords, 0)) AS 'december',SUM(numRecords) AS montototal FROM ( SELECT id, MONTH(created) AS MONTH, ROUND(sum(montototal)) AS numRecords FROM venta WHERE DATE_FORMAT(created, '%Y') = $year GROUP BY id, MONTH ) AS SubTable1")->row();
    $monthlyExp = $this->db->query("SELECT SUM(IF(MONTH = 1, numRecords, 0)) AS 'january', SUM(IF(MONTH = 2, numRecords, 0)) AS 'feburary', SUM(IF(MONTH = 3, numRecords, 0)) AS 'march', SUM(IF(MONTH = 4, numRecords, 0)) AS 'april', SUM(IF(MONTH = 5, numRecords, 0)) AS 'may', SUM(IF(MONTH = 6, numRecords, 0)) AS 'june', SUM(IF(MONTH = 7, numRecords, 0)) AS 'july', SUM(IF(MONTH = 8, numRecords, 0)) AS 'august', SUM(IF(MONTH = 9, numRecords, 0)) AS 'september', SUM(IF(MONTH = 10, numRecords, 0)) AS 'october', SUM(IF(MONTH = 11, numRecords, 0)) AS 'november', SUM(IF(MONTH = 12, numRecords, 0)) AS 'december', SUM(numRecords) AS montototal FROM ( SELECT id, MONTH(created) AS MONTH, ROUND(sum(montototal)) AS numRecords FROM egreso WHERE DATE_FORMAT(created, '%Y') = $year GROUP BY id, MONTH ) AS SubTable1")->row();
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => '/reporte' . $this->controlador,
      'monthly' => $monthlySales,
      'monthlyExp' => $monthlyExp,
      'year' => $year,
      'Top5product' => $Top5product,
      'empresas' => $this->Controlador_model->getAll('empresa'),
      'CustomerNumber' => $this->Controlador_model->contador('cliente'),
      'CategoriesNumber' => $this->Controlador_model->contador('productocategoria'),
      'ProductNumber' => $this->Controlador_model->contador('producto'),
      'TodaySales' => $TodaySales->sum
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function consolidar()
  {
    $finicio = $this->input->post('finicio');
    $factual = $this->input->post('factual');
    $empresa = $this->input->post('empresa');
    $datas = $this->Controlador_model->consolidar($finicio, $factual, $empresa);
    $mostrar = '';
    $mostrar .= '
    <table id="example1" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Orden</th>
          <th>Codigo</th>
          <th>Producto</th>
          <th>Marca</th>
          <th>Stock Inicial</th>
          <th>Ingreso de stock</th>
          <th>Salida de stock en ventas</th>
          <th>Stock Actual</th>
        </tr>
      </thead>
    <tbody>';
    $i = 0;
    foreach ($datas as $data) {
      $i++;
      $producto = $this->Controlador_model->get($data->producto, 'producto');
      if ($producto) {
        $marca = $this->Controlador_model->get($producto->marca, 'marca');
        $primero = $this->Controlador_model->primerMov($data->producto, $finicio, $factual, $empresa); //? trae el primer movimiento de su producto
        $entrada = $this->Controlador_model->SumMovi($data->producto, $finicio, $factual, $empresa);
        $stock = $this->Controlador_model->getStock($data->producto, $empresa);
        if ($producto->tipo == "1") {
          //? si el tipo de producto es "Servicio es por que no funciona con STOCK"
          $dataSTock = "SERVICIO";
          $dataPrimero = "SERVICIO";
          $dataEntrada = "SERVICIO";
        } else {
          $dataPrimero = ($primero ? $primero->stockanterior : 0);
          $dataEntrada = ($entrada ? $entrada->cantidad : 0);
          $dataSTock = ($stock ? $stock->cantidad : 0);
        }
        $mostrar .= '
          <tr>
            <td>' . $i . '</td>
            <td>' . $producto->codigo . '</td>
            <td>' . $producto->nombre . ' ' . ($marca ? $marca->nombre : '') . '</td>
            <td>' . ($marca ? $marca->nombre : '') . '</td>
            <td>' . $dataPrimero . '</td>
            <td>' . $dataEntrada . '</td>
            <td>' . $data->numero . '</td>
            <td>' . $dataSTock . '</td>
          </tr>';
      } else {
        continue;
      }
    }
    $mostrar .= '
    <tr>
      <td colspan="6">TOTALES</td>
      <td></td>
      <td></td>
    </tr>
    </tbody>
    </table>';

    echo json_encode(["dataHtml" => $mostrar, "dataQuery" => $datas]);;
  }

  public function vendido()
  {
    $finicio = $this->input->post('finicio');
    $factual = $this->input->post('factual');
    $empresa = $this->input->post('empresa');
    $datas = $this->Controlador_model->vendido($finicio, $factual, $empresa);
    $mostrar = '';
    $mostrar .= '
    <table id="example1" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Orden</th>
          <th>Codigo</th>
          <th>Producto</th>
          <th>Marca</th>
          <th>Categoria</th>
          <th>Tipo</th>
          <th>Cantidad</th>
          <th>Margen(%)</th>
        </tr>
      </thead>
    <tbody>';
    $suma = $this->Controlador_model->vendidototal($finicio, $factual, $empresa);
    $i = 0;
    $totalventa = 0;
    $totalmargen = 0;
    foreach ($datas as $data) {
      $i++;
      $producto = $this->Controlador_model->get($data->producto, 'producto');
      if ($producto) {
        $marca = $this->Controlador_model->get($producto->marca, 'marca');
        //? $data->suma = cuanto se a vendido segun la fehca
        $tipo = "";
        $categoria = $this->Controlador_model->get($producto->categoria, 'productocategoria');
        $margen = number_format(($data->suma / $suma->numero) * 100, 2);
        if ($producto->tipo == "0") {
          $tipo = 'ESTANDAR';
        }
        if ($producto->tipo == "1") {
          $tipo = 'COMBINACION';
        }
        if ($producto->tipo == "2") {
          $tipo = 'SERVICIO';
        }
        $totalventa += $data->suma;
        $totalmargen += ($data->suma / $suma->numero) * 100;
        $mostrar .= '
          <tr>
            <td>' . $i . '</td>
            <td>' . $producto->codigo . '</td>
            <td>' . $producto->nombre . '</td>
            <td>' . ($marca ? $marca->nombre : '') . '</td>
            <td>' . ($categoria ? $categoria->nombre : '') . '</td>
            <td>' . $tipo  . '</td>
            <td style="text-align:center">' . $data->suma . '</td>
            <td style="text-align:center">' . $margen . ' %</td>
          </tr>';
      } else {
        continue;
      }
    }

    $mostrar .= '
    <tfoot>
      <tr>
          <td colspan="6" style="text-align:right">TOTAL</td>
          <td style="text-align:center">' . $totalventa . '</td>
          <td style="text-align:center">' . $totalmargen . '%</td>
      </tr>
    </tfoot>
    </tbody>
    </table>';
    echo $mostrar;
  }

  public function valorizado()
  {
    $empresa = $this->input->post('empresa');
    $datas = $this->Controlador_model->valorizado($empresa);
    $mostrar = '';
    $mostrar .= '
    <table id="example1" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Orden</th>
          <th>Codigo</th>
          <th>Producto</th>
          <th>Categoria</th>
          <th>Precio de compra</th>
          <th>Stock</th>
          <th>SubTotal</th>
        </tr>
      </thead>
    <tbody>';
    $total = 0;
    $i = 0;
    foreach ($datas as $data) {
      $i++;
      if ($data->totalstock > 0) {
        $producto = $this->Controlador_model->get($data->producto, 'producto');
        if ($producto) {
          $marca = $this->Controlador_model->get($producto->categoria, 'productocategoria');
          $total += $producto->preciocompra * $data->totalstock;
          $mostrar .= '
        <tr>
          <td>' . $i . '</td>
          <td>' . $producto->codigo . '</td>
          <td>' . $producto->nombre . '</td>
          <td>' . ($marca ? $marca->nombre : '') . '</td>
          <td>' . number_format($producto->preciocompra, 2) . '</td>
          <td>' . $data->totalstock . '</td>
          <td class="text-right">' . number_format($producto->preciocompra * $data->totalstock, 2) . '</td>
        </tr>';
        } else {
          continue;
        }
      }
    }
    $mostrar .= '
    </tbody>
      <tfoot>
        <tr>
          <td colspan="5"></td>
          <td>Total:</td>
          <td class="text-right">' . number_format($total, 2) . '</td>
        </tr>
      </tfoot>
    </table>';
    echo $mostrar;
  }

  public function margenproductoexcel()
  {
    $empresa = $this->uri->segment(3);
    $datas = $this->db->order_by("nombre", "ASC")->get("producto")->result();
    $almacenes = $this->db->order_by("id", "ASC")->where("empresa", $empresa)->get("almacen")->result();
    $this->phpexcel->getProperties()->setTitle("Lista de Productos")->setDescription("Lista de Productos");
    $this->phpexcel->setActiveSheetIndex(0);
    $sheet = $this->phpexcel->getActiveSheet();
    $sheet->setTitle("Lista de Productos - Ventas");

    //$sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('A')->setWidth(9);
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

    $style_header = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7')));
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $sheet->getStyle('A1:J1')->applyFromArray($style_header);
    $sheet->getStyle('A1:J1')->applyFromArray($style);
    $queryProductos = $this->Controlador_model->getProductos();
    $queryAlmacen = $this->Controlador_model->getAlmacenes();

    $sheet->setCellValueByColumnAndRow(0, 1, 'NUMERO');
    $sheet->setCellValueByColumnAndRow(1, 1, 'CATEGORIA');
    $sheet->setCellValueByColumnAndRow(2, 1, 'CODIGO BARRA');
    $sheet->setCellValueByColumnAndRow(3, 1, 'PRODUCTO');
    $columnasAumentadas = 0;
    foreach ($queryAlmacen as $keyAlmacen => $valueAlmacen) {
      $columna = $keyAlmacen + 4;
      $columnasAumentadas += 1;
      $sheet->setCellValueByColumnAndRow($columna, 1, $valueAlmacen->nombre);
    }
    $sheet->setCellValueByColumnAndRow(($columnasAumentadas + 4), 1, "TOTAL STOCK");
    $sheet->setCellValueByColumnAndRow(($columnasAumentadas + 5), 1, "PRECIO DE COMPRA");
    $sheet->setCellValueByColumnAndRow(($columnasAumentadas + 6), 1, "PRECIO DE VENTA");
    $sheet->setCellValueByColumnAndRow(($columnasAumentadas + 7), 1, "MARGEN BRUTO");

    foreach ($queryProductos as $key => $valueProducto) {
      $fila = $key + 2;
      $indice = $key + 1;
      $dataCategoria = $this->Controlador_model->get($valueProducto->categoria, "productocategoria");
      $sheet->setCellValueByColumnAndRow(0, $fila, $indice);
      $sheet->setCellValueByColumnAndRow(1, $fila, $dataCategoria->nombre);
      $sheet->setCellValueByColumnAndRow(2, $fila, $valueProducto->codigoBarra);
      $sheet->setCellValueByColumnAndRow(3, $fila, $valueProducto->nombre);
      $sheet->getStyle('C' . $fila)->getNumberFormat()->applyFromArray(array('code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER));
      $columnasAumentadasP = 0;
      foreach ($queryAlmacen as $keyAlmacenP => $valueAlmacenP) {
        $queryStock = $this->db->where("producto", $valueProducto->id)->where("empresa", $empresa)->where("almacen", $valueAlmacenP->id)->get("stock")->row();
        $columnaP = $keyAlmacenP + 4;
        $columnasAumentadasP += 1;
        $sheet->setCellValueByColumnAndRow($columnaP, $fila, ($queryStock ? $queryStock->cantidad : 0));
      }
      $queryTotalStock = $this->Controlador_model->getTotalStock($valueProducto->id, $empresa);
      if ($valueProducto->precioventa == 0) {
        $margen = "";
      } else {
        $margenBruto =  (1 - ($valueProducto->preciocompra / $valueProducto->precioventa)) * 100;
      }
      $sheet->setCellValueByColumnAndRow(($columnasAumentadasP + 4), $fila, ($queryTotalStock ? $queryTotalStock->cantidad : 0));
      $sheet->setCellValueByColumnAndRow(($columnasAumentadasP + 5), $fila, $valueProducto->preciocompra);
      $sheet->setCellValueByColumnAndRow(($columnasAumentadasP + 6), $fila, $valueProducto->precioventa);
      $sheet->setCellValueByColumnAndRow(($columnasAumentadasP + 7), $fila, $margenBruto);
      $sheet->getStyle('J' . $fila)->getNumberFormat()->applyFromArray(array('code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
    }
    // Salida
    header("Content-Type: application/vnd.ms-excel");
    $nombreArchivo = 'vendido_' . date('YmdHis');
    header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
    header("Cache-Control: max-age=0");
    // Genera Excel
    $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, "Excel5");
    // Escribir
    $writer->save('php://output');
    exit;
  }

  function ajax_margeproducto()
  {
    $empresa = $this->input->post('empresa');
    $datas = $this->db->order_by("nombre", "ASC")->get("producto")->result();
    $almacenes = $this->db->order_by("id", "ASC")->where("empresa", $this->empresa)->get("almacen")->result();
    $dataHtmlAlmacen = '';
    foreach ($almacenes as $almacen) {
      $dataHtmlAlmacen .= '<th>' . $almacen->nombre . '</th>';
    }
    $mostrar = '';
    $mostrar .= '
    <table id="example1" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Orden</th>
          <th>Categoria</th>
          <th>Codigo Barra</th>
          <th>Nombre de producto</th>
          ' . $dataHtmlAlmacen . '
          <th>Stock total</th>
          <th>Precio de compra</th>
          <th>Precio de venta</th>
          <th>Margen bruto</th>
        </tr>
      </thead>
    <tbody>';
    $i = 0;
    foreach ($datas as $data) {
      $dataHtmlAlmacenCantidad = '';
      foreach ($almacenes as $almacen) {
        $querySTock = $this->db->where('producto', $data->id)->where('almacen', $almacen->id)->where("empresa", $this->empresa)->get("stock")->row();
        $dataHtmlAlmacenCantidad .= '<td>' . ($querySTock ? $querySTock->cantidad : 0) . '</td>';
      }
      $i++;
      $categoria = $this->Controlador_model->get($data->categoria, 'productocategoria');
      $totalStock = $this->db->select_sum("cantidad")->where('producto', $data->id)->get("stock")->row();
      if ($data->precioventa == 0) {
        $margen = "";
      } else {
        $margen = number_format((1 - ($data->preciocompra / $data->precioventa)) * 100, 2);
      }

      $mostrar .= '
        <tr>
          <td>' . $i . '</td>
          <td>' . $categoria->nombre . '</td>
          <td>' . $data->codigoBarra . '</td>
          <td>' .  $data->nombre . '</td>
          ' . $dataHtmlAlmacenCantidad . '
          <td>' . ($totalStock ? $totalStock->cantidad : 0) . '</td>
          <td class="text-right">' .  $data->preciocompra . '</td>
          <td class="text-right">' .  $data->precioventa . '</td>
          <td class="text-right">' . $margen . '%</td>
        </tr>';
    }
    /*<tfoot>
        <tr>
          <td colspan="5"></td>
          <td>Total:</td>
          <td class="text-right">' . number_format($total, 2) . '</td>
        </tr>
      </tfoot>*/
    $mostrar .= '
    </tbody>
    </table>';
    echo $mostrar;
  }


  public function consolidarexcel()
  {
    $finicio = $this->uri->segment(3);
    $factual = $this->uri->segment(4);
    $empresa = $this->uri->segment(5);
    $pedidos = $this->Controlador_model->consolidar($finicio, $factual, $empresa);
    // Propiedades del archivo excel
    $this->phpexcel->getProperties()->setTitle("Lista de Productos")->setDescription("Lista de Productos en Ventas");
    // Setiar la solapa que queda actia al abrir el excel
    $this->phpexcel->setActiveSheetIndex(0);
    // Solapa excel para trabajar con PHP
    $sheet = $this->phpexcel->getActiveSheet();
    $sheet->setTitle("Lista de Productos - Ventas");
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $style_header = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7')));
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $sheet->getStyle('A1:G1')->applyFromArray($style_header);
    $sheet->getStyle("A1:G1")->applyFromArray($style);
    $sheet->setCellValue('A1', 'ORDEN');
    $sheet->setCellValue('B1', 'CODIGO');
    $sheet->setCellValue('C1', 'DESCRIPCION');
    $sheet->setCellValue('D1', 'S. INICIAL');
    $sheet->setCellValue('E1', 'ENTRADA');
    $sheet->setCellValue('F1', 'SALIDA');
    $sheet->setCellValue('G1', 'STOCK');
    $i = 1;
    foreach ($pedidos as $value) {
      $i++;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      if ($producto) {
        $marca = $this->Controlador_model->get($producto->categoria, 'productocategoria');
        $primero = $this->Controlador_model->primerMov($value->producto, $finicio, $factual, $empresa);
        $entrada = $this->Controlador_model->SumMovi($value->producto, $finicio, $factual, $empresa);
        $stock = $this->Controlador_model->getStock($value->producto, $empresa);
        $sheet->setCellValue('A' . $i, $i - 1);
        $sheet->setCellValue('B' . $i, $producto->codigo);
        $sheet->setCellValue('C' . $i, ($producto->nombre . ' ' . ($producto->marca ? $marca->nombre : '')));
        $sheet->setCellValue('D' . $i, ($primero ? $primero->stockanterior : 0));
        $sheet->setCellValue('E' . $i, ($entrada ? $entrada->cantidad : 0));
        $sheet->setCellValue('F' . $i, $value->numero);
        $sheet->setCellValue('G' . $i, ($stock ? $stock->cantidad : 0));
      } else {
        continue;
      }
    }
    // Salida
    header("Content-Type: application/vnd.ms-excel");
    $nombreArchivo = 'consolidado_' . date('YmdHis');
    header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
    header("Cache-Control: max-age=0");
    // Genera Excel
    $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, "Excel5");
    // Escribir
    $writer->save('php://output');
    exit;
  }

  public function vendidoexcel()
  {
    $finicio = $this->uri->segment(3);
    $factual = $this->uri->segment(4);
    $empresa = $this->uri->segment(5);
    $pedidos = $this->Controlador_model->vendido($finicio, $factual, $empresa);
    // Propiedades del archivo excel
    $this->phpexcel->getProperties()->setTitle("Lista de Productos")->setDescription("Lista de Productos mas Vendido");
    // Setiar la solapa que queda actia al abrir el excel
    $this->phpexcel->setActiveSheetIndex(0);
    // Solapa excel para trabajar con PHP
    $sheet = $this->phpexcel->getActiveSheet();
    $sheet->setTitle("Lista de Productos - Ventas");
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $style_header = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7')));
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $sheet->getStyle('A1:G1')->applyFromArray($style_header);
    $sheet->getStyle("A1:G1")->applyFromArray($style);
    $sheet->setCellValue('A1', 'ORDEN');
    $sheet->setCellValue('B1', 'CODIGO');
    $sheet->setCellValue('C1', 'DESCRIPCION');
    $sheet->setCellValue('D1', 'MARCA');
    $sheet->setCellValue('E1', 'CATEGORIA');
    $sheet->setCellValue('F1', 'CANTIDAD');
    $sheet->setCellValue('G1', 'MARGEN (%)');
    $i = 1;
    $suma = $this->Controlador_model->vendidototal($finicio, $factual, $empresa);
    foreach ($pedidos as $value) {
      $i++;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      if ($producto) {
        $marca = $this->Controlador_model->get($producto->categoria, 'productocategoria');
        $margen = number_format(($value->suma / $suma->numero) * 100, 2);
        $sheet->setCellValue('A' . $i, $i - 1);
        $sheet->setCellValue('B' . $i, $producto->codigo);
        $sheet->setCellValue('C' . $i, $producto->nombre);
        $sheet->setCellValue('D' . $i, ($producto->marca ? $marca->nombre : ''));
        if ($producto->categoria == 0) {
          $categoria = 'ESTANDAR';
        }
        if ($producto->categoria == 1) {
          $categoria = 'COMBINACION';
        }
        if ($producto->categoria == 2) {
          $categoria = 'SERVICIO';
        }
        $sheet->setCellValue('E' . $i, $categoria);
        $sheet->setCellValue('F' . $i, $value->suma);
        $sheet->setCellValue('G' . $i, $margen . ' %');
      } else {
        continue;
      }
    }
    // Salida
    header("Content-Type: application/vnd.ms-excel");
    $nombreArchivo = 'vendido_' . date('YmdHis');
    header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
    header("Cache-Control: max-age=0");
    // Genera Excel
    $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, "Excel5");
    // Escribir
    $writer->save('php://output');
    exit;
  }

  public function valorizadoexcel()
  {
    $empresa = $this->uri->segment(3);
    $pedidos = $this->Controlador_model->valorizado($empresa);
    // Propiedades del archivo excel
    $this->phpexcel->getProperties()->setTitle("Lista de Productos")->setDescription("Lista de productos en Stock");
    // Setiar la solapa que queda actia al abrir el excel
    $this->phpexcel->setActiveSheetIndex(0);
    // Solapa excel para trabajar con PHP
    $sheet = $this->phpexcel->getActiveSheet();
    $sheet->setTitle("Lista de Productos - Stock");
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $sheet->getStyle('G')->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('I')->getNumberFormat()->setFormatCode('#,##0.00');
    $style_header = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7')));
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $sheet->getStyle('A1:I1')->applyFromArray($style_header);
    $sheet->getStyle("A1:I1")->applyFromArray($style);
    $sheet->setCellValue('A1', 'ORDEN');
    $sheet->setCellValue('B1', 'CODIGO');
    $sheet->setCellValue('C1', 'PRODUCTO');
    $sheet->setCellValue('D1', 'CATEGORIA');
    $sheet->setCellValue('E1', 'PRECIO DE COMPRA');
    $sheet->setCellValue('F1', 'STOCK TOTAL');
    $sheet->setCellValue('G1', 'SUB TOTAL');
    $i = 1;
    foreach ($pedidos as $value) {
      $i++;
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $queryStock = $this->Controlador_model->getTotalStock($value->producto, $empresa);
      if ($producto) {
        $categoria = $this->Controlador_model->get($producto->categoria, 'productocategoria');
        $subtotal = $queryStock->cantidad * $producto->preciocompra;
        $sheet->setCellValue('A' . $i, $i - 1);
        $sheet->setCellValue('B' . $i, $producto->codigo);
        $sheet->setCellValue('C' . $i, $producto->nombre);
        $sheet->setCellValue('D' . $i, $categoria->nombre);
        $sheet->setCellValue('E' . $i, $producto->preciocompra);
        $sheet->setCellValue('F' . $i, $queryStock->cantidad);
        $sheet->setCellValue('G' . $i, $subtotal);
      } else {
        continue;
      }
    }
    // Salida
    header("Content-Type: application/vnd.ms-excel");
    $nombreArchivo = 'valorizado_' . date('YmdHis');
    header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
    header("Cache-Control: max-age=0");
    // Genera Excel
    $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, "Excel5");
    // Escribir
    $writer->save('php://output');
    exit;
  }

  public function inventarioexcel()
  {
    $empresas = $this->uri->segment(3);
    $familias = $this->Controlador_model->getAll('productocategoria');
    $empresa = $this->Controlador_model->get($empresas, 'empresa');
    // Propiedades del archivo excel
    $this->phpexcel->getProperties()->setTitle("INVENTARIO DE ARTICULOS")->setDescription("INVENTARIO");
    // Setiar la solapa que queda actia al abrir el excel
    $this->phpexcel->setActiveSheetIndex(0);
    // Solapa excel para trabajar con PHP
    $sheet = $this->phpexcel->getActiveSheet();
    $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
    $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    $sheet->getPageSetup()->setFitToWidth(1);
    $sheet->getPageSetup()->setFitToHeight(0);
    $sheet->getHeaderFooter()->setOddHeader('&B&IINVENTARIO DE ARTICULOS &R' . date('m/d/Y'));
    $sheet->getHeaderFooter()->setOddFooter('&L&B' . $this->phpexcel->getProperties()->getTitle() . '&RPagina &P de &N');
    $this->phpexcel->getDefaultStyle()->getFont()->setSize(10);
    $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 3);
    $sheet->setTitle("REPUESTOS Y LUBRICANTES");
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $style_header = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7')));
    $center = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $left = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT));
    $right = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
    $borde = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
    $sheet->mergeCells('A1:D1');
    $sheet->setCellValue('A1', $empresa->serie . ' ' . $empresa->razonsocial);
    $sheet->getStyle("E1:F1")->applyFromArray($right);
    $sheet->mergeCells('E1:F1');
    $sheet->setCellValue('E1', date('g:i:s A', time()));
    $sheet->mergeCells('A2:D2');
    $sheet->getStyle('A3:F3')->applyFromArray($style_header);
    $sheet->getStyle('A3:F3')->applyFromArray($center);
    $sheet->setCellValue('A3', 'ORDEN');
    $sheet->setCellValue('B3', 'CODIGO');
    $sheet->setCellValue('C3', 'DESCRIPCION');
    $sheet->setCellValue('D3', 'MARCA');
    $sheet->setCellValue('E3', 'CANTIDAD');
    $sheet->setCellValue('F3', 'STOCK');
    $i = 3;
    $j = 0;
    $k = 0;
    foreach ($familias as $data) {
      $i++;
      $sheet->getStyle('A' . $i . ':F' . $i)->applyFromArray($borde);
      $sheet->mergeCells('A' . $i . ':F' . $i);
      $sheet->setCellValue('A' . $i, $data->nombre);
      $productos = $this->Controlador_model->getFamilia($data->id);
      foreach ($productos as $value) {
        $stock = $this->Controlador_model->getStock($value->id, $empresas);
        if ($stock) {
          $producto = $this->Controlador_model->get($value->id, 'producto');
          $j++;
          $i++;
          $sheet->getStyle('A' . $i)->applyFromArray($borde);
          $sheet->getStyle('B' . $i)->applyFromArray($borde);
          $sheet->getStyle('C' . $i)->applyFromArray($borde);
          $sheet->getStyle('D' . $i)->applyFromArray($borde);
          $sheet->getStyle('E' . $i)->applyFromArray($borde);
          $sheet->getStyle('F' . $i)->applyFromArray($borde);
          $sheet->setCellValue('A' . $i, $j);
          $sheet->setCellValue('B' . $i, $value->codigo);
          $sheet->setCellValue('C' . $i, $value->nombre);
          $sheet->setCellValue('D' . $i, "");
          $sheet->setCellValue('E' . $i, ($stock ? $stock->cantidad : 0));
          $sheet->setCellValue('F' . $i, '');
        }
      }
    }
    // Salida
    header("Content-Type: application/vnd.ms-excel");
    $nombreArchivo = 'inventario_' . date('Y-m-d_His');
    header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
    header("Cache-Control: max-age=0");
    // Genera Excel
    $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, "Excel5");
    // Escribir
    $writer->save('php://output');
    exit;
  }



  public function cargar()
  {
    $config["upload_path"] = realpath(APPPATH . "../files");
    $config["allowed_types"] = 'xls';
    $config["max_size"] = '0';
    $this->upload->initialize($config);
    if (!$this->upload->do_upload('file')) {
      print_r($this->upload->display_errors());
    } else {
      $data = array('upload_data' => $this->upload->data());
      $this->load->library('PHPExcel');
      $objPHPExcel = PHPExcel_IOFactory::load(APPPATH . '../files/' . $data['upload_data']['file_name']);
    }
    unlink($config["upload_path"] . '/' . $data['upload_data']['file_name']);
    $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection(); //estamos trayendo la coleccion de filas
    $header = array();
    $arr_data = array();
    foreach ($cell_collection as $cell) {
      # code...
      //aui estamos obteniendo las columnas a,b,c
      $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
      $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
      $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
      if ($row == 1) {
        $header[$row][$column] = $data_value;
      } else {
        $arr_data[$row][$column] = $data_value;
      }
    }
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista . 'ver',
      'datas' => $arr_data,
      'breads' => array(
        array('ruta' => $this->url, 'titulo' => $this->titulo_controlador),
        array('ruta' => 'javascript:;', 'titulo' => 'Cargar')
      )
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function ajax_inventarioproducto_pdf()
  {

    $empresa = $this->input->post('empresa');
    $ticket = '<embed src="' . $this->url . '/inventarioproducto_pdf/' . $empresa . '" type="application/pdf" width="100%" height="400"></embed>';
    echo $ticket;
  }

  public function inventarioproducto_pdf()
  {

    $empresa = $this->uri->segment(3);

    $data["productos"] = $this->db->where("tipo", '0')->get("producto")->result();
    $data["empresa"] = $this->Controlador_model->get($empresa, "empresa");

    $this->load->view('pdfreporteproductos', $data);

    $html = $this->output->get_output();
    $this->dompdf->loadHtml($html);
    $this->dompdf->setPaper('A4', 'portrait');
    $this->dompdf->render();
    $this->dompdf->stream("inventario_productos.pdf", array("Attachment" => 0));
  }

  public function ajax_listvariante($id)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where('producto', $id)->get('productovariante')->result();
    $dataproducto = $this->Controlador_model->get($id, "producto");
    $data = [];
    foreach ($query as $key => $value) {
      //add variables for action
      $boton = '';
      //add html for action
      $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="editvariante(' . $value->id . ')"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrarvariante(' . $value->id . ')"><i class="fa fa-trash"></i></a>';

      $data[] = array(
        $key + 1,
        $value->nombre,
        $value->cantidad,
        $value->precio,
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

  public function ajax_addvariante()
  {
    $data['producto'] = $this->input->post('varianteproducto');
    $data['nombre'] = $this->input->post('nombrevariante');
    $data['precio'] = $this->input->post('preciovariante');
    $data['cantidad'] = $this->input->post('cantidadvariante');
    $insert = $this->Controlador_model->save('productovariante', $data);
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_editvariante($id)
  {
    $data = $this->Controlador_model->get_by_id($id, 'productovariante');
    echo json_encode($data);
  }

  public function ajax_updatevariante()
  {
    $data['producto'] = $this->input->post('varianteproducto');
    $data['nombre'] = $this->input->post('nombrevariante');
    $data['precio'] = $this->input->post('preciovariante');
    $data['cantidad'] = $this->input->post('cantidadvariante');
    $this->Controlador_model->update(array('id' => $this->input->post('idvariante')), $data, 'productovariante');
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_deletevariante($id)
  {
    if ($this->Controlador_model->delete_by_id($id, 'productovariante')) {
      echo json_encode(array("status" => TRUE));
    }
  }

  function ajax_variante_precicompra($idproducto)
  {
    $dataproducto = $this->Controlador_model->get($idproducto, "producto");
    echo json_encode($dataproducto);
  }

  function ajax_dataLoteAlmacen($idproducto, $empresa)
  {
    $queryLotificar = $this->Controlador_model->queryLotificar($idproducto, $empresa); 
    $queryLotes = $this->db->where("producto", $idproducto)->get("lote")->result(); //! PULIR LOTE, QUE LOS LOTES SE RELACINEN A LA EMPRESA
    $almacenes = $this->db->where("empresa", $empresa)->get('almacen')->result();
    $htmlAlmacenesTitle = "";
    $htmlSinLotificar = "";
    if ($queryLotificar->num_rows() > 0) {
      $htmlSinLotificar = "
        <div class='alert alert-danger text-center'>
        <ul>";
      foreach ($queryLotificar->result() as $value) {
        $almacen = $this->Controlador_model->get($value->almacen, "almacen");
        $htmlSinLotificar .= '<li>' . $almacen->nombre . ' con <span class="badge badge-warning babgelote">' . $value->cantidad . '</span> de stock sin lotificar</li>';
      }
      $htmlSinLotificar .= "</ul></div>";
    }


    foreach ($almacenes as $value) {
      $htmlAlmacenesTitle .= '<th>' . $value->nombre . '</th>';
    }
    $dataHtml = '<table id="example1" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Lote</th>
        <th>Caducidad</th>
        ' . $htmlAlmacenesTitle . '
      </tr>
    </thead>';
    $dataHtml .= '<tbody>';
    foreach ($queryLotes as $lote) {
      $htmlAlmacenesStock = "";
      foreach ($almacenes as $almacen) {
        $queryCantidad = $this->db->select_sum("cantidad")->where("producto", $idproducto)->where("lote", $lote->id)->where("almacen", $almacen->id)->where("empresa", $empresa)->get("stock")->row();
        $cantidad = $queryCantidad->cantidad == '' ? 0 : $queryCantidad->cantidad;
        $htmlAlmacenesStock .= ' <td>' . $cantidad . '</td>';
      }
      $dataHtml .= '
          <tr>
            <td>' . $lote->lote . '</td>
            <td>' . $lote->vencimiento . '</td>
            ' . $htmlAlmacenesStock . '
          </tr>';
    }
    $dataHtml .= '</tbody></table>';

    echo json_encode(["dataHtml" => $dataHtml, "htmlSinLotificar" => $htmlSinLotificar]);
  }


  function _validatelote()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    if ($this->input->post('codigo_lote') == '') {
      $data['inputerror'][] = 'codigo_lote';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('vencimiento_lote') == '') {
      $data['inputerror'][] = 'vencimiento_lote';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }



  function ajax_addLote()
  {
    $this->_validatelote();
    $dataInsert['lote'] = $this->input->post('codigo_lote');
    $dataInsert['producto'] = $this->input->post('idproducto-lote');
    $dataInsert['vencimiento'] = $this->input->post('vencimiento_lote');
    $dataInsert['estado'] = 1;
    $dataInsert['created_at'] = date("Y-m-d H:i:s");
    $insert = $this->Controlador_model->save('lote', $dataInsert);
    if ($insert) {
      $query = $this->Controlador_model->get($insert, 'lote');
      $textLote = $query->lote . " | " . $query->vencimiento;
      echo json_encode(array("status" => TRUE, "idlote" => $insert, "textlote" => $textLote));
    }
  }

  private function _validateprocesoLotizar()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;


    if ($this->input->post('lote') == '') {
      $data['inputerror'][] = 'lotes';
      $data['error_string'][] = '';
      $data['status'] = FALSE;
    }

    if ($this->input->post('almacenlote') == '0') {
      $data['inputerror'][] = 'almacenlote';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('cantidadlote') == '') {
      $data['inputerror'][] = 'cantidadlote';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('lote') <> '' and $this->input->post('almacenlote') <> '') {
      $queryCantidad = $this->Controlador_model->queryCantidad($this->input->post("productolote"), $this->input->post('almacenlote'));
      $totalStock = $queryCantidad->cantidad <> "" ? $queryCantidad->cantidad : 0;
      if ($this->input->post('cantidadlote') >  $totalStock) {
        $data['inputerror'][] = 'cantidadlote';
        $data['error_string'][] = 'Solo cuentas con ' . $totalStock . ' de stock para lotizar';
        $data['status'] = FALSE;
      }
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  function ajax_lotizar()
  {
    $this->_validateprocesoLotizar();
    $descontar = $this->Controlador_model->queryDescontarAlmacen($this->input->post("productolote"), $this->input->post("almacenlote"));
    $cantidadDescontar = $descontar->cantidad - $this->input->post("cantidadlote");
    $queryLoteStock = $this->db->where("producto", $this->input->post("productolote"))->where("almacen", $this->input->post("almacenlote"))->where("lote", $this->input->post("lote"))->get("stock")->row();
    if ($queryLoteStock) {
      $dataUpdate["cantidad"] = $queryLoteStock->cantidad + $this->input->post("cantidadlote");
      $this->Controlador_model->update(["id" => $queryLoteStock->id], $dataUpdate, "stock");
    } else {
      $dataRegister["empresa"] = $this->empresa;
      $dataRegister["producto"] = $this->input->post("productolote");
      $dataRegister["almacen"] = $this->input->post("almacenlote");
      $dataRegister["lote"] = $this->input->post("lote");
      $dataRegister["cantidad"] = $this->input->post("cantidadlote");
      $this->Controlador_model->save("stock", $dataRegister);
    }
    if ($cantidadDescontar <= 0) {
      $this->Controlador_model->delete_by_id($descontar->id, "stock");
    } else {

      $dataDescontar["cantidad"] = $cantidadDescontar;
      $this->Controlador_model->update(["id" => $descontar->id],  $dataDescontar, "stock");
    }
    echo json_encode(["status" => TRUE]);
  }

  private function validateMarca()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    $query = $this->db->where('nombre', $this->input->post('nombremarca'))->get("marca")->row();

    if ($this->input->post('nombremarca') == '') {
      $data['inputerror'][] = 'nombremarca';
      $data['error_string'][] = 'Campo obligatorio';
      $data['status'] = FALSE;
    }

    if ($query) {
      $data['inputerror'][] = 'nombremarca';
      $data['error_string'][] = 'Esta marca ya esta registrada';
      $data['status'] = FALSE;
    }


    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  function ajax_addmarca()
  {
    $this->validateMarca();
    $numero = $this->Controlador_model->maxcodigo("marca");
    $numeros = $numero ? $numero->numero + 1 : 1;
    $cadena = "";
    for ($i = 0; $i < 2 - strlen($numeros); $i++) {
      $cadena = $cadena . '0';
    }
    $datainsert["empresa"] = $this->empresa;
    $datainsert["nombre"] = $this->input->post("nombremarca");
    $datainsert['codigo'] = $cadena . $numeros;
    $datainsert['numero'] = $numeros;
    $insert = $this->Controlador_model->save("marca", $datainsert);
    if ($insert) {
      $marcas = $this->db->order_by("nombre", "ASC")->get("marca")->result();
      echo json_encode(["status" => TRUE, "marcas" => $marcas, "idregistrado" => $insert]);
    }
  }
  function _validateCategoria()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    //$categoria = $this->Controlador_model->check($this->input->post('id'), $this->input->post('nombre'));
    $query = $this->db->where('nombre', $this->input->post('nombrecategoria'))->get("productocategoria")->row();
    if ($this->input->post('nombrecategoria') == '') {
      $data['inputerror'][] = 'nombrecategoria';
      $data['error_string'][] = 'Campo obligatorio';
      $data['status'] = FALSE;
    }

    if ($query) {
      $data['inputerror'][] = 'nombrecategoria';
      $data['error_string'][] = 'Esta categoria ya esta registrada';
      $data['status'] = FALSE;
    }


    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function conseguircodigo($tipoproducto, $categoria)
  {
      $tipo = $tipoproducto;
      $categoria = $categoria;
      $numero = $this->Controlador_model->codigos($tipo, $categoria);
      $numeros = $numero ? $numero->numero + 1 : 1;
      $cadena = "";
      for ($i = 0; $i < 3 - strlen($numeros); $i++) {
        $cadena = $cadena . '0';
      }
      $categorias = $this->Controlador_model->get($categoria, 'productocategoria');
      $codigo1 = $categorias ? $categorias->id : '00';
      $codigo = substr($categorias->nombre, 0, 1) . $tipo . $codigo1 . $cadena . $numeros;
      $output = array("codigo" => $codigo, "numero" => $numeros);
      return $output;
  }


  function ajax_addcategoria($tipoproducto)
  {
    $this->_validateCategoria();

    if (!is_null($this->input->post('chkExtras'))) {
      $EstadoExtras = "1";
    } else {
      $EstadoExtras = "0";
    }

    $config['upload_path'] = './files/productocategoria/';
    $config['encrypt_name'] = TRUE;
    $config['allowed_types'] = 'gif|jpg|jpeg|png';
    $config['max_width'] = '10000';
    $config['max_height'] = '10000';
    $this->load->library('upload', $config);
    $this->load->library('image_lib');
    if ($this->upload->do_upload('foto2')) {
      $datas = array('upload_data' => $this->upload->data());
      $config2['image_library'] = 'gd2';
      $config2['source_image'] = $datas['upload_data']['full_path'];
      $config2['create_thumb'] = TRUE;
      $config2['maintain_ratio'] = TRUE;
      $config2['width'] = 120;
      $config2['height'] = 120;
      $this->image_lib->clear();
      $this->image_lib->initialize($config2);
      $this->image_lib->resize();
      $image = $datas['upload_data']['file_name'];
      $image_thumb = $datas['upload_data']['raw_name'] . '_thumb' . $datas['upload_data']['file_ext'];
      $data['photo'] = $image;
      $data['photothumb'] = $image_thumb;
    }
    $numero = $this->Controlador_model->codigoscategoria();
    $numeros = $numero ? $numero->numero + 1 : 1;
    $cadena = "";
    for ($i = 0; $i < 2 - strlen($numeros); $i++) {
      $cadena = $cadena . '0';
    }

    $data['empresa'] = $this->empresa;
    $data['nombre'] = $this->input->post('nombrecategoria');
    $data['descripcion'] = $this->input->post('descripcioncategoria');
    $data['codigo'] = $cadena . $numeros;
    $data['numero'] = $numeros;
    $data['estadoextras'] =  $EstadoExtras;
    $insert = $this->Controlador_model->save("productocategoria", $data);

    if ($insert) {
      $dataCodigo = $this->conseguircodigo($tipoproducto, $insert);
      $categorias = $this->db->order_by("nombre", "ASC")->get("productocategoria")->result();
      echo json_encode(["status" => TRUE, "karl" => $categorias, "idregistrado" => $insert, "codigoproducto" => $dataCodigo]);
    }
  }


}