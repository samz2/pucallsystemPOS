<html>

<head>
  <title>COMPROBANTE</title>
  <style media="screen">
    body {
      font-size: 11px;
      font-family: Helvetica, Arial, Verdana, sans-serif;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    .cabecera1 {
      width: 50%;
      border-collapse: collapse;
    }

    .cabecera2 {
      font-size: 18px !important;
      width: 30%;
      text-align: center;
      position: absolute;
      left: 508px;
      top: 0px;
      z-index: -1;
      border-collapse: separate;
      border-radius: 10px;
      background: Gainsboro;
      font-weight: bold;
      border-spacing: 3px 3px;
      border: black 2px solid;
    }

    .cabecera3 {
      text-indent: 6px;
      margin-top: 10px;
      z-index: -1;
      border-collapse: separate;
      border-radius: 10px;
      border-spacing: 0px 0px;
      border: Silver 2px solid;
    }

    .cabecera5 {
      width: 100%;
      text-indent: 6px;
      margin-top: 10px;
      z-index: -1;
      border-collapse: separate;
      border-radius: 10px;
      border-spacing: 0px;
      border: Silver solid 2px;
    }

    .encabeza th {
      height: 20px;
      background: Gainsboro;
    }

    .cabecera5 td {
      border-top: Silver solid 1px;
      border-bottom: Silver solid 1px;
    }

    /*PARA LA PRIMERA FILA TIENES DOS OPCIONES*/
    /*Así sería la cosa si has empezado con un tr*/
    .cabecera5 tr:first-child td:first-child {
      border-top-left-radius: 10px;
    }

    .cabecera5 tr:first-child td:last-child {
      border-top-right-radius: 10px;
    }

    .cabecera5 tr:first-child td:only-child {
      border-top-right-radius: 10px;
      border-top-left-radius: 10px;
    }

    /*si en lugar de eso has usado la etiquetas thead y th es más sencillo todavía*/
    .cabecera5 th:first-child {
      border-top-left-radius: 10px;
    }

    .cabecera5 th:last-child {
      border-top-right-radius: 10px;
    }

    .cabecera5 th:only-child {
      border-top-right-radius: 10px;
      border-top-left-radius: 10px;
    }

    /*Y ASÍ PONEMOS EL PIE*/
    .cabecera5 tr:last-child td:first-child {
      border-bottom-left-radius: 10px;
    }

    .cabecera5 tr:last-child td:last-child {
      border-bottom-right-radius: 10px;
    }

    .cabecera5 tr:last-child td:only-child {
      border-bottom-right-radius: 10px;
      border-bottom-left-radius: 10px;
    }

    .zebra tr:nth-child(even) {
      background-color: Gainsboro;
    }

    .cabecera6 {
      width: 100%;
      text-indent: 100px;
      margin-top: 10px;
      z-index: -1;
      border-collapse: separate;
      border-radius: 10px;
      border-spacing: 0px 0px;
      border: Silver 2px solid;
    }

    .cabecera6 td {
      height: 20px;
    }

    span {
      font-weight: bold;
    }
  </style>
</head>

<body>
  <table style="width:100%">
    <tr>
      <td style="width:60%">
        <img src="<?= base_url() ?>files/Setting/<?= $empresa->ruc ?>.png" width="100px" alt="">
        <div>
          <?= $empresa->direccion ?>
        </div>
      </td>
      <td style="width:40%">
        <div style="border:1px solid #000; text-align:center; width:100%; padding:10px">
          <div style="font-weight:bold;">GUIA DE REMISION</div>
          <div style="font-weight:bold;">ELETRONICA REMITENTE</div>
          <div style="font-weight:bold;">R.U.C : <?= $empresa->ruc ?></div>
          <div style="font-weight:bold;">EG01-1</div>
        </div>
      </td>
    </tr>
  </table>
  <br>
  <div>
    <table style="width:50%">
      <thead>
        <tr>
          <th>
            DATOS DEL TRASLADO
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Fecha de emision: </td>
          <td style="text-align: left"><?= $data->created ?></td>
        </tr>
        <tr>
          <td>Fecha inicio de traslado: </td>
          <td style="text-align: left"><?= $data->fechatraslado ?></td>
        </tr>
        <tr>
          <td>Motivo de traslado: </td>
          <td style="text-align: left"><?= $motivostraslado ?> </td>
        </tr>
        <tr>
          <td>Modalidad de transporte: </td>
          <td style="text-align: left"><?= $modalidadtraslado ?></td>
        </tr>
        <tr>
          <td>Peso bruto total de la GUIA (KGM): </td>
          <td style="text-align: left"><?= $data->pesobrutobienes ?></td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
  $dataClienteDestino = $this->Controlador_model->get($data->destino_cliente, "cliente");
  if ($dataClienteDestino) {
    $textDataCliente = $dataClienteDestino->tipodocumento == "DNI" ? $dataClienteDestino->nombre . " " . $dataClienteDestino->apellido : "EL CLIENTE FUE ELIMINADO :(";
  }
  ?>
  <br>
  <div>
    <table style="width:100%">
      <thead>
        <tr>
          <th colspan="2">
            DATOS DEL DESTINATARIO
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="width:30%">Apellidos y nombres, denominacion o razon: </td>
          <td style="width:70%; text-align: left"><?= $textDataCliente ?></td>
        </tr>
        <tr>
          <td style="width:30%">Documento o identidad: </td>
          <td style="width:70%; text-align: left"><?= $dataClienteDestino ? $dataClienteDestino->documento : "EL CLIENTE FUE ELIMINADO :(" ?></td>
        </tr>
      </tbody>
    </table>
  </div>
  <br>
  <div>
    <table style="width:100%">
      <thead>
        <tr>
          <th colspan="2">
            DATOS DEL PUNTO DE PARTIDAD Y PUNTO DE LLEGADA
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="width:30%">Direccion del punto de partidad: </td>
          <td style="width:70%; text-align: left"><?= $empresa->ubigeo . " " . $empresa->direccion ?></td>
        </tr>
        <tr>
          <td style="width:30%">Direccion del punto de llegada: </td>
          <td style="width:70%; text-align: left"><?= ($data->destino_ubigeo . " " . $data->destino_direccion) ?></td>
        </tr>
      </tbody>
    </table>
  </div>
  <h4><?= ($data->modalidadtraslado == "01" ? "DATOS DEL TRANSPORTISTA" : "DATOS DEL TRANSPORTE") ?></h4>
  <?php if ($data->modalidadtraslado == '01') {
    $transportePublico =  $this->Controlador_model->get($data->transportistapublico, "transportepublico");
  ?>
    <table>
      <tr>
        <th>Apellidos y nombres, denominacion o razon social</th>
        <td><?= $transportePublico ? $transportePublico->razonsocial : "SIN DATOS" ?></td>
      </tr>
      <tr>
        <th>Numero de <?= $transportePublico ? $transportePublico->tipodocumento : "SIN DATOS" ?></th>
        <td><?= $transportePublico ? $transportePublico->documento : "SIN DATOS" ?></td>
      </tr>
    </table>
    <!-- PUBLICO $transportePublico -->

  <?php
  } else {
    $dataVehiculo = $this->Controlador_model->get($data->vehiculo_transporteprivado, "transporteprivado");
    $dataConductor = $this->Controlador_model->get($data->conductor_transporteprivado, "transporteprivado");
  ?>


    <table style="width:100%">
      <tr>
        <td style="width:45%">
          Datos de los vehiculos
          <table>
            <thead>
              <tr style="background-color:#969494; color:#fff">
                <th>Nro. de placa</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?= $dataVehiculo ? $dataVehiculo->documento : "SIN DATOS" ?></td>
              </tr>
            </tbody>
          </table>
        </td>
        <td style="width:45%">

          <table style="margin-left:10px">
            <tr>
              <td>Datos de los conductores</td>
            </tr>
            <tr style="background-color:#969494; color:#fff">
              <th>Tipo doc.</th>
              <th>Nro documento</th>
            </tr>
            <tr>
              <td><?= $dataConductor ? $dataConductor->tipodocumento : "SIN DATOS" ?></td>
              <td><?= $dataConductor ? $dataConductor->documento : "SIN DATOS" ?></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

  <?php } ?>
  <br>
  <div><b>DATOS DE LOS BIENES</b></div>
  <table style="margin-top:5px">
    <thead>
      <tr style="background-color:#969494; color:#fff">
        <th>Nro</th>
        <th>Cod. bien</th>
        <th>Descripcion</th>
        <th>Unidad de medida</th>
        <th>Cantidad</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($detalle as $key => $value){ ?>
        <?php
          $producto = $this->Controlador_model->get($value->producto, 'producto');
        ?>
        <tr>
        <td><?= $key+ 1?></td>
        <td><?=$producto ? $producto->codigo : "SIN DATOS"?></td>
        <td><?=$producto ? $producto->nombre : "SIN DATOS"?></td>
        <td><?= $value->medida ?></td>
        <td><?=$value->cantidad?></td>
      </tr>
      <?php } ?>
    </tbody>

  </table>
  <br>
  <!-- <table>
    <tr>
      <th>
        Observaciones
      </th>
      <td>
     
      </td>
    </tr>
  </table> -->

</body>

</html>