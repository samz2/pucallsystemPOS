<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONTROL DE STOCK EN CAJA</title>
    <style>
        @page {
            margin-top: 5px;
        }

        body {
            font-style: normal;
            font-weight: normal;
            font-family: Helvetica;
            font-size: 12px;
        }

        .table {
            margin-bottom: 10px;
        }

        tbody {
            color: #454545;
        }

        th {
            font-size: 15px;
            color: #322E2D;
        }

        thead {
            background-color: #BFBFBF;
        }

    </style>
</head>

<body>
    <?php
    $dataCaja = $this->Controlador_model->get($idcaja, "caja");
    $empresa = $this->Controlador_model->get($dataCaja->empresa, "empresa");
    ?>
    <table style="width:100%">
        <tr>
            <td style="width:30%">
                <img style="width:100px; height:100px" src="<?= base_url() ?>/files/Setting/<?= $empresa->ruc ?>.png" />
            </td>
            <td style="width:70%">
                <table style="width:100%">
                    <tr>
                        <td colspan="2" style="text-align:center; font-size:16px; font-weight:bold"><b>CONTROL DE STOCK EN CAJA</b></td>
                    </tr>
                    <tr>
                        <?php
                        $userApertura = $this->Controlador_model->get($dataCaja->usuario, "usuario");
                        $userCierre = $this->Controlador_model->get($dataCaja->usuario_cierre, "usuario");
                        ?>
                        <td style="width:25%">Usuario que aperturo:</td>
                        <td style="text-align:left"><?= ($userApertura ? $userApertura->usuario : "") ?></td>
                    </tr>
                    <tr>
                        <td style="width:25%">Usuario que cerro:</td>
                        <td style="text-align:left"><?= ($userCierre ? $userCierre->usuario : "") ?></td>
                    </tr>
                    <tr>
                        <td style="width:25%">Fecha/Hora de inicio:</td>
                        <td style="text-align:left"><?= $dataCaja->apertura ?></td>
                    </tr>
                    <tr>
                        <td style="width:25%">Fecha/Hora de final:</td>
                        <td style="text-align:left"><?= $dataCaja->cierre ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table border="1px" style="border-collapse:collapse; width:100%; text-align:center">
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Producto</th>
                <th>Categoria</th>
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
                        <td><?= $producto->codigo ?></td>
                        <td><?= $value->nombre ?></td>
                        <td><?= $categoria->nombre ?></td>
                        <td><?= $value->inicio_stock ?></td>
                        <td><?= $value->final_stock ?></td>
                    </tr>
                <?php } ?>
        </tbody>
    </table>
</body>

</html>