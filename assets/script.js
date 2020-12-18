$(document).ready(function () {
    setMode();
    autoScroll();
});

$(document).on("click", ".setting", function () {
  let isNotAutoScroll = $(this).hasClass("auto-scroll-disabled");
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

$(document).on("click", ".auto-scroll", function () {
    let isNotAutoScroll = $(this).hasClass("auto-scroll-disabled");
    Cookies.set("auto-scroll", isNotAutoScroll, {expires: 365});
    autoScroll();
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
        $('body').animate({
            scrollTop: $(document).height() - $(window).height()
        }, 500000, 'linear');
    } else {
        $('body').stop();
    }
}