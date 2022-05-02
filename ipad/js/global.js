(function ($) {
  "use strict";
  /*==================================================================
        [ Daterangepicker ]*/
  try {
    var isClick = 0;

    $(window).on("click", function () {
      isClick = 0;
    });
  } catch (er) {
    console.log(er);
  }

  try {
    var selectSimple = $(".js-select-simple");

    selectSimple.each(function () {
      var that = $(this);
      var selectBox = that.find("select");
      var selectDropdown = that.find(".select-dropdown");
      selectBox.select2({
        dropdownParent: selectDropdown,
      });
    });
  } catch (err) {
    console.log(err);
  }
})(jQuery);

const server = "https://startco-votes.herokuapp.com/"; //"http://localhost:5000/";
const socket = io.connect(server, {
  reconnectionDelay: 1000,
  reconnection: true,
  reconnectionAttempts: 10,
  transports: ["websocket"],
  // agent: false,
  // upgrade: false,
  // rejectUnauthorized: false
});
socket.io.on("error", (error) => {
  console.error("ERROR", error); //
});
socket.on("connect", () => {
  io = socket.id;
  console.log("CONNECTED", socket.id);
});
socket.on("connect_error", (err) => {
  console.log("socket connected error --> " + err);
});
socket.io.on("ping", () => {
  console.error("PING", error);
});

socket.on("habilitar", function (data) {
  console.log("habilitar", !data);
  $("#button").prop("disabled", !data);
  $("#turno").html("Sin turno");
});
socket.on("timeout", function (data) {
  console.log("timeout", data);
  $("#button").prop("disabled", data);
  $("#turno").html("Sin turno");
});

socket.on("check-inv", function (data) {
  console.log("check-inv", data);
  $(".inv").addClass("active");
  $.each(data, function (key, val) {
    console.log("INVS", $("#inv_" + val.id));
    $("#inv_" + val.id).removeClass("active");
  });
  invs = data;
});

socket.on("orden", function (data) {
  console.log("ORDEN", data);
  const index = data.findIndex((object) => {
    return object.io == io;
  });
  console.log("orden", index);
  if (index != -1) $("#turno").html("Tu turno es el " + (index + 1));
});

var io = null;
var inv = 0;
var orden = [];
var id = 0;
var tl, tl2, tl3;
var invs = [];

$(document).ready(function () {
  $(".full").click(function () {
    $(".full").fadeOut("slow");
    console.log("full");
    document.documentElement.requestFullscreen({ navigationUI: "hide" });
  });

  // alert(
  //   $(document).width() +
  //     "," +
  //     $(document).height() +
  //     "," +
  //     $(window).width() +
  //     "," +
  //     $(window).height()
  // );
  $("#button").click(function () {
    var est = $("#button").prop("disabled");
    if (!est) {
      $("#button").prop("disabled", true);
      console.log("click", "sehabilitando");
      socket.emit("solicitar", {
        id: inv,
        io: io,
      });
    }
  });
  $.getJSON("inversionistas/inversionistas.json", function (data) {
    var items = [];

    $.each(data, function (key, val) {
      var item = $(
        '<li class="inv active" data-nombre="' +
          val.nombre +
          '" id="inv_' +
          val.id +
          '"> <img class="img-fluid mb-2" src="inversionistas/' +
          val.foto +
          '"/><h3>' +
          val.nombre +
          "</h3><h4>" +
          val.fondo +
          "</h4></li>"
      );
      // console.log("val", val, item);
      $("#inversionistas").append(item);

      $.each(invs, function (key, val) {
        console.log("INVS", $("#inv_" + val.id));
        $("#inv_" + val.id).removeClass("active");
      });
    });
    $("#welcome .inv").click(function () {
      var id_inv = parseInt($(this).attr("id").replace("inv_", ""));
      // console.log("CLICK WELCOME", id_inv);
      if ($(this).hasClass("active")) {
        inv = id_inv;
        $("#inversionista-info h2").text($(this).attr("data-nombre"));
        tl.play();
        socket.emit(
          "add-inv",
          {
            est: false,
            id: id_inv,
          },
          (data) => {
            console.log("Welcome inv", data); // data will be "woot"
          }
        );
      }
    });
  });

  $(".logoabc").click(function () {
    location.reload();
  });

  tl = gsap.timeline({
    duration: 1,
    paused: true,
    ease: Power1.easeOut.easeOut,
  });
  tl.to("#welcome", {
    display: "none",
    autoAlpha: 0,
    y: "-50%",
  });
  tl.fromTo(
    "#form",
    {
      display: "none",
      autoAlpha: 0,
      y: "50%",
    },
    {
      display: "flex",
      autoAlpha: 1,
      y: 0,
    },
    "=-0.5"
  );
});
