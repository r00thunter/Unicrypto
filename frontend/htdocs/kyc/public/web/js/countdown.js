// -----------------------------------------------------------------------------------------------------
// COUNTDOWN
// -----------------------------------------------------------------------------------------------------

var ctd = document.getElementById('countdown');

countdown();

function countdown() {
    // ATTENTION - Ianuary is 0, February is 1 ......
    var launch_date = new Date(Date.UTC(2018, 6, 6, 0, 0));
    var days;
    var hours;
    var minutes;
    var seconds;
    var rest;
    var now = new Date();

    seconds = rest = Math.floor(((launch_date.getTime() - now.getTime()) / 1000));

    days = zero(Math.floor(seconds / 86400));
    seconds -= days * 86400;

    hours = zero(Math.floor(seconds / 3600));
    seconds -= hours * 3600;

    minutes = zero(Math.floor(seconds / 60));
    seconds -= minutes * 60;

    seconds = zero(Math.floor(seconds));

    function zero(n) {
        return (n < 10 ? '0' : false) + n;
    }

    rest <= 0 ? days = hours = minutes = seconds = '00' : setTimeout(countdown, 1000);

    ctd.innerHTML =
        '<li><div><span>' + days + '</span><br> day' + (days > 1 ? 's' : '') + '</div></li>' +
        '<li><div><span>' + hours + '</span><br> hour' + (hours > 1 ? 's' : '') + '</div></li>' +
        '<li><div><span>' + minutes + '</span><br> minute' + (minutes > 1 ? 's' : '') + '</div></li>' +
        '<li><div><span>' + seconds + '</span><br> second' + (seconds > 1 ? 's' : '') + '</div></li>';
}
