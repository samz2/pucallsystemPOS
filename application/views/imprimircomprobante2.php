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

        .negrita {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td style="text-align:center">
            <img src="<?= base_url().'files/Setting/'.$empresa->ruc ?>.png" width="100%">
            </td>
        </tr>
        <!--
        <tr>
            <td align="center" class="negrita"><?= $empresa->razonsocial ?></td>
        </tr>
        -->
        <tr>
            <td align="center" class="negrita"><?= $empresa->distrito . ' - ' . $empresa->provincia . ' - ' . $empresa->departamento ?></td>
        </tr>
        <tr>
            <td align="center"><?= ' TELF. ' . $empresa->telefono ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td align="center" class="negrita"><?= 'RECIBO DE INGRESO N째 ' . $ingreso->id ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
    <table>
        <tr>
            <td><span class="negrita">CAJERO(A):</span> <?= $usuario->nombre ?></td>
        </tr>

        <tr>
            <td><span class="negrita">CLIENTE:</span> <?= $cliente->nombre . ' ' . $cliente->apellido ?></td>
        </tr>
        <!--
      <tr>
        <td><span class="negrita">CODIGO:</span> <?= $venta->codigo ?></td>
      </tr>
      <tr>
        <td><span class="negrita">SERIE DE COBRANZA:</span> <?= $tienda->serie ?></td>
      </tr>
      -->
        <tr>
            <td><span class="negrita">INICIO:</span> <?= $venta->created ?></td>
        </tr>
        <tr>
            <td><span class="negrita">FINAL:</span> <?= $venta->vence ?></td>
        </tr>
        <tr>
            <td><span class="negrita">FECHA DE PAGO:</span> <?= $ingreso->created ?></td>
        </tr>
        <tr>
            <td><span class="negrita">HORA: </span> <?= $ingreso->hora ?></td>
        </tr>
        <tr>
            <td><span class="negrita">MODO: </span> <?= $ingreso->metodopago ?></td>
        </tr>
        
    </table>
    <hr>
    <table>
        <tr>
            <th style="width: 50%;">DESCRIPCION</th>
            <th align="right">TOTAL</th>
        </tr>
        <tr>
            <td><?= $ingreso->observacion ?></td>
            <td align="right"><?= $ingreso->monto ?></td>
        </tr>
    </table>
    <hr>
    <table>
        <tr>
            <th align="right">DEUDA S/ <?= $ingreso->restaventa ?></th>
        </tr>
        <tr>
            <td><span class="negrita">IMPORTE EN LETRA:</span> <?= num_to_letras($ingreso->monto) ?></td>
        </tr>
    </table>
    <hr>
    <table>
        <tr>
            <td align="center">NO SE ACEPTAN DEVOLUCIONES Y/O</td>
        </tr>
        <tr>
            <td align="center">CAMBIOS DESPUES DE LOS 2 DIAS</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
</body>

</html>