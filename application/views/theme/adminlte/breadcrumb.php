<div class="row margentop">
  <div class="col-sm-12">
  <ol class="breadcrumb pull-right" style="margin:0px; padding:0px">
      <li><a href="<?= base_url() ?>"> Dashboard</a></li>
      <?php if(isset($breads)){
         for($i = 0; $i < count($breads); $i++){ ?>
          <li><a href="<?php echo $breads[$i]['ruta'] ?>"><?php echo $breads[$i]['titulo'] ?></a></li>
      <?php }
        } ?>
  </ol>
  </div>
</div>
