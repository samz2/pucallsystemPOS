<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" /> 
<?php $empresa = $this->Controlador_model->get($this->empresa, 'empresa'); ?>
<?php if($empresa->tipo == 0) { ?>
  <title><?= substr($empresa->nombre, 0, 18) ?></title>
<?php } else { ?>
  <title><?= substr($empresa->razonsocial, 0, 18) ?></title>
<?php } ?>

<!-- jQuery -->
<!-- <link rel="stylesheet" href="<?= base_url().RECURSOS ?>js/jquery.mobile-1.4.5.css"  type='text/css'> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.min.js"></script>
<script type="text/javascript" src="<?= base_url().RECURSOS ?>js/card.js"></script>
<script type="text/javascript" src="<?= base_url().RECURSOS ?>js/es.js"></script>
<script type="text/javascript" src="<?= base_url().RECURSOS ?>js/jquery-2.2.2.min.js"></script>
<!-- <script type="text/javascript" src="<?= base_url().RECURSOS ?>js/jquery.mobile-1.4.5.js"></script> -->
<script type="text/javascript" src="<?= base_url().RECURSOS ?>js/loading.js"></script>
<!-- normalize & reset style -->
<link rel="stylesheet" href="<?= base_url().RECURSOS ?>css/normalize.min.css"  type='text/css'>
<link rel="stylesheet" href="<?= base_url().RECURSOS ?>css/reset.min.css"  type='text/css'>
<link rel="stylesheet" href="<?= base_url().RECURSOS ?>css/card.css"  type='text/css'>
<link rel="stylesheet" href="<?= base_url().RECURSOS ?>css/jquery-ui.css"  type='text/css'>
<!-- FullCalendar JS -->
<link rel="stylesheet" href="<?= base_url().RECURSOS ?>fullcalendar/main.css">
<script type="text/javascript" src="<?= base_url().RECURSOS ?>fullcalendar/main.js"></script>
<!-- Bootstrap Core CSS -->
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link href="<?= base_url().RECURSOS ?>css/bootstrap.min.css" rel="stylesheet">
<!-- bootstrap-horizon -->
<link href="<?= base_url().RECURSOS ?>css/bootstrap-horizon.css" rel="stylesheet">
<!-- datatable style -->
<link href="<?= base_url().RECURSOS ?>datatables/css/dataTables.bootstrap.css" rel="stylesheet">
<!-- font awesome -->
<link href="<?= base_url().RECURSOS ?>assets/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">
<!-- waves -->
<link rel="stylesheet" href="<?= base_url().RECURSOS ?>css/waves.min.css">
<!-- daterangepicker -->
<link rel="stylesheet" type="text/css" href="<?= base_url().RECURSOS ?>css/daterangepicker.css" />
<!-- Select 2 style -->
<!-- <link href="<?= base_url().RECURSOS ?>assets/select2/select2.css" rel="stylesheet" type="text/css" /> -->
<link rel="stylesheet" type="text/css" href="<?= base_url().RECURSOS ?>css/jquery.maxlength.css">
<!-- Sweet alert swal -->
<link rel="stylesheet" type="text/css" href="<?= base_url().RECURSOS ?>css/sweetalert.css">
<!-- Custom CSS -->
<link href="<?= base_url().RECURSOS ?>css/Style-Light.css" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url().RECURSOS ?>css/awesome-bootstrap-checkbox.css"/>
<!-- sweet alerts -->
<link href="<?= base_url().RECURSOS ?>assets/sweet-alert/sweet-alert.min.css" rel="stylesheet">
<link href="<?= base_url().RECURSOS ?>css/bootstrap-social.css" rel="stylesheet" >
<link rel="stylesheet" href="<?= base_url().RECURSOS ?>dist/css/lobibox.min.css"/>
<link rel="stylesheet" href="<?= base_url().RECURSOS ?>toast/dist/toasty.min.css"/>
<link rel="stylesheet" href="<?= base_url().RECURSOS ?>js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<link href="<?= base_url().RECURSOS ?>css/estilonew.css" rel="stylesheet" type="text/css">
<link href="<?= base_url().RECURSOS ?>css/bootstrap-social.css" rel="stylesheet">
<!-- Custom Files -->
<link href="<?= base_url().RECURSOS ?>css/helper.css" rel="stylesheet" type="text/css">
<link href="<?= base_url().RECURSOS ?>css/style.css" rel="stylesheet" type="text/css">
<!-- animate css -->
<link href="<?= base_url().RECURSOS ?>css/animate.css" rel="stylesheet" />
<!-- Waves-effect -->
<link href="<?= base_url().RECURSOS ?>css/waves-effect.css" rel="stylesheet">
<!--Seleccion de productos estilos-->
<link href="<?= base_url().RECURSOS ?>css/seleccion_producto.css" rel="stylesheet">

<script src="<?= base_url().RECURSOS ?>js/modernizr.min.js"></script>
<!-- favicon -->
<link rel="shortcut icon" href="<?= base_url() ?>favicon/faviconnew.ico" type="image/x-icon">
<link rel="apple-touch-icon" sizes="57x57" href="<?= base_url() ?>favicon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?= base_url() ?>favicon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?= base_url() ?>favicon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?= base_url() ?>favicon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?= base_url() ?>favicon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?= base_url() ?>favicon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="<?= base_url() ?>favicon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?= base_url() ?>favicon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?= base_url() ?>favicon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="<?= base_url() ?>favicon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?= base_url() ?>favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="<?= base_url() ?>favicon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url() ?>favicon/favicon-16x16.png">
<link rel="manifest" href="<?= base_url() ?>favicon/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="<?= base_url() ?>favicon/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<!-- Material Switch -->
<link href="<?= base_url().RECURSOS ?>css/material-switch.css" rel="stylesheet" />
<!-- STYLE MAIN -->
<link href="<?= base_url().RECURSOS ?>css/styles.css" rel="stylesheet">
