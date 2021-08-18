<?= $this->session->flashdata('mensaje') ?>
<div class="row">
    <!-- /.col -->
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Seleccionar empresa</h3>
            </div>
            <form class="form-horizontal">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Empresa<span class="required">*</span></label>
                        <div class="col-sm-10">
                            <select id="empresa" name="empresa" class="form-control" required>
                                <?php foreach ($empresas as $value) { ?>
                                    <option value="<?= $value->id ?>"><?= $value->razonsocial ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filtro por fecha</h3>
            </div>
            <form class="form-horizontal">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Fecha</label>
                        <div class="col-sm-5">
                            <input type="date" class="form-control" id="finicio" name="finicio" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-sm-5">
                            <input type="date" class="form-control" id="factual" name="factual" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-center">
                    <a onclick="generado()" class="btn btn-warning" data-toggle="tooltip" title="GENERAR"><i class="fa fa-upload"></i></a>
                    <a onclick="pendiente()" class="btn btn-danger" data-toggle="tooltip" title="PENDIENTE"><i class="fa fa-upload"></i></a>
                    <a onclick="location.reload()" class="btn btn-success" data-toggle="tooltip" title="RECARGAR"><i class="fa fa-repeat"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filtro por cliente</h3>
            </div>
            <form class="form-horizontal">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Cliente</label>
                        <div class="col-sm-10">
                            <select id="cliente" name="cliente" class="js-states form-control" lang="es"></select>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-center">
                    <a onclick="generadoCliente()" class="btn btn-warning" data-toggle="tooltip" title="GENERAR"><i class="fa fa-upload"></i></a>
                    <!-- <a onclick="pendiente()" class="btn btn-danger" data-toggle="tooltip" title="PENDIENTE"><i class="fa fa-upload"></i></a> -->
                    <a onclick="location.reload()" class="btn btn-success" data-toggle="tooltip" title="RECARGAR"><i class="fa fa-repeat"></i></a>
                </div>
            </form>
        </div>
    </div>
    <!-- /.col -->
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Lista de <?= $this->titulo_controlador ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="panel-body table-responsive">
                <table id="tabla" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Numero</th>
                            <th>Nombre</th>
                            <th>Vence</th>
                            <th>Estado</th>
                            <th>Monto</th>
                            <th>Saldo</th>
                            <th>Accion</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <table id="tablaCliente" class="table table-striped table-bordered" style="display: none;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>CPE</th>
                            <th>Fecha credito</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Monto</th>
                            <th>Sub Total</th>
                            <th>Accion</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6"><b class="pull-rigth">TOTAL</b></th>
                            <th id="subTotal"></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>

<script type="text/javascript">
    //for save method string
    var table;
    var table_cobro;
    $(document).ready(function() {

        $("#cliente").select2({
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
            placeholder: 'Seleccione un cliente',
            theme: "classic",
            minimumInputLength: 2,
            ajax: {
                url: "<?= $this->url ?>/buscarCliente",
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

        generado();
        $('.money').number(true, 2);
        $('.enteros').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        $('#metodopago').change(function(e) {
            if ($('#metodopago').val() !== 'TARJETA') {
                $('#tipocard').hide();
                $('#numberoperacion').hide();
                $('#tipotarjeta').removeAttr('required');
                $('#operacion').removeAttr('required');
            } else {
                $('#tipocard').show();
                $('#numberoperacion').show();
                $('#tipotarjeta').attr('required', 'required');
                $('#operacion').attr('required', 'required');
            }
        });
        $('#pago').on('keyup', function() {
            var change = parseFloat($('#monto').val()) - parseFloat($(this).val());
            if (change < 0) {
                $('#ReturnChange span').text(change.toFixed(2));
                $('#ReturnChange span').addClass("red");
                $('#ReturnChange span').removeClass("light-blue");
            } else {
                $('#ReturnChange span').text(change.toFixed(2));
                $('#ReturnChange span').removeClass("red");
                $('#ReturnChange span').addClass("light-blue");
            }
        });
    });

    function generadoCliente() {
        $("#tabla_wrapper").hide();
        $("#tablaCliente").show();
        let cliente = $("#cliente").val();
        table = $('#tablaCliente').DataTable({
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
                "url": "<?= $this->url ?>/ajax_list_generadocobrocliente",
                "data": {
                    'empresa': $('#empresa').val(),
                    'cliente': cliente
                },
                "type": "POST"
            },
        });
    }

    function generado() {
        $("#tablaCliente").hide();
        $("#tabla_wrapper").show();
        table = $('#tabla').DataTable({
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
                "url": "<?= $this->url ?>/ajax_list_generadocobro",
                "data": {
                    'inicio': $('#finicio').val(),
                    'final': $('#factual').val(),
                    'empresa': $('#empresa').val()
                },
                "type": "POST"
            }
        });
    };

    function pendiente() {
        table = $('#tabla').DataTable({
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
                "url": "<?= $this->url ?>/ajax_list_pendientecobro/" + '/' + $('#empresa').val(),
                "type": "POST"
            },
        });
    };

    function verIngreso(id, tipo) {
        $("#tipocard").hide();
        $("#numberoperacion").hide();
        jQuery(".opcion").select2({
            width: '100%'
        });
        if (tipo == 0) {
            var controlador = 'ingresofaltante';
        } else {
            var controlador = 'ingresocredito';
        }
        $('#form_pago')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        //Ajax Load data from ajax
        $.ajax({
            url: "<?= $this->url ?>/" + controlador + '/' + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id"]').val(data.id);
                $('[name="monto"]').val(data.montoactual);
                $('#modal_pago').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('CUENTA X COBRAR'); // Set title to Bootstrap modal title
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: 'top right',
                    msg: 'Error al obtener datos de ajax.'
                });
            }
        });
    };

    function savePago() {
        $('#btnSavePago').text('guardando...'); //change button text
        $('#btnSavePago').attr('disabled', true); //set button disable
        // ajax adding data to database
        $.ajax({
            url: "<?= $this->url ?>/ajax_updatecobro",
            type: "POST",
            data: $('#form_pago').serialize(),
            dataType: "JSON",
            success: function(data) {
                //if success close modal and reload ajax table
                if (data.status) {
                    $('#modal_pago').modal('hide');
                    reload_table();
                    Lobibox.notify('success', {
                        size: 'mini',
                        position: 'top right',
                        msg: "El registro fue actualizado exitosamente."
                    });
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#btnSavePago').text('GUARDAR'); //change button text
                $('#btnSavePago').attr('disabled', false); //set button enable
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: 'top right',
                    msg: "El registro no se pudo actualizar. Verifique la operación"
                });
                $('#btnSavePago').text('GUARDAR'); //change button text
                $('#btnSavePago').attr('disabled', false); //set button enable
            }
        });
    };

    function verPagos(venta, tipo) {
        $('#cobro_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('VER PAGO'); // Set Title to Bootstrap modal title
        if (tipo == 0) {
            var controlador = 'ajax_list_faltantes';
        } else {
            var controlador = 'ajax_list_creditos';
        }
        cargar_pagos(venta, controlador);
    };

    function cargar_pagos(venta, controlador) {
        table_cobro = $('#tabla_cobro').DataTable({
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
                "url": "<?= $this->url ?>/" + controlador + "/" + venta,
                "type": "POST"
            },
        });
    };

    function imprimircomprobante(id, venta) {
        var Url = '<?= $this->url ?>/imprimircomprobante/' + id + "/" + venta;
        window.open(Url, 'Pruebas', 'fullscreen=yes, scrollbars=auto');
    };

    function reload_table() {
        table.ajax.reload(null, false); //reload datatable ajax
    };

    function reload_tablecobro() {
        table_cobro.ajax.reload(null, false); //reload datatable ajax
    };

    function borrarpagos(id) {
        bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
            if (result === true) {
                $.ajax({
                    url: "<?= $this->url ?>/ajax_deletecobro/" + id,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        //if success reload ajax table
                        reload_table();
                        reload_tablecobro();
                        Lobibox.notify('success', {
                            size: 'mini',
                            position: 'top right',
                            msg: 'El registro fue eliminado exitosamente.'
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Lobibox.notify('error', {
                            size: 'mini',
                            position: 'top right',
                            msg: 'No se puede eliminar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
                        });
                    }
                });
            }
        });
    };
</script>

<div class="modal fade" id="modal_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="form_pago" class="form-horizontal" rol="form" action="" method="post" autocomplete="off">
            <input type="hidden" class="form-control" id="id" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Monto</label>
                        <div class="col-sm-10">
                            <input readonly class="form-control" id="monto" name="monto">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="metodopago">Metodo de Pago*</label>
                        <div class="col-sm-10">
                            <select id="metodopago" name="metodopago" class="form-control">
                                <option value="EFECTIVO">EFECTIVO</option>
                                <option value="TARJETA">TARJETA</option>
                                <!-- <option value="DESCUENTO PLANILLA">DESCUENTO PLANILLA</option> -->
                            </select>
                            <span class="help-block"></span>
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
                                <option value="VISA">VISA</option>
                                <option value="DISCOVER">DISCOVER</option>
                                <option value="MASTERCARD">MASTERCARD</option>
                                <option value="DINERS CLUB">DINERS CLUB</option>
                                <option value="AMERICAN EXPRESS">AMERICAN EXPRESS</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group" id="numberoperacion">
                        <label class="col-sm-2 control-label">Numero de operacion</label>
                        <div class="col-sm-10">
                            <input type="text" name="operacion" class="form-control enteros" id="operacion">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="pago">Pago<span class="required">*</span></label>
                        <div class="col-sm-10">
                            <input class="form-control money" type="text" id="pago" name="pago">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group ReturnChange">
                        <h4 class="col-sm-12" id="ReturnChange">Deuda <span>0</span> Soles</h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
                    <button type="button" id="btnSavePago" onclick="savePago()" class="btn btn-primary">GUARDAR</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="cobro_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title text-dark">Lista de pagos</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="panel-body table-responsive">
                                <table id="tabla_cobro" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Fecha/Hora</th>
                                            <th>Monto</th>
                                            <th>Deuda</th>
                                            <th>Observacion</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <!-- /.box -->
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->