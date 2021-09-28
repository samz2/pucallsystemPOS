<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>COMPROBANTE</title>
  <!-- Bootstrap Core CSS -->
  <script type="text/javascript">
    window.print();
  </script>
  <style type="text/css">
    html {
      margin: 0;
    }

    body {
      font-family: Helvetica, Arial, Verdana, sans-serif;
      /*Trebuchet MS,*/
      margin: 5mm 3mm 2mm 3mm;
      font-style: normal;
      font-size: 8pt !important;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    td {
      padding: 0;
    }

    .tabla-contado tr td {
      border: 0.5px solid #000;
      border-collapse: collapse !important;
    }

    .negrita {
      font-weight: bold;
    }
  </style>
</head>

<body>
  <?php
  $dataCaja = $this->Controlador_model->get($idcaja, "caja");
  $empresa = $this->Controlador_model->get($dataCaja->empresa, "empresa");
  $usuarioApertura = $this->Controlador_model->get($dataCaja->usuario, "usuario");
  $usuarioCierre = $this->Controlador_model->get($dataCaja->usuario_cierre, "usuario");
  ?>
  <table style="width:100%">
    <tr>
      <td style="text-align:center; font-size:16px; font-weight:bold"><b>CONTROL DE STOCK EN CAJA</b></td>
    </tr>
    <tr>
      <td style="text-align:center; font-size:16px; font-weight:bold"><b><?= $dataCaja->descripcion ?></b></td>
    </tr>
  </table>
  <hr>
  <table>
    <tr>
      <td><b>APERTURA:</b> <?= ($usuarioApertura ? $usuarioApertura->usuario : "SIN DATOS") ?> | <?= $dataCaja->apertura ?></td>
    </tr>
    <tr>
      <td><b>CIERRE:</b> <?= ($usuarioCierre ? $usuarioCierre->usuario : "") ?>  <?= (is_null($dataCaja->cierre) ? "" : " | ".$dataCaja->cierre) ?></td>
    </tr>
  </table>

  <table border="1px" style="border-collapse:collapse; width:100%; text-align:center; margin-top:4px">
    <thead>
      <tr>
        <th>Producto</th>
        <th>Inicio de Stock</th>
        <th>Final de Stock</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $query = $this->db->where("caja", $idcaja)->get("cajastock")->result();
      ?>
      <?php foreach ($query as $value) { ?>
        <?php
        $categoria = $this->db->where("id", $value->categoria)->get("productocategoria")->row();
        $producto = $this->db->where("id", $value->producto)->get("producto")->row();
        ?>
        <tr>
          <td><?= $value->nombre ?></td>
          <td><?= $value->inicio_stock ?></td>
          <?php
          if ($dataCaja->estado == "0") {
          ?>
            <td><?= ($value->final_stock == 0 ? "" : $value->final_stock) ?></td>
          <?php

          } else {
          ?>
            <td><?= $value->final_stock ?></td>
          <?php
          }
          ?>
        </tr>
      <?php } ?>
    </tbody>
  </table>

</body>

</html>