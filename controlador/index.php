<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous"> -->

    <title>StartController</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet" media="all">
</head>

<body>
    <form>
        <label class="toggle">
            <div class="toggle__wrapper">
                <input id="act" checked="false" type="checkbox">
                <div class="toggle__bg">
                </div>
            </div>
        </label>
    </form>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.socket.io/4.1.2/socket.io.min.js" integrity="sha384-toS6mmwu70G0fw54EGlWWeA4z3dyJ+dlXBtSURSKN4vyRFOcxd3Bzjj/AoOwY+Rg" crossorigin="anonymous"></script>
    <script>
        var act = false;
        const server = "https://startco-votes.herokuapp.com/" //"http://localhost:5000/";
        let av_active = false
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
            //habilitar(false)
            console.log("CONNECTED", socket.id);
        });
        socket.on("connect_error", (err) => {
            console.log("socket connected error --> " + err);
        });
        socket.io.on("ping", () => {
            console.error("PING", error);
        });
        socket.on("habilitar", function(data) {
            console.log("Habilitar FT", data);

            av_active = true
            $("#act").attr('checked', data);
            $('#act').prop('checked', data);
            act = data
            if (!av_active) {

            }
        });

        $(document).ready(function() {
            // $("#act").attr('checked', false);
            // $('#act').prop('checked', false);
            $(".toggle").click(function() {
                if ($("#act").is(":checked") != act) {
                    act = $("#act").is(":checked")
                    habilitar(act);
                    if ($("#act").is(":checked")) {
                        console.log("Check box in Checked");
                    } else {
                        console.log("Check box is Unchecked");
                    }
                }
            });
        });

        function habilitar(est) {
            console.log("Estado del botón", est);
            socket.emit(
                "habilitar", est,
                (data) => {
                    console.log("habilitar", data); // data will be "woot"
                }
            );
        }
    </script>
</body>

</html>