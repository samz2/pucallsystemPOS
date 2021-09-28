<?php

//Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
require __DIR__ . '/ticket/autoload.php';

require 'simple_html_dom.php';

use FontLib\Table\Type\post;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use function PHPSTORM_META\map;


//? NUEVO
class Inicio extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model(modelo(), 'Controlador_model');
    $this->load->library('Phpmailer_lib');
    $this->controlador = controlador();
    $this->titulo_controlador = humanize($this->controlador);
    $this->url = base_url() . $this->controlador;
    $this->vista = $this->controlador;
    $this->perfil = $this->session->userdata('perfil') ? $this->session->userdata('perfil') : FALSE;
    $this->caja = $this->session->userdata('caja') ? $this->session->userdata('caja') : FALSE;
    $this->venta = $this->session->userdata('venta') ? $this->session->userdata('venta') : FALSE;
    $this->usuario = $this->session->userdata('usuario') ? $this->session->userdata('usuario') : FALSE;
    $this->empresa = $this->session->userdata('empresa') ? $this->session->userdata('empresa') : FALSE;
    $this->cajaprincipal = $this->session->userdata('cajaprincipal') ? $this->session->userdata('cajaprincipal') : FALSE;
  }

  public function index()
  {
    $data = array(
      'contenido' => $this->vista,
      'empresa' => $this->Controlador_model->get($this->empresa, 'empresa'),
      'zonas' => $this->caja ? $this->Controlador_model->ventapendiente($this->caja) : '',
      'header' => $this->venta ? $this->Controlador_model->getmesa($this->venta) : '',
      'cajaprincipales' => $this->Controlador_model->getAll('cajaprincipal'),
      'zonasa' => $this->Controlador_model->getZonasEmpresa($this->empresa),
      'mesotas' => $this->Controlador_model->getMesasEmpresa($this->empresa),
      'categorias' => $this->Controlador_model->getAll('productocategoria'),
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function openregister()
  {
    $apertura = $this->Controlador_model->cajabierta('0', $this->input->post('cajaprincipal'));
    if ($apertura) {
      redirect($this->url);
      exit();
    }
    $numero = $this->Controlador_model->maximo('caja', $this->input->post('cajaprincipal'));
    $numeros = $numero ? $numero->numero + 1 : 1;
    $cadena = "";
    for ($i = 0; $i < 5 - strlen($numeros); $i++) {
      $cadena = $cadena . '0';
    }
    $dataCajaPincipal = $this->Controlador_model->get($this->input->post('cajaprincipal'), "cajaprincipal");
    $data['descripcion'] = 'CAJA N° ' . $cadena . $numeros;
    $data['numero'] = $numeros;
    $data['cajaprincipal'] = $this->input->post('cajaprincipal');
    $data['saldoinicial'] = $this->input->post('saldoinicial');
    $data['usuario'] = $this->usuario;
    $data['apertura'] = date("Y-m-d H:i:s");
    $data['created'] = date('Y-m-d');
    $registro = $this->Controlador_model->save('caja', $data);
    $this->procesoCajaStock($registro, $this->input->post('cajaprincipal'), TRUE);
    $CI = &get_instance();
    $CI->session->set_userdata('caja', $registro);
    $CI->session->set_userdata('cajaprincipal',  $this->input->post('cajaprincipal'));
    $this->caja = $CI->session->userdata('caja') ? $CI->session->userdata('caja') : FALSE;
    $this->cajaprincipal = $CI->session->userdata('cajaprincipal') ? $CI->session->userdata('cajaprincipal') : FALSE;
    //Registro de empresa en la en
    $queryventa = $this->Controlador_model->totalVentasNoProcesadas($this->usuario, $dataCajaPincipal->tienda, $registro); //? hacer que la consulta sea por caja
    if ($queryventa->num_rows() == 0) {
      $dataVenta['empresa'] = $dataCajaPincipal->tienda;
      $dataVenta['usuario_creador'] = $this->usuario;
      $dataVenta['caja'] = $registro;
      $dataVenta['mesa'] = NULL;
      $dataVenta['cliente'] = 1;
      $dataVenta['tipoventa'] = $dataCajaPincipal->tipoventa;
      $dataVenta['hora'] = date("H:i:s");
      $dataVenta['created'] = date("Y-m-d");
      $this->Controlador_model->save('venta', $dataVenta);
    }
    echo json_encode(array("status" => TRUE));
  }

  public function aperturar($cajaprincipal)
  {
    $cajaAbierto = $this->Controlador_model->cajabierta('0', $cajaprincipal);
    if ($cajaAbierto) {
      $dataCajaPrincipal = $this->Controlador_model->get($cajaprincipal, "cajaprincipal");
      $CI = &get_instance();
      $CI->session->set_userdata('cajaprincipal', $cajaprincipal);
      $CI->session->set_userdata('caja', $cajaAbierto->id);
      $queryventa = $this->Controlador_model->totalVentasNoProcesadas($this->usuario, $dataCajaPrincipal->tienda, $cajaAbierto->id);
      if ($queryventa->num_rows() == 0) {
        $dataVenta['empresa'] = $cajaAbierto->tienda;
        $dataVenta['usuario_creador'] = $this->usuario;
        $dataVenta['caja'] = $cajaAbierto->id;
        $dataVenta['mesa'] = NULL;
        $dataVenta['cliente'] = 1;
        $dataVenta['tipoventa'] = $cajaAbierto->tipoventa;
        $dataVenta['hora'] = date("H:i:s");
        $dataVenta['created'] = date("Y-m-d");
        $this->Controlador_model->save('venta', $dataVenta);
      }
    }
    redirect($this->url);
  }

  /* function impresiondesesiones()
  {
    echo "sesion cajaprincipal". $this->cajaprincipal." <br>";
    echo "sesion caja". $this->caja;
  } */


  private function procesoCajaStock($idcaja, $cajaprincipal, $estado)
  {
    if ($estado) {
      //? REGISTRO DEL STOCK INICIO
      $dataCajaPrincipal = $this->Controlador_model->get($cajaprincipal, "cajaprincipal");
      $queryproductos = $this->db->where("estado", "0")->where("estado_stockcaja", "1")->get("producto")->result();
      foreach ($queryproductos as $value) {
        $queryStock = $this->Controlador_model->getStockAlmacen($value->id,  $dataCajaPrincipal->almacen, NULL, $dataCajaPrincipal->tienda);
        $dataStockCaja["caja"] = $idcaja;
        $dataStockCaja["producto"] = $value->id;
        $dataStockCaja["categoria"] = $value->categoria;
        $dataStockCaja["tienda"] = $dataCajaPrincipal->tienda;
        $dataStockCaja["almacen"] = $dataCajaPrincipal->almacen;
        $dataStockCaja["tipo"] = "PRODUCTO";
        $dataStockCaja["nombre"] = $value->nombre;
        $dataStockCaja["inicio_stock"] = $queryStock ? $queryStock->cantidad : 0;
        $dataStockCaja["created_datetime"] = date("Y-m-d H:i:s");
        $this->Controlador_model->save("cajastock", $dataStockCaja);
      }
    } else {
      //? ACTUALIZACION  DEL STOCK FINAL
      $queryCajaStock = $this->db->where("caja", $idcaja)->get("cajastock")->result();
      foreach ($queryCajaStock as $data) {
        $queryStockFinal = $this->Controlador_model->getStockAlmacen($data->producto,  $data->almacen, NULL, $data->tienda);
        $dataStockCajaFinal["final_stock"] = $queryStockFinal ? $queryStockFinal->cantidad : 0;
        $this->db->where("id", $data->id)->update("cajastock", $dataStockCajaFinal);
      }
    }
  }

  function ajax_CrearNewVenta()
  {
    $dataCajaPincipal = $this->Controlador_model->get($this->cajaprincipal, "cajaprincipal");
    $dataventa = [
      'empresa' => $dataCajaPincipal->tienda,
      'usuario_creador' => $this->usuario,
      'caja' => $this->caja,
      'mesa' => null,
      'cliente' => 1,
      'hora' => date("H:i:s"),
      'created' => date("Y-m-d")
    ];
    $insert = $this->Controlador_model->save('venta', $dataventa);
    if ($insert) {
      echo json_encode(["status" => TRUE]);
    }
  }

  function ajax_salir_caja()
  {
    $CI = &get_instance();
    $CI->session->set_userdata('caja', NULL);
    $CI->session->set_userdata('cajaprincipal', NULL);
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_opcionmenu()
  {
    $data = '';
    $data .= '<li data-toggle="tooltip" data-html="true" data-placement="left" title="Regreso">
    <a onclick="salircaja()"><i class="fa fa-reply" aria-hidden="true"></i></a></li>';
    $data .= '<li data-toggle="tooltip" data-html="true" data-placement="left" title="Cerrar&nbsp;Caja">
    <a onclick="CloseRegister()" id="boton-CloseRegister"><i class="fa fa-times" aria-hidden="true"></i></a>
   </li>';
    $data .= '<li data-toggle="tooltip" data-html="true" data-placement="left" title="Cargar&nbsp;Pagina">
    <a href="javascript:void(0)" onclick="location.reload()"><i class="fa fa-refresh" aria-hidden="true"></i></a></li>';
    echo json_encode(["html" => $data]);
  }

  public function autocompletar()
  {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->autocompletar($q);
    }
  }


  public function ajax_codigodebarra()
  {
    $dataCajaPrincipal = $this->Controlador_model->get($this->cajaprincipal, "cajaprincipal");
    $codigobarra = $this->input->post("codigodebarra");
    $query = $this->Controlador_model->getCodigoBarra($codigobarra);
    if ($query) {
      if ($query->tipo == "1") {
        echo json_encode(
          [
            'consulta' => ['status' => TRUE, 'idproducto' => $query->id, 'precioproducto' => $query->precioventa, 'nombreproducto' => $query->nombre],
            'lote' => ['status' => $query->status_lote],
          ]
        );
        exit();
      } else if ($query->tipo == "0") {
        if ($query->status_lote == "1") {
          $dataLotes = $this->Controlador_model->dataLotes($query->id, $dataCajaPrincipal->almacen, $dataCajaPrincipal->tienda);
          if ($dataLotes->num_rows() > 1) {
            //? MODAL
            echo json_encode(
              [
                'consulta' => ['status' => TRUE, 'idproducto' => $query->id, 'precioproducto' => $query->precioventa, 'nombreproducto' => $query->nombre],
                'lote' => ['status' => $query->status_lote, 'totalotes' => $dataLotes->num_rows()]
              ]
            );
          } else {
            //? REGISTRO DIRECTO DE LOTES
            $dataStock = $dataLotes->row();
            echo json_encode(
              [
                'consulta' => ['status' => TRUE, 'idproducto' => $query->id, 'precioproducto' => $query->precioventa, 'nombreproducto' => $query->nombre],
                'lote' => ['status' => $query->status_lote,  'totalotes' => $dataLotes->num_rows(), 'lote' => $dataStock ? $dataStock->lote : null]
              ]
            );
          }
        } else {
          $queryStock = $this->Controlador_model->queryStock($query->id, $dataCajaPrincipal->almacen, $dataCajaPrincipal->tienda);
          if ($queryStock) {
            echo json_encode(
              [
                'consulta' => ['status' => TRUE, 'idproducto' => $query->id, 'precioproducto' => $query->precioventa, 'statuslote' => $query->status_lote, "nombreproducto" => $query->nombre],
                'lote' => ['status' => $query->status_lote]
              ]
            );
          } else {
            echo json_encode(['consulta' => ["status" => FALSE, "msg" => $query->nombre . " SIN STOCK (´･_･`)"]]);
          }
        }
      } else {
        echo json_encode(['consulta' => ["status" => FALSE, "msg" => ""]]);
      }
    } else {
      echo json_encode(['consulta' => ["status" => FALSE, "msg" => ""]]);
    }
  }

  private function _validate()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    if ($this->input->post('cliente') == '') {
      $data['inputerror'][] = 'clientes';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_update($idventa)
  {
    $venta = $this->Controlador_model->get($idventa, 'venta');
    if ($venta->estado == 1) {
      redirect($this->url);
    }
    $this->_validate();
    $data['tipoventa'] = $this->input->post('tipoventa');
    $data['cliente'] = $this->input->post('cliente');
    $this->Controlador_model->update(array('id' => $idventa), $data, 'venta');
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_Datos_venta()
  {
    $query = $this->Controlador_model->get($this->input->post("idventa"), "venta");
    if ($query) {
      $cliente = $this->Controlador_model->get($query->cliente, 'cliente');
      $data = [
        "tipoventa" => $query->tipoventa,
        "cliente" => $query->cliente,
        "clientes" => $cliente->documento . ' | ' . $cliente->nombre . ' | ' . $cliente->apellido
      ];
      echo json_encode($data);
    }
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



  public function ajax_addcliente($idventa)
  {
    $this->_validatecliente();
    $data['tipodocumento'] = $this->input->post('tipo');
    $data['documento'] = $this->input->post('documento');
    $data['nombre'] = $this->input->post('nombre');
    $data['apellido'] = $this->input->post('apellido');
    $data['direccion'] = $this->input->post('direccion');
    $data['telefono'] = $this->input->post('telefono');
    $data['correo'] = $this->input->post('email');
    $ultimo = $this->Controlador_model->save('cliente', $data);
    $venta['cliente'] = $ultimo;
    $this->Controlador_model->update(array('id' => $idventa), $venta, 'venta');
    $recoverCliente = $this->Controlador_model->get($ultimo, 'cliente');
    $cliente = $recoverCliente->id;
    $clientes = $recoverCliente->documento . ' | ' . $recoverCliente->nombre . ' ' . $recoverCliente->apellido;
    echo json_encode(array("status" => TRUE, 'cliente' => $cliente, 'clientes' => $clientes));
  }

  function validateProductoLibre()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    if ($this->input->post('nombreproductolibre') == '') {
      $data['inputerror'][] = 'nombreproductolibre';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('precioventa') == '') {
      $data['inputerror'][] = 'precioventa';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }


  public function ajax_addproductolibre()
  {
    $this->validateProductoLibre();
    echo json_encode(array("status" => TRUE));
  }

  private function NewVenta()
  {
    $dataCajaPrincipal = $this->Controlador_model->get($this->cajaprincipal, "cajaprincipal");
    $queryVentas = $this->Controlador_model->totalVentasNoProcesadas($this->usuario, $dataCajaPrincipal->tienda, $this->caja);
    if ($queryVentas->num_rows() == 0) {
      $data['empresa'] = $dataCajaPrincipal->tienda;
      $data['cliente'] = 1;
      $data['hora'] = date("H:i:s");
      $data['created'] = date("Y-m-d");
      $data['usuario_creador'] = $this->usuario;
      $data['caja'] = $this->caja;
      $data['mesa'] = null;
      $data['tipoventa'] = $dataCajaPrincipal->tipoventa;
      $insert = $this->Controlador_model->save('venta', $data);
    }
  }


  private function printfcomprobante($idventa, $metodoPago)
  {
    $venta = $this->Controlador_model->get($idventa, 'venta');
    $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
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
        <td colspan='3' style='text-align:right; font-weight:bold; border:none; color:#36a229''>Pagado($metodoPago) S/</td>
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

    return ["htmlComprobante" => $htmlComprobante, "htmlFotter" => $htmlFotter];
  }

  public function ajax_sendemail()
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

    if ($mail->send()) {
      echo json_encode(array('status' => TRUE));
      exit();
    } else {
      echo json_encode(array('status' => FALSE));
      exit();
    }
  }

  public function ajax_CloseRegister()
  {
    $mostrar = '
      <h2 class="text-center">
      RESUMEN DE CAJA
      <img src="' . site_url() . '/assets/adminlte/img/register.svg" alt="" style="width:100px">
      </h2>
       
     <input type="hidden" value="1" name="tipoproceso" id="tipoproceso">
   
     <div class="col-lg-12">
       <div class="tabs-vertical-env">
         <ul class="nav tabs-vertical">
           <li class="active">
             <a href="#procesoone" data-toggle="tab" aria-expanded="true" style="font-size:16px; font-weight:bold" onclick="cambiarproceso(1)">Proceso 1</a>
           </li>
           <li class="">
             <a href="#procesotwo" data-toggle="tab" aria-expanded="false" style="font-size:16px;font-weight:bold" onclick="cambiarproceso(0)">Proceso 2</a>
           </li>
         </ul>
   
         <div class="tab-content" style="width:100%">

           <div class="tab-pane active" id="procesoone">
             <div class="form-group text-center">
                <label>Inserte el monto total de efectivo</label>
                <input type="number" name="montototalcaja" class="form-control" id="montototalcaja" placeholder="Digitar">
                <span class="help-block"></span>
             </div>
             
         </div>
           <div class="tab-pane" id="procesotwo">
             
             <table class="table table-striped" >
             <tr>
               <th>TIPO DE MONEDA (SOLES)</th>
               <th>CANTIDAD MONEDA</th>
             </tr>
             <tr>
               <td>0.10</td>
               <td><input type="text" class="form-control" name="diezcentimos" id="diezcentimos" value="0"></td>
             </tr>
             <tr>
             <td>0.20</td>
             <td><input type="text" class="form-control" name="veintecentimos" id="veintecentimos" value="0"></td>
             </tr>
             <tr>
             <td>0.50</td>
             <td><input type="text" class="form-control" name="cincuentacentimos" id="cincuentacentimos" value="0">
             </td>
             </tr>
             <tr><td>1.00</td>
             <td><input type="text" class="form-control" name="unsol" id="unsol" value="0"></td></tr>
             <tr><td>2.00</td>
             <td><input type="text" class="form-control" name="dossoles" id="dossoles" value="0"></td></tr>
             <tr><td>5.00</td>
             <td><input type="text" class="form-control" name="cincosoles" id="cincosoles" value="0"></td></tr>
             <tr><td>10.00</td>
             <td><input type="text" class="form-control" name="diezsoles" id="diezsoles" value="0"></td></tr>
             <tr><td>20.00</td><td><input type="text" class="form-control" name="veintesoles" id="veintesoles" value="0"></td></tr>
             <tr><td>50.00</td><td><input type="text" class="form-control" name="cincuentasoles" id="cincuentasoles" value="0"></td></tr>
             <tr><td>100.00</td><td><input type="text" class="form-control" name="ciensoles" id="ciensoles" value="0"></td></tr>
             <tr><td>200.00</td><td><input type="text" class="form-control" name="doscientossoles" id="doscientossoles" value="0"></td></tr>
           </table>
           </div>
         </div>
       </div>
     </div>';
    echo json_encode(array('data' => $mostrar, 'status' => TRUE));
  }

  private function _validateCierre()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    if ($this->input->post('montototalcaja') == '') {
      $data['inputerror'][] = 'montototalcaja';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_SubmitRegister()
  {
    if ($this->input->post('tipoproceso') == 1) {
      $this->_validateCierre();
    }
    $queryVentas = $this->Controlador_model->geVentastCaja($this->caja, 'venta');
    $ventasContado = 0;
    $montoCreditos = 0;
    $totalVentasCredito = 0;
    $gasto = 0;
    $totalVentasContados = 0;
    $totalGastos = 0;
    $pagosEfectivo = $this->Controlador_model->getCajaPagos($this->caja, 'ingreso', "EFECTIVO"); //VENTA
    $pagosEfectivoGenerados = $this->Controlador_model->totalGenerados($this->caja, "EFECTIVO"); // VENTA GENERADOS
    $pagosTarjeta = $this->Controlador_model->getCajaPagos($this->caja, 'ingreso', "TARJETA"); //VENTA
    $pagosTarjetaGenerados = $this->Controlador_model->totalGenerados($this->caja, "TARJETA"); // VENTA GENERADOS
    $abonosCaja =  $this->Controlador_model->abonosCaja($this->caja);
    $totalAbono = 0;
    $totalAbonoGenerado = 0;
    foreach ($abonosCaja as $abono) {
      $totalAbono += $abono->monto;
      $totalAbonoGenerado += 1;
    }
    //? esta estado 3 de una venta es una venta anulada
    $devolucionesCaja = $this->db->where("caja", $this->caja)->where("estado", "3")->get("venta")->result();
    $devolucionesGeneradas = 0;
    $devoluciones = 0;
    foreach ($devolucionesCaja as $devolucion) {
      $devoluciones += $devolucion->deudatotal;
      $devolucionesGeneradas += 1;
    }
    foreach ($queryVentas as $venta) {
      if ($venta->formapago == 'CONTADO') {
        $ventasContado += $venta->deudatotal;
        $totalVentasContados += 1;
      } else {
        $montoCreditos += $venta->deudatotal;
        $totalVentasCredito += 1;
      }
    }
    $expences = $this->db->where("caja", $this->caja)->where("tipo", "CAJA")->where("tipopago", "EFECTIVO")->get("egreso")->result();
    foreach ($expences as $expence) {
      $gasto += $expence->montototal;
      $totalGastos += 1;
    }
    $dataCajaPincipal = $this->Controlador_model->get($this->cajaprincipal, "cajaprincipal");
    $dataEmpresa = $this->Controlador_model->get($dataCajaPincipal->tienda, "empresa");
    $data['usuario_cierre'] = $this->usuario;
    $data['contado'] = $ventasContado; //? Todas las ventas de tipo a contado
    $data['contadosgenerados'] = $totalVentasContados;
    $data['credito'] = $montoCreditos; //? Todas las ventas a tipo a credito
    $data['creditosgenerados'] = $totalVentasCredito;
    $data['efectivocontado'] = $pagosEfectivo->totalpagos ? $pagosEfectivo->totalpagos : 0; //? trae todos los pagos de la venta de tipo en efectivo
    $data['efectivogenerados'] = $pagosEfectivoGenerados;
    $data['tarjetacontado'] = $pagosTarjeta->totalpagos ? $pagosTarjeta->totalpagos : 0; //? trae todos los pagos de la venta de tipo en Tarjeta
    $data['tarjetagenerados'] = $pagosTarjetaGenerados;
    $data['gasto'] = $gasto; //? trae todos los gatos en efectivo de modalidad FLETE/COMPRA/OPERACION
    $data['gastosgenerados'] = $totalGastos;
    $data['abonos'] = $totalAbono; //? Trae todos los igresos a caja en efectivo de modalidad VENTA/ABONO/CUENTAPORCOBRAR
    $data['abonosgenerados'] = $totalAbonoGenerado;
    $data['devoluciones'] = $devoluciones; //? Trae todas las ventas que fueron anuladas
    $data['devolucionesgeneradas'] = $devolucionesGeneradas;
    $data['cierre'] = date("Y-m-d H:i:s");
    $data['estado'] = '1';
    $this->Controlador_model->update(array('id' => $this->caja), $data, 'caja');

    $datamonedero['empresa'] = $dataCajaPincipal->tienda;
    $datamonedero['usuario'] = $this->usuario;
    $datamonedero['caja'] = $this->caja;
    $datamonedero['status'] = $this->input->post('tipoproceso');
    if ($this->input->post('tipoproceso') == "0") {
      $diezcentimos = $this->input->post('diezcentimos');
      $veintecentimos = $this->input->post('veintecentimos');
      $cincuentacentimos = $this->input->post('cincuentacentimos');
      $unsol = $this->input->post('unsol');
      $dossoles = $this->input->post('dossoles');
      $cincosoles = $this->input->post('cincosoles');
      $diezsoles = $this->input->post('diezsoles');
      $veintesoles = $this->input->post('veintesoles');
      $cincuentasoles = $this->input->post('cincuentasoles');
      $ciensoles = $this->input->post('ciensoles');
      $doscientossoles = $this->input->post('doscientossoles');
      $datamonedero['diezcentimos'] = $diezcentimos;
      $datamonedero['veintecentimos'] = $veintecentimos;
      $datamonedero['cincuentacentimos'] = $cincuentacentimos;
      $datamonedero['unsol'] = $unsol;
      $datamonedero['dossoles'] = $dossoles;
      $datamonedero['cincosoles'] = $cincosoles;
      $datamonedero['diezsoles'] = $diezsoles;
      $datamonedero['veintesoles'] = $veintesoles;
      $datamonedero['cincuentasoles'] = $cincuentasoles;
      $datamonedero['ciensoles'] = $ciensoles;
      $datamonedero['doscientossoles'] = $doscientossoles;
      $total =  $diezcentimos + $veintecentimos + $cincuentacentimos + $unsol + $dossoles + $cincosoles + $diezsoles + $veintesoles + $cincuentasoles + $ciensoles + $doscientossoles;
      $datamonedero['montototal'] = $total;
    } else {
      $datamonedero['montototal'] = $this->input->post('montototalcaja');
    }
    $idCajaAntesDeVaciar = $this->caja;
    $dataPerfil = $this->Controlador_model->get($this->perfil, "perfil");
    $this->Controlador_model->save('monedero', $datamonedero);
    $this->procesoCajaStock($this->caja, NULL, FALSE); //? SE ENVIA FALSE PAARA QUE AGREGUE EL SOTCK FINAL
    $CI = &get_instance();
    $CI->session->set_userdata('caja', NULL);
    echo json_encode(array("status" => TRUE, "estado_reportecajacierre" => $dataPerfil->estado_reportecajacierre, "tipoimpresora" => $dataEmpresa->tipoimpresora, "idcaja" => $idCajaAntesDeVaciar, "datacaja" => $data));
    exit();
  }

  public function ajax_imprimircomprobante()
  {
    $venta = $this->Controlador_model->get($this->input->post("venta"), 'venta');
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
      $url = 'files/Setting/' . $empresa->ruc . '.png';
      $logo = EscposImage::load($url, false);
      // $printer->bitImage($logo);
    } catch (Exception $e) {
      /*No hacemos nada si hay error*/
    }
    /* Ahora vamos a imprimir un encabezado */
    if ($venta->tipoventa == 'OTROS') {
      $printer->text("\n" . "NOTA DE VENTA" . "\n");
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
    if ($venta->consumo == '1') {
      $total = $venta->montototal;
      $printer->text("Por consumo" . "\n");
      $printer->text("[1]                    " . $venta->montototal . "    " . number_format($venta->montototal, 2) . " \n");
    } else {
      $ventadetalle = $this->Controlador_model->pedidodetalle($this->venta);
      foreach ($ventadetalle as $value) {
        $total += $value->precio * $value->cantidad;
        $producto = $this->Controlador_model->get($value->producto, 'producto');
        $printer->text($producto->codigo . " " . $value->nombre . "\n");
        $printer->text("[" . $value->cantidad . "ssssssssss]                    " . $value->precio . "    " . number_format($value->precio * $value->cantidad, 2) . " \n");
      }
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
    $pagos = $this->Controlador_model->getDetalle($this->venta, 'ingreso');
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
    $printer->text("MESA: " . ($mesa) ? $mesa->nombre : "MESA TEMPORAL" . "\n");
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

  public function ajax_showcomprobante($ventaid)
  {
    $venta = $this->Controlador_model->get($ventaid, 'venta');
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
      'qrcode' => '../../' . $url_base . $numerocomprobante . '.png',
      'mesa' => $this->Controlador_model->get($venta->mesa, 'mesa'),
      'usuario' => $this->Controlador_model->get($venta->usuario_creador, 'usuario'),
      'ingresos' => $this->Controlador_model->getDetalle($ventaid, 'ingreso'),
      'ventadetalle' => $this->Controlador_model->comanda($ventaid)
    );
    $this->load->view('imprimircomprobante', $data);
  }


  public function ajax_imprimircierre($idcaja)
  {
    //? impresion directa
    $caja = $this->Controlador_model->get($idcaja, 'caja');
    $dataCajaPincipal = $this->Controlador_model->get($caja->cajaprincipal, 'cajaprincipal');
    $empresa = $this->Controlador_model->get($dataCajaPincipal->tienda, 'empresa');
    $usuario = $this->Controlador_model->get($caja->usuario, 'usuario');
    $monedero = $this->db->where('caja', $idcaja)->get('monedero')->row();
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
    $printer->text("TIPO DE MONEDA                 CANTIDAD DE MONEDA\n");
    $printer->text("------------------------------------------------" . "\n");
    /* Ahora vamos a imprimir los productos. Alinear a la izquierda para la cantidad y el nombre */
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("0.10                                   " . $monedero->diezcentimos . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("0.20                                   " . $monedero->veintecentimos . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("0.50                                   " . $monedero->cincuentacentimos . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("1.00                                   " . $monedero->unsol . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("2.00                                   " . $monedero->dossoles . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("5.00                                   " . $monedero->cincosoles . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("10.00                                  " . $monedero->diezsoles . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("20.00                                  " . $monedero->veintesoles . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("50.00                                  " . $monedero->cincuentasoles . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("100.00                                 " . $monedero->ciensoles . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("200.00                                 " . $monedero->doscientossoles . "\n");
    /* Terminamos de imprimir los productos, ahora va el total */
    $printer->text("------------------------------------------------" . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("TOTAL VENTA:                          " . number_format($montototal, 2) . "\n");
    /* Podemos poner también un pie de página */
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("------------------------------------------------" . "\n");
    $printer->text("PRODUCTOS VENDIDOS\n");
    $posales = $this->Controlador_model->resumenventa($idcaja);
    $totalventas = 0;
    foreach ($posales as $value) {
      $producto = $this->Controlador_model->get($value->producto, 'producto');
      $sumacantidad = $this->Controlador_model->ventaresumencantidad($caja->id, $value->producto);
      $sumasubtotal = $this->Controlador_model->ventaresumensubtotal($caja->id, $value->producto);
      $totalventas += $sumasubtotal->subtotal;
      $printer->setJustification(Printer::JUSTIFY_LEFT);
      $printer->text(ucwords(strtolower($producto->nombre)) . "\n");
      $printer->setJustification(Printer::JUSTIFY_RIGHT);
      $printer->text($sumacantidad->cantidad . " UND\n");
    }
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("------------------------------------------------" . "\n");
    /* Podemos poner también un pie de página */
    $printer->text("EL MUNDO ES DE QUIEN SE ATREVE\n");
    /* Alimentamos el papel 3 veces */
    $printer->feed(3);
    /* Cortamos el papel. Si nuestra impresora no tiene soporte para ello, no generará ningún error */
    $printer->cut();
    /* Por medio de la impresora mandamos un pulso. Esto es útil cuando la tenemos conectada por ejemplo a un cajón */
    $printer->pulse();
    /* Para imprimir realmente, tenemos que "cerrar" la conexión con la impresora. Recuerda incluir esto al final de todos los archivos */
    $printer->close();
  }

  public function ajax_showcierre($idcaja)
  {
    $monedero = $this->db->where('caja', $idcaja)->get('monedero')->row();
    $caja = $this->Controlador_model->get($idcaja, 'caja');
    $dataCajaPincipal = $this->Controlador_model->get($this->cajaprincipal, 'cajaprincipal');
    $data = array(
      'monedero' => $monedero,
      'montoCerrarCaja' => $monedero->montototal,
      'caja' => $caja,
      'usuario' => $this->Controlador_model->get($caja->usuario, 'usuario'),
      'empresa' => $this->Controlador_model->get($dataCajaPincipal->tienda, 'empresa'),
      'posales' => $this->Controlador_model->resumenventa($idcaja)
    );
    $this->load->view('imprimircierre', $data);
  }

  //! todo FALTA TERMINAR LA ALERTA
  public function alertaStock()
  {
    $html = "";
    $totalAlertas = 0;
    $tiendas = $this->Controlador_model->getAll("empresa");
    foreach ($tiendas as $key => $tienda) {
      $htmltienda = "<label class='label label-default tiendaAlert' style='font-size: 12px; " . ($key != 0 ? 'border-top-left-radius: 0px;border-top-right-radius: 0px;' : '') . " '>  $tienda->ruc $tienda->nombre</label>";
      $almacenes = $this->db->where("empresa", $tienda->id)->get("almacen")->result();
      $productosConAlerta = 0;
      $htmlAlamcenes = "";
      foreach ($almacenes as $almacen) {
        $HtmlProductosSinStock = "";
        $alertaEnAlmacenes = 0;
        $queryProductos = $this->db->where("estado", "0")->get("producto")->result();
        foreach ($queryProductos as $producto) {
          $StockAlmacen = $this->Controlador_model->getStockAlmacen($producto->id, $almacen->id, NULL, $tienda->id);
          $stockActual = $StockAlmacen ? $StockAlmacen->cantidad : 0;
          if ($stockActual <= $producto->alertqt) {
            $alertaEnAlmacenes += 1;
            $totalAlertas += 1;
            $productosConAlerta += 1;
            $HtmlProductosSinStock .= "
          <div style='margin-bottom:6px;'>
              <label class='label label-default' style='margin: 0px;display:block;border-bottom-left-radius: 0px;border-bottom-right-radius: 0px;'>
              $producto->nombre
              </label> 
              <div style='display:flex'>
              <label class='label label-warning' style='border-radius: 0px;background:#ffc107;color:#212529;display:block;flex:1;'>
              Stock Minimo: $producto->alertqt
              </label>
              <label class='label label-danger' style='border-radius: 0px;display:block;flex:1;'>
              Stock Actual $stockActual
              </label>
              </div> 
          </div>";
          }
        }
        if ($alertaEnAlmacenes > 0) {
          $htmlAlamcenes .= "
          <label class='alert alert-info' style='margin: 10px;display:block;text-align: center;padding: 3px;'>
          <div style='margin-bottom:5px'>$almacen->nombre</div> 
          " . "$HtmlProductosSinStock 
          </label>";
        }
      }
      if ($productosConAlerta > 0) {
        $html .= $htmltienda . $htmlAlamcenes;
      }
    }
    echo json_encode(array('data' => $html, 'numeroStock' => $totalAlertas));
  }

  public function alertaVence()
  {
    $html = "";
    $numeroStock = 0;
    $mescaducidad = $this->db->where("fechacaducidad <>", "0000-00-00")->select("DATEDIFF( fechacaducidad,CURRENT_DATE())  'dias', nombre, fechacaducidad")->get("producto")->result();
    $caducidad = $this->db->where("fechacaducidad <>", "0000-00-00")->where("fechacaducidad <", date("Y-m-d"))->get("producto")->result();
    $empresa = $this->Controlador_model->get($this->empresa, "empresa");
    foreach ($mescaducidad as $value) {
      $alerta = $empresa ? $empresa->fecha_aviso : 0;
      $faltante = $value->dias ? $value->dias : 0;
      if ($faltante <= $alerta && $faltante > 0) {
        $html .= "
      <li class='list-group-item '>
      <h4 class='list-group-item-heading'>" . $value->nombre . "</h4>" . "<p class='list-group-item-text'> <span class=\"label label-warning\" style=\"background:#ffc107; color:#212529\">CADUCA EN " . $faltante . ($faltante == 1 ? ' DIA' : ' DIAS') . "</span></p></li>";
        $numeroStock++;
      }
    }
    foreach ($caducidad as $value) {
      $hoy = new DateTime("NOW");
      $caducado = new DateTime($value->fechacaducidad);
      $direferencia = $hoy->diff($caducado);
      $html .= "
      <li class='list-group-item '>
      <h4 class='list-group-item-heading'>" . $value->nombre . "</h4>" . "<p class='list-group-item-text'> <span class='label label-danger'>CADUCADO HACE " . $direferencia->d . ($direferencia->d == 1 ? ' DIA' : ' DIAS') . "</span></p></li>";
      $numeroStock++;
    }

    echo json_encode(array('data' => $html, 'numeroStock' => $numeroStock));
  }

  public function ajax_agregarAdicionales($producto)
  {
    $dataProducto = $this->Controlador_model->get($producto, "producto");
    $dataCajaPincipal = $this->Controlador_model->get($this->cajaprincipal, "cajaprincipal");
    $columna = 0;
    //TODO: VARIANTES
    if ($dataProducto->variante == "1") {
      $columna += 1;
      $statusvariante = TRUE;
      $datavariantes = $this->Controlador_model->getVariante($producto);
      $htmlVariantes = "";
      $totalVariantes = 0;
      foreach ($datavariantes->result() as $variante) {
        $totalVariantes += 1;
        $htmlVariantes .= '
            <div style="display:inline-block">
              <label id="label-variante-' . $variante->id . '" for="input-variante-' . $variante->id . '" class="content-animalista" >
                <input class="variantes" type="hidden" id="variante-' . $variante->id . '" value="' . $variante->id . '">
                <input type="hidden" id="precioproducto-' . $variante->id . '"  value="' . $variante->precio . '">
                <input type="hidden" id="text-variante-' . $variante->id . '"  value="' . $variante->nombre . '">
                <input type="hidden" id="variante-cantidad-' . $variante->id . '"  value="' . $variante->cantidad . '">
                <div >
                <input onclick="seleccionarVariante(' . $variante->id . ')" class="radio" type="radio" id="input-variante-' . $variante->id . '" name="variante">
                </div>
                <div class="dataradio">
                  <div>' . $variante->nombre . '</div>
                  <div>' . $variante->precio . '</div>
                </div> 
              </label>
            </div> 
            ';
      }

      $mensajeVariantes = "<div class='alert alert-danger'><div>°‿‿°</div> <div>NO SE ENCONTRARON VARIANTES DEL PRODUCTO</div></div>";
      $totalVariantes == 0 ? $htmlVariantes .= $mensajeVariantes : "";
    } else {
      $htmlVariantes = "";
      $statusvariante = 0;
    }
    //TODO: EXTRAS 
    $statusExtras =  $this->Controlador_model->queryCategoria($dataProducto->categoria);
    if ($statusExtras) {
      $dataextras = $this->Controlador_model->getExtras($dataProducto->categoria);
      $htmlExtras = "";
      $statusextra = TRUE;
      $columna += 1;
      foreach ($dataextras->result() as $value) {
        $htmlExtras .= '
            <div style="display: inline-block">
            <div class="main-extra" id="extra-' . $value->id . '" style="position:relative">
            <input type="hidden" class="ExtraIdenti" value="' . $value->id . '">
            <input type="hidden" id="PrecioExtra-' . $value->id . '" value="' . $value->precio . '">
            <input type="hidden" id="NombreExtra-' . $value->id . '" value="' . $value->nombre . '">
                <span id="sinextra-' . $value->id . '" class="sin-extra" onclick="sinextraope(' . $value->id . ')"><i class="fa fa-minus-circle"></i></span>
                <div class="main-extra__uno">
                    <input type="checkbox" onclick="seleccionarExtraChecked(' . $value->id . ')" class="check_extra" id="checkep_extra-' . $value->id . '">
                </div>
                <label class="main-extra__two" onclick="seleccionarExtra(' . $value->id . ')">
                    <span id="extraname-' . $value->id . '">' . $value->nombre . '</span>
                    <span id="extraprecio-' . $value->id . '">S/. ' . $value->precio . '</span>
                </label>
            </div>
            </div>
              ';
      }
    } else {
      $htmlExtras = "";
      $statusextra = FALSE;
    }
    //TODO: LOTES
    $loteAlmacen = $this->db->where("producto", $dataProducto->id)->where("almacen", $dataCajaPincipal->almacen)->where("lote IS NOT NULL")->get("stock");
    if ($dataProducto->status_lote == '1' and $loteAlmacen->num_rows() > 1) {
      $statuslote = TRUE;
      $htmlLotes = "";
      $columna += 1;
      foreach ($loteAlmacen->result() as $value) {
        $lote = $this->Controlador_model->get($value->lote, "lote");
        $htmlLotes .= '
          <div style="display:inline-block">
            <label id="label-lote-' . $lote->id . '" for="input-lote-' . $lote->id . '" class="content-animalista" >
            <input class="lotes" type="hidden" id="lote-' . $lote->id . '" value="' . $lote->id . '">
              <div>
              <input onclick="seleccionarLote(' . $lote->id . ')" class="radio" type="radio" id="input-lote-' . $lote->id . '" name="lote">
              </div>
              <div class="dataradio">
                <div><label style="display:block">' . $lote->lote . '</label><label style="display:block">' . $lote->vencimiento . '</label></div>
              </div> 
            </label>
          </div> 
          ';
      }
    } else {
      if ($dataProducto->status_lote == '1') {
        $dataLote = $loteAlmacen->row();
        $statuslote = 1;
        $htmlLotes = $dataLote->lote;
      } else {
        $statuslote = 0;
        $htmlLotes = "";
      }
    }

    $htmlBoton = '
    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
    <button class="btn btn-primary" id="btnAgregarVenta"  onclick="procesoAgregarVenta({idproducto : ' . $producto . ', statusvariante : ' . $statusvariante . ', statuslote : ' . $statuslote . '})">Agregar</button>';
    $totalcolumnas = 12 / ($columna == 0 ? 1 : $columna);
    echo json_encode([
      "variantes" => ["status" => $statusvariante, "html" => $htmlVariantes],
      "extras" => ["status" => $statusextra, "html" => $htmlExtras],
      "lotes" => ["status" => $statuslote, "html" => $htmlLotes, "totalLotes" => $loteAlmacen->num_rows()],
      "boton" => $htmlBoton,
      "totalcolumnas" => $totalcolumnas
    ]);
  }

  private function showcomprobanteSaved($url, $phone, $idventa)
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
    // $this->dompdf->stream($filename, array("Attachment"=>1));
  }

  public function sentTicketWA($phone, $venta)
  {
    $url = "CPETemp";
    if (!file_exists($url)) {
      mkdir($url, 0777, true);
    }
    $this->showcomprobanteSaved($url, $phone, $venta);
  }

  private function crearPDF($idventa)
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
    $filename = $url . "/" . $venta->serie . "-" . $venta->numero . "-" . $cliente->documento . "-" . date('Y-m-d') . ".pdf";
    $html = $this->output->get_output();
    $this->dompdf->loadHtml($html);
    $this->dompdf->setPaper('A4', 'portrait');
    // $this->dompdf->setPaper(array(0, 0, 100, 100));
    $this->dompdf->render();
    $output = $this->dompdf->output();
    file_put_contents($filename, $output);
    // $this->dompdf->stream($filename, array("Attachment"=>1));
  }

  private function sendCPToNumber($uri, $phone)
  {
    $texto = "Hola, gracias por tu consumo. Puedes descargar tu Comprobante Electronico en el siguiente enlace! " . urlencode(base_url() . $uri);
    redirect("https://wa.me/51" . $phone . "?text=" . $texto);
  }

  public function deleteCPETemp()
  {
    $url = "CPETemp" . DIRECTORY_SEPARATOR;
    $this->rmdir_recursive($url);
  }

  public function rmdir_recursive($dir)
  {
    if (is_dir($dir)) {
      foreach (scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) continue;
        if (is_dir("$dir/$file")) $this->rmdir_recursive("$dir/$file");
        else unlink("$dir/$file");
      }
      rmdir($dir);
    }
  }

  public function ajax_newventa()
  {
    $dataCajaPrincipal = $this->Controlador_model->get($this->cajaprincipal, "cajaprincipal");
    $data['empresa'] = $dataCajaPrincipal->tienda;
    $data['cliente'] = 1;
    $data['usuario_creador'] = $this->usuario;
    $data['hora'] = date("H:i:s");
    $data['created'] = date("Y-m-d");
    $data['caja'] = $this->caja;
    $data['mesa'] = NULL;
    $data['tipoventa'] = $dataCajaPrincipal->tipoventa;
    $insert = $this->Controlador_model->save('venta', $data);
    $ventainsertada = $this->Controlador_model->get($insert, 'venta');
    $cliente = $this->Controlador_model->get($ventainsertada->cliente, 'cliente');
    $querynNum =  $this->Controlador_model->totalVentasNoProcesadas($this->usuario, $dataCajaPrincipal->tienda, $this->caja);
    if ($insert) {
      echo json_encode(
        [
          'idventa' => $insert,
          'totalventa' => $querynNum->num_rows(),
          'cliente' => $ventainsertada->cliente,
          'clientes' => $cliente->documento . " " . $cliente->nombre . " " . $cliente->apellido, // ,

        ]
      );
    }
  }

  function ajax_ventasReload()
  {
    $dataCajaPrincipal = $this->Controlador_model->get($this->cajaprincipal, "cajaprincipal");
    //? caca me quede
    $query =  $this->Controlador_model->totalVentasNoProcesadas($this->usuario, $dataCajaPrincipal->tienda, $this->caja);
    $html = "";
    $idselec = "";
    if ($query) {
      foreach ($query->result() as $key => $value) {
        if ($key == 0) {
          $idselec = $value->id;
        }
        $html .= ' <button style="border-top:0px; border-left:0px; border-right:0px" class="categories ' . ($key == 0 ? "selectedGat" : "") . ' " id="venta_' . $value->id . '" onclick="traerpedidosventa(' . $value->id . ')">
        <input value="' . $value->id . '" type="hidden">
        VENTA ' . ($key + 1) . '
        </button> ';
      }
    }
    echo json_encode(['datahtml' => $html, 'idselect' => $idselec]);
  }

  function ajax_ProcesoELiminarVenta()
  {
    $delete = $this->Controlador_model->delete_by_id($this->input->post("idventa"), "venta");
    if ($delete) {
      echo json_encode(["status" => TRUE]);
    }
  }

  function ajax_verif_stock()
  {
    $dataCajaPrincipal = $this->Controlador_model->get($this->cajaprincipal, "cajaprincipal");
    $queryventa = $this->Controlador_model->estadoVenta($this->input->post("venta"), $dataCajaPrincipal->tienda, "0");
    if ($queryventa) {
      $productos = json_decode($this->input->post("productos"), true);
      $dataenviar = [];
      foreach ($productos as $key => $value) {
        if ($value["tipoproducto"] == "NORMAL") {
          $EstadoProducto = FALSE;
          $idproducto = $value['id_producto'];
          $cantidadproducto = $value['statusvariante'] ?  ($value['cantidad_variante'] * $value['cantidad']) : $value['cantidad'];
          $cantidadTotalVerif = $cantidadproducto;
          $producto = $this->Controlador_model->get($idproducto, 'producto');
          if ($producto->tipo == '0') {
            //todo: El tipo de producto 0 es estandar
            $existenciaStock = $this->Controlador_model->existenciaStock($producto->id, $dataCajaPrincipal->almacen, $value['lote'], $dataCajaPrincipal->tienda); //todo: verificamos si el producto esta registra en stock
            if ($existenciaStock) {
              $EstadoProducto = $existenciaStock->cantidad >= $cantidadTotalVerif ? FALSE : TRUE;
            } else {
              $EstadoProducto = TRUE;
            }
          } else if ($producto->tipo == '2') {
            //todo: El tipo de producto 2 es combo
            $combo = $this->db->where('producto', $producto->id)->get('combo')->result(); //todo: verificamos si el tiene registro en la tabal combo
            if ($combo) {
              foreach ($combo as $key => $value2) {
                //Si no cuenta con sufuciente STOCK el como saldra como agotado
                $existenciaStockCombo = $this->db->where('almacen', $dataCajaPrincipal->almacen)->where('empresa', $dataCajaPrincipal->tienda)->where('producto',  $value2->item_id)->get('stock')->row(); //todo: verificamos si el producto esta registra en stock
                if ($existenciaStockCombo) {
                  $productoStock = $this->db->where('almacen', $dataCajaPrincipal->almacen)->where('producto', $value2->item_id)->where('cantidad <', $value2->cantidad * $cantidadTotalVerif)->get('stock')->row();
                  if ($productoStock) {
                    $EstadoProducto = TRUE; //cuando $EstadoProducto es TRUE ara que el div/boton este desahabilitado
                    break;
                  } else {
                    $EstadoProducto = FALSE;
                  }
                } else {
                  $EstadoProducto = TRUE;
                  break;
                }
              }
            } else {
              //todo: si no tine que productos en el combo se desahanilita el producto es tipo combo
              $EstadoProducto = TRUE;
            }
          } else {
            $EstadoProducto = FALSE;
          }
          $dataLote = $this->Controlador_model->get($value['lote'], "lote");
          if ($EstadoProducto == TRUE) {
            $totalstock = $this->totalStock($idproducto, 0, $value['lote']);
            $dataenviar[] = ["idproducto" => $idproducto, "totalstock" => $totalstock, "key_primary" => $value['key_primary'], "nombrelote" => ($dataLote ? $dataLote->lote : "")];
          }
        }
      }
      echo json_encode([
        "venta" => ["status" => TRUE],
        "tresPasos" => $dataCajaPrincipal->pasos,
        "dataenviar" => $dataenviar,
      ]);
    } else {
      $dataVenta = $this->db->where("id", $this->input->post("venta"))->where("estado", "1")->get("venta")->row();
      if ($dataVenta) {
        $dataUsuario = $this->Controlador_model->get($dataVenta->usuario_proceso, "usuario");
        $hf_procesado = new DateTime($dataVenta->hf_procesado);
        $time = $hf_procesado->format("g:i:s a");
        $dataJSON = [
          "venta" => [
            "status" => FALSE,
            "msg" => "La venta ya fue procesado por el usuario " . $dataUsuario->usuario . ". hora " . $time . "",
          ]
        ];
      } else {
        $dataJSON = [
          "venta" => ["status" => FALSE, "msg" => "La venta fue eliminada :( "]
        ];
      }
      echo json_encode($dataJSON);
    }
  }


  public function totalStock($idproducto, $cantidadvariante, $lote)
  {
    //? contamos para cuantas selecciones de ese producto le resta
    $queryproducto = $this->Controlador_model->get($idproducto, "producto");
    $dataCajaPrincipal = $this->Controlador_model->get($this->cajaprincipal, 'cajaprincipal');
    $cantidadRestante = [];
    if ($queryproducto->tipo == '0') {
      //todo: El tipo de producto 0 es estandar
      $stockproducto = $this->Controlador_model->stockproducto($queryproducto->id, $dataCajaPrincipal->tienda, $dataCajaPrincipal->almacen, $lote); //todo: verificamos si el producto esta registra en stock
      if ($stockproducto) {
        $cantidadRestante[] =  $cantidadvariante > 0 ? $stockproducto->cantidad / $cantidadvariante : $stockproducto->cantidad;
      } else {
        $cantidadRestante[] = 0;
      }
    } else if ($queryproducto->tipo == '2') {
      //todo: El tipo de producto 2 es combo
      $combo = $this->db->where('producto',  $queryproducto->id)->get('combo')->result(); //todo: verificamos si el tiene registro en la tabal combo
      if ($combo) {
        foreach ($combo as $key => $value2) {
          $existenciaStockCombo = $this->db->where('producto',  $value2->item_id)->where("almacen", $dataCajaPrincipal->almacen)->get('stock')->row(); //todo: verificamos si el producto esta registra en stock
          if ($existenciaStockCombo) {
            $verificarCantidad = $this->db->where('producto',  $value2->item_id)->where('cantidad >=', $value2->cantidad)->where('almacen', $dataCajaPrincipal->almacen)->get('stock')->row();
            if ($verificarCantidad) {
              $cantidadTotalStock =  $verificarCantidad->cantidad;
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
      $cantidadRestante[] = "Varios";
    }
    return min($cantidadRestante);
  }

  private function _validatprocesar()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    $venta = $this->Controlador_model->get($this->input->post("idventa"), 'venta');
    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');

    if ($this->input->post('pago') == "") {
      $data['inputerror'][] = 'pago';
      $data['error_string'][] = 'Este campor es obligatorio';
      $data['status'] = FALSE;
    } else {
      $deudatotalVenta = $this->input->post('deuda') - $this->input->post("descuento");
      if ($deudatotalVenta > $this->input->post('pago')) {
        $data['inputerror'][] = 'pago';
        $data['error_string'][] = 'No puedes cancelar la venta con el monto isertado';
        $data['status'] = FALSE;
      }
    }


    if ($this->input->post('fecha') == '') {
      $data['inputerror'][] = 'fecha';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($venta->tipoventa == 'FACTURA' && $cliente->tipodocumento == 'DNI') {
      $data['inputerror'][] = 'pago';
      $data['error_string'][] = 'Debe agregar cliente con RUC.';
      $data['status'] = FALSE;
    }

    if ($venta->tipoventa == 'BOLETA' && $cliente->tipodocumento == 'RUC') {
      $data['inputerror'][] = 'pago';
      $data['error_string'][] = 'Debe agregar cliente con DNI.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('formapago') == 'CONTADO') {
      if ($this->input->post('metodopago') == 'EFECTIVO') {
        if ($this->input->post('pago') == '') {
          $data['inputerror'][] = 'pago';
          $data['error_string'][] = 'Este campo es obligatorio.';
          $data['status'] = FALSE;
        }
      }
    }
    return $data;
  }


  function ajax_procesarVenta()
  {
    $dataPerfil = $this->Controlador_model->get($this->perfil, 'perfil');
    $dataCajaPrincipal = $this->Controlador_model->get($this->cajaprincipal, 'cajaprincipal');
    $idventa = $this->input->post("idventa");
    $dataventa = $this->db->where("id", $idventa)->where("estado", "0")->or_where("estado", '4')->get("venta")->row();
    if ($dataventa) {
      $respuestaValidate = $this->_validatprocesar();
      if ($respuestaValidate["status"] === FALSE) {
        echo json_encode(["proceso" => ["status" => TRUE, "validate" => FALSE, "contenido" => $respuestaValidate]]);
      } else {
        $dataproductos = json_decode($this->input->post("dataproductos"));
        if ($dataPerfil->cobradorcaja == '1' and $dataCajaPrincipal->pasos == '1') {
          $this->db->where("venta", $idventa)->delete("ventadetalle");
        }
        if ($this->input->post("formapago") == "CREDITOCLIENTE") {
          if ($this->input->post("creditosdisponibles") == "0") {
            //? Crear un nuevo credito
            $venta = $this->Controlador_model->get($idventa, "venta");
            $numero = $this->Controlador_model->ultimocredito($dataCajaPrincipal->tienda);
            $utimoCorrelativo = $numero ? $numero->numero + 1 : 1;
            $cadena = "";
            for ($i = 0; $i < 3 - strlen($utimoCorrelativo); $i++) {
              $cadena .= '0';
            }
            $dataCredito["tienda"] = $dataCajaPrincipal->tienda;
            $dataCredito["usuario_creador"] = $this->usuario;
            $dataCredito["cliente"] = $venta->cliente;
            $dataCredito["numero"] = $utimoCorrelativo;
            $dataCredito['codigo'] = 'C' . date('Y') . $cadena . $utimoCorrelativo;
            $dataCredito['inicio'] = date("Y-m-d");
            $dataCredito['created_time'] = date("Y-m-d H:i:s");
            $idcredito = $this->Controlador_model->save("credito", $dataCredito);
          } else {
            $idcredito = $this->input->post("creditosdisponibles");
          }
          $dataVentaDetalle["credito"] = $idcredito;
          $dataVentaDetalle["tienda"] = $dataCajaPrincipal->tienda;
          $dataVentaDetalle["venta"] = NULL;
          $dataVentaDetalleLibre["credito"] = $idcredito;
          $dataVentaDetalleLibre["venta"] = NULL;
        } else {
          $dataVentaDetalle["credito"] = NULL;
          $dataVentaDetalle["tienda"] = NULL;
          $dataVentaDetalle["venta"] = $idventa;
          $dataVentaDetalleLibre["credito"] = NULL;
          $dataVentaDetalleLibre["venta"] = $idventa;
        }
        $CantidadItem = 0;
        $DeudaTotal = 0;
        foreach ($dataproductos as $value) {
          $cantidadEscogido = $value->cantidad;
          $CantidadItem += $cantidadEscogido;
          $DeudaTotal += $value->total_pagar;
          if ($value->tipoproducto == "NORMAL") {
            $producto = $value->id_producto;
            $productodata = $this->Controlador_model->get($producto, 'producto');
            if ($value->statusvariante) {
              $dataVariante = $this->Controlador_model->get($value->id_variante, 'productovariante');
              $preciocomprainsert = $dataVariante ? $dataVariante->preciocompra : 0.00;;
            } else {
              $preciocomprainsert = $productodata->preciocompra;
            }
            $cantidadDescontar = $value->statusvariante ? $value->cantidad_variante_total :  $value->cantidad;
            $dataVentaDetalle["usuario"] = $this->usuario;
            $dataVentaDetalle["producto"] = $producto;
            $dataVentaDetalle["variante"] = ($value->statusvariante ? $value->id_variante : NULL);
            $dataVentaDetalle["lote"] = ($value->statuslote ? $value->lote : NULL);
            $dataVentaDetalle["nombre"] =  $value->text_proudcto;
            $dataVentaDetalle["precio"] = $value->precio_producto;
            $dataVentaDetalle["preciocompra"] = $preciocomprainsert;
            $dataVentaDetalle["cantidad"] = $value->cantidad;
            $dataVentaDetalle["tipoprecio"] = $value->tipoprecio;
            $dataVentaDetalle["subtotal"] = $value->total_pagar;
            $dataVentaDetalle["cantidadvariante"] = ($value->statusvariante ? $value->cantidad_variante : NULL);
            $dataVentaDetalle["time"] = date("H:i:s");
            $dataVentaDetalle["created"] = date("Y-m-d");
            $dataVentaDetalle["estado"] = "1";
            $this->Controlador_model->save('ventadetalle', $dataVentaDetalle);
            //? Todo este proceso sale con normalidad 
            //? registro de movimientos del producto
            if ($productodata->tipo == '0') {
              $cantidad = $this->Controlador_model->getStockAlmacen($producto, $dataCajaPrincipal->almacen, $value->lote, $dataCajaPrincipal->tienda);
              $movimiento['modalidad'] = "SALIDA";
              $movimiento['empresa'] = $dataCajaPrincipal->tienda;
              $movimiento['usuario'] = $this->usuario;
              if ($this->input->post("formapago") == "CREDITOCLIENTE") {
                $movimiento['credito'] = $idcredito;
                $movimiento['tipooperacion'] = "VENTA DE CREDITO AL CLIENTE";
              } else if ($this->input->post("formapago") == "CREDITO") {
                $movimiento['venta'] = $idventa;
                $movimiento['tipooperacion'] = "VENTA DE CREDITO";
              } else {
                $movimiento['venta'] = $idventa;
                $movimiento['tipooperacion'] = "VENTA";
              }
              $movimiento['producto'] = $producto;
              $movimiento['almacen'] = $dataCajaPrincipal->almacen;
              $movimiento['lote'] =  ($value->statuslote ? $value->lote : NULL);
              if ($value->statusvariante) {
                $dataVariante = $this->Controlador_model->get($value->id_variante, "productovariante");
                $totalDescontar = $dataVariante->cantidad * $value->cantidad;
                $movimiento['medida'] =  $dataVariante->nombre;
                $movimiento['medidacantidad'] = $dataVariante->cantidad;
                $movimiento['cantidaditem'] = $dataVariante->cantidad * $value->cantidad;
                $movimiento['totalitemoperacion'] = $dataVariante->cantidad * $value->cantidad;
              } else {
                $totalDescontar = $value->cantidad;
                $movimiento['medida'] =  "UNIDAD";
                $movimiento['medidacantidad'] = 1;
                $movimiento['cantidaditem'] = $value->cantidad;
                $movimiento['totalitemoperacion'] = $value->cantidad;
              }
              $movimiento['cantidad'] = $value->cantidad; //? LO QUE REGISTRA
              $movimiento['stockanterior'] = $cantidad ? $cantidad->cantidad : 0;
              if ($this->input->post("formapago") == "CREDITOCLIENTE") {
                $movimiento['tipo'] = 'SALIDA POR VENTA CON CREDITO AL CLIENTE';
              } else if ($this->input->post("formapago") == "CREDITO") {
                $movimiento['tipo'] = 'SALIDA POR VENTA CON CREDITO';
              } else {
                $movimiento['tipo'] = 'SALIDA POR VENTA';
              }
              $movimiento['stockactual'] = ($cantidad ? $cantidad->cantidad : 0) - $totalDescontar;
              $movimiento['created'] = date('Y-m-d');
              $movimiento['hora'] = date("H:i:s");
              $this->Controlador_model->save('movimiento', $movimiento);
            } else if ($productodata->tipo == '2') {
              $combos = $this->db->where('producto',  $producto)->get('combo')->result();
              foreach ($combos as $combo) {
                $stock = $this->Controlador_model->getStockProceso($combo->item_id, $dataCajaPrincipal->almacen, NULL, $dataCajaPrincipal->tienda);
                $movimientoCombo['modalidad'] = "SALIDA";
                $movimientoCombo['empresa'] = $dataCajaPrincipal->tienda;
                $movimientoCombo['usuario'] = $this->usuario;
                if ($this->input->post("formapago") == "CREDITOCLIENTE") {
                  $movimientoCombo['credito'] = $idcredito;
                  $movimientoCombo['tipooperacion'] = "VENTA DE CREDITO AL CLIENTE";
                } else if ($this->input->post("formapago") == "CREDITO") {
                  $movimientoCombo['venta'] = $idventa;
                  $movimientoCombo['tipooperacion'] = "VENTA DE CREDITO";
                } else {
                  $movimientoCombo['venta'] = $idventa;
                  $movimientoCombo['tipooperacion'] = "VENTA";
                }
                $movimientoCombo['producto'] = $combo->item_id;
                $movimientoCombo['productocombo'] = $producto;
                $movimientoCombo['almacen'] = $dataCajaPrincipal->almacen;
                $movimientoCombo['lote'] =  ($value->statuslote ? $value->lote : NULL);
                $movimientoCombo['medida'] =  "COMBO";
                $movimientoCombo['medidacantidad'] = $combo->cantidad;
                $movimientoCombo['cantidad'] = $value->cantidad; //? LO QUE REGISTRA
                $movimientoCombo['cantidaditem'] = $combo->cantidad * $value->cantidad;
                $movimientoCombo['totalitemoperacion'] = $combo->cantidad * $value->cantidad;
                $movimientoCombo['stockanterior'] = $stock ? $stock->cantidad : 0;
                if ($this->input->post("formapago") == "CREDITOCLIENTE") {
                  $movimientoCombo['tipo'] = 'SALIDA POR VENTA DE COMBO CON CREDITO AL CLIENTE"';
                } else if ($this->input->post("formapago") == "CREDITO") {
                  $movimientoCombo['tipo'] = 'SALIDA POR VENTA DE COMBO CON CREDITO';
                } else {
                  $movimientoCombo['tipo'] = 'SALIDA POR VENTA DE COMBO';
                }
                $movimientoCombo['stockactual'] = ($stock ? $stock->cantidad : 0) - ($combo->cantidad * $value->cantidad);
                $movimientoCombo['created'] = date('Y-m-d');
                $movimientoCombo['hora'] = date("H:i:s");
                $this->Controlador_model->save('movimiento', $movimientoCombo);
              }
            }
            $this->ajax_descontar_stock($producto, $cantidadDescontar, $value->lote, $dataCajaPrincipal->almacen, $dataCajaPrincipal->tienda);
          } else {
            $dataVentaDetalleLibre["usuario"] = $this->usuario;
            $dataVentaDetalleLibre["producto"] = NULL;
            $dataVentaDetalleLibre["variante"] = NULL;
            $dataVentaDetalleLibre["tipo"] = "2";
            $dataVentaDetalleLibre["lote"] = NULL;
            $dataVentaDetalleLibre["nombre"] = $value->text_proudcto;
            $dataVentaDetalleLibre["precio"] = $value->precio_producto;
            $dataVentaDetalleLibre["cantidad"] = $value->cantidad;
            $dataVentaDetalleLibre["tipoprecio"] = $value->tipoprecio;
            $dataVentaDetalleLibre["subtotal"] = $value->total_pagar;
            $dataVentaDetalleLibre["cantidadvariante"] = NULL;
            $dataVentaDetalleLibre["created"] = date("Y-m-d");
            $dataVentaDetalleLibre["time"] = date("H:i:s");
            $dataVentaDetalleLibre["estado"] = "1";
            $this->Controlador_model->save('ventadetalle', $dataVentaDetalleLibre);
          }
        }

        //? pagar venta
        $venta = $this->Controlador_model->get($idventa, 'venta');
        $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
        $dataventaUpdate['formapago'] = $this->input->post('formapago');
        $deudaTotal = $this->input->post("deuda") - $this->input->post("descuento");
        $dataventaUpdate['caja'] = $this->caja;
        $dataventaUpdate['usuario_proceso'] = $this->usuario;
        $dataventaUpdate['atender'] = "1";
        $dataventaUpdate['sound'] = "1";
        $dataventaUpdate['hf_procesado'] = date("Y-m-d H:i:s");
        $dataventaUpdate['vence'] = $this->input->post('formapago') == "CREDITO" ? $this->input->post('vence') : NULL;
        $dataventaUpdate['created'] = $this->input->post('fecha');
        $dataventaUpdate['totalitems'] = $CantidadItem;
        $dataventaUpdate['estado'] = '1';
        //Datos deuda de la venta
        $dataventaUpdate['montototal'] = $this->input->post("deuda");
        $dataventaUpdate['descuento'] = $this->input->post("descuento");
        $dataventaUpdate['deudatotal'] = $deudaTotal;
        $serie = substr($venta->tipoventa, 0, 1) . substr($empresa->serie, 1, 3);
        $numero = $this->Controlador_model->codigos($venta->tipoventa, $serie);
        $numeros = $numero ? $numero->consecutivo + 1 : 1;
        $cadena = "";
        for ($i = 0; $i < 6 - strlen($numeros); $i++) {
          $cadena = $cadena . '0';
        }
        $dataventaUpdate['serie'] = $serie;
        $dataventaUpdate['numero'] = $cadena . $numeros;
        $dataventaUpdate['consecutivo'] = $numeros;
        if ($this->input->post('formapago') == 'CONTADO') {
          $dataventaUpdate['montoactual'] = 0;
          $dataventaUpdate['pago'] = $this->input->post('pago');
          $dataventaUpdate['vuelto'] = $this->input->post('pago') - $deudaTotal;
          //? REGISTRO EN LA TABLA INGRESO
          $dataingreso['tipo'] = 'CAJA';
          $dataingreso['modalidad'] = 'VENTA';
          $dataingreso['monto'] = $deudaTotal;
          $dataingreso['empresa'] = $venta->empresa;
          $dataingreso['usuario'] = $this->usuario;
          //? CONCEPTO 3 ES INGRESO EN EFECTIVO
          //? CONCEPTO 23 ES INGRESO TRANSF. DINERO AREAS
          $dataingreso['concepto'] = $this->input->post('metodopago') == "EFECTIVO" ? 3 : 28;
          $dataingreso['caja'] = $this->caja;
          $dataingreso['venta'] = $idventa;
          $dataingreso['metodopago'] = $this->input->post('metodopago');
          $dataingreso['tipotarjeta'] = $this->input->post('metodopago') == "TARJETA" ? $this->input->post('tipotarjeta') : NULL;
          $dataingreso['created'] = date('Y-m-d');
          $dataingreso['hora'] = date('H:i:s');
          $this->Controlador_model->save('ingreso', $dataingreso);
        } else if ($this->input->post('formapago') == 'CREDITO') {
          /*  $comprobante = $this->db->where("tipo", "CREDITOS")->where("empresa", $this->empresa)->get("comprobante")->row();
          if (isset($comprobante)) {
            $dataventaUpdate["serie"]           = $comprobante->serie;
            $dataventaUpdate["numero"]          = $this->Controlador_model->addLeadingZeros(($comprobante->correlativo + 1));
            $dataventaUpdate["consecutivo"]     = (int)$comprobante->correlativo + 1;
            $z["correlativo"]                   = $dataventaUpdate["consecutivo"];
            $this->Controlador_model->update(array('id' => $comprobante->id), $z, 'comprobante');
          } */
          $dataventaUpdate['montoactual'] = $deudaTotal;
        } else {
          $dataCredito = $this->Controlador_model->get($idcredito, "credito");
          $dataUpdateCredito['montototal'] = $dataCredito->montototal + $DeudaTotal;
          $dataUpdateCredito['montoactual'] = $dataCredito->montoactual + $DeudaTotal;
          $dataUpdateCredito['totalitems'] = $dataCredito->totalitems + $CantidadItem;
          $dataUpdateCredito['totalpedido'] = $dataCredito->totalpedido + 1;
          $dataUpdateVenta["cliente"] = 1;
          $this->Controlador_model->update(["id" => $idventa], $dataUpdateVenta, "venta");
          $this->Controlador_model->update(["id" => $idcredito], $dataUpdateCredito, "credito");
        }
        if ($this->input->post('formapago') <> 'CREDITOCLIENTE') {
          $this->Controlador_model->update(array('id' => $idventa), $dataventaUpdate, 'venta');
          $htmlComprobante = $this->input->post('formapago') == 'CONTADO' ? $this->printfcomprobante($idventa, $this->input->post('metodopago')) : "";
          $this->NewVenta();
        } else {
          $htmlComprobante = "";
        }
        echo json_encode(["proceso" => ["status" => TRUE, "validate" => TRUE, "htmlcomprobante" => $htmlComprobante]]);
      }
    } else {
      $dataVenta = $this->db->where("id", $idventa)->where("estado", "1")->get("venta")->row();
      if ($dataVenta) {
        $dataUsuario = $this->Controlador_model->get($dataVenta->usuario_proceso, "usuario");
        $hf_procesado = new DateTime($dataVenta->hf_procesado);
        $time = $hf_procesado->format("g:i:s a");
        $dataJSON = [
          "proceso" => ["status" => FALSE, "msg" => "La venta ya fue procesado por el usuario " . $dataUsuario->usuario . ". hora " . $time . ""]
        ];
      } else {
        $dataJSON = [
          "proceso" => ["status" => FALSE, "msg" => "La venta fue eliminada :( "]
        ];
      }
      echo json_encode($dataJSON);
    }
  }

  public function ajax_descontar_stock($idproducto, $cantidad, $lote, $almacenDescontar, $tienda)
  {
    //todo: hacemos el descuento del stock
    $producto = $this->Controlador_model->get($idproducto, 'producto');
    if ($producto->tipo == 0) {
      //VALIDAR SI EL DESCUENTO ES POR LOTE
      $stock = $this->Controlador_model->getStockProceso($producto->id, $almacenDescontar, $lote, $tienda);
      $updateStock = [
        'cantidad' => ($stock->cantidad - $cantidad)
      ];
      $this->Controlador_model->update(['id' => $stock->id], $updateStock, 'stock');
    } else if ($producto->tipo == 2) {
      $combo = $this->Controlador_model->ProductoCombo($producto->id);
      foreach ($combo as $value) {
        $stock = $this->Controlador_model->getStockProceso($value->item_id, $almacenDescontar, $lote, $tienda);
        $updateStock = [
          'cantidad' => $stock->cantidad - ($value->cantidad * $cantidad)
        ];
        $this->Controlador_model->update(['id' => $stock->id], $updateStock, 'stock');
      }
    } else {
      // el producto no maneja el stock
    }
  }


  public function ajax_stockactual()
  {
    $idproducto = $this->input->post("idproducto");
    $queryProducto = $this->Controlador_model->get($idproducto, "producto");
    $dataCajaPincipal = $this->Controlador_model->get($this->cajaprincipal, "cajaprincipal");
    if ($queryProducto->tipo == '0') {
      $almacenes = $this->db->where("empresa", $dataCajaPincipal->tienda)->get('almacen')->result();
      $html = '
    <div class="row">
     <div class="col-lg-12">
     <table class="table table-bordered">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Almacen</th>
            <th>Cantidad</th>
          </tr>
        </thead>
     ';
      foreach ($almacenes as $value) {
        $dataProducto = $this->Controlador_model->get($idproducto, "producto");
        $dataStock = $this->Controlador_model->stockAlmacen($idproducto, $value->id, $dataCajaPincipal->tienda);
        $cantidadStock =  $dataStock->cantidad == "" ? 0 : $dataStock->cantidad;
        $html .= '
          <tbody>
            <tr class="' . ($cantidadStock == 0 ? "danger" : "success") . '">
              <td>' . $dataProducto->nombre . '</td>
              <td>' . $value->nombre . '</td>
              <td style="text-align:right" >' . $cantidadStock . '</td>
            </tr>
          </tbody>';
      }
      $html .= '
      </table>
     </div>
    </div>';
    } else {
      $stockCombo = $this->totalStock($idproducto, 0, FALSE);
      $html = "<div class='alert alert-success text-center' style='font-weight: bold;color: #8e27c1;background: #906fc5a1;border-color: #905ec5;'><h4>TOTAL DE STOCK $stockCombo </h4></div>";
    }
    echo json_encode(['datahtml' => $html]);
  }


  function ajax_alertComprobantes()
  {
    $queryCPBoletas = $this->Controlador_model->queryCPBoletas();
    $queryCPFacturas = $this->Controlador_model->queryCPFacturas();
    $html = '<div class="col-lg-12">';
    $totalBoletas = 0;
    if ($queryCPBoletas->num_rows() > 0) {
      $html .= '
      <div style="display:flex; justify-content:space-between; align-items:center">
      <h5>BOLETAS</h5>
      <h5 class="badgetotalesCP" id="subtotalCPBoletas"></h5>
      </div>
      <ul class="list-group">
      ';
      foreach ($queryCPBoletas->result() as $value) {
        $totalBoletas += $value->totalfecha;
        $html .= '
        <li class="list-group-item" style="border:none; padding-top:5px; padding-bottom:5px; padding-left:25px; padding-right:0px">
          ' . $value->created . '
          <span class="badge badge-danger" style="background:#777">' . $value->totalfecha . '</span>
        </li>';
      }
      $html .= '</ul>';
    }
    $totalFacturas = 0;
    if ($queryCPFacturas->num_rows() > 0) {
      $html .= '
      <div style="display:flex; justify-content:space-between; align-items:center">
      <h5>FACTURAS</h5>
      <h5 class="badgetotalesCP"  id="subtotalCPFacturas"></h5>
      </div>
      <ul class="list-group">
      ';
      foreach ($queryCPFacturas->result() as $valueF) {
        $totalFacturas += $valueF->cantidadFactura;
        $html .= '
        <li class="list-group-item" style="border:none; padding-top:5px; padding-bottom:5px; padding-left:25px; padding-right:0px">
          ' . $valueF->created . '
          <span class="badge badge-danger" style="background:#777">' . $valueF->cantidadFactura . '</span>
        </li>';
      }
      $html .= '</ul>';
    }
    $html .= '</div>';
    $totalCP = $totalBoletas + $totalFacturas;
    echo json_encode(["dataCP" => $html, "subtotalBoletas" => $totalBoletas, "subtotalFacturas" => $totalFacturas, "totalCP" => $totalCP]);
  }

  function ajax_img($id)
  {
    $datas = $this->Controlador_model->get($id, "producto");
    //$foto= $this->Controlador_model->get($datas->categoria, 'productocategoria');
    $data = array(
      'photo' => $datas->photo
    );
    echo json_encode($data);
  }

  function ajax_tabla_pedidosEnviados()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $dataCajaPincipal = $this->Controlador_model->get($this->cajaprincipal, "cajaprincipal");
    $query = $this->db->order_by('id', 'ASC')->where('empresa', $dataCajaPincipal->tienda)->where('estado', "4")->get('venta')->result();
    $data = [];
    foreach ($query as $key => $value) {
      $vendedor = $this->Controlador_model->get($value->usuario_creador, "usuario");
      $totalDeuda = $this->db->select_sum("subtotal")->where("venta", $value->id)->get("ventadetalle")->row();
      $cliente = $this->Controlador_model->get($value->cliente, "cliente");
      $boton = '';
      $boton .= "<a class='btn btn-success' id='cobrar-$value->id' onclick='cobrar($value->id)'><i class='fa fa-money'></i></a></td>";
      $data[] = array(
        $key + 1,
        ($cliente ? $cliente->documento . " | " . $cliente->nombre . " " . $cliente->apellido : "SIN DATOS"),
        $value->referencia,
        $vendedor->nombre,
        $totalDeuda->subtotal,
        $value->created . " / " . date("g:i a", strtotime($value->hora)),
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

  function ajax_EnviarPedidoCaja()
  {
    $idventa = $this->input->post("idventa");
    $dataproductos = json_decode($this->input->post("pedidosVentaDetalle"));
    foreach ($dataproductos as $value) {
      //? el producto es libre
      $dataVentaDetalle["venta"] = $idventa;
      $dataVentaDetalle["nombre"] = $value->text_proudcto;
      $dataVentaDetalle["precio"] =  $value->precio_producto;
      $dataVentaDetalle["cantidad"] =  $value->cantidad;
      $dataVentaDetalle["subtotal"] = $value->total_pagar;
      $dataVentaDetalle["time"] = date("H:i:s");
      $dataVentaDetalle["estado"] = "1";
      if ($value->tipoproducto == "NORMAL") {
        $producto = $value->id_producto;
        $cantidad_V = $value->cantidad_variante;
        $productodata = $this->Controlador_model->get($producto, 'producto');
        $dataVentaDetalle["producto"] = $producto;
        $dataVentaDetalle["variante"] = ($value->statusvariante ? $value->id_variante : NULL);
        $dataVentaDetalle["lote"] = ($value->statuslote ? $value->lote : NULL);
        $dataVentaDetalle["preciocompra"] = $productodata->preciocompra;
        $dataVentaDetalle["cantidadvariante"] =  ($value->statusvariante ? $cantidad_V : NULL);
        $dataVentaDetalle["tipo"] = "0"; //? el producto es normal
      } else {
        $dataVentaDetalle["tipo"] = "2"; //? el producto es libre
      }
      $this->Controlador_model->save('ventadetalle', $dataVentaDetalle);
    }
    $dataUpdateVenta = ["estado" => "4", "referencia" => $this->input->post("referencia")];
    $this->Controlador_model->update(["id" => $idventa], $dataUpdateVenta, "venta");
    echo json_encode(["proceso" => ["status" => TRUE]]);
  }

  function ajax_cobrar($idventa)
  {
    $dataVentaDetalle = $this->db->where("venta", $idventa)->get("ventadetalle")->result();
    $dataVenta = $this->Controlador_model->get($idventa, "venta");
    $cliente = $this->Controlador_model->get($dataVenta->cliente, "cliente");
    $dataVenta->textCliente = $cliente->documento . " | " . $cliente->nombre . " " . $cliente->apellido;
    echo json_encode(["dataVentaDetalle" => $dataVentaDetalle, "dataVenta" => $dataVenta]);
  }

  function ajax_tableDataProductos()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where("estado", "0")->get("producto")->result();
    $dataCajaPincipal = $this->Controlador_model->get($this->cajaprincipal, "cajaprincipal");
    $data = [];
    foreach ($query as $key => $value) {
      $datacategoria = $this->Controlador_model->get($value->categoria, 'productocategoria');
      if ($datacategoria->estado == "0") {
        $dataAdicionales = "<input type=hidden id='tipomedida-$value->id' value=UND/>
        <input type=hidden id='cantidad-tm$value->id' value='1'/>
        <input type=hidden id='nombre-producto-$value->id' value='$value->nombre'/>
        <input type=hidden id='precio-producto-$value->id' value='$value->precioventa'/>";
        $botonprecios = "";
        $boton = '';
        $preciosActivados = 0;
        $dataextrasProducto = "";
        $dataextrasProducto .= $datacategoria->estadoextras == "1" ?  " <label class='label label-success' style='margin:0px 1px'>EXTRAS</label> " : "";
        $dataextrasProducto .= $value->status_lote == "1" ? " <label class='label label-default' style='margin:0px 1px'>LOTES</label> " : "";
        $dataextrasProducto .= $value->variante == "1" ? " <label class='label label-info' style='margin:0px 1px'>VARIANTES</label> " : "";
        if ($value->tipo == '0') {
          $textTipo = "<label class='label label-default' style='display:flex; justify-content:center; align-items:center'>NORMAL $dataextrasProducto</label>";
        } else if ($value->tipo == '1') {
          $textTipo = "<label class='label label-warning' style='display:flex; justify-content:center; align-items:center; background:#ffc107; color:#212529'>SERVICIO $dataextrasProducto</label> ";
        } else if ($value->tipo == '2') {
          $textTipo = "<label class='label label-purple' style='display:flex; justify-content:center; align-items:center'>COMBO $dataextrasProducto</label>";
        }

        $queryLotes = $this->Controlador_model->queryLotes($dataCajaPincipal->tienda, $value->id, $dataCajaPincipal->almacen);
        if ($value->variante == "1" or $datacategoria->estadoextras == "1" or ($value->status_lote == "1" and $queryLotes->num_rows() > 1)) {
          $preciosActivados += 1;
          $eventovariantes = ' onclick="agregarAdicionales(' . $value->id . ')" ';
          $botonprecios .= " <button id='boton-producto-$value->id' style='display:block; margin:3px; width:100%;padding:1px'  class='btn btn-sm btn-success'  data-toggle='tooltip' title='AGREGAR' $eventovariantes  >
          <i class='fa fa-cart-plus' style='font-size: 16px;'></i>
          </button>";
        } else {
          if ($value->status_lote == "1" and $queryLotes->num_rows() == 1) {
            $dataLote = $queryLotes->row();
            $lote = $dataLote ? $dataLote->lote : null;
            $evento = 'onclick="agregaarventa(' . $value->id . ', ' . $value->precioventa . ',{statusvariante : false, lote : ' . $lote . ' ,statuslote : ' . ($lote ? true : false) . ', tipoproducto: \'NORMAL\', cantidad:1, cantidadvariante: null, codigoBarra : false,} ,\'\')"';
            $botonprecios .= "
            <button style='display:block; margin:3px; width:100%;padding:1px'  class='btn btn-sm btn-success'  data-toggle='tooltip' title='AGREGAR' $evento  >
            <i class='fa fa-cart-plus' style='font-size: 16px;'></i>
            </button>";
          } else {
            $evento_precioventa = 'onclick="agregaarventa(' . $value->id . ', ' . $value->precioventa . ',{statusvariante : false, lote : null, statuslote: false, tipoproducto: \'NORMAL\', cantidad:1, tipoprecio: \'NORMAL\', cantidadvariante: null, codigoBarra : false,} ,\'\')"';
            $evento_mayorista = 'onclick="agregaarventa(' . $value->id . ', ' . $value->preciomayorista . ',{statusvariante : false, lote : null, statuslote: false, tipoproducto: \'NORMAL\', cantidad:1, tipoprecio: \'MAYORISTA\', cantidadvariante: null, codigoBarra : false,} ,\'\')"';
            $evento_distribuidor = 'onclick="agregaarventa(' . $value->id . ', ' . $value->preciodistribuidor . ',{statusvariante : false, lote : null, statuslote: false, tipoproducto: \'NORMAL\', cantidad:1, tipoprecio:\'DISTRIBUIDOR\', cantidadvariante: null, codigoBarra : false,} ,\'\')"';
            $evento_especial = 'onclick="agregaarventa(' . $value->id . ', ' . $value->precioespecial . ',{statusvariante : false, lote : null, statuslote: false, tipoproducto: \'NORMAL\', cantidad:1, tipoprecio: \'ESPECIAL\', cantidadvariante: null, codigoBarra : false,} ,\'\')"';
            if ($value->estado_precioventa == "1") {
              $preciosActivados += 1;
              $botonprecios .= "
              <button style='display:block; margin:3px; width:100%;padding:1px'  class='btn btn-sm btn-success'  data-toggle='tooltip' title='AGREGAR' $evento_precioventa  ><i class='fa fa-cart-plus' style='font-size: 16px;'></i> 
              PRECIO VENTA S/. $value->precioventa 
              </button>";
            }
            if ($value->estado_preciomayorista == "1") {
              $preciosActivados += 1;
              $botonprecios .= "
              <button style='display:block; margin:3px; width:100%; padding:1px'  class='btn btn-sm btn-success'  data-toggle='tooltip' title='AGREGAR' $evento_distribuidor  ><i class='fa fa-cart-plus' style='font-size: 16px;'></i> 
              PRECIO DISTRIBUIDOR S/. $value->preciomayorista
              </button>";
            }
            if ($value->estado_preciomayorista == "1") {
              $preciosActivados += 1;
              $botonprecios .= "
              <button style='display:block; margin:3px; width:100%; padding:1px'  class='btn btn-sm btn-success'  data-toggle='tooltip' title='AGREGAR' $evento_mayorista  >
              PRECIO MAYORISTA S/. $value->preciomayorista <i class='fa fa-cart-plus' style='font-size: 16px;'></i>
              </button>";
            }
            if ($value->estado_precioespecial == "1") {
              $preciosActivados += 1;
              $botonprecios .= "
              <button style='display:block; margin:3px; width:100%; padding:1px'  class='btn btn-sm btn-success'  data-toggle='tooltip' title='AGREGAR' $evento_especial  >
              PRECIO ESPECIAL S/. $value->precioespecial <i class='fa fa-cart-plus' style='font-size: 16px;'></i>
              </button>";
            }
          }
        }
        $nombreProducto = $value->nombre;
        //$botonImagen = '<button style="display:block; width:50%; padding:0px" class="btn  btn-default btn-sm" name="photo" id="verFoto-' . $value->id . '" onclick="verimg(' . $value->id . ')"  title="Ver Imagen"><i class="fa fa-file-image-o"></i></button>';
        if ($value->tipo == '0' || $value->tipo == '2') {
          $botonVerStock = '<button style="display:block; width:100%; padding:0px; margin-top:5px" class="btn  btn-info btn-sm" id="verStock-' . $value->id . '-0" onclick="verstockactual(' . $value->id . ', 0)" title="Ver Stock">STOCK <i class="fa fa-search"></i></button> ';
        } else {
          $botonVerStock = "";
        }
        $mensajeprecio = "<div style='margin:0px; padding:10px 4px' class='alert alert-danger'><div>◔_◔</div><div>NO TIENES PRECIOS ACTIVADOS</div></div>";
        $data[] = array(
          $key + 1,
          $textTipo . $nombreProducto . $dataAdicionales . $botonVerStock,
          $value->codigoBarra,
          $datacategoria ? $datacategoria->nombre : "SIN DATOS",
          ($preciosActivados > 0 ? $botonprecios : $mensajeprecio),
        );
      } else {
        continue;
      }
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

  function ajax_creditos_cliente()
  {
    $dataCajaPrincipal = $this->Controlador_model->get($this->cajaprincipal, "cajaprincipal");
    $cliente = $this->input->post("cliente");
    $creditos = $this->db->where("cliente", $cliente)->where("tienda", $dataCajaPrincipal->tienda)->where("estado", "0")->get("credito")->result();
    echo json_encode($creditos);
  }
}
