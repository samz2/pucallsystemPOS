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
              <input type="text" class="form-control" name="productos" id="productos">
            </div>
          </div>

        </div>
        <div class="panel-footer text-center">
          <a onclick="verxFecha()" class="btn btn-warning" data-toggle="tooltip" data-placement="top">BUSCAR <i class="fa fa-search"></i></a>
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

    $("#productos").select2({
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
      theme: "classic",
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