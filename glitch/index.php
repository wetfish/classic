<html>
    <head>
        <title>Welcome to HTML</title>
        
        <script src="node_modules/wetfish-basic/dist/basic.js"></script>
        
        <style>
            body {
                background-color:#000;
                color:#eee;
                font-family:tahoma, sans-serif;
            }
            
            .url {
                width: 600px;
            }

            .images {
                position: relative;
            }

            .front, .back {
                position: absolute;
                top:0;
                left:0;
            }

            .front {
                z-index: 1;
            }

            .front img, .back img {
                transition: 0.5s all;
                opacity: 0;
            }

            .fadein {
                opacity: 1 !important;
            }

            .fadeout {
                opacity: 0 !important;
            }

            .stop-auto {
                display: none;
            }
        </style>
        
        <script>
            var interval = <?php if($_GET['interval']) { echo $_GET['interval']; } else { echo '3000'; } ?>;
            var auto;

            $(document).ready(function()
            {
                $('form').on('submit glitch', function(event)
                {
                    event.preventDefault();

                    var url = $('.url').value();

                    var img = document.createElement('img');
                    $(img).attr('src', "image.php?url="+url+"&rand="+Math.random());

                    if($('.front img').el.length)
                    {
                        // Move the current image to the back
                        $('.back').html('');
                        $('.back').el[0].appendChild($('.front img').el[0]);
                    }

                    $('.front').html('');
                    $('.front').el[0].appendChild(img);

                    $(img).on('load', function(event)
                    {
                        $(this).addClass('fadein');
                    });
                });

                $('.start-auto').on('click', function(event)
                {
                    // Don't start another interval if we're already auto-glitching
                    if(auto) return;
                    
                    $('.stop-auto').style({display: 'inline'});

                    auto = setInterval(function()
                    {
                        $('form').trigger('glitch');
                    }, interval);
                });

                $('.set-interval').on('click', function(event)
                {
                    interval = prompt("How long would you like to wait between glitches? (in miliseconds)");

                    // Reset autoglitch
                    $('.stop-auto').trigger('click');
                    $('.start-auto').trigger('click');
                });

                $('.stop-auto').on('click', function(event)
                {
                    $(this).style({display: 'none'});
                    clearInterval(auto);
                    auto = false;
                });

                <?php
                
                if($_GET['url'])
                    echo "$('form').trigger('glitch');";

                if($_GET['auto'])
                    echo "$('.start-auto').trigger('click');";

                ?>
            });
        </script>
    </head>
    
    <body>
        <h1>What's the URL?</h1>

        <form action="image.php">
            <input type="text" name="url" class="url" value="<?php echo $_GET['url']; ?>" />
            <input type="submit" value="Glitch" />
            <input type="button" value="Auto Glitch" class="start-auto" />
            <input type="button" value="Set Interval" class="set-interval" />
            <input type="button" value="Stop" class="stop-auto" />
        </form>

        <div class="images">
            <div class="front"></div>
            <div class="back"></div>
        </div>
    </body>
</html>
