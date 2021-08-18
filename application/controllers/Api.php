<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    public function get_categorias()
    {
        echo json_encode(h_get_productoCategoria());
    }
}