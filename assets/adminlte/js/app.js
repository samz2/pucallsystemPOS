$(document).ready(function() {
  $("#loadingimg").fadeOut("slow");
  $('[data-toggle="popover"]').popover();
   // select2 intialisation
   $(".js-select-options").select2();
   // WYSIWYG summernote editor
   $('#summernote').summernote({
      height: 200,
      toolbar: [
       // [groupName, [list of button]]
       ['style', ['bold', 'italic', 'underline', 'clear']],
       ['font', []],
       ['fontsize', ['fontsize']],
       ['color', ['color']],
       ['para', ['ul', 'ol', 'paragraph']],
       ['height', ['height']]
     ]
   });
   $('#summernote2').summernote({
      height: 200,
      toolbar: [
       // [groupName, [list of button]]
       ['style', ['bold', 'italic', 'underline', 'clear']],
       ['font', []],
       ['fontsize', ['fontsize']],
       ['color', ['color']],
       ['para', ['ul', 'ol', 'paragraph']],
       ['height', ['height']]
     ]
   });
   // tooltip intialisation
   $('[data-toggle="tooltip"]').tooltip();
} );


//removing virtual keyboard on mobile and tablet
var currentState = false;

function setSize() {
  var state = $(window).width() < 961;
  if (state != currentState) {
    currentState = state;
    if (state) {
      $('.barcode').removeAttr('id');
      $('.TAX').removeAttr('id');
      $('.Remise').removeAttr('id');
    } else {
      $('.barcode').attr('id', 'keyboard');
      $('.barcode').attr('id', 'num01');
      $('.barcode').attr('id', 'num02');
    }
  }
}

setSize();
$(window).on('resize', setSize);

// slim scroll setup
//for the product list in the left side
$(function(){
   $('#productList').slimScroll({
      height: '300px',
      alwaysVisible: true,
      railVisible: true,
   });
});
// and the right side
$(function(){
   $('#productList2').slimScroll({
      height: '740px',
      allowPageScroll: true,
      alwaysVisible: true,
      railVisible: true,
   });
});

// waves paramateres
Waves.init();
Waves.attach('.flat-box', ['waves-block']);
Waves.attach('.flat-box-btn', ['waves-button']);

/***************************** LOGIN form ***********/

$( ".LoginInput" ).focusin(function() {
  $( this ).find( "span" ).animate({"opacity":"0"}, 200);
});

$( ".LoginInput" ).focusout(function() {
  $( this ).find( "span" ).animate({"opacity":"1"}, 300);
});

/******** passwors confirmation validation ****************/

var password = document.getElementById("password")
  , confirm_password = document.getElementById("confirm_password");

function validatePassword(){
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity("Passwords Don't Match");
  } else {
    confirm_password.setCustomValidity('');
  }
}

if(password) password.onchange = validatePassword;
if(confirm_password) confirm_password.onkeyup = validatePassword;



/************************* modal shifting fix ****************************/

$(document.body)
.on('show.bs.modal', function () {
    if (this.clientHeight <= window.innerHeight) {
        return;
    }
    // Get scrollbar width
    var scrollbarWidth = getScrollBarWidth()
    if (scrollbarWidth) {
        $(document.body).css('padding-right', scrollbarWidth);
        $('.navbar-fixed-top').css('padding-right', scrollbarWidth);
    }
})
.on('hidden.bs.modal', function () {
    $(document.body).css('padding-right', 0);
    $('.navbar-fixed-top').css('padding-right', 0);
});

function getScrollBarWidth () {
    var inner = document.createElement('p');
    inner.style.width = "100%";
    inner.style.height = "200px";

    var outer = document.createElement('div');
    outer.style.position = "absolute";
    outer.style.top = "0px";
    outer.style.left = "0px";
    outer.style.visibility = "hidden";
    outer.style.width = "200px";
    outer.style.height = "150px";
    outer.style.overflow = "hidden";
    outer.appendChild (inner);

    document.body.appendChild (outer);
    var w1 = inner.offsetWidth;
    outer.style.overflow = 'scroll';
    var w2 = inner.offsetWidth;
    if (w1 == w2) w2 = outer.clientWidth;

    document.body.removeChild (outer);

    return (w1 - w2);
};
