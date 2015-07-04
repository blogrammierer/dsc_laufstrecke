<?php

add_action( 'wp_ajax_dsc_courses_form', 'dsc_courses_form_callback' );
add_action( 'wp_ajax_nopriv_dsc_courses_form', 'dsc_courses_form_callback' );

function dsc_courses_form_callback() {
    //global $wpdb; // this is how you get access to the database

    check_ajax_referer('dsc-courses-form', 'dsc_nonce');

    // Create Laufstrecken post entry
    $post_data = array(
        'post_title' => $_REQUEST['dsc-course-blogger-name'],
        'post_content' => $_REQUEST['dsc-course-desc'],
        'post_status' => 'draft',
        'post_author' => 1,
        'post_type' => 'laufstrecke'
    );
    $post_id = wp_insert_post($post_data);

    //require the needed files
    if($post_id) {
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');

        //then loop over the files that were sent and store them using  media_handle_upload();
        $course_file_attach_id = 0;
        $blogger_pic_attach_id = 0;
        if($_FILES) {
            foreach ($_FILES as $file => $array) {
                if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
                    echo "upload error : " . $_FILES[$file]['error'];
                    die();
                }
                $attach_id = media_handle_upload( $file, $post_id );
                if($file == 'dsc-course-file') {
                    $course_file_attach_id = $attach_id;
                    if(substr($array['name'], -3) == 'kmz') {
                        // Parse KMZ file -> extract ZIP, parse and map onto $_REQUEST
                        $path = extractKmz($attach_id);
                        if($path) {
                            $data = parseKml($path);
                            if(is_array($data)) {
                                $_REQUEST['dsc-course-start-lat'] = $data['start'][0];
                                $_REQUEST['dsc-course-start-lon'] = $data['start'][1];
                                $_REQUEST['dsc-course-end-lat'] = $data['end'][0];
                                $_REQUEST['dsc-course-end-lon'] = $data['end'][1];
                                $_REQUEST['dsc-waypoints-lat'] = $data['waypoints-lat'];
                                $_REQUEST['dsc-waypoints-lon'] = $data['waypoints-lon'];
                            }
                        }
                    }
                } else if ($file == 'dsc-course-blogger-picture') {
                    $blogger_pic_attach_id = $attach_id;
                }

            }
        }

        // Connect upload file with post
        if($blogger_pic_attach_id > 0) {
            update_post_meta($post_id,'_thumbnail_id', $blogger_pic_attach_id);
        }

        // Google Geocode API to get Infos of startpoint
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=". $_REQUEST['dsc-course-start-lat'] .','. $_REQUEST['dsc-course-start-lon'];
        $googleApiStartReturn = doCurlRequest($url);

        // Google Geocode API to get infos of endpoint
        if(isset($_REQUEST['dsc-course-end-lat']) && $_REQUEST['dsc-course-end-lat'] != '') {
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address=". $_REQUEST['dsc-course-end-lat'] .','. $_REQUEST['dsc-course-end-lon'];
            $googleApiEndReturn = doCurlRequest($url);
            update_field('endpunkt_json', $googleApiEndReturn, $post_id);
        }

        // Waypoints of route
        $waypointsArr = array();
        if(isset($_REQUEST['dsc-waypoints-lat']) && is_array($_REQUEST['dsc-waypoints-lat'])
            && isset($_REQUEST['dsc-waypoints-lon']) && is_array($_REQUEST['dsc-waypoints-lon'])
        ) {
            for($i=0;$i<count($_REQUEST['dsc-waypoints-lat']);$i++) {
                if(trim($_REQUEST['dsc-waypoints-lat'])!='' && trim($_REQUEST['dsc-waypoints-lon'])!='') {
                    array_push($waypointsArr, array($_REQUEST['dsc-waypoints-lat'][$i], $_REQUEST['dsc-waypoints-lon'][$i]));
                }
            }
            update_field('wegpunkte', json_encode($waypointsArr), $post_id);
        }

        // Google Direction API
        if(isset($_REQUEST['dsc-course-end-lat']) && $_REQUEST['dsc-course-end-lat'] != '') {
            // Start -> Ziel
            $url = "http://maps.googleapis.com/maps/api/directions/json?mode=walking&origin=" . $_REQUEST['dsc-course-start-lat'] . ',' . $_REQUEST['dsc-course-start-lon'];
            $url .= "&destination=" . $_REQUEST['dsc-course-end-lat'] . ',' . $_REQUEST['dsc-course-end-lon'];
            if($waypointsArr) {
                generateWaypoints($url, $waypointsArr);
            }
            $googleApiDirectionReturn = doCurlRequest($url);
            update_field('route_json', removeFontSizeAttr($googleApiDirectionReturn), $post_id);
        } else if($waypointsArr) {
            // Start == Ziel
            $url = "http://maps.googleapis.com/maps/api/directions/json?mode=walking&origin=" . $_REQUEST['dsc-course-start-lat'] . ',' . $_REQUEST['dsc-course-start-lon'];
            $url .= "&destination=" . $_REQUEST['dsc-course-start-lat'] . ',' . $_REQUEST['dsc-course-start-lon'];

            if($waypointsArr) {
                generateWaypoints($url, $waypointsArr);
            }
            $googleApiDirectionReturn = doCurlRequest($url);
            update_field('route_json',  removeFontSizeAttr($googleApiDirectionReturn), $post_id);
        }

        // ACF Fields
        update_field('startpunkt_-_latitude', trim($_REQUEST['dsc-course-start-lat']), $post_id);
        update_field('startpunkt_-_longitude', trim($_REQUEST['dsc-course-start-lon']), $post_id);
        update_field('startpunkt_json', $googleApiStartReturn, $post_id);
        update_field('endpunkt_-_latitude', trim($_REQUEST['dsc-course-end-lat']), $post_id);
        update_field('endpunkt_-_longitude', trim($_REQUEST['dsc-course-end-lon']), $post_id);
        update_field('blogger_name', trim($_REQUEST['dsc-course-blogger-name']), $post_id);
        update_field('blogger_url', trim($_REQUEST['dsc-course-blogger-url']), $post_id);
        update_field('blogger_laenge', trim($_REQUEST['dsc-length']), $post_id);

        if($course_file_attach_id > 0) {
            update_field('streckendatei', $course_file_attach_id, $post_id);
        }

    }
    wp_die(); // this is required to terminate immediately and return a proper response
}

function doCurlRequest($url) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, trim($url));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $json = curl_exec($ch);
    curl_close($ch);

    return $json;
}

function removeFontSizeAttr($string) {
    return str_replace(' style=\"font-size:0.9em\"', '', $string);
}

function generateWaypoints(&$url, &$waypointsArr) {
    $waypointsParam = "";
    foreach($waypointsArr as $waypoint) {
        $waypointsParam .= $waypoint[0].",".$waypoint[1]."|";
    }
    $waypointsParam = substr($waypointsParam, 0, -1);
    $url .= "&waypoints=".$waypointsParam;
}


function extractKmz($attach_id) {
    $file_data = explode("/", get_attached_file($attach_id));
    if(is_array($file_data)) {
        $zip = new ZipArchive();
        $upload_dir = wp_upload_dir();
        $filename = $file_data[count($file_data)-1];
        $path = $upload_dir['path'] .DIRECTORY_SEPARATOR. $filename;
        $res = $zip->open($path);
        if ($res === TRUE) {
            $zip->extractTo($upload_dir['path'].DIRECTORY_SEPARATOR .'zip_'.$attach_id);
            $zip->close();
            $path = $upload_dir['path'].DIRECTORY_SEPARATOR .'zip_'.$attach_id . DIRECTORY_SEPARATOR. 'diary.kml';
            return $path ;
        }
    }
    return false;
}

function parseKml($path) {
    $returnDataArr = array();
    $xml = simplexml_load_file($path);
    if($xml) {
        foreach($xml->Document->Placemark as $placemark) {
            if(property_exists($placemark, "styleUrl") && property_exists($placemark, "LineString")) {
                $coords = explode(" ", $placemark->LineString->coordinates);
                $returnDataArr['waypoints-lat'] = array();
                $returnDataArr['waypoints-lon'] = array();
                for($i=0; $i<count($coords); $i++) {
                    if($i==0 || $i==(count($coords)-1) || ($i%10)==0){
                        $lngLat = explode(",", $coords[$i]);
                        $latLng = array_reverse($lngLat);
                        if($i==0) {
                            $returnDataArr['start'] = $latLng;
                        } else if($i == count($coords)-1) {
                            $returnDataArr['end'] = $latLng;
                        } else {
                            array_push($returnDataArr['waypoints-lat'], $latLng[0]);
                            array_push($returnDataArr['waypoints-lon'], $latLng[1]);
                        }
                    }
                }
                break;
            }
        }
    }
    return $returnDataArr;
}
?>