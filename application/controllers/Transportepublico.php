<?php

class Transportepublico extends CI_Controller
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

    public function ajax_list()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $query = $this->db->order_by('id', 'desc')->get($this->controlador);
        $data = [];
        $no = 0;
        foreach ($query->result() as $value) {
            $no++;
            $botones = '';
            $botones .= '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Modificar" onclick="edit(' . "'" . $value->id . "'" . ')"><i class="fa fa-pencil"></i></a> ';
            $botones .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="borrar(' . "'" . $value->id . "'" . ')"><i class="fa fa-trash"></i></a> ';
            $data[] = array(
                $no,
                $value->tipodocumento,
                $value->documento,
                $value->razonsocial,
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

    private function _validate($statusproceso)
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('documento') == '') {
            $data['inputerror'][] = 'documento';
            $data['error_string'][] = 'Este campo es obligatorio.';
            $data['status'] = FALSE;
        } else {
            if ($statusproceso == 'add') {
                $queryInsert = $this->Controlador_model->serchDuplicado($this->input->post('documento'), NULL);
                if ($queryInsert) {
                    $data['inputerror'][] = 'documento';
                    $data['error_string'][] = 'Ya existe un transporte publico con este R.U.C';
                    $data['status'] = FALSE;
                }
            } else {
                $queryUpdate = $this->Controlador_model->serchDuplicado($this->input->post('documento'), $this->input->post('id'));
                if ($queryUpdate) {
                    $data['inputerror'][] = 'documento';
                    $data['error_string'][] = 'Ya existe un transporte publico con este R.U.C';
                    $data['status'] = FALSE;
                }
            }
        }

        if ($this->input->post('razonsocial') == '') {
            $data['inputerror'][] = 'razonsocial';
            $data['error_string'][] = 'Este campo es obligatorio.';
            $data['status'] = FALSE;
        }


        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function ajax_add($statusproceso)
    {
        $this->_validate($statusproceso);
        $data['tipodocumento'] = "R.U.C";
        $data['documento'] = $this->input->post('documento');
        $data['razonsocial'] = $this->input->post('razonsocial');
        $data['created_date_time'] = date("Y-m-d H:i:s");
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

    public function ajax_update($statusproceso)
    {
        $this->_validate($statusproceso);
        $data['documento'] = $this->input->post('documento');
        $data['razonsocial'] = $this->input->post('razonsocial');
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
