<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-border panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title text-dark" style="display:flex; justify-content:space-between; align-items:center">
                    <div>
                        LISTA DE TRANSPORTES PUBLICOS
                    </div>
                    <div>
                        <a onclick="location.reload()" class="btn btn-danger btn-sm" data-toggle="tooltip">RECARGAR <i class="fa fa-repeat"></i></a>
                        <a onclick="add()" class="btn btn-primary btn-sm" data-toggle="tooltip">NUEVO <i class="fa fa-plus"></i></a>
                    </div>
                </h3>
            </div>
            <!-- /.box-header -->
            <div class="panel-body table-responsive">
                <table id="tabla" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo documento</th>
                            <th>Documento</th>
                            <th>Razon social</th>
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

<script type="text/javascript">
    //for save method string
    var save_method;
    var table;
    $(document).ready(function() {
        $("#nombre").mayusculassintildes();
        //datatables
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
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?= $this->url ?>/ajax_list",
                "type": "POST"
            },
        });
        //set input/textarea/select event when change value, remove class error and remove text help block
        $("input").keyup(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).parent().removeClass('has-error');
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

        $('#botoncito').on('click', function() {
            if ($("#documento").val() == "") {
                $("#documento").parent().addClass("has-error");
                $("#documento").next().text("Esta campo es obligatorio.");
            } else {
                $(this).button('loading');
                $.ajax({
                    method: 'GET',
                    url: `https://apiperu.dev/api/ruc/${$('#documento').val()}?api_token=7460d2fa0d1d01c5fe9c96448ea0c3a1d800bae62461f6c27bfd48914e466e14`,
                    beforeSend: function() {
                        $('[name="razonsocial"]').val("");
                        $('[name="direccion"]').val("");
                        $('[name="razonsocial"]').val("");
                    },
                    success: function(data) {
                        $('#botoncito').button('reset');
                        if (data.success === true) {
                            $("#razonsocial").val(data.data.nombre_o_razon_social);
                            $("#direccion").val(data.data.direccion_completa);
                        } else {
                            Lobibox.notify('warning', {
                                size: 'mini',
                                position: "top right",
                                msg: "El RUC NO EXISTE"
                            });
                        }
                    },
                    error: function(data) {
                        $('#botoncito').button('reset');
                        Lobibox.notify('error', {
                            size: 'mini',
                            position: "top right",
                            msg: 'Error al obtener datos de ajax.'
                        });
                    }
                });
            }

        });

    });

    function add() {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('CREAR TRANSPORTE PUBLICO'); // Set Title to Bootstrap modal title
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
                    position: "top right",
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
        $('.help-block').empty(); // clear error string
        //Ajax Load data from ajax
        $.ajax({
            url: "<?= $this->url ?>/ajax_edit/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id"]').val(data.id);
                $('[name="documento"]').val(data.documento);
                $('[name="razonsocial"]').val(data.razonsocial);
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('MODIFICAR TRANSPORTE PUBLICO'); // Set title to Bootstrap modal title
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
</script>

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
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">R.U.C<span class="required">*</span></label>
                            <div class="col-md-9">
                                <input id="documento" name="documento" class="form-control enteros" type="text" maxlength="11">
                                <span class="help-block"></span>
                            </div>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" id="botoncito">
                                    <span class="fa fa-search"></span>
                                </button>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Razon social<span class="required">*</span></label>
                            <div class="col-md-9">
                                <input class="form-control" id="razonsocial" type="text" name="razonsocial">
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


<!-- End Bootstrap modal -->