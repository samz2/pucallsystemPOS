<div class="container">
  <!-- START ALERTS AND CALLOUTS -->
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">REPORTE DE VENTAS </h3>
        </div>
        <!-- /.box-header -->
        <form action="" class=" " method="POST" id="form_01" target="_blank" role="form">
          <div class="panel-body">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>Fecha inicio <span class="required">*</span></label>
                  <input class="form-control" id="finicio" type="date" name="finicio" value="<?= date('Y-m-d') ?>">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>Fecha final <span class="required">*</span></label>
                  <input class="form-control" id="factual" type="date" name="factual" value="<?= date('Y-m-d') ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>Empresa</label>
                  <select class="form-control" name="empresa" id="empresa">
                    <?php foreach ($empresas as $value) { ?>
                      <option value="<?= $value->id ?>"><?= $value->ruc . ' | ' . ($value->tipo == 0 ? $value->nombre : $value->razonsocial) ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>Tipo</label>
                  <select id="tipo" name="tipo" class="form-control">
                    <option value="RESUMEN">RESUMEN DE VENTAS</option>
                    <option value="DETALLE">DETALLES DE VENTAS</option>
                    <option value="CONTADOR">REPORTE CONTADOR</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="panel-footer text-center">
            <a onclick="procesobuscar()" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top"><i class="fa fa-search"></i> BUSCAR</a>
            <a onclick="procesodescargar()" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top">
              <i class="fa fa-file-excel-o"></i>
              DESCARGAR
            </a>
          </div>
        </form>
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
    <div class="col-xs-12">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="pull-left">
            <h3 class="panel-title">Lista de <?= $this->titulo_controlador ?></h3>
          </div>
          <div class="pull-right">
            <a onclick="location.reload()" class="btn btn-yahoo btn-sm" data-toggle="tooltip"><i class="fa fa-repeat"></i> RECARGAR</a>
          </div>
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

  function procesobuscar() {
    if ($("#tipo").val() == "RESUMEN") {
      resumen();
    } else if ($("#tipo").val() == "DETALLE") {
      detalle();
    } else {
      contadorbuscar();
    }
  }

  function procesodescargar() {
    if ($("#tipo").val() == "RESUMEN") {
      window.open('<?= $this->url ?>/resumenexcel/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val());
    } else if ($("#tipo").val() == "DETALLE") {
      window.open('<?= $this->url ?>/detalleexcel/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val());
    } else {
      window.open('<?= $this->url ?>/contador/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val());
    }
  }
</script>