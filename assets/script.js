$(document).ready(function () {
    setMode();

    let isAutoScroll = Cookies.get("auto-scroll") == 'true';
    if (isAutoScroll) {
        $('.setting').removeClass('auto-scroll');
    } else {
        $('.setting').addClass('auto-scroll');
    }

    //autoScroll();
    sendGAEventAds();
});

$(document).on("click", ".setting", function () {
    let isNotAutoScroll = $(this).hasClass("auto-scroll");
    Cookies.set("auto-scroll", isNotAutoScroll, {expires: 365});
    $(this).toggleClass('auto-scroll');
    autoScroll();
});


$(document).on("click", ".close-ads", function () {
    $('.ads-block').fadeOut();
    $('body').removeClass('show-ads');
    Cookies.set('intv-gtag-ads', '300000', {expires: 365});
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
        Cookies.set("mode", "night", {expires: 365});
        $("body").addClass("night");
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

function sendGAEventAds() {
    let timeout = Cookies.get('intv-gtag-ads');

    if (!timeout) {
        timeout = '300000';
    }

    timeout = parseInt(timeout);

    setTimeout(function () {
        timeout = timeout - 1000;
        Cookies.set('intv-gtag-ads', timeout, {expires: 365});

        if (timeout <= 0) {
            //$('.ads-block').fadeIn();
            //$('body').addClass('show-ads');
            gtag('event', 'Show Ads', {
                'event_category': 'Show Ads',
                'event_label': 'Show Ads'
            });
            Cookies.set('intv-gtag-ads', '300000', {expires: 365});
        }

        sendGAEventAds();
    }, 1000);
}
