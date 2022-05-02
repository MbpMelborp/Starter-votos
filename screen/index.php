<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <title>STARTCO SCREEN</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <?php if (isset($_REQUEST["full"])) { ?>
        <?php if (isset($_REQUEST["black"])) { ?>
            <link href="css/main_norm_black.css?v=3" rel="stylesheet" media="all">
        <?php } else { ?>
            <link href="css/main_norm.css?v=3" rel="stylesheet" media="all">
        <?php } ?>
    <?php } else { ?>
        <link href="css/main.css?v=3" rel="stylesheet" media="all">
    <?php } ?>
    <style>
        .inv {
            width: <?php isset($_REQUEST["size"]) ? print($_REQUEST["size"] / 1) : print(12 / 1) ?>%;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">

    <div class="wrapper d-flex justify-content-center align-items-center">
        <div class="loop">
            <video id="loop" playsinline controls="hidden" autoplay loop preload="metadata" style="pointer-events: none;">
                <source src="https://startco-vids.s3.amazonaws.com/startco/loop.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="timer">
            <video id="counter" playsinline controls="hidden" autoplay paused preload="metadata" style="pointer-events: none;">
                <source src="https://startco-vids.s3.amazonaws.com/startco/counter.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <div class="inversionistas-wrap ">
            <div class="inversionistas-wrap-int">

                <ul id="inversionistas" class="row justify-content-start align-items-center flex-nowrap">
                </ul>
                <video id="timer" paused playsinline controls="hidden" paused preload="metadata" style="pointer-events: none;">
                    <?php if (isset($_REQUEST["test"])) { ?>
                        <source src="https://startco-vids.s3.amazonaws.com/startco/timer_test.mp4" type="video/mp4">
                    <?php } else { ?>
                        <?php if (isset($_REQUEST["hd"])) { ?>
                            <?php if (isset($_REQUEST["black"])) { ?>
                                <?php if (isset($_REQUEST["v"])) { ?>
                                    <source src="https://startco-vids.s3.amazonaws.com/startco/timer-hd-black3.mp4" type="video/mp4">
                                <?php } else { ?>
                                    <source src="https://startco-vids.s3.amazonaws.com/startco/timer-hd-black4.mp4" type="video/mp4">
                                <?php } ?>
                            <?php } else { ?>
                                <source src="https://startco-vids.s3.amazonaws.com/startco/timer-hd.mp4" type="video/mp4">
                            <?php } ?>
                        <?php } else { ?>
                            <source src="https://startco-vids.s3.amazonaws.com/startco/timer.mp4" type="video/mp4">
                        <?php } ?>
                    <?php } ?>
                    Your browser does not support the video tag.
                </video>
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
        var inver_size = <?php isset($_REQUEST["size"]) ? print($_REQUEST["size"]) : print(12) ?>;
        var orden_act = []
        var total_time = 20
        var timeo = null
        var time_20 = total_time;
        var interval_20 = null;
        var habilitar_orden = false
        var pass_orden = true
        const server = "https://startco-votes.herokuapp.com/" //"http://localhost:5000/";
        const socket = io.connect(server, {
            reconnectionDelay: 1000,
            reconnection: true,
            reconnectionAttempts: 10,
            transports: ["websocket"],
        });


        function str_pad_left(string, pad, length) {
            return (new Array(length + 1).join(pad) + string).slice(-length);
        }


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
        socket.on("habilitar", function(data) {

            console.log("habilitar", data);
            finishInterval()
            if (data) {

                var timer = document.getElementById('timer');
                timer.pause();
                timer.currentTime = 0;
                timer.play();
                // initVotes();
            } else {
                initVotes();
                $(".loop").show();
                $(".timer").hide();
                $(".inversionistas-wrap").hide();
                var loop = document.getElementById('loop');
                loop.pause();
                loop.currentTime = 0;
                loop.play();
                loop.volume = 0;
            }
        });


        socket.on("count", function(data) {
            var cont = 3
            $(".loop").hide();
            $(".timer").show();
            $(".inversionistas-wrap").hide();

            var video = document.getElementById('counter');
            video.pause();
            video.currentTime = 0;
            video.play();

            var loop = document.getElementById('loop');
            loop.pause();
            loop.currentTime = 0;

            setTimeout(function() {
                video.pause();
                video.currentTime = 0;
            }, 4000);



            interval = setInterval(function() {
                video.play();
                cont--
                if (cont == 0) {

                    video.setAttribute("controls", "hidden")
                    $(".timer").hide();
                    $(".loop").hide();
                    $(".inversionistas-wrap").show();
                    clearInterval(interval);

                    initInterval();

                    gsap.fromTo(".inv-i", {
                        opacity: 0,
                        x: "-1vw",
                    }, {
                        opacity: 1,
                        ease: "power3.inOut",
                        duration: 0.5,
                        stagger: 0.05,
                        x: 0,
                        onComplete: () => {
                            console.log("FINISH ANIM")
                            // initInterval();
                        }
                    });

                }
            }, 1000);
        });
        socket.on("orden", function(data) {
            size = data.length;
            console.log("ORDEN full ->", data)
            if (orden_act.length < size) {
                orden_act = data;
                $.each(data, function(key, val) {
                    var inv = getInversionista(val.id);
                    var id = "#inv-" + (key + 1)
                    var item = $(id);

                    if (inv.nombre != $(id + "-name").data("name")) {
                        console.log("ORDEN", (key + 1), inv, item, $(id + " h3"))

                        // console.log("INV NOAMVE", inv.nombre.length)
                        if (inv.nombre.length > 28) {
                            $(id + "-name").addClass("small")
                            $(id + "-name").text(inv.nombre);
                        } else {
                            var html = inv.nombre.split(" ").join("<br>")
                            console.log("INV HTML", html)
                            $(id + "-name").html(html);
                        }
                        $(id + "-name").data("name", inv.nombre);
                        $(id + "-fondo").text(inv.fondo);

                        var color = '#8ffbb4'
                        if ((key + 1) > 5) {
                            color = '#DB2E50'
                        } else {
                            var audio = new Audio('select.mp3');
                            audio.play();
                        }
                        $(id + "-name").addClass("active")
                        $(id + "-fondo").addClass("active")

                        gsap.to(id, {
                            scale: 1,
                            duration: 1.5,
                            background: color,
                            ease: "power3.inOut",
                            onComplete: () => {
                                gsap.to(id, {
                                    duration: 1.5,
                                    background: 'transparent',
                                    ease: "power3.inOut"
                                });
                            }
                        });
                        gsap.fromTo(id + " .img-i", {
                            scale: 1.2,
                            background: color,
                        }, {
                            scale: 1,
                            background: "transparent",
                            delay: 0.2,
                            duration: 0.5,
                            ease: "power3.inOut",
                            onComplete: () => {

                            }
                        });

                        gsap.to(id + "-img", {
                            duration: 0.2,
                            scale: 1,
                            opacity: 0,
                            ease: "power3.inOut",
                            onComplete: () => {
                                $(id + "-img").attr("src", "inversionistas/" + inv.foto);
                                gsap.to(id + "-img", {
                                    duration: 0.3,
                                    opacity: 1,
                                    ease: "power3.inOut"
                                });
                            }
                        });
                    }
                })
            }
            console.log("Notificar orden")
            socket.emit("notificar", true);
        });

        $(document).ready(function() {
            document.getElementById('loop').addEventListener('mousedown', e => {
                console.log("interactive")
            });
            $.getJSON("inversionistas/inversionistas.json?v=" + Math.random(), function(data) {
                inversionistas = data;
                initVotes()
            })
            var video = document.getElementById('counter');
            video.addEventListener('loadedmetadata', function() {
                console.log("loadedmetadata", video.duration)
                if (video.buffered.length === 0) return;
                const bufferedSeconds = video.buffered.end(0) - video.buffered.start(0);
                console.log(`${bufferedSeconds} Counter seconds of video are ready to play.`);
            });

            var loop = document.getElementById('loop');
            loop.addEventListener('loadedmetadata', function() {
                if (loop.buffered.length === 0) return;
                const bufferedSeconds = loop.buffered.end(0) - loop.buffered.start(0);
                console.log(`${bufferedSeconds} Loop seconds of video are ready to play.`);
            });

            var timer = document.getElementById('timer');
            timer.addEventListener('loadedmetadata', function() {
                if (timer.buffered.length === 0) return;
                const bufferedSeconds = timer.buffered.end(0) - timer.buffered.start(0);
                console.log(`${bufferedSeconds} Loop seconds of video are ready to play.`);
            });

            for (let index = 0; index < inver_size; index++) {
                images[index] = new Image();
                images[index].src = "inversionistas/nop.png";
            }

            var loop = document.getElementById('loop');
            loop.volume = 0;

            var video = document.getElementById('counter');
            video.pause();
            video.currentTime = 0;
        });

        function getInversionista(id) {
            var inv = inversionistas.filter(function(inv) {
                return inv.id == id;
            });
            return inv[0];
        }

        function initVotes() {
            console.log("INIT VOTES")

            orden_act = [];
            $("#inversionistas").html("")
            for (let index = 0; index < inver_size; index++) {
                images[index] = new Image();
                images[index].src = "inversionistas/nop.png";
                var item = $('<li class="inv" id="inv-' + (index + 1) + '"><div class="inv-i"> <h4>' + (index + 1) + '</h4><div class="img-i"><img id="inv-' + (index + 1) + '-img" class="img-fluid mb-2" src="inversionistas/nop.png"></div><h3 id="inv-' + (index + 1) + '-name">&nbsp;</h3><h5 id="inv-' + (index + 1) + '-fondo">&nbsp;</h5></div></li>');
                $("#inversionistas").append(item);
                item.delay(index * 20).fadeIn(500);
            }
        }

        function finishInterval() {
            if (interval_20)
                clearInterval(interval_20);
            time_20 = total_time;
        }

        function initInterval() {

            var video = $('#timer');

            video.on('timeupdate', function() {
                deshabil();
            });
            video.on('play', function() {
                // if (video[0].currentTime < video[0].duration) {
                console.log('🎥 on Play');
                // }
            });
            video.on('playing', function() {
                // if (video[0].currentTime < video[0].duration) {
                //     console.log('🎥 on Playing');
                // }
            });
            video.on('ended', function() {
                console.log('🎥 on ended');
                socket.emit("timeout", true);
            });
        }

        function deshabil() {
            var video = $('#timer');
            if (Math.round(video[0].currentTime) == Math.round(video[0].duration) - 13) {
                console.log("Current", Math.round(video[0].currentTime), "Duration", Math.round(video[0].duration))
                console.log('🎥  FINISH');
                socket.emit("timeout", true);
            }
        }
    </script>
</body>

</html>