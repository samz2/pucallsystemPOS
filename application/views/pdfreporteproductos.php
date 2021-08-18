<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>CAJA GENERAL</title>
</head>
<style type="text/css">
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

    /* Responsive Table */
    table.focus-on tbody tr.focused th,
    table.focus-on tbody tr.focused td {
        background-color: #317eeb;
        color: #ffffff;
    }

    .label-text {
        display: inline-block;
        font-size: 10px;
        font-weight: bold;
        border-radius: 5px;
        color: #404040;
        padding: 3px;
    }

    .label-success {
        background-color: #33b86c;

    }

    .label-danger {
        background-color: #ef5350;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    

    thead {
        background-color: #BFBFBF;
    }

    #cab {
        font-style: oblique;
    }

    .borderbottom{
        border-bottom: 1px solid  #BFBFBF;
    }
</style>

<body>
    <?php $perfil = $this->Controlador_model->get($this->session->userdata('perfil'), 'perfil'); ?>
    <table border="0" >
        <tr>
            <td >REPORTE</td>
            <th style="text-align: center;"><?= $empresa->razonsocial ?></th>
            <td align="right"><?= date('d/m/Y', time()) ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td align="center"><?= $perfil->nombre ?> ADMINISTRACION GENERAL</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td align="center"><?= date('d/m/Y') ?></td>
            <td>&nbsp;</td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <table border="0" class="borderbottom" style="border-collapse:collapse">
        <thead>
            <tr>
                <th>ORDEN</th>
                <th>CODIGO</th>
                <th>CODIGO BARRA</th>
                <th>NOMBRE</th>
                <th>STOCK</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $key => $value) {  ?>

                <?php $stock = $this->db->where("producto", $value->id)->get("stock")->row() ?>
                <tr >

                    <td class="borderbottom"><?= ($key + 1) ?></td>
                    <td class="borderbottom"><?= $value->codigo ?></td>
                    <td class="borderbottom"><?= $value->codigoBarra ?></td>
                    <td class="borderbottom"><?= $value->nombre ?></td>
                    <td class="borderbottom"><?= ($stock ? $stock->cantidad : 0) ?></td>
                    <td class="borderbottom"><?= $value->estado == "0" ? "<label class='label-text label-success'>ACTIVO</label>" : "<label class='label-text label-danger'>INACTIVO</label>" ?></td>
                </tr>

            <?php } ?>
        </tbody>
    </table>
</body>

</html>