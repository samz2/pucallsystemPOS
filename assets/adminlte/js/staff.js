//for save method string
var save_method;
var table;
var tables;
$(document).ready(function() {
  $('#documento').attr('minLength', 8);
  $('#documento').attr('maxlength', 8);
  $("#nombre").mayusculassintildes();
  $("#apellido").mayusculassintildes();
  $("#direccion").mayusculassintildes();
  $('#documento').numeric();
  $('#tipodocumento').change(function(e) {
    if ($('#tipodocumento').val() == 'DNI') { //muestro el div1 y oculto los demas
      $('#documento').attr('minLength', 8);
      $('#documento').attr('maxlength', 8);
      $('#direccion').removeAttr('required').valid();
    } else if ($('#tipodocumento').val() == 'RUC') { //muestro el div2 y oculto los demas
      $('#documento').attr('minLength', 11);
      $('#documento').attr('maxlength', 11);
      $('#direccion').attr('required', 'required');
    }
  });
  $('#botoncito').on('click', function() {
    var tipo = $('#tipodocumento').val();
    var documento = $('#documento').val();
    $(this).button('loading');
    $.ajax({
      type:'POST',
      url: "staff/consulta_reniec",
      data: { "tipo": tipo, "documento": documento },
      success: function(data) {
        $('#botoncito').button('reset');
        document.getElementById('nombre').value = data.nombre;
        document.getElementById('apellido').value = data.apellido;
        document.getElementById('direccion').value = data.direccion;
      }
    });
  });
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
    //Feature control the processing indicator.
    "processing": true,
    //Feature control DataTables' server-side processing mode.
    "serverSide": true,
    //Initial no order.
    "order": [],
    // Load data for the table's content from an Ajax source
    "ajax": {
      "url": "staff/ajax_list",
      "type": "POST"
    },
    //Set column definition initialisation properties.
    "columnDefs": [{
      //last column
      "targets": [-1],
      //set not orderable
      "orderable": true,
    }],
  });
  //set input/textarea/select event when change value, remove class error and remove text help block
  $("input").change(function() {
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
  });
  $("textarea").change(function() {
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
  });
  $("select").change(function() {
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
  });
  jQuery(".opcion").select2({
    width: '100%'
  });
});

function add() {
  save_method = 'add';
  $('#form')[0].reset(); // reset form on modals
  $('.form-group').removeClass('has-error'); // clear error class
  $('.help-block').empty(); // clear error string
  $('#modal_form').modal('show'); // show bootstrap modal
  $('.modal-title').text('Crear Staff'); // Set Title to Bootstrap modal title
};

function save() {
  $('#btnSave').text('guardando...'); //change button text
  $('#btnSave').attr('disabled', true); //set button disable
  var url;
  if(save_method == 'add') {
    url = "staff/ajax_add";
    msgsuccess = "El registro fue creado exitosamente.";
    msgerror = "El registro no se pudo crear verifique las validaciones.";
  } else {
    url = "staff/ajax_update";
    msgsuccess = "El registro fue actualizado exitosamente.";
    msgerror = "El registro no se pudo actualizar. Verifique la operación";
  }
  // ajax adding data to database
  $.ajax({
    url : url,
    type: "POST",
    data: $('#form').serialize(),
    dataType: "JSON",
    success: function(data) {
      //if success close modal and reload ajax table
      if(data.status) {
          $('#modal_form').modal('hide');
          reload_table();
          Lobibox.notify('success', {
            size: 'mini',
            msg: msgsuccess
          });
      } else {
        for (var i = 0; i < data.inputerror.length; i++) {
          $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
          $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
        }
      }
      $('#btnSave').text('Guardar'); //change button text
      $('#btnSave').attr('disabled',false); //set button enable
    },
    error: function (jqXHR, textStatus, errorThrown) {
      Lobibox.notify('error', {
        size: 'mini',
        msg: msgerror
      });
      $('#btnSave').text('Guardar'); //change button text
      $('#btnSave').attr('disabled',false); //set button enable
    }
  });
};

function reload_table() {
  table.ajax.reload(null,false); //reload datatable ajax
};

function edit(id) {
  save_method = 'update';
  $('#form')[0].reset(); // reset form on modals
  $('.form-group').removeClass('has-error'); // clear error class
  $('.help-block').empty(); // clear error string
  //Ajax Load data from ajax
  $.ajax({
    url : "staff/ajax_edit/" + id,
    type: "GET",
    dataType: "JSON",
    success: function(data) {
      $('[name="id"]').val(data.id);
      $('[name="tipodocumento"]').val(data.tipodocumento);
      $('[name="documento"]').val(data.documento);
      $('[name="nombre"]').val(data.nombre);
      $('[name="apellido"]').val(data.apellido);
      $('[name="direccion"]').val(data.direccion);
      $('[name="telefono"]').val(data.telefono);
      $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
      $('.modal-title').text('Modificar Staff'); // Set title to Bootstrap modal title
    },
    error: function (jqXHR, textStatus, errorThrown) {
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
        url : "staff/ajax_delete/"+id,
        type: "POST",
        dataType: "JSON",
        success: function(data) {
          //if success reload ajax table
          $('#modal_form').modal('hide');
          reload_table();
          Lobibox.notify('success', {
            size: 'mini',
            msg: 'El registro fue eliminado exitosamente.'
          });
        },
        error: function (jqXHR, textStatus, errorThrown) {
          Lobibox.notify('error', {
            size: 'mini',
            msg: 'No se puede eliminar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
          });
        }
      });
    }
  });
};

function combo(combo) {
  save_method = 'addcombo';
  $('#form_combo')[0].reset(); // reset form on
  $('#staff').val(combo);
  $('#btnSaveCombo').text('GRABAR');
  $('.form-group').removeClass('has-error'); // clear error class
  $('.help-block').empty(); // clear error string
  $('#combo_form').modal('show'); // show bootstrap modal
  $('.modal-title').text('Crear Combinacion'); // Set Title to Bootstrap modal title
  cargar_combos(combo);
};

function cargar_combos(combo) {
  tables = $('#tabla_combo').DataTable({
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
    //Feature control the processing indicator.
    "processing": true,
    //Feature control DataTables' server-side processing mode.
    "serverSide": true,
    //Initial no order.
    "order": [],
    // Load data for the table's content from an Ajax source
    "ajax": {
      "url": "staff/ajax_list_combo/"+combo,
      "type": "POST"
    },
    //Set column definition initialisation properties.
    "columnDefs": [{
      //last column
      "targets": [-1],
      //set not orderable
      "orderable": true,
    }],
  });
};

function savecombo() {
  $('#btnSaveCombo').text('guardando...'); //change button text
  $('#btnSaveCombo').attr('disabled',true); //set button disable
  // ajax adding data to database
  $.ajax({
    url : 'staff/ajax_addcombo',
    type: "POST",
    data: $('#form_combo').serialize(),
    dataType: "JSON",
    success: function(data) {
      //if success close modal and reload ajax table
      if(data.status) {
          reload_tables();
          $('#form_combo')[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            msg: 'El registro fue creado exitosamente.'
          });
          $('#btnSaveCombo').text('GRABAR'); //change button text
          $('#btnSaveCombo').attr('disabled',false); //set button enable
      } else {
        for (var i = 0; i < data.inputerror.length; i++) {
          $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
          $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
        }
        $('#btnSaveCombo').text('GRABAR'); //change button text
        $('#btnSaveCombo').attr('disabled',false); //set button enable
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      Lobibox.notify('error', {
        size: 'mini',
        msg: 'El registro no se pudo crear verifique las validaciones.'
      });
      $('#btnSaveCombo').text('GRABAR'); //change button text
      $('#btnSaveCombo').attr('disabled',false); //set button enable
    }
  });
};

function borrarcombo(id) {
  bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
    if (result === true) {
      $.ajax({
        url : "staff/ajax_deletecombo/"+id,
        type: "POST",
        dataType: "JSON",
        success: function(data) {
          //if success reload ajax table
          reload_tables();
          Lobibox.notify('success', {
            size: 'mini',
            msg: 'El registro fue eliminado exitosamente.'
          });
        },
        error: function (jqXHR, textStatus, errorThrown) {
          Lobibox.notify('error', {
            size: 'mini',
            msg: 'No se puede eliminar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
          });
        }
      });
    }
  });
};

function reload_tables() {
  tables.ajax.reload(null,false); //reload datatable ajax
};
