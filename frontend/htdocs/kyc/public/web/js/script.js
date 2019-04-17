'use strict';

$(document).ready(function () {

    setTimeout(function () {
        $('.loader_bg').fadeToggle();
    }, 1500);

    $('.menu-icon').on('click', function () {
        $(this).toggleClass('on');
        if ($(this).hasClass('on')) {
            $('.one').addClass('fadeOut animated');
            setTimeout(function () {
                $('.one').addClass('hide').removeClass('fadeOut animated');
                $('.info').toggleClass('hide fadeIn animated');
                setTimeout(function () {
                    $('.info').removeClass('fadeIn animated');
                }, 1200);
            }, 1200);
        } else {
            $('.info').addClass('fadeOut animated');
            setTimeout(function () {
                $('.info').addClass('hide').removeClass('fadeOut animated');
                $('.one').toggleClass('hide fadeIn animated');
                setTimeout(function () {
                    $('.one').removeClass('fadeIn animated');
                }, 1200);
            }, 1200);
        }
    });


    // function([string1, string2],target id,[color1,color2])
    consoleText(['With incredible trading features'], "text");

    function consoleText(words, id) {
        var visible = true;
        var letterCount = 1;
        var x = 1;
        var waiting = false;
        var target = document.getElementById(id);
        window.setInterval(function() {

            if (letterCount === 0 && waiting === false) {
                waiting = true;
                target.innerHTML = words[0].substring(0, letterCount);
                window.setTimeout(function() {
                    var usedWord = words.shift();
                    words.push(usedWord);
                    x = 1;
                    letterCount += x;
                    waiting = false;
                }, 500)
            } else if (letterCount === words[0].length + 1 && waiting === false) {
                waiting = true;
                window.setTimeout(function() {
                    x = -1;
                    letterCount += x;
                    waiting = false;
                }, 1000)
            } else if (waiting === false) {
                target.innerHTML = words[0].substring(0, letterCount);
                letterCount += x;
            }
        }, 70)
    }

});

