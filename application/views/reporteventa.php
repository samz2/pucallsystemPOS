<div class="container">
  <!-- START ALERTS AND CALLOUTS -->
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><?= $this->titulo_controlador ?> Especifico</h3>
        </div>
        <!-- /.box-header -->
        <form action="" class="form-horizontal " method="POST" id="form_01" target="_blank" role="form">
          <div class="panel-body">
            <div class="form-group">
              <label class="col-sm-2 control-label">Fecha<span class="required">*</span></label>
              <div class="col-sm-5">
                <input class="form-control" id="finicio" type="date" name="finicio" value="<?= date('Y-m-d') ?>">
              </div>
              <div class="col-sm-5">
                <input class="form-control" id="factual" type="date" name="factual" value="<?= date('Y-m-d') ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label" for="factual">Empresa</label>
              <div class="col-sm-10">
                <select class="form-control" name="empresa" id="empresa">
                  <?php foreach ($empresas as $value) { ?>
                    <option value="<?= $value->id ?>"><?= $value->ruc . ' | ' . ($value->tipo == 0 ? $value->nombre : $value->razonsocial) ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="panel-footer text-center">
            <a onclick="resumen()" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="RESUMEN"><i class="fa fa-upload"></i></a>
            <a onclick="resumenexcel()" style="position:relative" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="RESUMEN">
              <i class="fa fa-file-excel-o"></i>
              <i style="font-size:10px; position:absolute; bottom:6px; right:4px" class="fa fa-download"></i>
            </a>
            <a onclick="detalle()" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="DETALLE"><i class="fa fa-upload"></i></a>
            <a onclick="detalleexcel()" style="position:relative" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="DETALLE">
              <i class="fa fa-file-excel-o"></i>
              <i style="font-size:10px; position:absolute; bottom:6px; right:4px" class="fa fa-download"></i>
            </a>
            <a onclick="contador()" style="position: relative;" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="CONTADOR">
              <i class="fa fa-file-excel-o"></i>
              <i style="font-size:10px; position:absolute; bottom:6px; right:4px" class="fa fa-download"></i>
            </a>
            <a onclick="location.reload()" class="btn btn-yahoo" data-toggle="tooltip" title="RECARGAR"><i class="fa fa-repeat"></i></a>
          </div>
        </form>
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
    <div class="col-xs-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Lista de <?= $this->titulo_controlador ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="panel-body table-responsive">
          <div id="div_grafico"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.row -->
</div>

<script type="text/javascript">
  function resumen() {
    $.ajax({
      url: '<?= $this->url ?>/resumen/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val(),
      type: 'post',
      beforeSend: function() {
        $("#div_grafico").html('<br><h3>Cargando datos...</h3>');
      },
      success: function(data) {
        $("#div_grafico").html(data);
        $('#example1').dataTable({
          language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
              "first": "Primero",
              "last": "Ultimo",
              "next": "Siguiente",
              "previous": "Anterior"
            }
          },
        });
      }
    });
  };

  function detalle() {
    $.ajax({
      url: '<?= $this->url ?>/detalle/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val(),
      type: 'post',
      beforeSend: function() {
        $("#div_grafico").html('<br><h3>Cargando datos...</h3>');
      },
      success: function(data) {
        $("#div_grafico").html(data);
        $('#example1').dataTable({
          language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
              "first": "Primero",
              "last": "Ultimo",
              "next": "Siguiente",
              "previous": "Anterior"
            }
          },
        });
      }
    });
  };

  function contador() {
    window.open('<?= $this->url ?>/contador/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val());
  };

  function resumenexcel() {
    window.open('<?= $this->url ?>/resumenexcel/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val());
  };

  function detalleexcel() {
    window.open('<?= $this->url ?>/detalleexcel/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val());
  };
</script>