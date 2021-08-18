<!DOCTYPE html>
<html>

<head>
  <?php $this->load->view(THEME . 'head') ?>
  <meta property="og:image" content="http://wondercool.eu/wp-content/uploads/2019/01/logo-wondercool-redes-sociales-3-lineas-original-300x300.png" />
  <meta property="og:image:secure_url" content="https://wondercool.eu/wp-content/uploads/2019/01/logo-wondercool-redes-sociales-3-lineas-original-300x300.png" />
  <meta property="og:image:type" content="image/png" />
  <meta property="og:image:width" content="300" />
  <meta property="og:image:height" content="300" />
</head>


<body>
  <!-- header logo: style can be found in header.less -->
  <?php $this->load->view(THEME . 'topbar') ?>
  <?php if (isset($this->venta)) { ?>
    <!-- Content Header (Page header) -->
    <?php $this->load->view($contenido) ?>
  <?php } else { ?>
    <div class="container cont-principal">
      <!-- Content Header (Page header) -->
      <?php $this->load->view(THEME . 'breadcrumb') ?>
      <?php $this->load->view($contenido) ?>
    </div>
  <?php } ?>
  <!-- slim scroll script -->
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery.slimscroll.min.js"></script>
  <!-- waves material design effect -->
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/waves.min.js"></script>
  <!-- Bootstrap Core JavaScript -->
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/bootstrap.min.js"></script>
  <!-- select2 plugin script -->
  <!-- <script src="<?= base_url() . RECURSOS ?>assets/select2/select2.min.js" type="text/javascript"></script> -->
  <!--BOOTBOX VENTANA DE ALERTAS -->
  <script src="<?= base_url() . RECURSOS ?>js/bootbox.min.js"></script>
  <!-- dalatable scripts -->
  <script src="<?= base_url() . RECURSOS ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?= base_url() . RECURSOS ?>datatables/js/dataTables.bootstrap.js"></script>
  <!-- summernote js -->
  <script src="<?= base_url() . RECURSOS ?>js/summernote.js"></script>
  <!-- chart.js script -->
  <script src="<?= base_url() . RECURSOS ?>js/Chart.js"></script>
  <!-- moment JS -->
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/moment.min.js"></script>
  <!-- Include Date Range Picker -->
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/daterangepicker.js"></script>
  <!-- Sweet Alert swal -->
  <script src="<?= base_url() . RECURSOS ?>js/sweetalert.min.js"></script>
  <!-- ajax form -->
  <script src="<?= base_url() . RECURSOS ?>js/jquery.form.min.js"></script>
  <!-- custom script -->
  <script src="<?= base_url() . RECURSOS ?>js/app.js"></script>
  <script src="<?= base_url() . RECURSOS ?>js/jquery.mayusculassintildes.js"></script>
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery.numeric.js"></script>
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery.plugin.js"></script>
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery.maxlength.js"></script>
  <!-- <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/headroom.min.js"></script> -->

  <!--form validation-->
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>assets/jquery.validate/jquery.validate.min.js"></script>
  <!--form validation init-->
  <script src="<?= base_url() . RECURSOS ?>assets/jquery.validate/form-validation-init.js"></script>
  <script src="<?= base_url() . RECURSOS ?>js/localization/messages_es.min.js"></script>
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery.number.js"></script>
  <script src="<?= base_url() . RECURSOS ?>js/jquery.mayusculassintildes.js"></script>
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery.numeric.js"></script>
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/checktree.js"></script>
  <!-- sweet alerts -->
  <script src="<?= base_url() . RECURSOS ?>assets/sweet-alert/sweet-alert.min.js"></script>
  <script src="<?= base_url() . RECURSOS ?>assets/sweet-alert/sweet-alert.init.js"></script>
  <script src="<?= base_url() . RECURSOS ?>assets/jquery-blockui/jquery.blockUI.js"></script>
  <script src="<?= base_url() . RECURSOS ?>js/lobibox.js"></script>
  <script src="<?= base_url() . RECURSOS ?>js/estilonew.js"></script>
  <script src="<?= base_url() . RECURSOS ?>js/Impresora.js"></script>
  <script src="<?= base_url() . RECURSOS ?>js/jquery.fullscreen.js"></script>
  <script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/addclear.js"></script>
  <script src="<?= base_url() . RECURSOS ?>js/xlsx.js"></script>
  <script>
    jQuery(document).ready(function(e) {
      jQuery(".cont-principal").on("swipe", swipeHandler);
      jQuery(".cont-principal").click(function(e){
        jQuery("#mySidebar").removeClass("active");
      });
    });

    function swipeHandler(event, phase, direction) {
      jQuery("#mySidebar").addClass("active");

    }
  </script>
</body>

</html>