var transform =
{
    x: 0,
    y: 0,
    rotate: 0,
    flip: 1,
    
    // Function for managing transforms
    update: function(selector)
    {
        $(selector).style({'transform': 'translate('+transform.x+'px, '+transform.y+'px) rotate('+transform.rotate+'deg) scaleX('+transform.flip+')'});
        $(selector).style({'-webkit-transform': 'translate('+transform.x+'px, '+transform.y+'px) rotate('+transform.rotate+'deg) scaleX('+transform.flip+')'});
    }
}

var swim =
{
    up: 30,
    down: 50,
    next: 'up',
    
    update: function()
    {
        var pos = $('#kristyfish').el[0].getBoundingClientRect();

        // If the fish is off the screen
        if(pos.left > $(window).width() || pos.left < -(pos.width))
        {
            // Special handlers to make sure the fish never swims off into infinity
            if(pos.left > $(window).width())
            {
                transform.x = $(window).width();
            }
            
            if(pos.left < -(pos.width))
            {
                transform.x = -(pos.width);
            }
            
            transform.y = Math.random() * $('body').height();
            transform.flip *= -1;

            swim.up *= -1;
            swim.down *= -1;
        }

        if(swim.next == 'up')
        {
            transform.x += swim.up;
            transform.rotate = 10;
            swim.next = 'down';
        }
        else
        {
            transform.x += swim.down;
            transform.rotate = 0;
            swim.next = 'up';
        }

        transform.update('#kristyfish');
    }
}

function resize()
{
    var height = Math.max($(window).height(), $('html').height());
    $('.fishwrap').style({'height': height + 'px'});
}

$(document).ready(function()
{
    $('.fishwrap').append("<img src='/src/img/ghostfish.png' id='kristyfish'>");

    $('#kristyfish').on('load', function()
    {
        transform.x = $(window).width();
        transform.y = Math.random() * $('body').height();
        transform.update(this);

        // Start swimming
        setTimeout(function()
        {
            $('#kristyfish').addClass('swimming');
            $('#kristyfish').on('transitionend', function()
            {
                swim.update();
            });

            swim.update();
        }, 10);
    });

    resize();
});

$(window).on('load resize', function()
{
    resize();
});
