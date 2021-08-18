<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <link rel="shortcut icon" href="<?= base_url().RECURSOS ?>images/favicon.ico">
  <!-- favicon -->
  <link rel="shortcut icon" href="<?= base_url().RECURSOS ?>/favicon.ico" type="image/x-icon">
  <link rel="icon" href="<?= base_url().RECURSOS ?>/favicon.ico" type="image/x-icon">  
  <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />  
  <title>PUCALLRESTO</title>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css'>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css'>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome-animation/0.3.0/font-awesome-animation.min.css'><link rel="stylesheet" href="<?= base_url().RECURSOS ?>css/login.css"  type='text/css'>
</head>
<body>
<!-- partial:index.partial.html -->
<header class="top-header">
</header>
<!--dust particel-->
<div>
  <div class="starsec"></div>
  <div class="starthird"></div>
  <div class="starfourth"></div>
  <div class="starfifth"></div>
</div>
<!--Dust particle end--->

<div class="container text-center text-dark mt-5">
  <div class="row">
    <div class="col-lg-4 d-block mx-auto">
      <div class="row">
        <div class="col-xl-12 col-md-12 col-md-12">
          <div class="card">
            <div class="card-body wow-bg" id="formBg">
                      <?php if($empresa->logo) { ?>
          <img src="<?= base_url() ?>files/Setting/<?= $empresa->logo ?>" alt="logo"  style='max-height: 45px; max-width: 200px; margin: 0 auto'>
        <?php } else { ?>
          <img src="<?= base_url().RECURSOS ?>img/logo.png" alt="logo">
        <?php } ?>

              <p class="text-muted">Iniciar sesión en su cuenta</p>

       <?= validation_errors() ?>
       <form id="signupForm" action="" method="post" novalidate="novalidate" role="form" autocomplete="off">         
              <div class="input-group mb-3">
                <input type="text" name="usuario" id="usuario" class="form-control textbox-dg" placeholder="usuario" required> 
              </div>
              <div class="input-group mb-4"> 
                <input type="password" name="password" class="form-control textbox-dg" placeholder="Password" required> 
              </div>

              <div class="row">
                <div class="col-12"><input type="submit" class="btn btn-primary btn-block logn-btn" value="Entrar"> </div>
              </div>

              <div class="mt-6 btn-list">
                <button type="button" class="socila-btn btn btn-icon btn-facebook fb-color"><i class="fab fa-facebook-f faa-ring animated"></i></button> <button type="button" class="socila-btn btn btn-icon btn-google incolor"><i class="fab fa-linkedin-in faa-flash animated"></i></button> <button type="button" class="socila-btn btn btn-icon btn-twitter tweetcolor"><i class="fab fa-twitter faa-shake animated"></i></button> <button type="button" class="socila-btn btn btn-icon btn-dribbble driblecolor"><i class="fab fa-dribbble faa-pulse animated"></i></button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
    </form> 
</div>
<!-- partial -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script><script  src="<?= base_url().RECURSOS ?>js/login.js"></script>
    <!-- jQuery -->
  <script type="text/javascript" src="<?= base_url().RECURSOS ?>js/jquery-2.2.2.min.js"></script>
  <!-- waves material design effect -->
  <script type="text/javascript" src="<?= base_url().RECURSOS ?>js/waves.min.js"></script>
  <!-- Bootstrap Core JavaScript -->
  <script type="text/javascript" src="<?= base_url().RECURSOS ?>js/bootstrap.min.js"></script>
  <!--form validation-->
  <script type="text/javascript" src="<?= base_url().RECURSOS ?>jquery.validate/jquery.validate.min.js"></script>
  <!--form validation init-->
  <script src="<?= base_url().RECURSOS ?>jquery.validate/form-validation-init.js"></script>
  <script src="<?= base_url().RECURSOS ?>js/localization/messages_es.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#usuario').focus();
      $('#login-modal').modal('show').on('hide.bs.modal', function (e) {
        e.preventDefault();
      });
    });
  </script>
</body>
</html>
