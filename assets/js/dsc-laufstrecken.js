jQuery(document).ready(function($) {
    var scope_low = 5;
    var scope_medium = 10;
    var scope_high = 20;

    /**
     * Form
     */
    var dialogForm = $("#dsc-courses-form-wrap");

    dialogForm.dialog({
        'dialogClass' : 'dsc-courses-dialog',
        'modal' : true,
        'autoOpen' : false,
        'closeOnEscape' : true,
        open: function(event, ui) {
            $("#dsc-courses-form").show();
            $("#dsc-success-msg").hide();
        },
        beforeClose: function( event, ui ) {
            $("#dsc-courses-form").show();
            $("#dsc-success-msg").hide();
        },
        'buttons' : [
            {
                'text' : 'Schließen',
                'class' : 'button-primary',
                'click' : function() {
                    $(this).dialog('close');
                }
            }
        ]
    });

    $(".dsc-open-modal, #dsc-course-cta, .dsc-btn-cta").on("click", function(e) {
        e.preventDefault();
        dialogForm.dialog("open");
    });

    // Show Endpoint on Click
    $("#dsc-open-endpoint").on("click", function() {
        $("#dsc-endpoint-wrap").slideToggle("slow");
    });

    // Add more waypoints
    $(".dsc-waypoint-wrap").each(function(i, item) {
       cloneItem = $(item).clone();
        $(cloneItem).prop("id", "dsc-waypoint-wrap-clone");
        $("#dsc-courses-form").after(cloneItem);
    });
    $("#dsc-waypoint-add").on("click", function(e) {
        e.preventDefault();
        waypointItem = $("#dsc-waypoint-wrap-clone").clone();
        $(waypointItem).prop("id", "");
        $(this).before(waypointItem);
    });

    // AJAX Calls
    var options = {
        data: {'action': 'dsc_courses_form'},
        target:        '#response-output',      // target element(s) to be updated with server response
        beforeSubmit:  formBeforeSubmit,     // pre-submit callback
        success:       formSuccess,    // post-submit callback
        url:    ajaxurl                 // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    };

    // bind form using 'ajaxForm'
    $('#dsc-courses-form').ajaxForm(options);
    function formBeforeSubmit(arr, $form, options) {
        start_lat = $("#dsc-course-start-lat").val();
        start_lon = $("#dsc-course-start-lon").val();
        blogger_name = $("#dsc-course-blogger-name").val();
        blogger_url = $("#dsc-course-blogger-url").val();
        blogger_length = $("#dsc-length").val();
        validate = true;

        if(start_lat == '') {
            $("#dsc-course-start-lat").addClass("dsc-form-error");
            validate = false;
        }
        if(start_lon == '') {
            $("#dsc-course-start-lon").addClass("dsc-form-error");
            validate = false;
        }
        if(blogger_name == '') {
            $("#dsc-course-blogger-name").addClass("dsc-form-error");
            validate = false;
        }
        if(blogger_url == '') {
            $("#dsc-course-blogger-url").addClass("dsc-form-error");
            validate = false;
        }
        if(blogger_length == '') {
            $("#dsc-length").addClass("dsc-form-error");
            validate = false;
        }


        if(!validate) {
            $('html, body').animate({
                scrollTop: $("body").offset().top
            }, 500);
            return false;
        }
    }

    function formSuccess(response) {
        $("#dsc-courses-form").hide('fast');
        $("#dsc-success-msg").show('slow');
        $('html, body').animate({
            scrollTop: $("body").offset().top
        }, 500);
        document.getElementById("dsc-courses-form").reset();
    }
    /**
     * Tooltip
     */
    $('.dsc-tooltip').tooltip();

    /**
     * Search
     */
    $("#dsc-search-input").on("keypress", function(e) {
        if (e.keyCode == 13 || e.which == 13 ) {
            doSearch(this)
        }
    });
    $('#dsc-search-input:text').focus(
        function(){
            $(this).val('');
    });
    $(".dsc-icon-lupe").on("click", function() {
        doSearch($("#dsc-search-input"));
    });

    function doSearch(obj) {
        term = $(obj).val();

        if(term != '') {
            geocoder.geocode( {'address': term}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    lat = results[0].geometry.location.lat();
                    lng = results[0].geometry.location.lng();
                    latlng = new google.maps.LatLng(lat, lng);
                    map.panTo(latlng);
                    map.setZoom(12);
                }
            });
        }
    }

    /**
     * Feed onClick
     */
    $(".dsc-item").on("click", function(e) {
        course_id = $(this).data("course_id");
        marker = allMarkerObjs[course_id][0];
        if(marker) {
            google.maps.event.trigger(marker, 'click');
            $('html, body').animate({
                scrollTop: $("#map-canvas").offset().top
            }, 500);
        }
    });

    /**
     * Google Maps API
     */
    var map;
    var directionsDisplay = [];
    var directionsService;
    var stepDisplay;
    var geocoder;
    var allMarkerObjs = {};
    var bounds = new google.maps.LatLngBounds();

    function initialize() {
        var centerLatLong = new google.maps.LatLng(50.958004, 10.268789);
        var centerZoom = 6;

        var mapOptions = {
            mapTypeControl: true,
            streetViewControl: true,
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.SMALL,
                position: google.maps.ControlPosition.RIGHT_BOTTOM
            }
        };
        map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        // DirectionService
        directionsService = new google.maps.DirectionsService();
        // Instantiate an info window to hold step text.
        stepDisplay = new google.maps.InfoWindow();
        // GeoCoder
        geocoder = new google.maps.Geocoder();

        var rendererOptions = {
            map: map
        };

        $.each(dsc_courses, function(i, item) {
            if(item.start_lat != '') {
                directionsDisplay[i] = new google.maps.DirectionsRenderer(rendererOptions);
                var start =  new google.maps.LatLng(item.start_lat, item.start_lon);
                if(item.end_lat!="" && item.end_lon != '') {
                    var end =  new google.maps.LatLng(item.end_lat, item.end_lon);
                } else {
                    var end = start;
                }

                bounds.extend(start);
                bounds.extend(end);

                // add waypoints if exists
                waypts = [];
                if(item.waypoints && item.waypoints.length > 0) {
                    waypts.push({
                        location: new google.maps.LatLng(item.waypoints[0][0],item.waypoints[0][1]),
                        stopover: false
                    });
                }

                var request = {
                    origin: start,
                    destination: end,
                    waypoints: waypts,
                    optimizeWaypoints: false,
                    travelMode: google.maps.TravelMode.WALKING
                };

                // Route the directions and pass the response to a
                // function to create markers for each step.
                directionsService.route(request, function(response, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        directionsDisplay[i].setDirections(response);
                        directionsDisplay[i].setOptions({preserveViewport: true,suppressMarkers: true});
                        showSteps(response, item);
                    }
                });
            }
        });
        if(dsc_courses.length>0) {
            //now fit the map to the newly inclusive bounds
            map.fitBounds(bounds);
        } else {
            map.setCenter(centerLatLong);
            map.setZoom(centerZoom);
        }
    }

    function showSteps(directionResult, item) {
        var greenIcon = new google.maps.MarkerImage(dsc_pluginurl+'assets/img/marker_gruen.svg',
            null, null, null, new google.maps.Size(24,36));
        var orangeIcon = new google.maps.MarkerImage(dsc_pluginurl+'assets/img/marker_orange.svg',
            null, null, null, new google.maps.Size(24,36));
        var redIcon = new google.maps.MarkerImage(dsc_pluginurl+'assets/img/marker_rot.svg',
            null, null, null, new google.maps.Size(24,36));

        var myRoute = directionResult.routes[0].legs[0];
        totalDistance = parseInt(item.length);
        /*$.each(directionResult.routes[0].legs, function(i, item) {
            totalDistance += item.distance.value;
        });*/

        markerIcon = greenIcon;
        if(totalDistance >= scope_low && totalDistance < scope_medium) {
            markerIcon = orangeIcon;
        } else if (totalDistance >= scope_medium) {
            markerIcon = redIcon;
        }
        var startMarker = new google.maps.Marker({
            position: myRoute.start_location,
            icon: markerIcon,
            map: map
        });
        attachInstructionText(startMarker, item, myRoute);

        var endMarker = new google.maps.Marker({
            position: myRoute.end_location,
            icon: markerIcon,
            map: map
        });
        attachInstructionText(endMarker, item, myRoute);

        // Append MarkerJSONObj
        allMarkerObjs[item.id] = new Array();
        allMarkerObjs[item.id][0] = startMarker;
        allMarkerObjs[item.id][1] = endMarker;
    }

    function attachInstructionText(marker, item, route) {
        google.maps.event.addListener(marker, 'click', function() {
            // Open an info window when the marker is clicked on,
            // containing the text of the step.
            stepDisplay.setContent(generateInfoboxContent(item, route));
            stepDisplay.open(map, marker);
        });
    }

    function generateInfoboxContent(item, route) {
        diffrent = false;
        if(route.start_address!=route.end_address) {
            diffrent = true;
        }
        /*totalDuration = 0;
        totalDistance = 0;
        for(var i=0; i<route.steps.length; ++i) {
            totalDistance += route.steps[i].distance.value;
            totalDuration += route.steps[i].duration.value;
        }*/

        html = '<div class="dsc-infobox">';
        html = html + '<div class="dsc-infobox-blogger clearfix">';
        if(item.blogger_img!='') {
            html = html + '<img width="64" height="64" src="' + item.blogger_img + '" alt="' + item.blogger_name + '" />';
        }
        html = html + '<div class="dsc-row">';
        html = html + '<span class="date">'+item.date+'</span>, ';
        html = html + '<a href="' + item.blogger_url + '" target="_blank">' + item.blogger_name + '</a>';
        html = html + '</div>';
        html = html + '<div class="dsc-row dsc-subline">';
        html = html + '<span class="dsc-route-distance">' + showTotalDistance(item.length) + '</span> - ';
        html = html + '<span class="dsc-route-complexity">' + showCourseComplexity(item.length) + '</span>';
        html = html + '</div>';
        html = html + '</div>';
        html = html + '<p class="clear dsc-city"><span class="dsc-label">Ort:</span>' + route.start_address.split(",")[1] + '</p>';
        html = html + '<p class="clear dsc-begin"><span class="dsc-label">Start:</span>' + route.start_address.split(",")[0];
        if(diffrent) {
            html = html + '<span class="dsc-label dsc-end">Ende:</span>' + route.end_address.split(",")[0];
        }
        html = html + '</p>';
        html = html + '<p class="clear dsc-desc"><span class="dsc-label">Besonderheiten:</span>' + item.desc + '</p>';
        html = html + '</div>';

        return html;
    }

    function showCourseComplexity(val) {
        // val == value in meters
        if(parseInt(val) <= scope_low) {
            return "einfach";
        } else if (parseInt(val) <= scope_medium) {
            return "mittel";
        }
        return "schwer";
    }

    function showTotalDistance(val) {
        dist = (Math.round((parseInt(val))*10, 1)/10)+"";
        return dist.replace(".",",") + " km";
    }

    google.maps.event.addDomListener(window, 'load', initialize);


    /**
     * Complexity Filter
     */
    $("#dsc-complexity li a").on("click", function(e) {
        $("#dsc-complexity li").removeClass("dsc-scope-active");
        $(this).parent().addClass("dsc-scope-active");
        var obj = this;
        e.preventDefault();
        $.each(directionsDisplay, function(i, item) {
           item.setMap(null);
        });
        $.each(dsc_courses, function(i, item) {
            markerArr = allMarkerObjs[item.id];
            if(markerArr) {
                resetMarker(markerArr[0]);
                resetMarker(markerArr[1]);
                if($(obj).data("scope")==0){
                    addMarker(markerArr[0]);
                    addMarker(markerArr[1]);
                    directionsDisplay[i].setMap(map);
                }else if($(obj).data("scope")==1) {
                    if(parseInt(item.length) <= scope_low) {
                        addMarker(markerArr[0]);
                        addMarker(markerArr[1]);
                        directionsDisplay[i].setMap(map);
                    }
                } else if($(obj).data("scope")==2) {
                    if(parseInt(item.length) > scope_low && parseInt(item.length) <= scope_medium) {
                        addMarker(markerArr[0]);
                        addMarker(markerArr[1]);
                        directionsDisplay[i].setMap(map);
                    }
                } else {
                    if(parseInt(item.length) > scope_medium) {
                        addMarker(markerArr[0]);
                        addMarker(markerArr[1]);
                        directionsDisplay[i].setMap(map);
                    }
                }
            }
        });
    });
    function resetMarker(marker) {
        if(marker) {
            marker.setMap(null);
        }
    }

    function addMarker(marker) {
        if(marker) {
            marker.setMap(map);
        }
    }
});
