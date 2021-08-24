<script type="text/javascript">
  $(document).ready(function() {
    var clientesCD = [];
    ProductosArr = [];
    var tabledatosPedidosEnviados;
    <?php if ($this->caja) { ?>
      $('#metodopago').change(() => {
        if ($('#metodopago').val() == 'EFECTIVO') {
          $('#tipocard').hide("fast");
        } else {
          $('#tipocard').show("fast");
        }
      })
      datosProductosVenta();
      categoriaSeleccionar(); //todo: trae todas las categorias
      if ($("#tresPasos").val() == '1') {
        $("#content-referencia").show();
      }
      opcionesmenu();
      if ($("#cobradorCaja").val() == '1') {
        datosPedidosEnviados();
      } else {
        ventasReload() //? traemos todas las ventas pendientes
        drawDataProductoLS(null);
      }
    <?php } ?>
    var down = false;
    var scrollLeft = 0;
    var x = 0;
    $('#categoriasP').mousedown(function(e) {
      down = true;
      scrollLeft = this.scrollLeft;
      x = e.clientX;
    }).mouseup(function() {
      down = false;
    }).mousemove(function(e) {
      if (down) {
        this.scrollLeft = scrollLeft + x - e.clientX;
      }
    }).mouseleave(function() {
      down = false;
    });

    $(".limpiar").addClear();
    $('.money').number(true, 2);

    $("#clientes").autocomplete({
      source: "<?= $this->url ?>/autocompletar",
      minLength: 2,
      select: function(event, ui) {
        $("#cliente").val(ui.item.cliente);
        save();
      }
    });

    $("#tipoventa").change(() => {
      $("#tipoventa").parent().removeClass("has-error");
      $("#tipoventa").next().empty();
    });

    $("#codigodebarra").focus();

    /*
    $("#codigodebarra").autocomplete({
      source: "<?= $this->url ?>/autocompleteCodigoBarra",
      minLength: 2,
      select: function(event, ui) {
        if (ui.item.status) {
          $("#busquedacodigobarra").html(`<i class="fa fa-spin fa-spinner"></i>`);
          agregaarventa(ui.item.idproducto, ui.item.idcategoria, ui.item.precioproducto)
        } else {}

      }
    });
    */

    $("#codigodebarra").keypress(function() {

      let datacodigobarra = $("#codigodebarra").val();

      if (datacodigobarra.length > 1) {
        $("#busquedacodigobarra").html(`<i class="fa fa-spin fa-spinner"></i>`);
        $.ajax({
          url: "<?= $this->url ?>/ajax_codigodebarra",
          type: "POST",
          data: {
            codigodebarra: datacodigobarra,
          },
          dataType: "JSON",
          success: function(data) {
            if (data.consulta.status) {
              if (data.lote.status) {
                if (data.lote.totalotes > 1) {
                  //? MODAL
                  agregarAdicionales(data.consulta.idproducto);
                } else {
                  //? REGISTRO DIRECTO DE LOTE
                  agregaarventa(data.consulta.idproducto, data.consulta.precioproducto, {
                    statusvariante: false,
                    lote: data.lote.lote,
                    statuslote: true
                  }, "");
                }
              } else {
                agregaarventa(data.consulta.idproducto, data.consulta.precioproducto, {}, "");
              }
            } else {
              if (data.consulta.msg != "") {
                Lobibox.alert("info", {
                  title: "Informacion",
                  msg: data.consulta.msg
                })
              }
              $("#busquedacodigobarra").html(`<i class="fa fa-search"></i>`);
              $("#codigodebarra").val("");
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            Lobibox.notify('error', {
              size: 'mini',
              position: 'top right',
              msg: 'Codigo de error: codigodebarra. comunicarse con el administrador'
            });
          }
        });
      } else {
        $("#busquedacodigobarra").html(`<i class="fa fa-search"></i>`);
      }
    });

    $("#formapago").change(() => {
      if ($('#formapago').val() == 'CONTADO') {
        $('#metodo').show();
        $('#pagado').show();
        $("#vencimiento").hide();
        $('.ReturnChange').show();
      } else {
        $('#metodo').hide();
        $('#pagado').hide();
        $("#vencimiento").show();
        $('.ReturnChange').hide();
      }
    })

    $("input").keyup(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).parent().removeClass('has-error');
      $(this).next().empty();
    });

    $('#descuento').on('keyup', function() {
      var change = parseFloat($('#MontoPagar2 span').text()) - parseFloat($(this).val());
      if (change > 0) {
        $('#pago').val(change.toFixed(2));
        $('#pago').removeClass("red");
        $('#pago').addClass("light-blue");
      } else {
        $('#pago').val('0.00');
        $('#pago').addClass("red");
        $('#pago').removeClass("light-blue");
      }
    });

    $('#pago').on('keyup', function() {
      var change = parseFloat($(this).val()) - (parseFloat($('#MontoPagar2 span').text()) - parseFloat($('#descuento').val()));
      if (change > 0) {
        $('#ReturnChange span').text(change.toFixed(2));
        $('#ReturnChange span').removeClass("red");
        $('#ReturnChange span').addClass("light-blue");
      } else {
        $('#ReturnChange span').text('0.00');
        $('#ReturnChange span').addClass("red");
        $('#ReturnChange span').removeClass("light-blue");
      }
    });

    $("#searchProd").keyup(function(e) {
      /*if (e.keyCode == '13') {}*/
      let valor = $("#searchProd").val();
      if (valor == "") {
        $("#CategriaSeleccionar").show();
        $("#Categoriaproducto").hide();
        $("#CategoriaproductoTodos").hide();
      } else {
        $("#CategriaSeleccionar").hide();
        $("#Categoriaproducto").hide();
        $("#CategoriaproductoTodos").show();
        //! Busquedad
        var filter = $(this).val();
        $("#CategoriaproductoTodos #proname").each(function() {
          // If the list item does not contain the text phrase fade it out
          if ($(this).text().search(new RegExp(filter, "i")) < 0) {
            // Show the list item if the phrase matches
            $(this).parent().parent().parent().hide();
          } else {
            $(this).parent().parent().parent().show();
          }
        });
      }
    });

    $('#documento').attr('minLength', 8);
    $('#documento').attr('maxlength', 8);
    $("#nombre").mayusculassintildes();
    $(".libre").mayusculassintildes();
    $("#apellido").mayusculassintildes();
    $("#direccion").mayusculassintildes();
    $('#documento').numeric();
    $("#documento").prop("placeholder", "BUSCAR POR RENIEC");
    $('#tipo').change(function(e) {
      $("#documento").val("");
      $("#nombre").val("");
      $("#apellido").val("");
      $("#direccion").val("");
      $("#telefono").val("");
      $("#email").val("");
      if ($('#tipo').val() == 'DNI') { //muestro el div1 y oculto los demas
        $('#documento').attr('minLength', 8);
        $('#documento').attr('maxlength', 8);
        $('#nombre').siblings('label').text("Nombre");
        $('#apellido').parent().show();
        $("#documento").prop("placeholder", "BUSCAR POR RENIEC");
      } else if ($('#tipo').val() == 'RUC') { //muestro el div2 y oculto los demas
        $('#documento').attr('minLength', 11);
        $('#documento').attr('maxlength', 11);
        $('#nombre').siblings('label').text("Razón social");
        $('#apellido').parent().hide();
        $("#documento").prop("placeholder", "BUSCAR POR SUNAT");
      }
    });

    $('#cachIH').submit(function(event) {
      event.preventDefault();
      $('#aperturar').button('loading');
      $.ajax({
        url: "<?= $this->url ?>/openregister",
        type: "POST",
        data: {
          saldoinicial: $('#saldoinicial').val(),
          empresa: $("#empresaIH").val()
        },
        success: function(data) {
          window.location.href = "<?= $this->url ?>/aperturar/" + $("#empresaIH").val();
        },
        error: function(jqXHR, textStatus, errorThrown) {
          $('#aperturar').button('reset');
          Lobibox.notify('error', {
            size: 'mini',
            position: 'top right',
            msg: 'Error al obtener datos de ajax.'
          });
        }
      });
    });


    $('#botoncito').on('click', function() {
      $(this).button('loading');
      let tipo = $("#tipo").val() == "DNI" ? "dni" : "ruc";
      $.ajax({
        method: 'GET',
        url: `https://apiperu.dev/api/${tipo}/${$('#documento').val()}?api_token=7460d2fa0d1d01c5fe9c96448ea0c3a1d800bae62461f6c27bfd48914e466e14`,
        beforeSend: function() {
          $('[name="nombre"]').val("");
          $('[name="direccion"]').val("");
          $('[name="apellido"]').val("");
        },
        success: function(data) {
          $('#botoncito').button('reset');
          if (data.success === true) {
            if ($("#tipo").val() == "DNI") {
              $("#nombre").val(data.data.nombres);
              $("#apellido").val(data.data.apellido_paterno + " " + data.data.apellido_materno);
            } else {
              $("#nombre").val(data.data.nombre_o_razon_social);
              $("#direccion").val(data.data.direccion_completa);
            }
          } else {
            let dataMSG = $("#tipo").val() == "DNI" ? "El DNI EXISTE" : "El RUC NO EXISTE";
            Lobibox.notify('warning', {
              size: 'mini',
              position: "top right",
              msg: dataMSG
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


    });

    $('#botoncito2').on('click', function() {
      $(this).button('loading');
      $.ajax({
        method: 'post',
        url: "<?= $this->url ?>/consulta_reniec",
        data: {
          tipo: $('#tipo2').val(),
          documento: $('#documento2').val()
        },
        beforeSend: function() {
          $('[name="nombre2"]').val("");
          $('[name="apellido2"]').val("");
          $('[name="direccion2"]').val("");
        },
        success: function(data) {
          $('#botoncito2').button('reset');
          nombre = data.nombre;
          apellido = data.apellido;
          direccion = data.direccion;
          $('[name="nombre2"]').val(nombre);
          $('[name="apellido2"]').val(apellido);
          $('[name="direccion2"]').val(direccion);
        },
        error: function(data) {
          $('#botoncito2').button('reset');
          //toast.error("Algo inesperado ha sucedido");
          Lobibox.notify('warning', {
            size: 'mini',
            position: "top right",
            msg: 'Nuestros servidores estan en mantenimiento.'
          });
        }
      });
    });

  });

  function OpenRegister(estado, tienda) {
    if (estado == 1) {
      $("#empresaIH").val(tienda);
      $('#CashinHand').modal('show');
    } else {
      window.location.href = "<?= $this->url ?>/aperturar/" + tienda;
    }
  };



  function grabarcliente() {
    $('#cliente_form').modal('show');
    $('.modal-title').text('CREAR CLIENTE');
  };

  function savecliente() {
    $('#btnSavecliente').text('GUARDANDO...'); //change button text
    $('#btnSavecliente').attr('disabled', true); //set button disable
    // ajax adding data to database
    $.ajax({
      url: '<?= $this->url ?>/ajax_addcliente',
      type: "POST",
      data: $('#form_cliente').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          $('#cliente_form').modal('hide');
          $('#form_cliente')[0].reset();
          $("#cliente").val(data.cliente);
          $("#clientes").val(data.clientes);
          //toast.success("El registro fue creado exitosamente");
          save();
          Lobibox.notify('success', {
            size: 'mini',
            position: 'top right',
            msg: 'El registro fue creado exitosamente.'
          });
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
        }
        $('#btnSavecliente').text('GUARDAR'); //change button text
        $('#btnSavecliente').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        toast.error("Algo inesperado ha sucedido");
        // Lobibox.notify('error', {
        //   size: 'mini',
        //   msg: 'El registro no se pudo crear verifique las validaciones.'
        // });
        $('#btnSavecliente').text('GUARDAR'); //change button text
        $('#btnSavecliente').attr('disabled', false); //set button enable
      }
    });
  };

  function cambiarproceso(tipoproceso) {
    $("#tipoproceso").val(tipoproceso);
    if (tipoproceso == "1") {
      document.getElementById("montototalcaja").focus();
    } else {}

  }


  function savecliente2(identificador, idventa) {
    $('#btnSavecliente2').text('guardando...'); //change button text
    $('#btnSavecliente2').attr('disabled', true); //set button disable
    // ajax adding data to database
    $.ajax({
      url: '<?= $this->url ?>/ajax_addcliente2',
      type: "POST",
      data: $('#form_cliente2').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          $('#cliente_form2').modal('hide');
          $('#form_cliente2')[0].reset();
          $(`#cliente${identificador}`).val(data.cliente);
          $(`#clientes${identificador}`).val(data.clientes);
          //TODO: Insertando al localStorage
          let clientesCD = JSON.parse(localStorage.getItem(`cuentadividida_${idventa}`));
          let dataselect = document.querySelector(`#tipoventa${identificador}`);
          clientesCD.forEach(function(value, index) {
            if (value.indice == identificador) {
              value.cliente = data.cliente;
              value.textocliente = data.clientes
            }
          });
          localStorage.setItem(`cuentadividida_${idventa}`, JSON.stringify(clientesCD));
          //TODO: ------END-----
          //toast.success("El registro fue creado exitosamente");
          Lobibox.notify('success', {
            size: 'mini',
            position: 'top right',
            msg: 'El registro fue creado exitosamente.'
          });
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
        }
        $('#btnSavecliente2').text('GRABAR'); //change button text
        $('#btnSavecliente2').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        toast.error("Algo inesperado ha sucedido");
        // Lobibox.notify('error', {
        //   size: 'mini',
        //   msg: 'El registro no se pudo crear verifique las validaciones.'
        // });
        $('#btnSavecliente2').text('GRABAR'); //change button text
        $('#btnSavecliente2').attr('disabled', false); //set button enable
      }
    });
  };

  function inicio() {
    $.ajax({
      url: "<?= $this->url ?>/ajax_inicio",
      type: "POST",
      success: function(data) {
        if (data.status) {

        }
        location.reload(true);
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

  function comandar() {
    $.ajax({
      url: "<?= $this->url ?>/GenerarPedido",
      type: "POST",
      success: function(data) {
        $('#listaproducto').load("<?= $this->url ?>/listaproducto");
        $('#generarpedido').button('reset');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        toast.error("Algo inesperado ha sucedido");
        // Lobibox.notify('error', {
        //   size: 'mini',
        //   msg: 'Error al obtener datos de ajax.'
        // });
        $('#generarpedido').button('reset');
      }
    });
  };

  function delete_posale(id) {
    // ajax delete data to database
    $.ajax({
      url: "<?= $this->url ?>/delete/" + id,
      type: "POST",
      dataType: "JSON",
      success: function(data) {},
      error: function(jqXHR, textStatus, errorThrown) {
        toast.error("Algo inesperado ha sucedido");
        // Lobibox.notify('error', {
        //   size: 'mini',
        //   msg: 'Error al obtener datos de ajax.'
        // });
      }
    });
  };

  //TODO -----------------------------------------------------------------------------------------------------------
  //!                  RECORDAR EN LOCALSTORAGE SE GUARDA LA SIGUIENTE IFORMACION EN EL SIGUIENTE ORDEN           -
  //!                   [IDRODUCTO, CANDTIDA DEL PRODUCTO A LLEVADR, OBSERVACION DEL PRODUCTO/OPCION ]            -
  //TODO -----------------------------------------------------------------------------------------------------------
  function addoptions(producto, venta, correlativo) {
    let dataStorage = JSON.parse(localStorage.getItem(`${venta}_${correlativo}`));
    $('#form_options')[0].reset(); // reset form on modals
    $('[name="detalle"]').val(producto);
    $(`[name="CorrelativoIdentif"]`).val(correlativo);
    $(`[name="VentaModalOpcion"]`).val(venta);
    $('[name="CantidadModalOpcion"]').val(dataStorage[1]);
    $('[name="precioModalOpcion"]').val(dataStorage[3]);
    $('[name="opcion"]').val(dataStorage[2]);
    $('#options').modal('show');
    $('.modal-title').text('AGREGAR OPCION');
  };



  function CantidadProductokeyup(event, key_primary) {

    let TeclaPresionado = event.keyCode || event.which;

    if (TeclaPresionado === 8) {
      let StockRestablecer = $(`#CantidadProductoAnterior-${key_primary}`).val() - ($(`#CantidadProductoActual-${key_primary}`).val() == "" ? 0 : $(`#CantidadProductoActual-${key_primary}`).val());

      console.log("restablecer " + StockRestablecer);

    } else if ((TeclaPresionado >= 48 && TeclaPresionado <= 57) || (TeclaPresionado >= 96 && TeclaPresionado <= 105)) {

      let StockDescontar = $(`#CantidadProductoActual-${key_primary}`).val() - $(`#CantidadProductoAnterior-${key_primary}`).val();
      console.log("Descontar Stock " + StockDescontar)

    } else {

    }


  }

  function CantidadProductoDown(event, key_primary) {

    let TeclaPresionado = event.keyCode || event.which;
    if ((TeclaPresionado >= 48 && TeclaPresionado <= 57) || (TeclaPresionado >= 96 && TeclaPresionado <= 105)) {

      //? guardamos la cantidad en cantidadanteriror antes que sea modificado
      let cantidadactual = $(`#CantidadProductoActual-${key_primary}`).val() == "" ? 0 : $(`#CantidadProductoActual-${key_primary}`).val();
      $(`#CantidadProductoAnterior-${key_primary}`).val(cantidadactual);

    } else if (TeclaPresionado === 8) {
      //? guardamos la cantidad en cantidadanteriror antes que sea eliminado
      let cantidadactual = $(`#CantidadProductoActual-${key_primary}`).val();
      $(`#CantidadProductoAnterior-${key_primary}`).val(cantidadactual);

    }


  }

  function CantidadProductoPress(event, key_primary) {

  }


  function addPoptions() {
    //TODO: En el eleemnto "detalle" va el id del producto
    let opcion = $("#opcion").val();
    let idproducto = $("#detalle").val();
    let idventa = $("#VentaModalOpcion").val();
    let cantidadProducto = $("#CantidadModalOpcion").val();
    let precioModalOpcion = $("#precioModalOpcion").val();
    let correlativo = $("#CorrelativoIdentif").val();
    let DatasStorage = JSON.parse(localStorage.getItem(`${idventa}_${correlativo}`));
    let DataAddStorage = JSON.stringify([idproducto, cantidadProducto, opcion, precioModalOpcion]);
    localStorage.setItem(`${idventa}_${correlativo}`, DataAddStorage);
    let DatasStorageUpdate = JSON.parse(localStorage.getItem(`${idventa}_${correlativo}`));
    $(`#pooptions${idproducto}`).text(DatasStorageUpdate[2]);
    $('#options').modal('hide');
    $('#form_options')[0].reset();
  };

  function saleBtn() {
    let idventa = $("#ventaseleccionada").val();
    $('#vendiendo').html("<i class='fa fa-spinner fa-spin'></i>");
    $('#vendiendo').attr("disabled", true);
    var subtotal = parseFloat($(`#MontoPagar2 span`).text() - $(`#descuento`).val());

    if ($('#pago').val() != "") {
      var pago = parseFloat($('#pago').val(), 10);
      if (pago < subtotal) {
        $("#pago").closest('div').addClass("has-error");
        $('#pago').siblings('span').text("Debe colocar un monto mayor o igual que el total");
        $('#vendiendo').html("Enviar");
        $('#vendiendo').attr("disabled", false);
      } else {
        $("#pago").closest('div').removeClass("has-error");
        $('#pago').siblings('span').text("");
        $('#vendiendo').html("<i class='fa fa-spinner fa-spin'></i>");
        $('#vendiendo').attr("disabled", true);
        $('#clientes').val("00000000 | CLIENTES VARIOS | ");
        EnviarPedido(idventa); // TODO: Registra en ventadetalle
      }
    } else {
      $("#pago").closest('div').addClass("has-error");
      $('#pago').siblings('span').text("Debe colocar un monto mayor o igual que el total");
      $('#vendiendo').html("Enviar");
      $('#vendiendo').attr("disabled", false);
    }

  };


  function EnviarPedido(idventa) {
    var DeudaTotal = 0;
    var CantidadItem = 0;
    //TODO:-------------------------------Guardamos los produtos en ventadetalle------------------------------------------------
    let key = localStorage.getItem(`productos_${idventa}`);
    if (key != null) {
      let ProductosDataLS = JSON.parse(key);
      $.ajax({
        url: "<?= $this->url ?>/ajax_procesarVenta",
        type: "POST",
        data: {
          "idventa": idventa,
          "dataproductos": JSON.stringify(ProductosDataLS),
          'formapago': $(`#formapago`).val(),
          'metodopago': $(`[name="metodopago"]`).val(),
          'descuento': parseFloat($(`[name="descuento"]`).val()),
          'pago': parseFloat($(`[name="pago"]`).val()),
          'tipotarjeta': $(`[name="tipotarjeta"]`).val(),
          'operacion': $(`[name="operacion"]`).val(),
          'vence': $(`[name="vence"]`).val(),
          'fecha': $(`[name="fecha"]`).val(),
          'deuda': parseFloat($("#MontoPagar2 span").text())
        },
        dataType: "JSON",
        success: function(data) {
          if (data.proceso.status) {
            // true sin problemas.
            if (data.proceso.validate) {
              eliminarDataLS(idventa);
              DatosSecundariosPedido(); //TODO: Cuenta todos los datos de la parte de bottom del va venta
              if ($(`#formapago`).val() == "CREDITO") {
                Lobibox.notify('success', {
                  size: 'mini',
                  position: "top right",
                  msg: 'El credito fue creado correctamente.'
                });
              } else {
                $('#comprobante').modal('show');
                $('.modal-title').text('COMPROBANTE');
                $('#modal-dataVenta').html(data.proceso.htmlcomprobante.htmlComprobante);
                $('#modal-fotter-cerrar').html(data.proceso.htmlcomprobante.htmlFotter);
              }

              if ($("#cobradorCaja").val() == '1') {
                datosPedidosEnviados();
                $("#opcionmenu").show();
                $("#contenedorPedidosVenta").show();
                $("#opcionmenuPedidoVenta").hide();
                $("#contenedorProcesoPago").hide();
              }
              ventasReload();
              alertComprobantes(); //Busca si hay mas Boletas/Facturas sin emitir
              $('#form_vender')[0].reset();
              $('#AddSale').modal('hide');
            } else {
              for (var i = 0; i < data.proceso.contenido.inputerror.length; i++) {
                $('[name="' + data.proceso.contenido.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                $('[name="' + data.proceso.contenido.inputerror[i] + '"]').next().text(data.proceso.contenido.error_string[i]); //select span help-block class set text error string
              }
            }
            $('#vendiendo').text('Enviar');
            $('#vendiendo').attr("disabled", false);
          } else {
            $("#AddSale").modal("hide");
            eliminarDataLS(idventa);
            ventasReload();
            Lobibox.alert("info", {
              title: "Informacion",
              msg: data.proceso.msg,
            });
          }

        },
        error: function(jqXHR, textStatus, errorThrown) {
          Lobibox.notify('error', {
            size: 'mini',
            position: "top right",
            msg: 'Codigo de error: ajax_EnviarPedido, contactarse con pucallsystem'
          });
          $('#vendiendo').text('Enviar');
          $('#vendiendo').attr("disabled", false);
        }
      });

    }
  }

  function CrearNewVenta() {
    $.ajax({
      url: "<?= $this->url ?>/ajax_CrearNewVenta",
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        ventasReload(); //? recargamos todas las ventas
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  }


  function printfcomprobante(idventas) {

    $('#comprobante').modal('show');
    $('.modal-title').text('COMPROBANTE');

    $.ajax({
      url: "<?= $this->url ?>/printfcomprobante",
      data: {
        "idventa": idventas
      },
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        $('#dataventadetalle').html(data.html);
        $('#fotter-cerrar').html(data.fotter);
        $("#SendMail").html(data.email);
        $("#SendWP").html(data.whatsapp);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'Error al obtener datos de ajax'
        });
      }
    });

  };

  function sendMail(idventas) {
    var correo = $('#correo').val();
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
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: 'Correo enviado'
          });
        } else {
          Lobibox.notify('warning', {
            size: 'mini',
            position: "top right",
            msg: 'Ocurrio un problema. vuelve a intentarlo'
          });
        }
        $("#enviarcorreo").attr("disabled", false);
        $("#enviarcorreo").html("<i class='fa fa-paper-plane'></i>");
      },
      error: function(jqXHR, textStatus, errorThrown) {
        $("#enviarcorreo").attr("disabled", false);
        $("#enviarcorreo").html("<i class='fa fa-paper-plane'></i>");

      }
    });
  };



  function imprimircomprobante(tipoimpresora, venta) {
    if (tipoimpresora === 0) {
      $.ajax({
        url: '<?= $this->url ?>/imprimircomprobante',
        type: 'POST',
        data: {
          'venta': venta
        }
      });
    }
    if (tipoimpresora === 1) {
      var Url = '<?= $this->url ?>/showcomprobante/' + venta;
      window.open(Url, 'Pruebas', 'fullscreen=yes, scrollbars=auto');
    }
  };

  function opcionesmenu() {
    $.ajax({
      url: "<?= $this->url ?>/opcionmenu",
      type: "POST",
      dataType: "json",
      success: function(data) {
        $(`#opcionmenu`).html(data.html);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        //toast.error("Ocurrió algo inesperado");
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });


  }

  function salircaja() {
    $.ajax({
      url: "<?= $this->url ?>/ajax_salir_caja",
      type: "POST",
      dataType: "json",
      success: function(data) {
        location.reload();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'Error al obtener datos de ajax caja.'
        });
      }
    });
  }

  function CloseRegister() {
    event.preventDefault();
    let totalventa = $("#ventaspendientes").children().length; // le ponemos meenos uno por que el boton + tiene tambien esa clase
    if (totalventa == 0) {
      $("#boton-CloseRegister").attr("disabled", true);
      $("#boton-CloseRegister").html(`<i class="fa fa-spin fa-spinner" style="font-size:30px"></i>`);

      $.ajax({
        url: "<?= $this->url ?>/CloseRegister",
        type: "POST",
        dataType: "json",
        success: function(data) {
          $("#boton-CloseRegister").attr("disabled", false);
          $("#boton-CloseRegister").html(`<i class="fa fa-times"></i>`);
          $('#closeregsection').html(data.data);
          $('#CloseRegister').modal('show');
          $("input").keyup(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).parent().removeClass('has-error');
            $(this).next().empty();
          });
        },
        error: function(jqXHR, textStatus, errorThrown) {
          $("#boton-CloseRegister").attr("disabled", false);
          $("#boton-CloseRegister").html(`<i class="fa fa-times"></i>`);
          Lobibox.notify('error', {
            size: 'mini',
            position: "top right",
            msg: 'Codigo de error de ajax: CloseRegister'
          });
        }
      });

    } else {
      Lobibox.alert("info", {
        title: "Informacion",
        msg: "No tiene que haber ventas"
      })
    }
  };

  function SubmitRegister() {
    if ($("#montototalcaja").val() != "") {
      $('#cerrarcaja').attr('disabled', true);
      $('#cerrarcaja').html('<i class="fa fa-spinner fa-spin"></i>');
      $("#montototalcaja").parent().removeClass("has-error");
      $("#montototalcaja").next().text("");
      Lobibox.confirm({
        title: "¡Avertencia!",
        msg: "¿Estas seguro que deseas cerrar la CAJA?",
        buttons: {
          cancel: {
            text: 'CANCELAR',
            'class': 'btn'
          },
          ok: {
            text: 'ACEPTAR',
            'class': 'btn btn-danger'
          },
        },
        callback: function($this, type) {
          if (type == 'ok') {
            $.ajax({
              url: "<?= $this->url ?>/SubmitRegister",
              type: "POST",
              data: $('#form_cierre').serialize(),
              dataType: "JSON",
              success: function(data) {
                if (data.status) {
                  //? El perfil de 3 es caja
                  if (data.usuarioperfil == 1 || data.usuarioperfil == 2) {
                    printfcierre(data.tipoimpresora, data.idcaja); //? le muestra el reporte al cerrar caja
                  }
                  location.reload();
                } else {
                  $('#cerrarcaja').attr('disabled', false);
                  $('#cerrarcaja').text('CERRAR CAJA');
                  for (var i = 0; i < data.inputerror.length; i++) {
                    $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                    $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                  }
                }
              }
            })
          } else {
            $('#cerrarcaja').attr('disabled', false);
            $('#cerrarcaja').text('CERRAR CAJA');
          }
        }
      });
    } else {
      $("#montototalcaja").parent().addClass("has-error");
      $("#montototalcaja").next().text("Esta campo es obligatorio");
    }

  };

  function printfcierre(tipoimpresora, idcaja) {
    if (tipoimpresora === '0') {
      $.ajax({
        url: '<?= $this->url ?>/imprimircierre/' + idcaja,
        type: 'POST',
      });
    }
    if (tipoimpresora === '1') {
      var Url = '<?= $this->url ?>/showcierre/' + idcaja;
      window.open(Url, 'Pruebas', 'fullscreen=yes, scrollbars=auto');
    }
  };

  //TODO----------------------------------------------------------------------------------------------------------------------------------------
  function agregarnewventa() {
    $("#agregarnewventa").attr("disabled", true);
    $("#ProcesarVenta").attr("disabled", true);
    $("#agregarnewventa").html("<i class='fa fa-spinner fa-spin'></i>");
    QuitarResalteDeVentaOld();
    $.ajax({
      url: "<?= $this->url ?>/ajax_newventa",
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        $("#cliente").val(data.cliente);
        $("#clientes").val(data.clientes);
        $("#ventaspendientes").append(drawNewVenta(data.idventa, data.totalventa));
        $("#ventaseleccionada").val(data.idventa);
        drawDataProductoLS(null);
        DatosVenta();
        $("#agregarnewventa").attr("disabled", false);
        $("#agregarnewventa").html("<i class='fa fa-plus'></i>");
        resaltar();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: 'top right',
          msg: 'Error al obtener datos de ajax.'
        });
        $("#agregarnewventa").attr("disabled", false);
        $("#agregarnewventa").html("<i class='fa fa-plus'></i>");
      }
    });
  }

  function drawNewVenta(idventa, totalventa) {

    $("#ventaspendientes").children().removeClass("selectedGat");
    $("#ventaseleccionada").val(idventa);
    let datahtml = ` <button style="border-top:0px; border-left:0px; border-right:0px" id="venta_${idventa}" class="categories selectedGat" onclick="traerpedidosventa(${idventa})">
    <input value="${idventa}" type="hidden">
    VENTA ${totalventa}
    </button> `;
    return datahtml;

  }

  function resaltar() {
    $('.help-block').empty(); // clear error string
    $("#tipoventa").closest('div').removeClass("has-error"); //clear error 

  }

  function traerpedidosventa(idventa) {
    QuitarResalteDeVentaOld(); //Antese que se cambie el id de la venta Demarcamos los productos de esa venta
    $("#ventaspendientes").children().removeClass("selectedGat");
    $("#ventaseleccionada").val(idventa);
    $(`#venta_${idventa}`).addClass("selectedGat");
    DatosVenta(); //traemos los datos de laventa
    ResalteSeleccionados(); // marcamos los productos de la venta actual seleccionada
    DatosSecundariosPedido();
    drawDataProductoLS(null);
    resaltar();
  }

  function DatosVenta() {
    let idventa = $("#ventaseleccionada").val();
    if (idventa != "") {
      $.ajax({
        url: "<?= $this->url ?>/ajax_Datos_venta",
        data: {
          "idventa": idventa
        },
        type: "POST",
        dataType: "JSON",
        success: function(data) {
          $("#tipoventa").val(data.tipoventa);
          $("#cliente").val(data.cliente);
          $("#clientes").val(data.clientes);
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

  function QuitarResalteDeVentaOld() {
    let idventa = $("#ventaseleccionada").val();
    let dataPLS = localStorage.getItem(`productos_${idventa}`);
    if (dataPLS != null) {
      let array = JSON.parse(dataPLS);
      for (value of array) {
        $(`#ProductoLista${value.id_producto}`).removeClass("productoIsSelected");
      }
    }
  }


  //? traemos todas las ventas
  function ventasReload() {
    $("#ventaspendientes").empty();
    // $("#ventaspendientes").html(`<button id="agregarnewventa" style="border-top:0px; border-left:0px" class="categories" onclick="agregarnewventa()"><i class="fa fa-plus"></i></button>`);
    $("#ventaspendientes").children().removeClass("selectedGat");
    $.ajax({
      url: "<?= $this->url ?>/ajax_ventasReload",
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        $("#ventaspendientes").append(data.datahtml);
        $("#ventaseleccionada").val(data.idselect); //insertamos el id de la venta para saber de quien sera el producto
        drawDataProductoLS(null);
        DatosVenta(); //traemos los datos de laventa
        DatosSecundariosPedido();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'Error al obtener datos de ajax'
        });
      }
    });
  }

  function ELiminarVenta() {
    $("#eliminarventa").attr("disabled", true);
    $("#eliminarventa").html("<i class='fa fa-spinner fa-spin'></i>");
    let idventa = $("#ventaseleccionada").val();
    let key = localStorage.getItem(`productos_${idventa}`);
    if (idventa != "") {
      if (key != null) {
        let arrayLS = JSON.parse(key);
        if (arrayLS.length > 0) {
          Lobibox.alert("info", {
            title: "Informacion",
            position: "top right",
            msg: "Tienes que eliminar los productos seleccionados",
          });
          $("#eliminarventa").attr("disabled", false);
          $("#eliminarventa").html("<i class='fa fa-minus'></i>");
        } else {
          ProcesoELiminarVenta(idventa);
        }
      } else {
        ProcesoELiminarVenta(idventa);
      }
    } else {
      Lobibox.alert("info", {
        title: "Informacion",
        position: "top right",
        msg: "Tienes que seleccionar una venta",
      });
      $("#eliminarventa").attr("disabled", false);
      $("#eliminarventa").html("<i class='fa fa-minus'></i>");
    }


  }

  function ProcesoELiminarVenta(idventa) {

    $.ajax({
      url: "<?= $this->url ?>/ajax_ProcesoELiminarVenta",
      type: "POST",
      data: {
        'idventa': idventa
      },
      dataType: "JSON",
      success: function(data) {

        $(`#venta_${idventa}`).removeClass("selectedGat");
        $(`#venta_${idventa}`).prev().addClass("selectedGat");

        if (localStorage.getItem(`productos_${idventa}`) != null) {
          localStorage.removeItem(`productos_${idventa}`);
        }


        let idventaPREV = $(`.selectedGat`).children().val();
        $("ventaseleccionada").val(idventaPREV);

        $(`#venta_${idventa}`).remove();
        traerpedidosventa(idventaPREV);

        $("#eliminarventa").attr("disabled", false);
        $("#eliminarventa").html("<i class='fa fa-minus'></i>");

      },
      error: function(jqXHR, textStatus, errorThrown) {
        //toast.error("Ha ocurrido algo inesperado");
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });

  }

  function sentTicketWA(venta) {
    const phone = $("#telefonoWP").val();
    window.open("<?= $this->url ?>/sentTicketWA/" + phone + "/" + venta);
  }

  function enviomasivo_documento_electronico() {
    var slider = document.createElement("input");
    slider.type = "range";
    swal({
      content: slider,
    });
    $.ajax({
      url: '<?= base_url() ?>venta/enviomasivo/' + <?= date('Y-m-d') ?> + '/' + <?= date('Y-m-d') ?>,
      method: 'POST',
      dataType: "JSON",
    }).then(function(data) {
      if (data.respuesta == 'ok') {
        swal({
          title: 'Resultado',
          text: 'Su comprobante se ha procesado correctamente!',
          html: true,
          type: "success",
          confirmButtonText: "Ok",
          confirmButtonColor: "#2196F3"
        });
      } else {
        swal({
          title: 'ERROR',
          text: 'No Existen comprobantes pendientes de envio',
          html: true,
          type: "error",
          confirmButtonText: "Ok",
          confirmButtonColor: "#2196F3"
        });
      }
    }, function(reason) {
      console.log(reason);
    });
  };

  // TODO: --------------------------------Esto es el codigo nuevo-----------------------------
  function categoriaSeleccionar() {
    $("#retrocederFila").hide();
    $("#Categoriaproducto").hide();
    $("#CategriaSeleccionar").load('<?= $this->url ?>/load_CategriaSeleccionar');
  }


  function EliminaProducto(producto, key) {
    $(`#BTN-EliminaProducto-${key}`).attr("disabled", true);
    $(`#BTN-EliminaProducto-${key}`).html(`<i class="fa fa-spin fa-spinner"></i>`);
    let idventa = $("#ventaseleccionada").val();
    let ProductosLS = localStorage.getItem(`productos_${idventa}`);
    if (ProductosLS != null) {
      for (index in ProductosArr) {
        if (ProductosArr[index].key_primary == key) {
          $(`#ProductoLista${producto}`).removeClass("productoIsSelected opacar_div");
          $(`#Agotado${producto}`).remove();
          $(`#content-padre-${producto}`).remove();
          EliminarProductoArray(index, ProductosArr[index].id_producto);
          $(`#BTN-EliminaProducto-${key}`).attr("disabled", false);
          $(`#BTN-EliminaProducto-${key}`).html(`<i class="fa fa-minus-circle"></i>`);
          DatosSecundariosPedido();
          break;
        } else {
          continue;
        }
      }
    }
  }

  function EliminarProductoArray(index, id_producto) {

    let venta = $("#ventaseleccionada").val();
    ProductosArr.splice(index, 1);
    localStorage.setItem(`productos_${venta}`, JSON.stringify(ProductosArr));

    //? si elimina todos los productos del array tambien eliminamos del LS
    let dataLsProductos = JSON.parse(localStorage.getItem(`productos_${venta}`));
    if (dataLsProductos.length <= 0) {
      localStorage.removeItem(`productos_${venta}`);
    }
    drawDataProductoLS(null);
  }




  function categoria(categoria) {
    $.ajax({
      url: "<?= $this->url ?>/ajax_productos_categoria",
      type: "POST",
      data: {
        'categoria': categoria
      },
      success: function(data) {
        $("#CategriaSeleccionar").hide();
        $("#retrocederFila").show();
        $("#Categoriaproducto").show();
        $("#CategoriaproductoTodos").hide();
        $("#Categoriaproducto").html(data);
        ResalteSeleccionados();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert("error");
      }
    });
  }

  function ResalteSeleccionados() {
    let idventa = $("#ventaseleccionada").val();
    let dataPLS = localStorage.getItem(`productos_${idventa}`);
    if (dataPLS != null) {
      let array = JSON.parse(dataPLS);
      for (value of array) {
        $(`#ProductoLista${value.id_producto}`).addClass("productoIsSelected");
      }
    }
  }

  function productolibre() {
    $("#addproductolibre").modal('show');
  }

  function saveLibre() {
    $('#btnSaveLibre').text('guardando...'); //change button text
    $('#btnSaveLibre').attr('disabled', true); //set button disable
    let ventaseleccionada = $("#ventaseleccionada").val();
    if (ventaseleccionada != "") {
      $.ajax({
        url: '<?= $this->url ?>/ajax_addproductolibre',
        type: "POST",
        data: $('#form_libre').serialize(),
        dataType: "JSON",
        success: function(data) {
          if (data.status) {
            $('#addproductolibre').modal('hide');
            $('#form_libre')[0].reset();
            agregaarventa(data.producto, data.precio, {
              statusvariantes: false
            }, data.nombre_producto);
            DatosSecundariosPedido();
          } else {
            for (var i = 0; i < data.inputerror.length; i++) {
              $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
              $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
            }
          }
          $('#btnSaveLibre').text('GRABAR'); //change button text
          $('#btnSaveLibre').attr('disabled', false); //set button enable
        },
        error: function(jqXHR, textStatus, errorThrown) {
          $('#btnSaveLibre').text('GRABAR'); //change button text
          $('#btnSaveLibre').attr('disabled', false); //set button enable
        }
      });
    } else {
      $('#addproductolibre').modal('hide');
      $('#btnSaveLibre').text('GRABAR'); //change button text
      $('#btnSaveLibre').attr('disabled', false); //set button enable
      Lobibox.alert("info", {
        title: 'Informacion',
        msg: 'Tiene que haber una venta selecionada'
      });
    }

  };

  function retroceder() {
    $('#Categoriaproducto').hide();
    $("#CategriaSeleccionar").show();
    categoriaSeleccionar();
  }

  function asignarindicePro(idventa) {
    let dataproductoLS = JSON.parse(localStorage.getItem(`productos_${idventa}`));
    let indiceinsert;
    if (dataproductoLS == null) {
      indiceinsert = 0;
    } else {
      if (dataproductoLS.length > 0) {
        let indiceEncont = dataproductoLS.length - 1; //obtenemos el ultimo arreglo insertado
        let dataArreglo = dataproductoLS[indiceEncont];
        indiceinsert = dataArreglo.key_primary + 1;
      } else {
        indiceinsert = 0;
      }
    }
    return indiceinsert;
  }

  function cantidadPro(idventa, idproducto) {
    let dataproductoLS = JSON.parse(localStorage.getItem(`productos_${idventa}`));
    let cantidadP;

    if (dataproductoLS != null) {
      for (value of dataproductoLS) {
        if (value.id_producto == idproducto) {
          cantidadP = value.cantidad + 1;
          break;
        } else {
          continue;
        }
      }

      if (cantidadP > 1) {
        return cantidadP;
      } else {
        return 1;
      }
    } else {
      return 1;
    }

  }

  function datainsert(idproducto, precioproducto, idventa, nameproducto, cantidad_variante, idvariante, objectParametros) {
    let key = asignarindicePro(idventa);
    let data = {
      key_primary: key,
      id_producto: idproducto,
      text_proudcto: nameproducto,
      id_variante: idvariante,
      statusvariante: objectParametros.statusvariante,
      total_pagar: precioproducto,
      precio_producto: precioproducto,
      cantidad: 1,
      cantidad_variante: cantidad_variante,
      cantidad_variante_total: cantidad_variante,
      lote: objectParametros.lote,
      statuslote: objectParametros.statuslote,
      EstadoMas: false,
      EstadoMenos: true,
    };

    ProductosArr.push(data);

  }

  function guardarProductoLS(idventa, idproducto) {
    localStorage.setItem(`productos_${idventa}`, JSON.stringify(ProductosArr));
    $(`#ProductoLista${idproducto}`).addClass("productoIsSelected");

  }

  function drawDataProductoLS(idproducto) {

    $("#pedidosventa").html("");
    $("#busquedacodigobarra").html(`<i class="fa fa-search"></i>`);
    $("#codigodebarra").val("");
    $(`#boton-product-${idproducto}`).attr("disabled", false);
    $("#addProcesoVE").modal("hide");
    $("#btnAgregarVenta").attr("disabled", false);

    let idventa = $("#ventaseleccionada").val();
    if (idventa != "") {
      ProductosArr = JSON.parse(localStorage.getItem(`productos_${idventa}`));
      if (ProductosArr) {
        ProductosArr.forEach(function(value, index) {
          $("#pedidosventa").append(drawaHtmlProd(value));
        })
      } else {
        ProductosArr = [];
      }
    } else {
      console.log("venta no seleccionada: drawDataProductoLS()")
    }


  }

  //? Antiguo modelo
  function drawaHtmlProd(value) {

    let html = `
      <div class="seleccion" id="content-padre-${value.id_producto}">
                <input id="Productos" value="" type="hidden">
              <div class="seleccion__item--producto">
                
                  <div class="seleccion__item-head">
                  ${value.text_proudcto} ${(value.statusvariante ? `[<span id="total_variante-${value.key_primary}">${value.cantidad_variante_total}</span>]` : "")}
                  </div>
                  <div class="seleccion__item-body">
                  <span style="color:#DDA433">P/U: </span><span>S/</span> <span id="CostoProducto-${value.key_primary}">${value.precio_producto}</span> 
                  </div> 
              </div>

              <div class="seleccion__item">

                  <div class="seleccion__item-head">
                    Cantidad 
                  </div>
                  <div class="seleccion__item-body">

                    <div class="input-group" style="width:130px;">
                        <div class="spinner-buttons input-group-btn">
                            <button type="button" onclick="MasMenos(0, ${value.id_producto}, ${value.key_primary})" class="btn spinner-up btn-inverse waves-effect waves-light btn-sm" id="BotonMas-${value.key_primary}">
                              <i class="fa fa-minus"></i>
                            </button>
                      </div>
                        <input onkeyup="CantidadProducto(${value.key_primary})" class="spinner-input form-control" id="CantidadProducto-${value.key_primary}" type="number" value="${value.cantidad}">
                        <div class="spinner-buttons input-group-btn">
                              <button type="button" onclick="MasMenos(1, ${value.id_producto}, ${value.key_primary})" class="btn spinner-up btn-inverse waves-effect waves-light btn-sm">
                                  <i class="fa fa-plus"></i>
                              </button>
                        </div>
                  </div>
                  
                  </div> 

              </div>

              <div class="seleccion__item">
                  <div class="seleccion__item-head">
                    Total 
                  </div>
                  <div class="seleccion__item-body">
                    <div class="input-group">
                      <span class="input-group-addon">S/.</span>
                      <input onkeyup="TotalPagarServicio(${value.key_primary})" step="0.1" type="number" id="TotalPagar-${value.key_primary}" value="${value.total_pagar}"  class="form-control">                                                  
                    </div>
                  </div> 
              </div>
            

              <div class="seleccion__item--botoneliminar">
              <button class="botoneliminar btn btn-warning btn-sm"   id="BTN-EliminaProducto-${value.key_primary}" title="CANCELAR" onclick="EliminaProducto(${value.id_producto}, ${value.key_primary})">
              <i class="fa fa-minus-circle"></i>
              </button>
              </div>

              <div style="display:flex; justify-content:center; width:100%" >
                  <div class="alert alert-danger alert-dismissable" style="margin:3px; padding:2px; display:none" id="ContenedorMensajeStock-${value.key_primary}" >
                    
                  </div>
              </div>
              
          </div>
      `;
    return html;
  }



  function TotalPagarServicio(datakey_primary) {

    let ventaseleccionada = $(`#ventaseleccionada`).val();

    let costoproducto = parseFloat($(`#CostoProducto-${datakey_primary}`).text());
    let TotalPagar = parseFloat($(`#TotalPagar-${datakey_primary}`).val());

    let ResultCantidad = isNaN(parseFloat(TotalPagar / costoproducto)) ? 0.00 : parseFloat(TotalPagar / costoproducto);

    $(`#CantidadProducto-${datakey_primary}`).val(ResultCantidad.toFixed(2));

    for (value of ProductosArr) {
      if (value.key_primary == datakey_primary) {
        value.cantidad = ResultCantidad.toFixed(2);

        value.total_pagar = isNaN(TotalPagar.toFixed(2)) ? 0 : TotalPagar.toFixed(2);
        let cantidad_variante_total = parseFloat(value.cantidad_variante * ResultCantidad);
        value.cantidad_variante_total = cantidad_variante_total;
        $(`#total_variante-${datakey_primary}`).text(cantidad_variante_total.toFixed(2))
        //? Guardamos en el localStorage.
        localStorage.setItem(`productos_${ventaseleccionada}`, JSON.stringify(ProductosArr));
        //? actulizamos los datos secundatos
        DatosSecundariosPedido();
        break;
      } else {
        continue;
      }
    }
  }

  function CantidadProducto(key_primary) {

    let ventaseleccionada = $(`#ventaseleccionada`).val();

    let costoproducto = parseFloat($(`#CostoProducto-${key_primary}`).text());
    let CantidadProducto = parseInt($(`#CantidadProducto-${key_primary}`).val());

    let operacion = isNaN(parseFloat(costoproducto * CantidadProducto)) ? 0 : parseFloat(costoproducto * CantidadProducto);
    $(`#TotalPagar-${key_primary}`).val(operacion.toFixed(2));

    for (index in ProductosArr) {

      if (ProductosArr[index].key_primary == key_primary) {

        let cantidadproducto = isNaN(CantidadProducto.toFixed(2)) ? 0 : CantidadProducto.toFixed(2);
        let totalvariante = parseInt(ProductosArr[index].cantidad_variante * cantidadproducto);
        ProductosArr[index].cantidad_variante_total = totalvariante;
        $(`#total_variante-${key_primary}`).text(totalvariante);

        ProductosArr[index].cantidad = cantidadproducto;

        ProductosArr[index].total_pagar = operacion.toFixed(2);
        break;
      } else {
        continue;
      }

    }
    //? Guardamos en el localStorage.
    localStorage.setItem(`productos_${ventaseleccionada}`, JSON.stringify(ProductosArr));
    //? actulizamos los datos secundatos
    DatosSecundariosPedido();

  }

  //! obsoleto

  function MasMenos(estadoBTN, idproducto, key) {

    let cantidadproducto = parseInt($(`#CantidadProducto-${key}`).val());
    let cantidadmedida = parseInt($(`#CantidadMedida-${key}`).val());
    if (estadoBTN) {

      //? si es true ese por que selecciono el boton mas 
      $(`#BotonMas-${idproducto}`).attr("disabled", true);
      $(`#BotonMas-${idproducto}`).html("<i class='fa fa-spin fa-spinner'></i>");

      //? si se hizo el descuento del stock
      VerificarProducto(idproducto, key, estadoBTN, 1, cantidadmedida);

    } else {

      $(`#BotonMenos-${idproducto}`).attr("disabled", true);
      $(`#BotonMenos-${idproducto}`).html("<i class='fa fa-spin fa-spinner'></i>");

      if ($(`#CantidadProducto-${key}`).val() > 0) {

        VerificarProducto(idproducto, key, estadoBTN, 1, cantidadmedida);

      } else {

        $(`#BotonMenos-${idproducto}`).attr("disabled", false);
        $(`#BotonMenos-${idproducto}`).html("<i class='fa fa-plus'></i>");

        Lobibox.alert("info", {
          title: "Informacion",
          position: "top right",
          msg: "Verifique la cantidad de la operacion :(",
        });
      }

    }

  }

  function VerificarProducto(idproducto, key, estadoBTN, cantidadaproducto, cantidadmedida) {
    let indexBusqueda;
    let venta = $("#ventaseleccionada").val();
    //? Con el key_primary buscamos en que indice se encuentra y asi traer el objetos 
    for (index in ProductosArr) {
      if (ProductosArr[index].key_primary == key) {
        indexBusqueda = index;
        break;
      } else {
        continue;
      }
    }
    //? Registrar 1 cantidad mas en el Array y despues lo registramos en el localstorage
    let CantidadTotal;
    let totalcantidad;
    if (estadoBTN) {
      totalcantidad = parseInt(ProductosArr[indexBusqueda].cantidad + cantidadaproducto);
      if (ProductosArr[indexBusqueda].statusvariante) {
        //? aumentamos variante
        ProductosArr[indexBusqueda].cantidad_variante_total = parseInt(ProductosArr[indexBusqueda].cantidad_variante * totalcantidad);
      }
    } else {
      totalcantidad = parseInt(ProductosArr[indexBusqueda].cantidad - 1);
      if (ProductosArr[indexBusqueda].statusvariante) {
        ProductosArr[indexBusqueda].cantidad_variante_total = parseInt(ProductosArr[indexBusqueda].cantidad_variante * totalcantidad);
      }
    }
    ProductosArr[indexBusqueda].cantidad = totalcantidad;
    let TotalPagar = parseFloat(ProductosArr[indexBusqueda].precio_producto * totalcantidad);
    ProductosArr[indexBusqueda].total_pagar = TotalPagar;
    if (ProductosArr[indexBusqueda].cantidad == 1) {
      ProductosArr[indexBusqueda].EstadoMenos = true;
    } else {
      ProductosArr[indexBusqueda].EstadoMenos = false;
    }
    localStorage.setItem(`productos_${venta}`, JSON.stringify(ProductosArr));
    DatosSecundariosPedido();
    drawDataProductoLS(idproducto);
  }

  function cerrarMSJ(key) {
    $(`#ContenedorMensajeStock-${key}`).hide();
  }

  function save() {
    // ajax adding data to database
    $.ajax({
      url: "<?= $this->url ?>/ajax_update/" + $(`#ventaseleccionada`).val(),
      type: "POST",
      data: $('#form_principal').serialize(),
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          /*
          Lobibox.notify('success', {
            size: 'mini',
            position: 'top center',
            msg: "El registro fue actualizado exitosamente."
          });
          */
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        //toast.error("El registro no se pudo actualizar. Verifique la operación");
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: "El registro no se pudo actualizar. Verifique la operación"
        });
      }
    });
  };

  function eliminarDataLS(idventa) {
    let key = localStorage.getItem(`productos_${idventa}`);
    if (key != null) {
      localStorage.removeItem(`productos_${idventa}`);
    }
  }

  //TODO: -------------------------------------------------------------------------------------------------------------------------------------------------------------

  function agregarAdicionales(idproducto) {
    $(`#varianteseleccionada`).val("");
    $(`#loteseleccionado`).val("");
    $("#MensajeVariante").html("");
    $("#MensajeVariante").removeClass("alert alert-danger alert-dismissable");
    let ventaseleccionada = $(`#ventaseleccionada`).val();
    if (ventaseleccionada == "") {
      Lobibox.alert("info", {
        title: "Informacion",
        position: "top right",
        msg: "Tienes que crear una venta",
      });
    } else {
      $(`#boton-producto-${idproducto}`).attr("disabled", true);
      $(`#boton-producto-${idproducto}`).html("<i class='fa fa-spinner fa-spin'></i>");
      $.ajax({
        url: '<?= $this->url ?>/ajax_agregarAdicionales/' + idproducto,
        type: "POST",
        dataType: "JSON",
        success: function(data) {
          $(`#boton-producto-${idproducto}`).attr("disabled", false);
          $(`#boton-producto-${idproducto}`).html("<i class='fa fa-shopping-cart'></i>");
          document.getElementById("ColumnaVariante").className = '';
          document.getElementById("ColumnaExtra").className = '';
          document.getElementById("ColumnaLotes").className = '';
          $(`#ColumnaExtra`).addClass(`col-lg-${data.totalcolumnas}`);
          $(`#ColumnaVariante`).addClass(`col-lg-${data.totalcolumnas}`);
          $(`#ColumnaLotes`).addClass(`col-lg-${data.totalcolumnas}`);
          $("#listaVariante").html(data.variantes.html);
          $("#listExtras").html(data.extras.html);
          //? Agregar lote por defecto
          data.lotes.totalLotes == 1 ? $("#loteseleccionado").val(data.lotes.html) : $("#listLotes").html(data.lotes.html);
          $(`#ProcesoAddVE`).html(data.boton);
          data.variantes.status ? $(`#ColumnaVariante`).show() : $(`#ColumnaVariante`).hide();
          data.lotes.status && data.lotes.totalLotes > 1 ? $(`#ColumnaLotes`).show() : $(`#ColumnaLotes`).hide();
          data.extras.status ? $(`#ColumnaExtra`).show() : $(`#ColumnaExtra`).hide();
          $("#addProcesoVE").modal('show');
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert("Codigo de error de ajax: agregarAdicionales")
          $(`#boton-producto-${idproducto}`).attr("disabled", false);
          $(`#boton-producto-${idproducto}`).html("<i class='fa fa-shopping-cart'></i>");
        }
      });
    }
  }

  function seleccionarVariante(idvariante) {
    //? ocultamos el mensaje de alerta de variantes
    $(`#MensajeVariante`).hide("fast");
    $(`.variantes`).each(function(index, element) {
      let idvariante = $(element).val();
      $(`#label-variante-${idvariante}`).removeClass("selectVariantes");
    });
    $(`#label-variante-${idvariante}`).addClass("selectVariantes");
    $("#varianteseleccionada").val(idvariante);
  }

  function seleccionarLote(idlote) {
    $(`.lotes`).each(function(index, element) {
      let idlote = $(element).val();
      $(`#label-lote-${idlote}`).removeClass("selectLotes");
    });
    $(`#label-lote-${idlote}`).addClass("selectLotes");
    $("#loteseleccionado").val(idlote);
  }

  function procesoAgregarVenta(dataObject) {
    $("#btnAgregarVenta").attr("disabled", true);
    $("#btnAgregarVenta").html(`<i class="fa fa-spin fa-spinner"></i>`);
    $("#MensajeVariante").html("");
    $("#MensajeVariante").addClass("alert alert-danger alert-dismissable");
    let precioproducto = 0;
    let idvariante = $(`#varianteseleccionada`).val();
    if (dataObject.statusvariante == 0) {
      //? si es 0 tomara el precio del producto normal
      precioproducto = parseFloat($(`#precio-producto-${dataObject.idproducto}`).val());
    } else {
      //? Tomara el precio de la variante selecionada
      precioproducto = parseFloat($(`#precioproducto-${idvariante}`).val());
    }
    let textProductoAdicional = "";
    if (dataObject.statusvariante == 1) {
      //? concatenara el nombre de la variante con el producto
      textProductoAdicional += ` ${$(`#text-variante-${idvariante}`).val()} `;
    } else {
      textProductoAdicional += "";
    }
    let precioTotalExtras = 0;
    //? sumamos el total y a los nombres concatenamos, de todos los extras que se seleccionaron
    $(`.ExtraIdenti`).each(function(index, value) {
      let idextra = $(this).val();
      if ($(`#checkep_extra-${idextra}`).is(":checked")) {
        let PrecioExtra = parseFloat($(`#PrecioExtra-${idextra}`).val());
        precioTotalExtras += PrecioExtra
        textProductoAdicional += `, + 1 ${$(`#NombreExtra-${idextra}`).val()}`;
      } else {
        if ($(`#extra-${idextra}`).hasClass("active-tachar-content")) {
          textProductoAdicional += `, Sin ${$(`#NombreExtra-${idextra}`).val()}`;
        }
      }
    });
    let precioTotalGlobal = precioTotalExtras + precioproducto;
    let VarianExtrData = {
      statusvariante: dataObject.statusvariante,
      statuslote: dataObject.statuslote,
      lote: dataObject.statuslote ? $("#loteseleccionado").val() : null,
      variante: idvariante,
      data: textProductoAdicional
    };
    if (dataObject.statusvariante == 1) {
      let variante = $(`#varianteseleccionada`).val();
      if (variante != "") {
        if (dataObject.statuslote == 1) {
          let lote = $(`#loteseleccionado`).val();
          if (lote != "") {
            agregaarventa(dataObject.idproducto, precioTotalGlobal, VarianExtrData, "");
          } else {
            $(`#MensajeVariante`).show("fast");
            $("#MensajeVariante").append(`<button type="button" class="close" aria-hidden="true" style="right:0px" onclick="varianteMSG()">×</button> Tienes que escoger un <span class="alert-link">lote</span>.`)
            $("#btnAgregarVenta").attr("disabled", false);
            $("#btnAgregarVenta").text(`Agregar`);
          }
        } else {
          agregaarventa(dataObject.idproducto, precioTotalGlobal, VarianExtrData, "");
        }
      } else {
        $(`#MensajeVariante`).show("fast");
        $("#MensajeVariante").append(`<button type="button" class="close" aria-hidden="true" style="right:0px" onclick="varianteMSG()">×</button> Tienes que escoger una <span class="alert-link">Variante</span>.`)
        $("#btnAgregarVenta").attr("disabled", false);
        $("#btnAgregarVenta").text(`Agregar`);
      }
    } else {
      if (dataObject.statuslote == 1) {
        let lote = $(`#loteseleccionado`).val();
        if (lote != "") {
          agregaarventa(dataObject.idproducto, precioTotalGlobal, VarianExtrData, "");
        } else {
          $(`#MensajeVariante`).show("fast");
          $("#MensajeVariante").append(`<button type="button" class="close" aria-hidden="true" style="right:0px" onclick="varianteMSG()">×</button> Tienes que escoger un <span class="alert-link">lote</span>.`)
          $("#btnAgregarVenta").attr("disabled", false);
          $("#btnAgregarVenta").text(`Agregar`);
        }
      } else {
        agregaarventa(dataObject.idproducto, precioTotalGlobal, VarianExtrData, "");
      }
    }
  }

  function varianteMSG() {
    $("#MensajeVariante").hide("fast");
  }

  function agregaarventa(idproducto, precioproducto, VariExtrData, nombreproducto) {
    let idventaseleccionada = $("#ventaseleccionada").val();
    let idvariante, tipomedida, cantidad_variante, precioinsert, nombreinsert;
    precioinsert = precioproducto;
    if (VariExtrData.statusvariante) {
      idvariante = VariExtrData.variante;
      cantidad_variante = $(`#variante-cantidad-${idvariante}`).length > 0 ? $(`#variante-cantidad-${idvariante}`).val() : 1;
      nombreinsert = $(`#nombre-producto-${idproducto}`).val() + VariExtrData.data;
    } else {
      idvariante = false;
      cantidad_variante = 1;
      nombreinsert = $(`#nombre-producto-${idproducto}`).val();
    }
    $(`#boton-product-${idproducto}`).attr("disabled", true);
    if (idventaseleccionada != "") {
      let idventa = $("#ventaseleccionada").val();
      let textoproducto = nombreinsert;
      if (nombreproducto != "") {
        textoproducto = nombreproducto;
      }
      let EstadoBusqueda = false;
      let verifi = localStorage.getItem(`productos_${idventa}`);
      if (verifi != null) {
        for (value of ProductosArr) {
          if (VariExtrData.statusvariante) {
            //? buscara a la variante y lo aumentara
            if (value.id_variante == idvariante) {
              EstadoBusqueda = true;
              let cantidad = parseInt(value.cantidad + 1); //?cantidad de seleccion
              let total = parseFloat(value.precio_producto * cantidad);
              value.cantidad_variante_total = parseInt(value.cantidad_variante * cantidad);
              value.cantidad = cantidad;
              value.total_pagar = total;
              if (value.cantidad == 1) {
                value.EstadoMenos = true;
              } else {
                value.EstadoMenos = false;
              }
              break;
            } else {
              continue;
            }
          } else {
            //? buscara el producto y lo aumentara
            if (value.id_producto == idproducto) {

              EstadoBusqueda = true;
              let cantidad = parseInt(value.cantidad + 1); //?cantidad de seleccion

              let total = parseFloat(value.precio_producto * cantidad);

              value.cantidad_variante_total = parseInt(value.cantidad_variante * cantidad);
              value.cantidad = cantidad;
              value.total_pagar = total;

              if (value.cantidad == 1) {
                value.EstadoMenos = true;
              } else {
                value.EstadoMenos = false;
              }
              break;
            } else {
              continue;
            }
          }
        }
      }
      if (EstadoBusqueda == false) {
        datainsert(idproducto, precioinsert, idventa, textoproducto, cantidad_variante, idvariante, VariExtrData);
      }
      guardarProductoLS(idventa, idproducto); //? en este metodo agregarmos "productoIsSelected" 
      drawDataProductoLS(idproducto); //? le ponesmo esto al segundo por que recargargar llamamos a este metodo
      DatosSecundariosPedido();
    } else {
      //? mensaje para informar que tiene que seleccionar crear un venta
      Lobibox.alert("info", {
        title: "Informacion",
        position: "top right",
        msg: "Tienes que crear una venta",
      });
      $(`#boton-product-${idproducto}`).attr("disabled", false);
    }
  } //! no se utiliza


  function seleccionarExtra(idextra) {

    if ($(`#extra-${idextra}`).hasClass("active-tachar-content") == false) {
      if ($(`#extra-${idextra}`).hasClass("extra-active")) {
        $(`#extra-${idextra}`).removeClass("extra-active");
        $(`#checkep_extra-${idextra}`).prop("checked", false);
      } else {
        $(`#extra-${idextra}`).addClass("extra-active");
        $(`#checkep_extra-${idextra}`).prop("checked", true);
      }
    }
  }

  function sinextraope(idextra) {

    if ($(`#extra-${idextra}`).hasClass("active-tachar-content")) {
      $(`#sinextra-${idextra} i`).removeClass("active-sin-extra");
      $(`#checkep_extra-${idextra}`).attr("disabled", false);

      $(`#extra-${idextra}`).removeClass("active-tachar-content");
      $(`#extraname-${idextra}`).removeClass("active-tachar-extra");
      $(`#extraprecio-${idextra}`).removeClass("active-tachar-extra");

    } else {

      $(`#sinextra-${idextra} i`).addClass("active-sin-extra");
      $(`#checkep_extra-${idextra}`).attr("checked", false);
      $(`#checkep_extra-${idextra}`).attr("disabled", true);
      $(`#extra-${idextra}`).removeClass("extra-active");
      $(`#extra-${idextra}`).addClass("active-tachar-content");
      $(`#extraname-${idextra}`).addClass("active-tachar-extra");
      $(`#extraprecio-${idextra}`).addClass("active-tachar-extra");

    }

  }


  function DatosSecundariosPedido() {
    let idventa = $("#ventaseleccionada").val();
    $(`#addVariante`).modal("hide");
    let key = localStorage.getItem(`productos_${idventa}`);
    let TotalItem = 0;
    let MontoPagar = 0;
    if (key != null) {
      let ProductoLS = JSON.parse(key);
      for (value of ProductoLS) {
        TotalItem += parseFloat(value.cantidad);
        MontoPagar += parseFloat(value.total_pagar);
      }
    }
    if (TotalItem == 0) {
      $("#ProcesarVenta").attr("disabled", true);
    } else {
      $("#ProcesarVenta").attr("disabled", false);
    }

    $("#ItemsNumVenta span").text(TotalItem.toFixed(2));
    $("#SubtotVenta").text(MontoPagar.toFixed(2));
    $("#total").text(MontoPagar.toFixed(2));

  }

  function grabar() {
    $("#ProcesarVenta").attr("disabled", true);
    $("#ProcesarVenta").html("<i class='fa fa-spin fa-spinner'></i>");
    var cliente = $("#clientes").val();
    cliente = cliente == "" ? "|" : cliente;
    var tipoComprobante = $("#tipoventa").val();
    var grabarventa = false;
    var msg = "";
    var documento = cliente.split("|");
    documento[0] = documento[0].trim();
    switch (tipoComprobante) {
      case 'FACTURA':
        if (documento[0].length != 11) {
          grabarventa = false;
          msg = "Debe seleccionar un cliente con RUC";
        } else {
          grabarventa = true;
        }
        break;

      case 'BOLETA':
        if (documento[0].length != 8) {
          grabarventa = false;
          msg = "Debe seleccionar un cliente con DNI";
        } else {
          grabarventa = true;
          console.log("SI es Boleta y el cliente esta con DNI de 8");
        }
        break;
      case 'OTROS':
        grabarventa = true;
        break;
    }

    if (grabarventa) {
      let idventa = $("#ventaseleccionada").val();
      let dataPedidos = JSON.parse(localStorage.getItem(`productos_${idventa}`));
      $.ajax({
        url: "<?= $this->url ?>/ajax_verif_stock",
        type: "POST",
        data: {
          "productos": JSON.stringify(dataPedidos),
          "venta": idventa
        },
        dataType: "JSON",
        success: function(data) {
          if (data.venta.status) {
            if (data.dataenviar.length > 0) {
              for (value of data.dataenviar) {
                $(`#ContenedorMensajeStock-${value.key_primary}`).show("fast");
                if (value.nombrelote != "") {
                  $(`#ContenedorMensajeStock-${value.key_primary}`).html(`<button type="button" style="position:relative; right:0px" class="close" onclick="cerrarMSJ(${value.key_primary})">×</button>
                      El lote: "${value.nombrelote}" cuenta con <span class="alert-link" id="CantidadStock-${value.key_primary}">${value.totalstock}</span> de stock .`);
                } else {
                  $(`#ContenedorMensajeStock-${value.key_primary}`).html(`<button type="button" style="position:relative; right:0px" class="close" onclick="cerrarMSJ(${value.key_primary})">×</button>
                      Solo cuenta con <span class="alert-link" id="CantidadStock-${value.key_primary}">${value.totalstock}</span> stock .`);
                }
              };
            } else {
              if (data.tresPasos == '1' && $("#cobradorCaja").val() == '0') {
                bootbox.confirm("¿Seguro desea enviar el pedido a caja?", function(result) {
                  if (result === true) {
                    $.ajax({
                      url: "<?= $this->url ?>/ajax_EnviarPedidoCaja",
                      type: "POST",
                      data: {
                        "referencia": $("#referencia").val(),
                        "idventa": idventa,
                        "pedidosVentaDetalle": JSON.stringify(dataPedidos)
                      },
                      dataType: "JSON",
                      success: function(data) {
                        if (data.proceso.status) {
                          eliminarDataLS(idventa);
                          //DatosSecundariosPedido();
                          ventasReload();
                          $("#referencia").val("");
                          Lobibox.notify('success', {
                            size: 'mini',
                            position: "top right",
                            msg: 'El pedido fue enviado correctamente a caja.'
                          });
                        }
                      },
                      error: function(jqXHR, textStatus, errorThrown) {
                        Lobibox.notify('error', {
                          size: 'mini',
                          position: "top right",
                          msg: 'codigo de error: ajax_EnviarPedidoCaja'
                        });
                      }
                    });
                  }
                });
              } else {
                $('#form_vender')[0].reset(); // reset form on modals
                $('.form-group').removeClass('has-error'); // clear error class
                $('.help-block').empty(); // clear error string
                $("#tipoventa").closest('div').removeClass("has-error"); //clear error 
                DatosPago();
                $(`#ReturnChange span`).text("0.00");
                $(`#ReturnChange span`).addClass("red");
                $('#AddSale').modal('show');
                $('.modal-title').text('PROCESAR VENTA');
                $("#tipocard").hide();
                $("#vencimiento").hide();
                $("#numberoperacion").hide();
                $('#operacion').attr('minLength', 4);
                $('#operacion').attr('maxlength', 4);
                if ($('#formapago').val() == 'CONTADO') {
                  $('#metodo').show();
                  $('#pagado').show();
                  $("#descontado").show();
                  $("#vencimiento").hide();
                  $('.ReturnChange').show();
                } else {
                  $('#metodo').hide();
                  $('#pagado').hide();
                  $("#descontado").hide();
                  $("#vencimiento").show();
                  $('.ReturnChange').hide();
                }
                if ($('#metodopago').val() == 'EFECTIVO') {
                  $('#tipocard').hide();
                } else {
                  $('#tipocard').show();
                }
              }
            }
          } else {
            eliminarDataLS(idventa);
            ventasReload();
            Lobibox.alert("info", {
              title: "Informacion",
              position: "top right",
              msg: data.venta.msg,
            });
          }
          $("#ProcesarVenta").attr("disabled", false);
          $("#ProcesarVenta").text("PROCESAR VENTA");
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert("error ajax_verif_stock");
          $("#ProcesarVenta").attr("disabled", false);
          $("#ProcesarVenta").text("PROCESAR VENTA");
        }
      });

    } else {
      $("#tipoventa").closest('div').addClass("has-error");
      $('#tipoventa').siblings('span').text(msg);
      $("#ProcesarVenta").attr("disabled", false);
      $("#ProcesarVenta").text("PROCESAR VENTA");
    }

  };

  function DatosPago() {
    let clientes = $("#clientes").val();
    let cliente = $("#cliente").val();
    let tipoventa = $("#tipoventa").val();
    $("#customerName span").text(clientes);
    $('[name="cliente"]').val(cliente);
    $('[name="tipoventa"]').val(tipoventa);
    //TODO: el total pago lo traemos de la vista no hacemos ajax
    $("#ItemsNum2 span").text($(`#ItemsNumVenta span`).text());
    $("#MontoPagar2 span").text($(`#SubtotVenta`).text());
    $("#pago").val($(`#SubtotVenta`).text());

  }

  function grabarcliente2(identificador, idventa) {
    $("#form_cliente2")[0].reset();
    let footer = document.querySelector("#divfooter");
    footer.innerHTML = `
    <button type="button" id="btnSavecliente2" onclick="savecliente2(${identificador}, ${idventa})" class="btn btn-primary">GRABAR</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal" >CERRAR</button>`;
    $('#cliente_form2').modal('show');
    $('.modal-title').text('CREAR CLIENTE 2');
  }


  function verstockactual(id, boton) {
    $(`#verStock-${id}-${boton}`).attr("disabled", true);
    $(`#verStock-${id}-${boton}`).html("STOCK <i class='fa fa-spin fa-spinner'</i>");
    $.ajax({
      url: "<?= $this->url ?>/ajax_stockactual",
      type: "POST",
      data: {
        idproducto: id
      },
      dataType: "JSON",
      success: function(data) {
        $("#datastockactual").html(data.datahtml);
        $('#stockactual').modal('show');
        $(`#verStock-${id}-${boton}`).attr("disabled", false);
        $(`#verStock-${id}-${boton}`).html("STOCK <i class='fa fa-search'</i>");
        $('.modal-title').text('STOCK ACTUAL');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert("error de ajax ajax_stockactual");
        $(`#verStock-${id}-${boton}`).attr("disabled", false);
        $(`#verStock-${id}-${boton}`).html("STOCK <i class='fa fa-search'</i>");
      }
    });

  }

  function verimg(id) // solo recibe datos
  {
    $(`#verFoto-${id}`).attr("disabled", true);
    $(`#verFoto-${id}`).html("<i class='fa fa-spin fa-spinner'></i>"); // div al buscar
    $.ajax({
      url: "<?= $this->url ?>/ajax_img/" + id,
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        $('#verfotoactual').modal('show');
        $('.modal-title').text('IMAGEN DEL PRODUCTO');
        $(`#verFoto-${id}`).attr("disabled", false);
        $(`#verFoto-${id}`).html("<i class='fa fa-file-image-o'></i>"); // div al buscar
        if (data.photo) {
          $("#datasfotoactual").html(`<img src="<?= base_url() ?>files/products/${data.photo}" name="photo" id="photo" alt="" width="400px" height="360px" srcset="">`)
        } else {
          $("#datasfotoactual").text("SIN IMAGEN");
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert("error de ajax ajax_img");
        $(`#verFoto-${id}`).attr("disabled", false);
        $(`#verFoto-${id}`).html("<i class='fa fa fa-file-image-o'></i>");
      }
    });
  }

  function datosPedidosEnviados() {
    tabledatosPedidosEnviados = $('#tabla_pedidosEnviados').DataTable({
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
        "url": "<?= $this->url ?>/ajax_tabla_pedidosEnviados",
        "type": "GET"
      },
    });
  }

  function cobrar(idventa) {
    $(`#cobrar-${idventa}`).attr("disabled", true);
    $(`#cobrar-${idventa}`).html("<i class='fa fa-spinner fa-spin'></i>");
    $("#referencia").attr("readonly", true);
    /*$("#botonSalirVenta").attr("disabled", true);
    $("#botonSalirVenta").html("<i class='fa fa-spinner fa-spin'></i>");*/
    $.ajax({
      url: "<?= $this->url ?>/ajax_cobrar/" + idventa,
      type: "POST",
      dataType: "JSON",
      success: function(data) {

        $("#ventaseleccionada").val(data.dataVenta.id)
        for (value of data.dataVentaDetalle) {
          let VarianExtrData = {
            statusvariante: value.variante ? true : false,
            statuslote: value.lote ? true : false,
            lote: value.lote ? value.lote : false,
            variante: value.variante,
            data: ""
          };
          agregaarventa(value.producto, value.subtotal, VarianExtrData, value.nombre)
        }
        $(`#cobrar-${idventa}`).attr("disabled", false);
        $(`#cobrar-${idventa}`).html("<i class='fa fa fa-money'></i>");
        $("#referencia").val(data.dataVenta.referencia);
        $("#tipoventa").val(data.dataVenta.tipoventa);
        $("#cliente").val(data.dataVenta.cliente);
        $("#clientes").val(data.dataVenta.textCliente);
        $("#opcionmenu").hide();
        $("#contenedorPedidosVenta").hide();
        $("#opcionmenuPedidoVenta").show();
        $("#contenedorProcesoPago").show();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert("error de ajax ajax_cobrar");
        $(`#cobrar-${idventa}`).attr("disabled", false);
        $(`#cobrar-${idventa}`).html("<i class='fa fa fa-money'></i>");
      }
    });
  }

  function salirPedidoVenta() {
    let idventa = $("#ventaseleccionada").val();
    eliminarDataLS(idventa);
    ProductosArr = [];
    tabledatosPedidosEnviados.ajax.reload(null, false);
    $("#opcionmenu").show();
    $("#contenedorPedidosVenta").show();
    $("#opcionmenuPedidoVenta").hide();
    $("#contenedorProcesoPago").hide();
  }

  function datosProductosVenta() {
    tableDataProductos = $('#tableDataProductos').DataTable({
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
        "url": "<?= $this->url ?>/ajax_tableDataProductos",
        "type": "POST",
      },
    });
  }

  function refrescarData() {
    tableDataProductos.ajax.reload(null, false);
  }
</script>
<script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>