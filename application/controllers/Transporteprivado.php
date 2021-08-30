<?php

class Transporteprivado extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(modelo(), 'Controlador_model');
        $this->controlador = controlador();
        $this->titulo_controlador = titulo_controlador();
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

    public function ajax_list($tipo)
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $query = $this->db->order_by('id', 'desc')->where("tipo", $tipo)->get($this->controlador);
        $data = [];
        $no = 0;

        foreach ($query->result() as $value) {
            $no++;
            $botones = '';
            $botones .= '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Modificar" onclick="edit(' . "'" . $value->id . "'" . ')"><i class="fa fa-pencil"></i></a> ';
            $botones .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="borrar(' . "'" . $value->id . "'" . ')"><i class="fa fa-trash"></i></a> ';
            if ($tipo == "VEHICULO") {
                $data[] = array(
                    $no,
                    $value->tipo,
                    $value->documento,
                    $value->codigo,
                    $botones
                );
            } else {
                $data[] = array(
                    $no,
                    $value->tipo,
                    $value->tipodocumento,
                    $value->documento,
                    $value->codigo,
                    $botones
                );
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

    private function _validate($statusregistro)
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;
        if ($this->input->post('tipoproceso') == 'VEHICULO') {
            if ($this->input->post('numeroplaca') == '') {
                $data['inputerror'][] = 'numeroplaca';
                $data['error_string'][] = 'Este campo es obligatorio.';
                $data['status'] = FALSE;
            } else {
                $validateVehiculo = $this->Controlador_model->validateVehiculo($this->input->post('numeroplaca'));
                if ($statusregistro == 'add' and $validateVehiculo) {
                    $data['inputerror'][] = 'numeroplaca';
                    $data['error_string'][] = 'Este numero de placa ya esta registrado en su sistema';
                    $data['status'] = FALSE;
                }
            }
        } else {
            if ($this->input->post('documento') == '') {
                $data['inputerror'][] = 'documento';
                $data['error_string'][] = 'Este campo es obligatorio.';
                $data['status'] = FALSE;
            } else {
                $validateVehiculo = $this->Controlador_model->validateDocumento($this->input->post('documento'), $this->input->post('tipodocumento'));
                if ($statusregistro == 'add' and $validateVehiculo) {
                    $data['inputerror'][] = 'numeroplaca';
                    $data['error_string'][] = 'Este documento ya esta registrado en su sistema';
                    $data['status'] = FALSE;
                }
            }
        }


        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function ajax_add($statusregistro)
    {
        $this->_validate($statusregistro);
        $data['tipo'] = $this->input->post('tipoproceso');
        $data['tipodocumento'] = $this->input->post('tipoproceso') == "VEHICULO" ? "PLACA" : $this->input->post('tipodocumento');
        $data['documento'] = $this->input->post('tipoproceso') == "VEHICULO" ? $this->input->post('numeroplaca') : $this->input->post('documento');
        $data['created_date_time'] = date("Y-m-d H:i:s");
        $numero = $this->Controlador_model->maxcodigo($this->controlador);
        $numeros = $numero ? $numero->numero + 1 : 1;
        $cadena = "";
        for ($i = 0; $i < 2 - strlen($numeros); $i++) {
            $cadena .= '0';
        }
        $data['numero'] = $numeros;
        $data['codigo'] = $cadena . $numeros;
        $insert = $this->Controlador_model->save($this->controlador, $data);
        if ($insert) {
            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_edit($id)
    {
        $data = $this->Controlador_model->get_by_id($id, $this->controlador);
        echo json_encode($data);
    }

    public function ajax_update($statusregistro)
    {
        $this->_validate($statusregistro);
        $dataquery = $this->Controlador_model->get($this->input->post('id'), "transporteprivado");
        if ($dataquery->tipo == "CONDUCTOR") {
            $data['tipodocumento'] = $this->input->post('tipodocumento');
            $data['documento'] = $this->input->post('documento');
        } else {
            $data['documento'] = $this->input->post('numeroplaca');
        }
        $this->Controlador_model->update(array('id' => $this->input->post('id')), $data, $this->controlador);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)
    {
        if ($this->Controlador_model->delete_by_id($id, $this->controlador)) {
            echo json_encode(array("status" => TRUE));
        }
    }
}
