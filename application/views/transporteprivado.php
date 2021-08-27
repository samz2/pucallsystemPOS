<div class="row">
    <div class="col-xs-12">
        <div class="panel  panel-border-info">
            <div class="panel-heading">
                <h3 class="panel-title text-title-panel">Operaciones</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tipo</label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-lg-9">
                                    <select class="form-control" name="tipo" id="tipo" style="margin-left:6px">
                                        <option value="VEHICULO">VEHICULO</option>
                                        <option value="CONDUCTOR">CONDUCTOR</option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                                <div class="col-lg-3">
                                    <button onclick="generados()" id="botonprocesar" class="btn btn-warning">BUSCAR <i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-border panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-dark" style="display:flex; justify-content: space-between; align-items:center">
                    <div>
                        Lista de transportes privados
                    </div>
                    <div>
                        <a onclick="location.reload()" class="btn btn-danger btn-sm" data-toggle="tooltip">RECARGAR <i class="fa fa-repeat"></i></a>
                        <a onclick="add()" class="btn btn-primary btn-sm" data-toggle="tooltip">NUEVO <i class="fa fa-plus"></i></a>
                    </div>

                </h3>
            </div>
            <div class="panel-body table-responsive" id="content-table-vehiculo">
                <table id="tabla-vehiculo" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Numero de placa</th>
                            <th>Codigo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="panel-body table-responsive" id="content-table-conductor">
                <table id="tabla-conductor" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Tipo de documento</th>
                            <th>Documento</th>
                            <th>Codigo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title text-center"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal" autocomplete="off">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Tipo <span class="required">*</span></label>
                            <div class="col-md-9">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group" id="content-tipodocumento">
                            <label class="control-label col-md-3">Tipo de documento<span class="required">*</span></label>
                            <div class="col-md-9">
                                <select class="form-control" id="tipodocumento" name="tipodocumento">
                                    <option value="DNI">DNI</option>
                                    <option value="R.U.C">R.U.C</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group" id="content-documento">
                            <label class="control-label col-md-3">Documento<span class="required">*</span></label>
                            <div class="col-md-9">
                                <input class="form-control" id="documento" type="text" name="documento">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group" id="content-numeroplaca">
                            <label class="control-label col-md-3">N° Placa<span class="required">*</span></label>
                            <div class="col-md-9">
                                <input class="form-control" id="numeroplaca" type="text" name="numeroplaca">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">GUARDAR</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    //for save method string
    var save_method;
    var table;
    $(document).ready(function() {
        $("#nombre").mayusculassintildes();
        generados();
        $("input").keyup(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("textarea").keyup(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("select").change(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        tiporegistro();
    });

    function generados() {
        event.preventDefault();
        let tablaDataTable;
        if ($("#tipo").val() == "VEHICULO") {
            $('#content-table-vehiculo').show();
            $('#content-table-conductor').hide();
            tablaDataTable = $('#tabla-vehiculo');
        } else {
            $('#content-table-vehiculo').hide();
            $('#content-table-conductor').show();
            tablaDataTable = $('#tabla-conductor');
        }
        table = tablaDataTable.DataTable({
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
            "destroy" : true,
            "ajax": {
                "url": "<?= $this->url ?>/ajax_list/" + $("#tipo").val(),
                "type": "POST"
            },
        });
    }

    function add() {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        tiporegistro();
        $("#tipoproceso").preppend(`<select class="form-control" id="tipoproceso" name="tipoproceso" onchange="tiporegistro()">
        <option value="VEHICULO">VEHICULO</option><option value="CONDUCTOR">CONDUCTOR</option></select>`);
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('TRANSPORTES PRIVADOS'); // Set Title to Bootstrap modal title
    };

    function save() {
        $('#btnSave').text('GUARDANDO...'); //change button text
        $('#btnSave').attr('disabled', true); //set button disable
        var url;
        if (save_method == 'add') {
            url = "<?= $this->url ?>/ajax_add/" + save_method;
            msgsuccess = "El registro fue creado exitosamente.";
            msgerror = "El registro no se pudo crear verifique las validaciones.";
        } else {
            url = "<?= $this->url ?>/ajax_update/" + save_method;
            msgsuccess = "El registro fue actualizado exitosamente.";
            msgerror = "El registro no se pudo actualizar. Verifique la operación";
        }
        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data) {
                //if success close modal and reload ajax table
                if (data.status) {
                    $('#modal_form').modal('hide');
                    reload_table();
                    Lobibox.notify('success', {
                        size: 'mini',
                        position: 'top right',
                        msg: msgsuccess
                    });
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#btnSave').text('GUARDAR'); //change button text
                $('#btnSave').attr('disabled', false); //set button enable
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: 'top right',
                    msg: msgerror
                });
                $('#btnSave').text('GUARDAR'); //change button text
                $('#btnSave').attr('disabled', false); //set button enable
            }
        });
    };


    function reload_table() {
        table.ajax.reload(null, false); //reload datatable ajax
    };

    function edit(id) {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty();

        $.ajax({
            url: "<?= $this->url ?>/ajax_edit/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id"]').val(data.id);
                $('[name="tipoproceso"]').val(data.tipo);
                $("#tipodocumento").val(data.tipodocumento);
                data.tipo == "VEHICULO" ? $('#numeroplaca').val(data.documento) : $('#documento').val(data.documento);
                $("#tipoproceso").preppend(`<input value="${data.tipoproceso}" readonly> `);
                tiporegistro();
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text(`MODIFICAR ${data.tipo}`);
                console.log(data)
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    msg: 'Error al obtener datos de ajax.'
                });
            }
        });
    };

    function borrar(id) {
        bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
            if (result === true) {
                $.ajax({
                    url: "<?= $this->url ?>/ajax_delete/" + id,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        //if success reload ajax table
                        $('#modal_form').modal('hide');
                        reload_table();
                        Lobibox.notify('success', {
                            size: 'mini',
                            position: 'top right',
                            msg: 'El registro fue eliminado exitosamente.'
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Lobibox.notify('warning', {
                            size: 'mini',
                            msg: 'El registro esta siendo utilizados en otros.'
                        });
                    }
                });
            }
        });
    };


    function tiporegistro() {
        if ($("#tipoproceso").val() == "CONDUCTOR") {
            $("#content-tipodocumento").show("fast");
            $("#content-documento").show("fast");
            $("#content-numeroplaca").hide("fast");
        } else {
            $("#content-tipodocumento").hide("fast");
            $("#content-documento").hide("fast");
            $("#content-numeroplaca").show("fast");
        }

    }
</script>

<!-- End Bootstrap modal -->