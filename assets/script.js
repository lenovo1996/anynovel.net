$(document).ready(function () {
  setMode();
});

$(document).on("click", ".switch-mode", function () {
  let mode = Cookies.get("mode");

  $("body").removeClass("day night");
  
  if (mode == 'day') {
    Cookies.set("mode", "night", { expires: 365 });
    $("body").addClass("night");
  } else {
    Cookies.set("mode", "day", { expires: 365 });
    $("body").addClass("day");
  }
});

function setMode() {
  let mode = Cookies.get("mode");
  if (!mode) {
    Cookies.set("mode", "day", { expires: 365 });
    $("body").addClass("day");
  } else {
    $("body").removeClass("day night");
    $("body").addClass(mode);
  }
}
