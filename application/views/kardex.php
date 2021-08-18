<?= $this->session->flashdata('mensaje') ?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Filtro por fecha</h3>
      </div>
      <!-- /.box-header -->
      <form class="form-horizontal" id="form_01" autocomplete="off">
        <div class="panel-body">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="fecha">Fecha<span class="required">*</span></label>
            <div class="col-sm-5">
              <input type="date" class="form-control" id="fechainicio" name="fechainicio" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-sm-5">
              <input type="date" class="form-control" id="fechafinal" name="fechafinal" value="<?= date('Y-m-d') ?>">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="producto1">Producto<span class="required">*</span></label>
            <div class="col-sm-10">
              <input type="hidden" class="form-control" name="producto" id="producto">
              <input type="text" class="form-control" name="productos" id="productos">
            </div>
          </div>
        </div>
        <div class="panel-footer text-center">
          <a onclick="verxFecha()" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="GENERAR"><i class="fa fa-upload"></i></a>
        </div>
      </form>
    </div>
  </div>
  <!-- /.col -->
  <div class="col-xs-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title text-dark">Movimiento de <?= $this->titulo_controlador ?></h3>
      </div><!-- /.box-header -->
      <!-- /.box -->
      <div class="panel-body table-responsive">
        <div id="div_grafico"></div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
  jQuery(".opcion").select2({
    width: '100%'
  });
  $('#form_01').validate({
    onkeyup: false,
  });
  $('#form_02').validate({
    onkeyup: false,
  });
  $("#productos").autocomplete({
    source: "<?= $this->url ?>/autocompletar",
    minLength: 2,
    select: function( event, ui ) {
      $("#producto").val(ui.item.producto);
    }
  });
});
function verxFecha() {
  $.ajax({
    url: '<?= $this->url ?>/kardexFecha',
    data: { 'producto': $('#producto').val(), 'fechainicio': $('#fechainicio').val(), 'fechafinal': $('#fechafinal').val() },
    type: 'post',
    beforeSend: function() {
      $("#div_grafico").html('<br><h3>Cargando datos...</h3>');
    },
    success: function(data) {
      $("#div_grafico").html(data);
    }
  });
};
</script>
<script type="text/javascript" src="<?= base_url().RECURSOS ?>js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
