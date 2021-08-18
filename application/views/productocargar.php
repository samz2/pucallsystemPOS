<?= $this->session->flashdata('mensaje') ?>
<div class="row">
  <div class="col-xs-12">
      <div class="portlet">
        <form action="<?= $this->url ?>/crear" class="form-horizontal " method="POST" id="form_principal" role="form">
          <div class="portlet-heading">
            <h3 class="portlet-title text-dark">Lista de <?= $this->titulo_controlador ?></h3>
            <div class="portlet-widgets">
              <button class="btn btn-info btn-xs" type="submit"><i class="fa fa-save"></i> Guardar</button>
            </div>
            <div class="clearfix"></div>
          </div>
          <!-- /.box-header -->
          <div class="portlet-body table-responsive">
            <table id="example1" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>TIPO</th>
                  <th>CATEGORIA</th>
                  <th>NOMBRE</th>
                  <th>PRECIO VENTA</th>
                  <th>DESCRIPCION</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 0; foreach($datas as $data) { ?>
                  <?php if(isset($data["A"]) != "") { $i++; ?>
                    <tr>
                      <input type='hidden' name='tipo<?= $i ?>' id='tipo<?= $i ?>' value='<?= isset($data["A"]) ? $data["A"] : '' ?>' />
                      <input type='hidden' name='categoria<?= $i ?>' id='categoria<?= $i ?>' value='<?= isset($data["B"]) ? $data["B"] : '' ?>' />
                      <input type='hidden' name='nombre<?= $i ?>' id='nombre<?= $i ?>' value='<?= isset($data["C"]) ? $data["C"] : '' ?>' />
                      <input type='hidden' name='precioventa<?= $i ?>' id='precioventa<?= $i ?>' value='<?= isset($data["D"]) ? $data["D"] : '' ?>' />
                      <input type='hidden' name='descripcion<?= $i ?>' id='descripcion<?= $i ?>' value='<?= isset($data["E"]) ? $data["E"] : '' ?>' />
                      <td><?= $i ?></td>
                      <td><?= isset($data["A"]) ? $data["A"] : '' ?></td>
                      <td><?= isset($data["B"]) ? $data["B"] : '' ?></td>
                      <td><?= isset($data["C"]) ? $data["C"] : '' ?></td>
                      <td><?= isset($data["D"]) ? $data["D"] : '' ?></td>
                      <td><?= isset($data["E"]) ? $data["E"] : '' ?></td>
                    </tr>
                  <?php } ?>
                  <input type='hidden' name='contador' id='contador' value='<?= $i ?>' />
                <?php } ?>
              </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </form>
      </div>
      <!-- /.box -->
  </div>
</div>
