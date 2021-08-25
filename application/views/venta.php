<div class="container">
  <div class="row" id="cuerpo_comprobante">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Filtro por fecha</h3>
        </div>
        <form class="form-horizontal" autocomplete="off">
          <div class="panel-body">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="fecha">Fecha<span class="required">*</span></label>
              <div class="col-sm-5">
                <input type="date" class="form-control" id="finicio" name="finicio" value="<?= date('Y-m-d') ?>">
              </div>
              <div class="col-sm-5">
                <input type="date" class="form-control" id="factual" name="factual" value="<?= date('Y-m-d') ?>">
              </div>
            </div>
            <div class="form-ghumanizeroup">
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
          <div class="panel-footer text-center">
            <a onclick="verventas()" class="btn btn-warning" data-toggle="tooltip">Buscar <i class="fa fa-search"></i></a>
            <a onclick="enviomasivo_documento_electronico()" class="btn btn-danger" data-toggle="tooltip">Envio masivo <i class="fa fa-upload"></i></a>
            <a onclick="location.reload()" class="btn btn-yahoo" data-toggle="tooltip" title="RECARGAR"><i class="fa fa-repeat"></i></a>
          </div>
        </form>
      </div>
    </div>
    <!-- /.col -->
    <div class="col-md-12" id="respuesta_proceso"></div>
    <hr>
    <div class="col-xs-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Lista de <?= $this->titulo_controlador ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="panel-body table-responsive">
          <table id="venta" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Caja</th>
                <th>Metodo pago</th>
                <th>Tipo</th>
                <th>Venta</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th>Sunat</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Acciones BTN</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal ticket -->
<div class="modal fade in" id="modal_ventadetalle" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">COMPROBANTE</h4>
      </div>
      <div class="modal-body">
        <div class="form-body table-responsive" id="modal-dataVenta"></div>
      </div>
      <div class="modal-footer" id="modal-fotter-cerrar">

      </div>
    </div>
  </div>
</div>
<!-- /.Modal -->

<!-- Modal ticket -->
<div class="modal fade" id="vernotas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">COMPROBANTE</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" autocomplete="off" method="post">
          <div class="form-body table-responsive" id="vernotasdetalle"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal -->

<script type="text/javascript">
  const RUTA_API = "http://localhost:8000";
  const $impresoraSeleccionada = document.querySelector("#impresoraSeleccionada");
  const refrescarNombreDeImpresoraSeleccionada = () => {
    Impresora.getImpresora()
      .then(nombreImpresora => {
        $impresoraSeleccionada.textContent = '80mm Series Printer';
      });
  };
  $(document).ready(function() {
    verventas();
    refrescarNombreDeImpresoraSeleccionada();
    $('.money').number(true, 2);
    $('#descripcion').mayusculassintildes();
    $('#form_notas').validate({
      onkeyup: false,
    });
    $('#operacion').attr('minLength', 4);
    $('#operacion').attr('maxlength', 4);
    $('#motivocreditos').hide();
    $('#motivodebitos').hide();
    $("#metodo").hide();
    $("#tipocard").hide();
    $("#numberoperacion").hide();
    $('#montos').hide();
    $('#tiponota').change(function(e) {
      if ($('#tiponota').val() == '1') {
        $('#motivocreditos').show();
        $('#motivodebitos').hide();
        $('#montos').hide();
        $("#metodo").hide();
        $("#tipocard").hide();
        $("#numberoperacion").hide();
      } else {
        $('#motivocreditos').hide();
        $('#motivodebitos').show();
        $('#montos').show();
        $("#metodo").show();
        if ($('#metodopago').val() == 'TARJETA') {
          $("#tipocard").show();
          $("#numberoperacion").show();
        }
      }
    });
    $('#metodopago').change(function(e) {
      if ($('#metodopago').val() == 'EFECTIVO') {
        $('#tipocard').hide();
        $('#numberoperacion').hide();
      } else {
        $('#tipocard').show();
        $('#numberoperacion').show();
      }
    });
  });

  function verventas() {
    table = $('#venta').DataTable({
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
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": "<?= $this->url ?>/verventas/" + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val(),
        "type": "GET"
      },
    });
  };

  function anular(id, empresa) {
    bootbox.prompt({
      title: "INGRESE EL MOTIVO PARA ANULAR ESTA VENTA",
      className: 'rubberBand animated',
      inputType: "text",
      callback: function(result) {
        if (result == "" || result == null) {
          /*
          Lobibox.notify('error', {
            size: 'mini',
            position:"top right",
            msg: 'Ingrese un motivo'
          });
          */
        } else {
          $.ajax({
            url: "<?= $this->url ?>/ajax_anular/" + id + "/" + empresa,
            type: "POST",
            dataType: "JSON",
            data: {
              result: result
            },
            success: function(data) {
              reload_table();
              Lobibox.notify('success', {
                size: 'mini',
                position: "top right",
                msg: 'El registro fue anulado exitosamente.'
              });
            },
            error: function(jqXHR, textStatus, errorThrown) {
              Lobibox.notify('error', {
                size: 'mini',
                position: "top right",
                msg: 'No se puede anular este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
              });
            }
          });
        }
      }
    });
  };

  function imprimir(venta, tipoimpresora) {
    if (tipoimpresora == 0) {
      $.ajax({
        url: '<?= $this->url ?>/imprimir/' + venta,
        type: 'POST',
      });
    }
    if (tipoimpresora == 1) {
      var Url = '<?= $this->url ?>/cpepdf/' + venta;
      window.open(Url, 'Pruebas', 'fullscreen=yes, scrollbars=auto');
    }
    if (tipoimpresora == 2) {
      var request = $.ajax({
        url: "<?= $this->url ?>/getimprimir/" + venta,
        method: "POST",
        dataType: "json"
      });
      request.done(function(msg) {
        let impresora = new Impresora(RUTA_API);
        impresora.setFontSize(1, 1);
        impresora.setAlign("center");
        if (msg.empresa.tipo == 0) {
          impresora.write(msg.empresa.nombre + "\n");
          impresora.write(msg.empresa.razonsocial + "\n");
        } else {
          impresora.write(msg.empresa.razonsocial + "\n");
        }
        if (msg.ventas.tipoventa !== 'OTROS') {
          impresora.write(msg.empresa.distrito + " " + msg.empresa.provincia + " " + msg.empresa.departamento + "\n");
          impresora.write("RUC " + msg.empresa.ruc + "\n");
          impresora.write("TELF. " + msg.empresa.telefono + "\n");
          impresora.write(msg.ventas.tipoventa + " DE VENTA ELECTRONICA\n");
        } else {
          impresora.write("TICKET DE VENTA\n");
        }
        impresora.write(msg.ventas.serie + "-" + msg.ventas.numero + "\n");
        impresora.setAlign("left");
        impresora.write(msg.cliente.tipo + ": " + msg.cliente.documento + "\n");
        impresora.write("CLIENTE: " + msg.cliente.nombre + " " + msg.cliente.apellido + "\n");
        impresora.write("DIRECCION: " + msg.cliente.direccion + "\n");
        if (msg.ventas.tipoventa !== 'OTROS') {
          impresora.write("FECHA DE EMISION: " + msg.ventas.created + " " + msg.ventas.hora + "\n");
          impresora.write("FECHA DE VENC.: " + msg.ventas.created + "\n");
          impresora.write("MONEDA: SOLES\n");
        }
        impresora.feed(2);
        impresora.write("[CANT.]   DESCRIPCION         P/U      TOTAL\n");
        $.each(msg.ventadetalle, function(index, value) {
          impresora.write(" " + value.nombre + "\n");
          impresora.write(" [ " + value.cantidad + " ]                     " + value.precio + "        " + value.subtotal + "\n");
        });
        impresora.setAlign("right");
        impresora.write("OP. EXONERADA: S/ " + msg.ventas.montototal + "\n");
        impresora.write("IGV: S/ 0.00\n");
        impresora.write("TOTAL: S/ " + msg.ventas.montototal + "\n");
        $.each(msg.pagos, function(index, value) {
          impresora.write(value.metodopago + ": S/ " + value.monto + "\n");
        });
        impresora.write("VUELTO: S/ " + msg.vuelto + "\n");
        impresora.setAlign("left");
        impresora.feed(2);
        impresora.write("IMPORTE EN LETRA: " + msg.importeletra + "\n");
        impresora.write("VENDEDOR: " + msg.usuario.nombre + " " + msg.usuario.apellido + "\n");
        if (msg.ventas.tipoventa !== 'OTROS') {
          impresora.write("HASH: " + msg.ventas.hash + "\n");
          impresora.setAlign("center");
          impresora.qr(msg.codigoqr);
        }
        impresora.setAlign("center");
        impresora.write("NO SE ACEPTAN DEVOLUCIONES Y/O\n");
        impresora.write("CAMBIOS DESPUES DE LAS 24 HORAS\n");
        impresora.feed(2);
        if (msg.ventas.tipoventa !== 'OTROS') {
          impresora.write("REPRESENTACION IMPRESA DE\n");
          impresora.write("COMPROBANTE ELECTRONICO\n");
          impresora.write("AUTORIZADO MEDIANTE LA RESOLUCION\n");
          impresora.write("DE INTENDENCIA N°. 034-005-0005315\n");
        } else {
          impresora.write("GRACIAS POR SU COMPRA\n");
        }
        impresora.end().then();
      });
      request.fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
      });
    }
  };

  function showTicket(id, metodopago) {
    $.ajax({
      url: "<?= $this->url ?>/printfcomprobante/" + id,
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        $('.modal-title').text('COMPROBANTE');
        $('#modal-dataVenta').html(data.htmlComprobante);
        $('#modal-fotter-cerrar').html(data.htmlFotter);
        $('#modal_ventadetalle').modal('show');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  };

  function notas(id) {
    $.ajax({
      url: "<?= $this->url ?>/notas/" + id,
      type: "POST",
      success: function(data) {
        $('#form_notas')[0].reset();
        $('#ventadetalle').html(data);
        $('#notas').modal('show');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert("error");
      }
    });
  };

  function reload_table() {
    table.ajax.reload(null, false); //reload datatable ajax
  };

  function vernotas(id) {
    $.ajax({
      url: "<?= $this->url ?>/vernotas/" + id,
      type: "POST",
      success: function(data) {
        $('#vernotasdetalle').html(data);
        $('#vernotas').modal('show');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert("error");
      }
    });
  };

  function procesar_documento_electronico(id) {
    var light = $('#cuerpo_comprobante').parent();
    $("#vernotas").modal('hide');
    $(light).block({
      message: '<div class="loader"></div> <p><br />Enviando data, espera un momento!...</p>',
      overlayCSS: {
        backgroundColor: '#fff',
        opacity: 0.8,
        cursor: 'wait'
      },
      css: {
        border: 0,
        padding: 0,
        backgroundColor: 'none'
      }
    });
    $.ajax({
      url: '<?= $this->url ?>/emitir/' + id,
      method: 'POST',
      dataType: "json",
    }).then(function(data) {
      if (data.respuesta == 'ok') {
        alertComprobantes();
        swal({
          title: 'Resultado',
          text: 'Su comprobante se ha procesado correctamente!',
          html: true,
          type: "success",
          confirmButtonText: "Ok",
          confirmButtonColor: "#2196F3"
        }, function() {
          $("#respuesta_proceso").html('<div class="alert alert-success alert-styled-left alert-arrow-left alert-bordered">\
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>\
        Su Documento se ha procesado correctamente...<br><br>\
        HASH: ' + data.hash_cpe + '</div>');

        });
      } else {
        swal({
          title: 'ERROR',
          text: data.mensaje,
          html: true,
          type: "error",
          confirmButtonText: "Ok",
          confirmButtonColor: "#2196F3"
        }, function() {
          $(light).unblock();
          $("#respuesta_proceso").html('<div class="alert alert-danger alert-styled-left alert-bordered">\
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>\
        ' + data.mensaje + '.\
        </div>');
        });
      }
      $(light).unblock();
      reload_table();
    }, function(reason) {
      $(light).unblock();
      console.log(reason);
    });
  };

  function enviomasivo_documento_electronico() {

    bootbox.confirm("Seguro desea emitir masivamente?", function(result) {
      if (result === true) {
        var light = $('#cuerpo_comprobante').parent();
        $(light).block({
          message: '<p><i class="fa fa-spinner fa-spin"></i> Enviando data, espera unos minutos por favor!...</p>',
          overlayCSS: {
            backgroundColor: '#fff',
            opacity: 0.8,
            cursor: 'wait'
          },
        });
        $.ajax({
          url: '<?= $this->url ?>/enviomasivo/' + $('#finicio').val() + '/' + $('#factual').val() + "/" + $("#empresa").val(),
          method: 'POST',
          dataType: "JSON",
        }).then(function(data) {
          if (data.respuesta == 'ok') {
            alertComprobantes();
            swal({
              title: 'Resultado',
              text: 'Su comprobante se ha procesado correctamente!',
              html: true,
              type: "success",
              confirmButtonText: "Ok",
              confirmButtonColor: "#2196F3"
            }, function() {
              $("#respuesta_proceso").html('<div class="alert alert-success alert-styled-left alert-arrow-left alert-bordered">\
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>\
        Su Documento se ha procesado correctamente...<br><br>\
        # comprobantes procesados: ' + data.procesado + '<br><br>\
        # comprobantes no procesados: ' + data.noprocesado + '</div>');
            });
          } else {
            swal({
              title: 'ERROR',
              text: 'No Existen comprobantes pendientes de envio',
              html: true,
              type: "error",
              confirmButtonText: "Ok",
              confirmButtonColor: "#2196F3"
            }, function() {
              $(light).unblock();
              $("#respuesta_proceso").html('<div class="alert alert-danger alert-styled-left alert-bordered">\
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>\
        No Existen comprobantes pendientes de envio.\
        </div>');
            });
          }
          $(light).unblock();
          reload_table();
        }, function(reason) {
          $(light).unblock();
          console.log(reason);
        });
      }
    });

  };

  function sentTicketWA(venta) {
    const phone = $("#telefonoWP").val();
    window.open("<?= $this->url ?>/sentTicketWA/" + phone + "/" + venta);
  }

  function sendMail(idventas) {
    let correo = $('#correo').val();
    $("#enviarcorreo").attr("disabled", true);
    $("#enviarcorreo").html("<i class='fa fa-spinner fa-spin'></i>");
    $.ajax({
      url: "<?= $this->url ?>/sendemail",
      data: {
        "idventa": idventas,
        "correo": correo
      },
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          $("#modal_ventadetalle").modal("hide");
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: 'Correo enviado'
          });
          $("#enviarcorreo").attr("disabled", false);
          $("#enviarcorreo").html("<i class='fa fa-paper-plane'></i>");
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        $("#enviarcorreo").attr("disabled", false);
        $("#enviarcorreo").html("<i class='fa fa-paper-plane'></i>");
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'Error al enviar Correo'
        });
      }
    });
  };
</script>
<!-- /.Modal -->

<!-- Modal ticket -->
<div class="modal fade" id="notas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="<?= $this->url . '/crearnotas' ?>" class="form-horizontal" autocomplete="off" method="POST" id="form_notas" role="form">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">NOTA CREDITO / DEBITO</h4>
        </div>
        <div class="modal-body">
          <h4><i class="fa fa-home"></i> Detalle del comprobante</h4>
          <hr>
          <div class="form-body table-responsive" id="ventadetalle"></div>
          <hr>
          <h4><i class="fa fa-home"></i> Detalle de la nota</h4>
          <hr>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="tiponota">Tipo Nota<span class="required">*</span></label>
            <div class="col-sm-10">
              <select id="tiponota" name="tiponota" class="form-control" required>
                <option value="">SELECCIONAR TIPO NOTA</option>
                <option value="1">NOTA CREDITO</option>
                <option value="2">NOTA DEBITO</option>
              </select>
            </div>
          </div>

          <div id="motivodebitos" class="form-group">
            <label class="col-sm-2 control-label" for="motivodebito">Motivo<span class="required">*</span></label>
            <div class="col-sm-10">
              <select id="motivodebito" name="motivodebito" class="form-control">
                <option value="">SELECCIONAR MOTIVO</option>
                <option value="01">INTERES POR MORA</option>
                <option value="02">AUMENTO EN EL VALOR</option>
                <option value="03">PENALIDADES</option>
              </select>
            </div>
          </div>

          <div id="motivocreditos" class="form-group">
            <label class="col-sm-2 control-label" for="motivocredito">Motivo<span class="required">*</span></label>
            <div class="col-sm-10">
              <select id="motivocredito" name="motivocredito" class="form-control">
                <option value="">SELECCIONAR MOTIVO</option>
                <option value="01">ANULACION DE LA OPERACION</option>
                <option value="02">ANULACION POR ERROR EN EL RUC</option>
                <option value="03">CORRECION POR ERROR EN LA DESCRIPCION</option>
                <option value="04">DESCUENTO GLOBAL</option>
                <option value="05">DESCUENTO POR ITEM</option>
                <option value="06">DEVOLUCION TOTAL</option>
                <option value="07">DEVOLUCION POR ITEM</option>
                <option value="08">BONIFICACION</option>
                <option value="09">DISMINUCION EN EL VALOR</option>
              </select>
            </div>
          </div>

          <div class="form-group" id="metodo">
            <label class="col-sm-2 control-label" for="metodopago">Metodo de Pago*</label>
            <div class="col-sm-10">
              <select id="metodopago" name="metodopago" class="form-control" required>
                <option value="EFECTIVO">EFECTIVO</option>
                <option value="TARJETA">TARJETA DE CREDITO</option>
              </select>
            </div>
          </div>

          <div class="form-group" id="tipocard">
            <label class="col-sm-2 control-label">Tipo Tarjeta</label>
            <div class="col-sm-10">
              <i class="fa fa-cc-visa fa-2x" id="visa" aria-hidden="true"></i>
              <i class="fa fa-cc-mastercard fa-2x" id="mastercard" aria-hidden="true"></i>
              <i class="fa fa-cc-amex fa-2x" id="amex" aria-hidden="true"></i>
              <i class="fa fa-cc-discover fa-2x" id="discover" aria-hidden="true"></i>
              <select class="form-control" name="tipotarjeta" id="tipotarjeta">
                <option value="VISA">VISA</option <option value="DISCOVER">DISCOVER</option>
                <option value="MASTERCARD">MASTERCARD</option>
                <option value="DINERS CLUB">DINERS CLUB</option>
                <option value="AMERICAN EXPRESS">AMERICAN EXPRESS</option>
              </select>
            </div>
          </div>

          <div class="form-group" id="numberoperacion">
            <label class="col-sm-2 control-label">Número de operacion</label>
            <div class="col-sm-10">
              <input type="text" name="operacion" class="form-control enteros" id="operacion">
            </div>
          </div>

          <div id="montos" class="form-group">
            <label class="col-sm-2 control-label" for="monto">Monto<span class="required">*</span></label>
            <div class="col-sm-10">
              <input class="form-control money" id="monto" type="text" name="monto" value="">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="descripcion">Descripcion<span class="required">*</span></label>
            <div class="col-sm-10">
              <input required class="form-control" id="descripcion" type="text" name="descripcion" value="">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn btn-primary" value="Crear">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- /.Modal -->