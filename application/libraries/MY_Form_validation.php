<?php

/*
 * Extiende la libreria form_validation de codeigniter
 */

class MY_Form_validation extends CI_Form_validation {

    public function __construct($rules = array()) {
        parent::__construct($rules);
        $CI = & get_instance();
        $this->_error_prefix = '<div class="alert alert-danger">'
                . '<button type="button" class="close" data-dismiss="alert">&times;</button>'
                . '<h4 class="alert-heading"><strong>Error de validación!</strong></h4>';
        $this->_error_suffix = '</div>';
    }

    /*
     * Valida si un campo es unico, tanto para los metodos guardar como para editar
     */

    public function unique($value, $params) {
        list($table, $field) = explode(".", $params, 2);
        $this->CI->form_validation->set_message('unique', 'El Registro <strong>' . $value . '</strong> ya existe');
        if (!empty($table) && !empty($field)) {
            if ($this->CI->input->post('id')) {
                $this->CI->db->where('id !=', $this->CI->input->post('id'));
                $this->CI->db->where($field, $value);
                $query = $this->CI->db->get($table);
                if ($query->row()) {
                    return FALSE;
                } else {
                    return TRUE;
                }
            } else {
                $query = $this->CI->db->where($field, $value)->get($table);
                if ($query->row()) {
                    return FALSE;
                } else {
                    return TRUE;
                }
            }
        } else {
            show_error('Call to Form_validation::unique() failed, parameter not in "table.column" notation');
        }
    }

    /*
     * Permite evaluar por form_validation si existen datos en varios campos..
     * Es para que dos o mas campos no puedan reptirse en la misma tabla
     */

    public function unique_varios($value, $params) {
        list($table, $field) = explode(".", $params, 2);
        $fields = explode(".", $field);
        $this->CI->form_validation->set_message('unique_varios', 'Los datos ya existen en la base de datos');
        if (!empty($table) && !empty($field)) {
            if ($this->CI->input->post('id')) {
                $this->CI->db->where('id !=', $this->CI->input->post('id'));
                foreach ($fields as $fieldF) {
                    $this->CI->db->where($fieldF, $this->CI->input->post($fieldF));
                }
                $query = $this->CI->db->get($table);
                if ($query->row()) {
                    return FALSE;
                } else {
                    return TRUE;
                }
            } else {
                foreach ($fields as $fieldF) {
                    $this->CI->db->where($fieldF, $this->CI->input->post($fieldF));
                }
                $query = $this->CI->db->get($table);
                if ($query->row()) {
                    return FALSE;
                } else {
                    return TRUE;
                }
            }
        } else {
            show_error('Call to Form_validation::unique_varios() failed, parameter not in "table.column" notation');
        }
    }

    /*
     * Extendemos la libraria de validacion de codigniter para reemplazar la forma en como muestra los errores 
     * el sistema, en este caso se mostraran todos en la cabecera en una ventana tipo alerta que se puede cerrar
     */

    public function error_string($prefix = '', $suffix = '') {
        // No errrors, validation passes!
        if (count($this->_error_array) === 0) {
            return '';
        }

        if ($prefix == '') {
            $prefix = $this->_error_prefix;
        }

        if ($suffix == '') {
            $suffix = $this->_error_suffix;
        }

        // Generate the error string
        $str = $prefix;
        foreach ($this->_error_array as $val) {
            if ($val != '') {
                $str .= '<li>' . $val . "</li>";
            }
        }

        return $str .=$suffix;
    }

}
