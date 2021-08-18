<?php

$config = array(
    'usuario' => array(
        array(
            'field' => 'usuario',
            'label' => 'Usuario',
            'rules' => 'trim|required|unique[usuario.usuario]'
        ),
        array(
            'field' => 'password',
            'label' => 'Contraseña',
            'rules' => 'trim|required|min_length[4]'
        ),
        array(
            'field' => 're_password',
            'label' => 'Verificar Contraseña',
            'rules' => 'trim|required|min_length[4]|matches[password]'
        ),
    ),
    'actualizar_usuario' => array(
        array(
            'field' => 'usuario',
            'label' => 'Usuario',
            'rules' => 'trim|required|unique[usuario.usuario]'
        ),
        array(
            'field' => 'password',
            'label' => 'Contraseña',
            'rules' => 'trim|min_length[4]'
        ),
        array(
            'field' => 're_password',
            'label' => 'Verificar Contraseña',
            'rules' => 'trim|min_length[4]|matches[password]'
        ),
    ),
    'cliente' => array(
      array(
          'field' => 'nombre',
          'label' => 'Nombre',
          'rules' => 'trim|required'
      ),
    ),
    'empresa' => array(
      array(
          'field' => 'razonsocial',
          'label' => 'Razon Social',
          'rules' => 'trim|required|unique[empresa.razonsocial]'
      ),
      array(
          'field' => 'ruc',
          'label' => 'RUC',
          'rules' => 'trim|required|unique[empresa.ruc]'
      ),
      array(
          'field' => 'direccion',
          'label' => 'Direccion',
          'rules' => 'trim|required'
      ),
    ),
    'serie' => array(
        array(
            'field' => 'serie',
            'label' => 'Serie',
            'rules' => 'trim|required'
        ),
    ),
    'producto' => array(
      array(
          'field' => 'preciodescuento',
          'label' => 'Precio Descuento',
          'rules' => 'trim|required'
      ),
    ),
    'pedido' => array(
      array(
          'field' => 'monto',
          'label' => 'Monto',
          'rules' => 'trim|required'
      ),
    ),
    'pagar' => array(
        array(
            'field' => 'monto',
            'label' => 'Monto',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'fechapago',
            'label' => 'Fecha de Pago',
            'rules' => 'trim|required'
        ),
    ),
    'cantidad' => array(
        array(
            'field' => 'numero',
            'label' => 'Cantidad',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'ventadetalle',
            'label' => 'Detalle de Venta',
            'rules' => 'trim|required'
        ),
    ),
    'subtotal' => array(
        array(
            'field' => 'preciodescuento',
            'label' => 'Subtotal',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'ventadetalle',
            'label' => 'Detalle de Venta',
            'rules' => 'trim|required'
        ),
    ),
    'perfil' => array(
        array(
            'field' => 'perfil',
            'label' => 'Perfil',
            'rules' => 'trim|required'
        ),
    ),
    'caja' => array(
        array(
            'field' => 'saldoinicial',
            'label' => 'Saldo Inicial',
            'rules' => 'trim|required'
        ),
    ),
    'egreso' => array(
        array(
            'field' => 'categoria',
            'label' => 'Categoria Egreso',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'amount',
            'label' => 'Monto',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'reference',
            'label' => 'Referencia',
            'rules' => 'trim|required'
        ),
    ),
    'egresocategoria' => array(
        array(
            'field' => 'nombre',
            'label' => 'Nombre',
            'rules' => 'trim|required'
        ),
    ),
    'tienda' => array(
        array(
            'field' => 'nombre',
            'label' => 'Nombre',
            'rules' => 'trim|required'
        ),
    ),
    'mesa' => array(
      array(
        'field' => 'nombre',
        'label' => 'Nombre',
        'rules' => 'trim|required'
      ),
    ),
    'registro' => array(
      array(
        'field' => 'saldo',
        'label' => 'Saldo Inicial',
        'rules' => 'trim|required'
      ),
    ),
    'cuenta' => array(
      array(
        'field' => 'pago',
        'label' => 'Pago',
        'rules' => 'trim|required'
      ),
    ),
    'productocategoria' => array(
      array(
        'field' => 'nombre',
        'label' => 'Nombre',
        'rules' => 'trim|required'
      ),
    ),
    'combo' => array(
        array(
            'field' => 'combo',
            'label' => 'Combo',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'producto',
            'label' => 'Producto',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'cantidad',
            'label' => 'Cantidad',
            'rules' => 'trim|required|is_natural_no_zero'
        ),
    ),
);
