$(function() {

    var banks = [];
    var locs;
    var mapId = document.getElementById("ftl");
    var infoId = document.getElementById("ftw");

    $(mapId)
            .height($(window).height())
            .width($(window).width());

    var map = new google.maps.Map2(mapId);
    var geocoder = new GClientGeocoder();

    // http://www.iconarchive.com/icons/zeusbox/halloween/32/RIP-stone-icon.png

    var myIcon = new GIcon(G_DEFAULT_ICON);
    myIcon.image = "/img/RIP-stone-icon-32x32.png";
    myIcon.iconSize = new GSize(32, 32);
    myIcon.shadowSize = new GSize(42, 31);
    myIcon.iconAnchor = new GPoint(16, 16);
    myIcon.infoWindowAnchor = new GPoint(16, 16);

    var markerOptions = { icon:myIcon };

    var MAX = 0;

    var HTML = {
        bookA : '<p><a href="http://www.amazon.com/gp/product/0470520388?ie=UTF8&tag=subpriintera-20&linkCode=as2&camp=1789&creative=390957&creativeASIN=0470520388"><img border="0" src="/img/books/bailout-nation.jpg"></a><img src="http://www.assoc-amazon.com/e/ir?t=subpriintera-20&l=as2&o=1&a=0470520388" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;"> </img></p>',
        bookB: '<p><a href="http://www.amazon.com/gp/product/0670021253?ie=UTF8&tag=subpriintera-20&linkCode=as2&camp=1789&creative=390957&creativeASIN=0670021253"><img border="0" src="/img/books/too-big-to-fail.jpg"></a><img src="http://www.assoc-amazon.com/e/ir?t=subpriintera-20&l=as2&o=1&a=0670021253" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;"> </img></p>'
    };


    $.extend({
        init: function(callback) {

            var outerCallback = callback || false;

            $(document.body).prepend("<div id='loading'><h1>Tallying the Deceased...</h1></div>");

            $(infoId)
                    .prepend(HTML.bookA)
                    .prepend(HTML.bookB)

            $.ajax({
                type: 'GET',
                url: 'latest-GMaps-cache.json',
                dataType: 'json',
                success: function(data) {
                    locs = data;
                    $.ajax({
                        type: 'GET',
                        url: 'latest-YQL-cache.json',
                        dataType: 'json',
                        success: function(data) {
                            MAX = data.query.results.table.tbody.tr.length;
                            for (var i = 0; i < MAX; i++)
                            {
                                var tr = data.query.results.table.tbody.tr[i];
                                var loc = {
                                    name: tr.td[0].a.content,
                                    city: tr.td[1].p,
                                    state: tr.td[2].p,
                                    certNumber: tr.td[3].p,
                                    closingDate: tr.td[4].p,
                                    dateUpdate: tr.td[5].p
                                };
                                banks.push(loc);
                            }

                            $('#go').click(function() {
                                for (var i = 0; i < MAX; i++)
                                {
                                    (function(i) {
                                        var currentLocation = locs[i];
                                        var currentBank = banks[i];
                                        if (currentLocation.response !== "OK")
                                        {
                                            throw new Error("Bank failure was also a request failure.");
                                        }
                                        else {
                                            var point = new GLatLng(currentLocation.lat, currentLocation.lng);
                                            var marker = new GMarker(point, markerOptions);
                                            map.addOverlay(marker);
                                            GEvent.addListener(marker, "click", function() {
                                                var myHtml = "<p><strong>" +
                                                             currentBank.name +
                                                             "</strong></p><p>" +
                                                             currentLocation.address +
                                                             "</p><p>Closing Date: " +
                                                             currentBank.closingDate +
                                                             "</p>";
                                                map.openInfoWindowHtml(point, myHtml);
                                            });
                                        }
                                    }(i));
                                }
                                $(this).fadeOut('slow', function() {
                                    $(infoId)
                                            .animate(
                                    {
                                        top: '0',
                                        left: '0',
                                        width: '108px'
                                    }, 500)
                                            .find('p')
                                            .fadeIn('slow');
                                });
                                return false;
                            });
                        }
                    });
                }
            });

            setMapCenter("United States of America", function() {
                $(window).trigger("load");
            });

            function setMapCenter(address, cb) {
                geocoder.getLatLng(
                        address,
                        function(point) {
                            if (!point) {
                                throw new Exception(address + " not found");
                            } else {
                                map.setCenter(point, 4);
                            }
                            if (cb) cb();
                        }
                        );
            }

        },
        navigation: function() {

            var animateOptions = {
                left:'20'            };

            $(infoId).animate(animateOptions, 2000);
        }
    });


    $.init();

});


$(window).load(function() {
    $('#loading').fadeOut('slow', function() {
        $.navigation();
    });
    var pageTracker = _gat._getTracker("UA-3312370-7");
    pageTracker._trackPageview();
});