<?php

class Cajaprincipal extends CI_Controller
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
            'tiendas' => $this->Controlador_model->getAll("empresa"),
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
            $tienda = $this->Controlador_model->get($value->tienda, "empresa");
            $almacen = $this->Controlador_model->get($value->almacen, "almacen");
            $botones = '';
            $botones .= '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Modificar" onclick="edit(' . "'" . $value->id . "'" . ')"><i class="fa fa-pencil"></i></a> ';
            $botones .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="borrar(' . "'" . $value->id . "'" . ')"><i class="fa fa-trash"></i></a> ';
            $estado = "";
            $data[] = array(
                $no,
                $value->nombre,
                $tienda ? $tienda->nombre : "SIN DATOS",
                $almacen ? $almacen->nombre : "SIN DATOS",
                $value->tipoventa,
                $estado,
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

    private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('nombre') == '') {
            $data['inputerror'][] = 'nombre';
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
        $data['nombre'] = $this->input->post('nombre');
        $data['descripcion'] = $this->input->post('descripcion');
        $data['tienda'] = $this->input->post("tienda");
        $data['almacen'] = $this->input->post("almacen");
        $data['tipoventa'] = $this->input->post("tipoventa");
        $data['pasos'] = $this->input->post("pasos");
        $insert = $this->Controlador_model->save($this->controlador, $data);
        if ($insert) {
            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_edit($id)
    {
        $data = $this->Controlador_model->get_by_id($id, $this->controlador);
        $data->almacenes =  $queryAlamcenes = $this->db->order_by("nombre", "ASC")->where("empresa", $data->tienda)->get("almacen")->result();
        echo json_encode($data);
    }

    public function ajax_update()
    {
        $this->_validate();
        $data['nombre'] = $this->input->post('nombre');
        $data['descripcion'] = $this->input->post('descripcion');
        $data['tienda'] = $this->input->post("tienda");
        $data['almacen'] = $this->input->post("almacen");
        $data['tipoventa'] = $this->input->post("tipoventa");
        $data['pasos'] = $this->input->post("pasos");
        $this->Controlador_model->update(array('id' => $this->input->post('idcajaprincipal')), $data, $this->controlador);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)
    {
        if ($this->Controlador_model->delete_by_id($id, $this->controlador)) {
            echo json_encode(array("status" => TRUE));
        }
    }

    function ajax_tiendaalamcen($idempresa)
    {
        $queryAlamcenes = $this->db->order_by("nombre", "ASC")->where("empresa", $idempresa)->get("almacen")->result();
        echo json_encode($queryAlamcenes);
    }
}
