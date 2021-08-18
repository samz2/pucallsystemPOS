<?php

function controlador() {
    $CI = & get_instance();
    return $CI->router->fetch_class();
}

function tabla() {
    $CI = & get_instance();
    return $CI->router->fetch_class();
}

function titulo_controlador() {
    return humanize(controlador());
}

function modelo() {
    return ucfirst(controlador()) . '_model';
}

function mensaje_alerta($tipo, $accion) {
    $CI = & get_instance();
    switch ($tipo) {
        case 'hecho' :
            return $CI->session->set_flashdata('mensaje', '<div class="alert alert-success alert-dismissable">'
                            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
                            . $CI->lang->line('mensaje_'.$tipo.'_'.$accion)
                            . '</div>'
            );
        case 'error' :
            return $CI->session->set_flashdata('mensaje', '<div class="alert alert-danger alert-dismissable">'
                            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
                            . $CI->lang->line('mensaje_'.$tipo.'_'.$accion)
                            . '</div>'
            );
    }
}

function getMenuHijos($id){
    $CI = & get_instance();
    return $CI->db->where('menu_id',$id)->order_by('posicion','asc')->get('menu')->result();
}
