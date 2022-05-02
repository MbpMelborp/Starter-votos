<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <title>STARTCO CONTROL</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet" media="all">
</head>

<body>
    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="inversionistas-wrap ">
            <h1>Inversionistas</h1>
            <ul id="inversionistas" class="row justify-content-center">

            </ul>
            <div id="estado">
                <span>
                    DESACTIVADO
                </span>

            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.7.1/gsap.min.js" integrity="sha512-UxP+UhJaGRWuMG2YC6LPWYpFQnsSgnor0VUF3BHdD83PS/pOpN+FYbZmrYN+ISX8jnvgVUciqP/fILOXDjZSwg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdn.socket.io/4.1.2/socket.io.min.js" integrity="sha384-toS6mmwu70G0fw54EGlWWeA4z3dyJ+dlXBtSURSKN4vyRFOcxd3Bzjj/AoOwY+Rg" crossorigin="anonymous"></script>
    <script>
        var images = [];
        var act = false;
        var inversionistas = null
        var interval = null
        var size = 12;
        const server = "https://startco-votes.herokuapp.com/" //"http://localhost:5000/";
        const socket = io.connect(server, {
            reconnectionDelay: 1000,
            reconnection: true,
            reconnectionAttempts: 10,
            transports: ["websocket"],
        });
        socket.io.on("error", (error) => {
            console.error("ERROR", error); //
        });
        socket.on("check-inv", function(data) {
            console.log("check-inv", data);
            $.each(data, function(key, val) {
                console.log("INVS", $("#inv_" + val.id));
                $("#inv_" + val.id).addClass("active");
            });
            invs = data;
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

        socket.on("habilitar", function(data) {

            console.log("habilitar", data);
            if (data) {
                $("#estado").addClass("ok");
                $("#estado span").text("ACTIVADO");
            } else {
                $("#estado").removeClass("ok");
                $("#estado span").text("DESACTIVADO");

                $(".inv .order").removeClass("ok").html("-");
                $(".inv .order").removeClass("not").html("-");
            }
        });

        socket.on("orden", function(data) {
            console.log("ORDEN", data);
            $.each(data, function(key, val) {
                console.log("INVS", $("#inv_" + val.id));
                $("#inv_" + val.id).addClass("active");
                $("#inv_" + val.id + " .order").html(key + 1);
                if (key < 5) {
                    $("#inv_" + val.id + " .order").addClass("ok")
                } else {
                    $("#inv_" + val.id + " .order").addClass("not")
                }
            });
        });

        $(document).ready(function() {
            $.getJSON("inversionistas/inversionistas.json", function(data) {
                inversionistas = data;
                $.each(data, function(key, val) {
                    var item = $(
                        '<li class="inv" data-nombre="' +
                        val.nombre +
                        '" id="inv_' +
                        val.id +
                        '"><span class="order"> - </span> <span class="estado"></span>  <img class="img-fluid mb-2" src="inversionistas/' +
                        val.foto +
                        '"/><h3>' +
                        val.nombre +
                        "</h3><h4>" +
                        val.fondo +
                        "</h4></li>"
                    );
                    // console.log("val", val, item);
                    $("#inversionistas").append(item);
                })
            })
        });

        function getInversionista(id) {
            var inv = inversionistas.filter(function(inv) {
                return inv.id == id;
            });
            return inv[0];
        }

        function initVotes() {
            console.log("INIT VOTES")
            $("#inversionistas").html("")
            for (let index = 0; index < size; index++) {
                images[index] = new Image();
                images[index].src = "inversionistas/" + (index + 1) + ".png";
                var item = $('<li class="inv" id="inv-' + (index + 1) + '"><div class="inv-i"> <h4>' + (index + 1) + '</h4><img id="inv-' + (index + 1) + '-img" class="img-fluid mb-2" src="inversionistas/nop.png"><h3 id="inv-' + (index + 1) + '-name">&nbsp;</h3><h5 id="inv-' + (index + 1) + '-fondo">&nbsp;</h5></div></li>');
                $("#inversionistas").append(item);
                item.delay(index * 20).fadeIn(500);
            }
        }
    </script>
</body>

</html>