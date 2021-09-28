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
            <label class="col-sm-2 control-label" for="producto1">Tipo de filtrado <span class="required">*</span></label>
            <div class="col-sm-10">
              <select class="form-control" id="tipofiltrado" name="tipofiltrado">
                <option value="1">FECHA Y PRODUCTO</option>
                <option value="2">FECHA</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="fecha">Fecha<span class="required">*</span></label>
            <div class="col-sm-5">
              <input type="date" class="form-control" id="fechainicio" name="fechainicio" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-sm-5">
              <input type="date" class="form-control" id="fechafinal" name="fechafinal" value="<?= date('Y-m-d') ?>">
            </div>
          </div>
          <div class="form-group" id="content-producto">
            <label class="col-sm-2 control-label" for="producto1">Producto<span class="required">*</span></label>
            <div class="col-sm-10">
              <!--<input type="hidden" class="form-control" name="producto" id="producto">-->
              <select type="text" class="form-control" name="productos" id="producto"></select>
            </div>
          </div>

        </div>
        <div class="panel-footer text-center">
          <a onclick="verxFecha()" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top"><i class="fa fa-search"></i> BUSCAR</a>
        </div>
      </form>
    </div>
  </div>
  <!-- /.col -->
  <div class="col-xs-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title text-dark clearfix">
          <div class="pull-left">Movimiento de <?= $this->titulo_controlador ?></div>
          <div class="pull-right">
            <div style="display:flex">
              <div style="display:flex; flex-direction:column; text-align: center;margin-right:5px">
              <i class='fa fa-arrow-circle-left' style='color:#c31717; font-size:25px'></i>
              <label>SALIDA</label>
            </div>
            <div style="display:flex; flex-direction:column; text-align:center">
            <i class='fa fa-arrow-circle-right' style='color:#15790e; font-size:25px'></i>
              <label>ENTRADA</label>
            </div>
            </div>
          </div>
        </h3>
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
    operacionesvista();
    jQuery(".opcion").select2({
      width: '100%'
    });
    $('#form_01').validate({
      onkeyup: false,
    });
    $('#form_02').validate({
      onkeyup: false,
    });
    /*
    $("#productos").autocomplete({
      source: "<?= $this->url ?>/autocompletar",
      minLength: 2,
      select: function(event, ui) {
        $("#producto").val(ui.item.producto);
      }
    });
    */
    $("#tipofiltrado").change(function() {
      operacionesvista();
    });

    $("#producto").select2({
      language: {
        noResults: function() {
          return "No hay resultado";
        },
        searching: function() {
          return "Buscando..";
        },
        inputTooShort: function() {
          return "Debes ingresar mas caracteres...";
        }
      },
      placeholder: 'Buscar producto',
      minimumInputLength: 2,
      ajax: {
        url: "<?= $this->url ?>/buscarProducto",
        dataType: "json",
        delay: 250,
        data: function(params) {
          return {
            q: params.term
          }
        },
        processResults: function(response) {
          return {
            results: response
          }
        },
        cache: true
      },

    });

  });

  function verxFecha() {
    $.ajax({
      url: '<?= $this->url ?>/kardexFecha',
      data: {
        'producto': $('#producto').val(),
        'fechainicio': $('#fechainicio').val(),
        'fechafinal': $('#fechafinal').val(),
        'tipofiltrado': $('#tipofiltrado').val(),
      },
      type: 'post',
      beforeSend: function() {
        $("#div_grafico").html('<br><h3>Cargando datos...</h3>');
      },
      success: function(data) {
        $("#div_grafico").html(data);
        $('#tabla_kardex').dataTable({
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

  function operacionesvista() {
    if ($("#tipofiltrado").val() == "1") {
      $("#content-producto").show("fast");
    } else {
      $("#content-producto").hide("fast");
    }
  }
</script>
<script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>