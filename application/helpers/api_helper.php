<?php 
function h_get_productoCategoria() {
    $CI =& get_instance();
    return $CI->db->get('productocategoria')->result();
}