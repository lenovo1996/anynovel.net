$(document).ready(function () {
    setMode();
    autoScroll();

    let isAutoScroll = Cookies.get("auto-scroll") == 'true';
    if (isAutoScroll) {
        $('.setting').removeClass('auto-scroll');
    }
});

$(document).on("click", ".setting", function () {
    let isNotAutoScroll = $(this).hasClass("auto-scroll");
    Cookies.set("auto-scroll", isNotAutoScroll, {expires: 365});
    autoScroll();
});

$(document).on("click", ".switch-mode", function () {
    let mode = Cookies.get("mode");

    $("body").removeClass("day night");

    if (mode == 'day') {
        Cookies.set("mode", "night", {expires: 365});
        $("body").addClass("night");
    } else {
        Cookies.set("mode", "day", {expires: 365});
        $("body").addClass("day");
    }
});

function setMode() {
    let mode = Cookies.get("mode");
    if (!mode) {
        Cookies.set("mode", "day", {expires: 365});
        $("body").addClass("day");
    } else {
        $("body").removeClass("day night");
        $("body").addClass(mode);
    }
}

function autoScroll() {
    let isAutoScroll = Cookies.get("auto-scroll");
    if (isAutoScroll == 'true') {
        $('html, body').animate({
            scrollTop: $(document).height() - $(window).height()
        }, 500000, 'linear');
    } else {
        $('html, body').stop();
    }
}