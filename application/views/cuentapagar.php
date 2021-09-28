<?= $this->session->flashdata('mensaje') ?>
<div class="row">
    <!-- /.col -->
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filtro por fecha</h3>
            </div>
            <div class="panel-body">
                <form>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="mes">Fecha inicio <span class="required">*</span></label>
                                <input type="date" class="form-control" id="finicio" name="finicio" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="mes">Fecha fin <span class="required">*</span></label>
                                <input type="date" class="form-control" id="factual" name="factual" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="mes">Tiendas <span class="required">*</span></label>
                                <select class="form-control" id="tienda" name="tienda">
                                <option value="0">TODOS</option>
                                    <?php foreach($empresas as $empresa){ ?>
                                    <option value="<?=$empresa->id?>"><?=$empresa->ruc." | SERIE ".$empresa->serie." | ".$empresa->nombre?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="mes">Tipo <span class="required">*</span></label>
                                <select class="form-control" id="tipodeuda" name="tipodeuda">
                                    <option value="FLETE">FLETE</option>
                                    <option value="COMPRA">COMPRA</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="panel-footer text-center">
                <a onclick="generado()" class="btn btn-warning btn-sm" data-toggle="tooltip"><i class="fa fa-search"></i> BUSCAR</a>
                <a onclick="pendiente()" class="btn btn-danger btn-sm" data-toggle="tooltip"><i class="fa fa-clipboard"></i> PENDIENTE</a>
            </div>

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
                            <th><b>#</b></th>
                            <th><b>Tienda</b></th>
                            <th><b>Tipo</b></th>
                            <th><b>Proveedor</b></th>
                            <th><b>Documento</b></th>
                            <th><b>Monto</b></th>
                            <th><b>Saldo</b></th>
                            <th><b>Estado</b></th>
                            <th><b>Acción BTN</b></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>


<div class="modal fade" id="modal_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="form_pago" class="form-horizontal" rol="form" action="" method="post" autocomplete="off">
            <input type="hidden" class="form-control" id="id" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title text-center"></h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="tipodeudaformulario" name="tipodeudaformulario">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Monto</label>
                        <div class="col-sm-10">
                            <input readonly class="form-control" id="monto" name="monto">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Pago realizado</label>
                        <div class="col-sm-10">
                            <select id="pagorealizado" name="pagorealizado" class="form-control" onchange="operacion_pagorealizado()">
                                <option value="CAJA">CAJA</option>
                                <option value="EMPRESA">TIENDA</option>
                            </select>
                        </div>
                    </div>

                    <div class="row" id="content-tienda_pagar">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="tienda_pagar" class="col-sm-2 control-label">Tienda</label>
                                <div class="col-sm-10">
                                <select class="form-control" name="tienda_pagar" id="tienda_pagar" onchange="operaciontiendapagar()">
                                    <?php foreach ($empresas as $empresa) { ?>
                                        <option value="<?=$empresa->id?>"><?= $empresa->ruc . "  SERIE " . $empresa->serie . " | " . $empresa->nombre ?></option>
                                    <?php } ?>
                                </select>
                                <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group" id="content-caja">
                        <label class="col-sm-2 control-label">Cajas</label>
                        <div class="col-sm-10">
                            <select id="caja" name="caja" class="form-control">
                                <?= $cajas ?>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="metodopago">Metodo de Pago*</label>
                        <div class="col-sm-10">
                            <select id="metodopago" name="metodopago" class="form-control" onchange="metodopagooperacion()">
                                <option value="EFECTIVO">EFECTIVO</option>
                                <option value="TARJETA">TARJETA</option>
                                <option value="DEPOSITO">DEPOSITO</option>
                                <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                <!-- <option value="PAGO ANTERIOR">PAGO ANTERIOR</option> -->
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

<div class="modal fade" id="pagos_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title text-center"></h3>
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
                                <table id="tabla_pagos" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Operacion</th>
                                            <th>usuario</th>
                                            <th>T. Pago</th>
                                            <th>Observacion</th>
                                            <th>Fecha/Hora</th>
                                            <th>Monto</th>
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
</div>

<script type="text/javascript">
    //for save method string
    var table;
    var table_pago;
    $(document).ready(function() {
        generado();
        operacion_pagorealizado()
        $('.money').number(true, 2);
        $('.enteros').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        metodopagooperacion();
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

        $("input").keyup(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("textarea").keyup(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("select").change(function() {
            if (this.id == "concepto") {
                $(this).next().next().empty();
            } else {
                $(this).next().empty();
            }
            $(this).parent().parent().removeClass('has-error');
        });

    });

    function metodopagooperacion() {
        if ($('#metodopago').val() == 'EFECTIVO') {
            $('#tipocard').hide("fast");
            $('#numberoperacion').hide("fast");
            $('#tipotarjeta').removeAttr('required');
            $('#operacion').removeAttr('required');
        } else {
            if ($('#metodopago').val() == 'TARJETA') {
                $('#tipocard').show("fast");
                $('#tipotarjeta').attr('required', 'required');
            } else {
                $('#tipocard').hide("fast");
            }
            $('#numberoperacion').show("fast");
            $('#operacion').attr('required', 'required');
        }
    }


    function generado() {
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
            "ajax": {
                "url": "<?= $this->url ?>/ajax_list_generadopago/" + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#tipodeuda').val() + '/' + $("#tienda").val(),
                "type": "POST"
            },
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
                "url": "<?= $this->url ?>/ajax_list_pendientepago/" + $("#tienda").val() + '/' + $('#tipodeuda').val(),
                "type": "POST"
            },
        });
    };

    function pagardeuda(id, tipodeuda) {
        $("#tipocard").hide();
        $("#numberoperacion").hide();
        $('#form_pago')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        operacion_pagorealizado();
        $.ajax({
            url: "<?= $this->url ?>/ingresopago/" + id + "/" + tipodeuda + "/" + $("#tienda_pagar").val(),
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $("#tipodeudaformulario").val(tipodeuda);
                $('[name="id"]').val(data.id);
                $('[name="id"]').val(data.id);
                $('[name="monto"]').val(data.montoactual);
                $("#caja").empty();
                if (data.cajas.length > 0) {
                    for (value of data.cajas) {
                        $("#caja").append(`<option value = "${value.id}">${value.nombre} | ${value.descripcion}</option>`);
                    }
                } else {
                    $("#caja").append(`<option value = "0"> ¯\_(ツ)_/¯ LA TIENDA NO TIENE CAJAS REGISTRADAS </option>`);
                }
                $('#modal_pago').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('CUENTA POR PAGAR'); // Set title to Bootstrap modal title
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

    function savePago() {
        $('#btnSavePago').text('guardando...');
        $('#btnSavePago').attr('disabled', true);
        $.ajax({
            url: "<?= $this->url ?>/ajax_updatepago",
            type: "POST",
            data: $('#form_pago').serialize(),
            dataType: "JSON",
            success: function(data) {
                if (data.status) {
                    $('#modal_pago').modal('hide');
                    reload_table();
                    Lobibox.notify('success', {
                        size: 'mini',
                        position: "top right",
                        msg: "El registro fue actualizado exitosamente."
                    });
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#btnSavePago').text('GUARDAR');
                $('#btnSavePago').attr('disabled', false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: "top right",
                    msg: "El registro no se pudo actualizar. Verifique la operación"
                });
                $('#btnSavePago').text('GUARDAR');
                $('#btnSavePago').attr('disabled', false);
            }
        });
    };

    function verPagos(venta, tipodeuda) {
        $('#pagos_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('VER PAGOS'); // Set Title to Bootstrap modal title
        cargar_pagos(venta, tipodeuda);
    };

    function cargar_pagos(venta, tipodeuda) {
        table_pago = $('#tabla_pagos').DataTable({
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
                "url": "<?= $this->url ?>/ajax_list_pagos/" + venta + "/" + tipodeuda,
                "type": "POST"
            },
        });
    };

    function reload_table() {
        table.ajax.reload(null, false); //reload datatable ajax
    };

    function reload_tablepago() {
        table_pago.ajax.reload(null, false); //reload datatable ajax
    };

    function borrarpagos(id) {
        bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
            if (result === true) {
                $.ajax({
                    url: "<?= $this->url ?>/ajax_deletepagos/" + id,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        reload_tablepago();
                        reload_table();
                        Lobibox.notify('success', {
                            size: 'mini',
                            position: "top right",
                            msg: 'El registro fue eliminado exitosamente.'
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Lobibox.notify('error', {
                            size: 'mini',
                            position: "top right",
                            msg: 'No se puede eliminar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
                        });
                    }
                });
            }
        });
    };

    function proveedorExcel() {
        $.ajax({
            url: '<?= $this->url ?>/compra_excel',
            type: 'POST',
            success: function() {
                window.open('<?= $this->url ?>/compra_excel/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#usuario').val());
            },
        });
    };

    function operacion_pagorealizado() {
        let pagorealizado = $("#pagorealizado").val();
        if (pagorealizado == "CAJA") {
            $("#content-caja").show("fast");
        } else {
            $("#content-caja").hide("fast");
        }
    }
</script>