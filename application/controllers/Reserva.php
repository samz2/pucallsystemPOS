
<?php

require 'simple_html_dom.php';

class Reserva extends CI_Controller
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
            'empresas' => $this->Controlador_model->getAll('empresa'),
            'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
        );
        $this->load->view(THEME . TEMPLATE, $data);
    }



    public function ajax_registroautomatico()
    {
        /*
    $milerArray = ["dato1" => "Retorno de datos de miler"];
    $josn = {
      "miler" : "data",
      "otrodata" : "otro p"
    };
    echo json_encode($milerArray);
    */
        $resultado = array(
            "result1" =>  "Hola miler sin JSON 1",
            "result2" =>  "Hola miler sin JSON 2"
        );
        echo json_encode($resultado);
    }

    public function ajax_list($finicio, $factual, $empresa)
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $query = $this->Controlador_model->verReservas($finicio, $factual, $empresa);
        $data = [];
        foreach ($query as $key => $value) {
            $date = date_create($value->fecha);
            //add variables for action
            $boton = '';
            //add html fodr action
            $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="edit(' . $value->id . ')"><i class="fa fa-pencil"></i></a> ';
            $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar(' . $value->id . ')"><i class="fa fa-trash"></i></a> ';
            $data[] = array(
                $key + 1,
                date_format($date, 'd/m/Y'),
                $value->hora,
                $value->especialidad,
                $value->medico,
                ($value->estado == 1) ? '<span class="label label-success">PAGADO</span>' : '<span class="label label-warning">POR PAGAR</span>',
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

    private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;
        $check = $this->Controlador_model->check($this->input->post('id'), $this->input->post('documento'));

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

        if ($check) {
            $data['inputerror'][] = 'documento';
            $data['error_string'][] = 'Este campo ya esta registrado en BD.';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function ajax_add()
    {
        $check = $this->Controlador_model->check($this->input->post('idPaciente'), $this->input->post('documento'));
        $idPaciente = 0;
        if ($check) {
            $idPaciente = $check[0]->id;
        } else {
            $paciente['tipodocumento'] = $this->input->post('tipo');
            $paciente['documento'] = $this->input->post('documento');
            $paciente['nombre'] = $this->input->post('nombre');
            $paciente['apellido'] = $this->input->post('apellido');
            $paciente['telefono'] = $this->input->post('telefono');
            $pacienteSaved = $this->Controlador_model->save('cliente', $paciente);
            if ($pacienteSaved) {
                $idPaciente = $pacienteSaved;
            }
        }
        $data['paciente'] = $idPaciente;
        $data['especialidad'] = $this->input->post('especialidad');
        $data['empresa'] = $this->input->post('r_empresa');
        $data['medico'] = $this->input->post('medico');
        $data['consultorio'] = $this->input->post('consultorio');
        $data['fecha'] = $this->input->post('fecha');
        $data['hora'] = $this->input->post('hora');
        $data['estado'] = $this->input->post('estado');
        if ($this->Controlador_model->save($this->controlador, $data)) {
            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_edit($id)
    {
        $data = $this->Controlador_model->get_by_id($id);
        echo json_encode($data->row());
    }

    public function ajax_update()
    {
        $check = $this->Controlador_model->check($this->input->post('idPaciente'), $this->input->post('documento'));
        $idPaciente = 0;
        if ($check) {
            $idPaciente = $check[0]->id;
        } else {
            $paciente['tipodocumento'] = $this->input->post('tipo');
            $paciente['documento'] = $this->input->post('documento');
            $paciente['nombre'] = $this->input->post('nombre');
            $paciente['apellido'] = $this->input->post('apellido');
            $paciente['telefono'] = $this->input->post('telefono');
            $pacienteSaved = $this->Controlador_model->save('cliente', $paciente);
            if ($pacienteSaved) {
                $idPaciente = $pacienteSaved;
            }
        }
        $data['paciente'] = $idPaciente;
        $data['especialidad'] = $this->input->post('especialidad');
        $data['empresa'] = $this->input->post('r_empresa');
        $data['medico'] = $this->input->post('medico');
        $data['consultorio'] = $this->input->post('consultorio');
        $data['fecha'] = $this->input->post('fecha');
        $data['hora'] = $this->input->post('hora');
        $data['estado'] = $this->input->post('estado');
        $this->Controlador_model->update(array('id' => $this->input->post('id')), $data, $this->controlador);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)
    {
        if ($this->Controlador_model->delete_by_id($id, $this->controlador)) {
            echo json_encode(array("status" => TRUE));
        }
    }

    public function consulta_reniec()
    {
        consuta_reniec_helper();
    }



    // public function consulta_reniec() {
    //   if($this->input->is_ajax_request()) {
    //         $tipo = $this->input->post('tipo');
    //         $documento = $this->input->post('documento');
    //         if($tipo == 'DNI') {
    //           $consulta = file_get_html('https://eldni.com/buscar-por-dni?dni='.$documento);
    //           $partes = array();
    //           foreach($consulta->find('td') as $header) {
    //             $partes[] = $header->plaintext;
    //           }
    //           $output = array("apellido" => $partes[1].' '.$partes[2], "direccion" => '', 'nombre' => $partes[0]);
    //         } else {
    //           $arrContextOptions = array("ssl" => array("verify_peer" => false, "verify_peer_name" => false));
    //           $data = file_get_contents("https://api.sunat.cloud/ruc/".$documento, false, stream_context_create($arrContextOptions));
    //           $info = json_decode($data, true);
    //           if($data === '[]' || $info['fecha_inscripcion'] === '--') {
    //             $output = array('nombre' => 'NADA');
    //           } else {
    //             $output = array('nombre' => $info['razon_social'], 'apellido' => '', 'direccion' => $info['domicilio_fiscal']);
    //           }
    //         }
    //         $this->output->set_content_type('application/json')->set_output(json_encode($output));
    //       } else {
    //         show_404();
    //       }

    // }

}
