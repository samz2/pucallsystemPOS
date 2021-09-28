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
              <label class="col-sm-2 control-label" for="finicio">Fecha<span class="required">*</span></label>
              <div class="col-sm-5">
                <input class="form-control" id="finicio" type="date" name="finicio" value="<?= date('Y-m-d') ?>">
              </div>
              <div class="col-sm-5">
                <input class="form-control" id="factual" type="date" name="factual" value="<?= date('Y-m-d') ?>">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Empresa<span class="required">*</span></label>
              <div class="col-sm-10">
                <select id="empresa" name="empresa" class="form-control">
                  <?php foreach ($empresas as $value) { ?>
                    <option value="<?= $value->id ?>"><?= $value->ruc . ' | ' . ($value->tipo == 0 ? $value->nombre : $value->razonsocial) ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="panel-footer text-center">
            <!--  <a onclick="general()" class="btn btn-primary" data-toggle="tooltip" title="GENERAL"><i class="fa fa-upload"></i></a>
            <a onclick="generalpdf()" class="btn btn-info" data-toggle="tooltip" title="IMPRIMIR"><i class="fa fa-print"></i></a> -->
            <a onclick="especifico()" class="btn btn-warning btn-sm" data-toggle="tooltip"><i class="fa fa-search"></i> BUSCAR</a>
            <!--  <a onclick="especificopdf()" class="btn btn-info" data-toggle="tooltip" title="IMPRIMIR"><i class="fa fa-print"></i></a> -->
            <a onclick="location.reload()" class="btn btn-yahoo btn-sm" data-toggle="tooltip"><i class="fa fa-repeat"></i> RECARGAR</a>
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

<!-- Modal ticket -->
<div class="modal fade" id="ticket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <div class="form-body table-responsive" id="printSection"></div>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal -->

<!-- Modal ticket -->
<div class="modal fade" id="caja_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">DETALLE DE CAJA</h4>
      </div>
      <div class="modal-body">
        <div class="form-body table-responsive" id="seccioncaja"></div>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal -->

<!--  modal zona-->
<div class="modal fade" id="modal_stockcaja" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="overflow: auto;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title text-center" id="title_stockcaja"></h3>
      </div>
      <div class="modal-body form">
        <div class="text-right">
          <input type="hidden" id="idcajaseleccionado">
          <a onclick="DescargarCajaStock()" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top">
            <i class="fa fa-file-pdf-o"></i>
            IMPRIMIR A4
          </a>
          <input type="hidden" id="idcajaseleccionado">
          <a onclick="DescargarMiniStock()" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top">
            <i class="fa fa-print"></i>
            IMPRIMIR
          </a>
        </div>
        <!-- <div class="text-right">
          <input type="hidden" id="idcajaseleccionado">
          <a onclick="DescargarMiniStock()" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top">
            <i class="fa fa-file-print"></i>
            IMPRIMIR
          </a>
        </div> -->
        <table id="tablastockcaja" class="table table-bordered table-striped">
          <thead>
            <tr class="text-title-panel">
              <th><b>#</b></th>
              <th><b>Codigo</b></th>
              <th><b>Producto</b></th>
              <th><b>categoria</b></th>
              <th><b>Inicio de Stock</b></th>
              <th><b>Final de Stock</b></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
  const RUTA_API = "http://localhost:8000";
  const $impresoraSeleccionada = document.querySelector("#impresoraSeleccionada");
  const refrescarNombreDeImpresoraSeleccionada = () => {
    Impresora.getImpresora()
      .then(nombreImpresora => {
        $impresoraSeleccionada.textContent = '';
      });
  };
  var tablastockcaja;
  $(document).ready(function() {
    $('.restaurar').bind('click', function(e) {
      e.preventDefault();
      bootbox.confirm("Seguro desea Resturar esta caja?", function(result) {
        if (result === true) {
          window.location = e.currentTarget;
        }
      });
    });
  });

  function general() {
    $.ajax({
      url: '<?= $this->url ?>/general/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val(),
      type: 'POST',
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

  function especifico() {
    $.ajax({
      url: '<?= $this->url ?>/especifico/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val(),
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

  function generalpdf() {
    $.ajax({
      url: "<?= $this->url ?>/generalpdf",
      data: {
        'finicio': $('#finicio').val(),
        'factual': $('#factual').val()
      },
      type: "POST",
      success: function(data) {
        $('#printSection').html(data);
        $('#ticket').modal('show');
        $('.modal-title').text('IMPRIMIR CAJA GENERAL');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  };

  function especificopdf() {
    $.ajax({
      url: "<?= $this->url ?>/especificopdf",
      data: {
        'finicio': $('#finicio').val(),
        'factual': $('#factual').val()
      },
      type: "POST",
      success: function(data) {
        $('#printSection').html(data);
        $('#ticket').modal('show');
        $('.modal-title').text('IMPRIMIR CAJA ESPECIFICO');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  };

  function verGenerar(concepto) {
    $.ajax({
      url: '<?= $this->url ?>/generaldetalle',
      data: {
        'finicio': $('#finicio').val(),
        'factual': $('#factual').val(),
        'concepto': concepto
      },
      type: 'post',
      success: function(data) {
        $('#printSection').html(data);
        $('#ticket').modal('show');
        $('.modal-title').text('VER DETALLE');
        $('#example2').dataTable({
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
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  };

  function verDetalle(concepto) {
    $.ajax({
      url: "<?= $this->url ?>/generaldetallepdf",
      data: {
        'finicio': $('#finicio').val(),
        'factual': $('#factual').val(),
        'concepto': concepto
      },
      type: "POST",
      success: function(data) {
        $('#printSection').html(data);
        $('#ticket').modal('show');
        $('.modal-title').text('IMPRIMIR CAJA GENERAL DETALLE');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  };

  function showTicket(id) {
    $.ajax({
      url: "<?= $this->url ?>/ShowTicket/" + id,
      type: "POST",
      success: function(data) {
        $('#seccioncaja').html(data);
        $('#caja_form').modal('show');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  };

  function imprimir(caja, tipoimpresora) {
    if (tipoimpresora == 0) {
      $.ajax({
        url: '<?= $this->url ?>/imprimir/' + caja,
        type: 'POST',
      });
    }
    if (tipoimpresora == 1) {
      var Url = '<?= $this->url ?>/cpepdf/' + caja;
      window.open(Url, 'Pruebas', 'fullscreen=yes, scrollbars=auto');
    }
    if (tipoimpresora == 2) {
      var request = $.ajax({
        url: "<?= $this->url ?>/getimprimir/" + caja,
        method: "POST",
        dataType: "json"
      });
      request.done(function(msg) {
        let impresora = new Impresora(RUTA_API);
        impresora.setFontSize(1, 1);
        impresora.setAlign("center");
        impresora.write("CIERRRE CAJA DIARIO\n");
        impresora.write(msg.caja.descripcion + "\n");
        impresora.write("FECHA DE APERTURA: " + msg.caja.apertura + "\n");
        impresora.write('------------------------------------------------' + "\n");
        impresora.setAlign("left");
        impresora.write("ENCARGADO: " + msg.usuario.nombre + "\n");
        impresora.write("SALDO INICIAL: " + msg.caja.saldoinicial + "\n");
        impresora.write("SALDO EN EFECTIVO: " + msg.saldoefectivo + "\n");
        impresora.write('------------------------------------------------' + "\n");
        $.each(msg.empresas, function(index, value) {
          impresora.setAlign("center");
          if (value.tipo == 1) {
            impresora.write(value.razonsocial + "\n");
          } else {
            impresora.write(value.nombre + "\n");
            impresora.write(value.razonsocial + "\n");
          }
          impresora.write(value.distrito + " " + value.provincia + " " + value.departamento + "\n");
          impresora.write("RUC " + value.ruc + "\n");
          impresora.write('------------------------------------------------' + "\n");
          impresora.write('TIPO DE PAGO                           MONTOS' + "\n");
          impresora.write('------------------------------------------------' + "\n");
          impresora.setAlign("left");
          impresora.write("CONTATO: " + "\n");
          impresora.setAlign("right");
          impresora.write(value.contado + "\n");
          impresora.setAlign("left");
          impresora.write("CREDITO: " + "\n");
          impresora.setAlign("right");
          impresora.write(value.credito + "\n");
          impresora.setAlign("left");
          impresora.write("EFECTIVO: " + "\n");
          impresora.setAlign("right");
          impresora.write(value.efectivo + "\n");
          impresora.setAlign("left");
          impresora.write("TARJETA: " + "\n");
          impresora.setAlign("right");
          impresora.write(value.tarjeta + "\n");
        });
        impresora.setAlign("center");
        impresora.write("EL MUNDO ES DE QUIEN SE ATREVE\n");
        impresora.cut();
        impresora.end().then();
      });
      request.fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
      });
    }
  };

  function restaurarcaja(idcaja, empresa) {
    Lobibox.confirm({
      title: "¡Avertencia!",
      msg: "¿Estas seguro que de restablecer este CAJA?",
      buttons: {
        cancel: {
          text: 'Cancelar',
          'class': 'btn'
        },
        ok: {
          text: 'Restablecer',
          'class': 'btn btn-danger'
        },
      },
      callback: function($this, type) {
        if (type == 'ok') {
          $.ajax({
            url: "<?= $this->url ?>/restaurar",
            type: "POST",
            data: {
              idcaja: idcaja,
              empresa: empresa
            },
            dataType: 'JSON',
            success: function(data) {
              if (data.status) {
                Lobibox.notify('success', {
                  size: 'mini',
                  position: 'top right',
                  msg: 'Se restablecio la caja correctamente'
                });
                especifico();
              } else {
                Lobibox.alert('info', {
                  title: 'INFORMACION',
                  msg: 'Tienes una caja abierta'
                });
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              Lobibox.notify('error', {
                size: 'mini',
                position: 'top right',
                msg: 'Error al obtener datos de ajax.'
              });
            }
          });
        }
      }
    });
  }

  function stockcaja(idcaja) {
    $("#idcajaseleccionado").val(idcaja);
    $("#modal_stockcaja").modal("show");
    $("#title_stockcaja").text("CONTROL DE STOCK EN CAJA");
    tablastockcaja = $('#tablastockcaja').DataTable({
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
      "destroy": true,
      "ajax": {
        "url": "<?= $this->url ?>/ajax_stockcaja/" + idcaja,
        "type": "GET"
      },
    });
  }

  function DescargarCajaStock() {
    window.open("<?= $this->url ?>/ajax_descargarStockCaja/" + $("#idcajaseleccionado").val());
  }

  function DescargarMiniStock() {
    var Url = '<?= $this->url ?>/ajax_miniStockCaja/' + $("#idcajaseleccionado").val();
    window.open(Url, 'Pruebas', 'fullscreen=yes, scrollbars=auto');
  }
</script>