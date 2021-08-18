/**
 * JQuery plugin to transform input in <input> or <textarea> automatically to uppercase and
 * delete acuted chars
 *
 * @author Jaime Hablutzel
 */

(function($) {
    $.fn.mayusculassintildes = function(options ) {

        var settings = $.extend( {
              'allownumbers': false
            }, options);


        this.each(function () {
            var $this = $(this);
            if ($this.is('textarea') || $this.is('input:text')) {
                $this.keypress(function (e) {

                    if (e.charCode == 0) { // for firefox
                        return true;
                    }

                    var pressedKey = e.charCode;

                    // caracteres tildados a su equivalente sin tilde
                    (pressedKey == 225 || pressedKey == 193) ? pressedKey = 97 : ""; //a
                    (pressedKey == 233 || pressedKey == 201) ? pressedKey = 101 : "";//e
                    (pressedKey == 237 || pressedKey == 205) ? pressedKey = 105 : "";   //i
                    (pressedKey == 243 || pressedKey == 211) ? pressedKey = 111 : "";//o
                    (pressedKey == 250 || pressedKey == 218) ? pressedKey = 117 : "";   //u
                    var str = String.fromCharCode(pressedKey);


                    var regex = /[a-zñ0-9-#. ]/i;

                    if (settings.allownumbers) {
                        regex = /[a-zñ0-9 ]/i;
                    }

                    // restrictive REGEX
                    if (str.match(regex)) {
                        var startpos = this.selectionStart;
                        var endpos = this.selectionEnd;
                        this.value = this.value.substr(0, startpos) + str.toUpperCase() + this.value.substr(endpos);
                        this.setSelectionRange(startpos + 1, startpos + 1);
                    }
                    return false;

                });
            }
        });
    };

})(jQuery);
