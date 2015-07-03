<?php

add_shortcode('dsc_map_init', 'dsc_map_init');
add_shortcode('dsc_map_form', 'dsc_map_form');
add_shortcode('dsc_cta_button', 'dsc_cta_button');
add_shortcode('dsc_feed', 'dsc_feed');
add_shortcode('dsc_cta_widget', 'dsc_cta_widget');
add_shortcode('dsc_prices', 'dsc_prices');

function dsc_map_init($attr) {
    $attr = shortcode_atts( array(
        'title' => ''
    ), $attr );
    $raw_courses = get_posts(
        array(
            'post_type' => 'laufstrecke',
            'post_status' => 'publish'
        )
    );

    $url = plugin_dir_url(dirname( __FILE__));
    $courses = array();
    foreach($raw_courses as $course) {
        $img_src_url = '';
        if ( has_post_thumbnail($course->ID)) { // Check if Thumbnail exists
            $imgArr = wp_get_attachment_image_src(get_post_thumbnail_id($course->ID), 'thumbnail', false);
            $img_src_url = $imgArr[0];
        }
        $date = new DateTime($course->post_date);
        $length = get_post_meta($course->ID, 'blogger_laenge')[0];

        $tmpArr = array(
            'id' => $course->ID,
            'title' => $course->post_title,
            'desc' =>  wp_trim_words($course->post_content, 25),
            'date' => $date->format("d.m.Y"),
            'blogger_name' => get_post_meta($course->ID, 'blogger_name')[0],
            'blogger_url' =>  get_post_meta($course->ID, 'blogger_url')[0],
            'blogger_img' => $img_src_url,
            'start_lat' => get_post_meta($course->ID, 'startpunkt_-_latitude')[0],
            'start_lon' => get_post_meta($course->ID, 'startpunkt_-_longitude')[0],
            'waypoints' => json_decode(get_post_meta($course->ID, 'wegpunkte')[0], true),
            'end_lat' => get_post_meta($course->ID, 'endpunkt_-_latitude')[0],
            'end_lon' => get_post_meta($course->ID, 'endpunkt_-_longitude')[0],
            //'course_file' => get_field('streckendatei', $course->ID),
            'length' => $length
        );
        array_push($courses, $tmpArr);
    }
    $html = '<div class="pull-right">'. do_shortcode('[dsc_cta_widget]') .'</div><p class="dsc-text">Der Sommer steht vor der Tür und mit ihm sieht man immer mehr sporttreibende Menschen auf den Straßen. Egal ob morgens vor der Arbeit oder abends, Laufen ist immer und überall möglich. In den letzten Jahren ist dieser Sport fast schon zu einer Bewegung geworden. Politiker wie Joschka Fischer beschreiben sogar in einem Buch, wie das Joggen sie selber verändert hat. Firmen veranstalten regelmäßige Laufevents und Prominente geben Tipps in Ratgebern, welcher Schuh den besten Lauf garantiert.
                </p><p class="dsc-text">Auf der Karte sind die verschiedenen Laufstrecken unserer Teilnehmer verzeichnet. Auch du kannst dabei sein: Mach mit und zeige uns deine Lieblingstrecke! Als Dankeschön erhälst du ein gedrucktes Exemplar vom E-Books <a href="https://www.otto.de/shoppages/richtig-laufen-ebook">"Richtig Laufen"</a>.</p>';
    $html .= do_shortcode("[dsc_cta_button]");
    $html .= '<script>';
    $html .= 'var dsc_courses = '.json_encode($courses);
    $html .= '</script>';
    $html .= '<div id="dsc-complexity"><span>Schwierigkeitsgrad: <a class="dsc-tooltip" data-toggle="tooltpip" data-placement="top" title="Die Schwierigkeitsgrade unterteilen sich in 3 verschiedene Klassen. Die leichten Strecken haben eine Länge von 5 km, die mittleren gehen bis zu 10 km und die schweren haben eine Länge von mehr als 10 km."></a></span>';
    $html .= '<ul>';
    $html .= '<li id="dsc-easy"><a href="#" id="dsc-easy-link" data-scope="1">Leicht</a></li>';
    $html .= '<li id="dsc-medium"><a href="#" id="dsc-medium-link" data-scope="2">Mittel</a></li>';
    $html .= '<li id="dsc-hard"><a href="#" id="dsc-hard-link" data-scope="3">Schwer</a></li>';
    $html .= '<li id="dsc-remove"><a href="#" id="dsc-remove-link" data-scope="0"><span class="dsc-remove"></span></a></li>';
    $html .= '</ul>';
    $html .= '</div>';
    $html .= '<div id="dsc-search">';
    $html .= '<svg class="icon-lupe dsc-icon-lupe"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'.$url.'assets/img/icons.svg#icon-lupe"></use></svg><input type="text" name="dsc-search-input" value="" id="dsc-search-input" placeholder="Suche nach Orten" />';
    $html .= '</div>';
    $html .= '<div id="map-canvas"></div>';
    $html .= '<div class="dsc-join">';
    $html .= '<h3>So bist du dabei:</h3>';
    $html .= '<ol>
        <li>Trage deine Laufstrecke über GPS Koordinaten ein.</li>
        <li>Lade ein persönliches Bild und zusätzlich Infos (z.B. deinen Blog) hoch.</li>
        <li>Poste die Laufstrecke auf deinem Blog und erzähle, was besonders an dieser Strecke ist.</li>
        <li>Schick uns eine E-Mail an <a href="mailto:kooperation@otto.de">kooperation@otto.de</a> um das gedruckte E-Book <a href="https://www.otto.de/shoppages/richtig-laufen-ebook">"Richtig Laufen"</a> zugeschickt zu bekommen.</li>
        <li>Fertig! Mit der Teilnahme unserer Aktion habt ihr die Chance attraktive Preise zu gewinnen.</li>
</ol>';
    $html .= '</div>';

    return $html;
}


function dsc_map_form($attr) {
    $attr = shortcode_atts( array(
        'title' => ''
    ), $attr );

    $html = '<div id="dsc-courses-form-wrap">';
    $html .= '<form  id="dsc-courses-form" name="dsc-courses-form" method="post" enctype="multipart/form-data">';
    $html .= '<input type="hidden" value="'. wp_create_nonce( 'dsc-courses-form' ) .'" name="dsc_nonce" />';
    $html .= '<h1>Zeige uns deine Lieblingsstrecke und gewinne!</h1>';
    $html .= '<h2 class="clearfix"><span>1. Trage einfach die GPS-Koordinaten* deiner Strecke ein:</span></h2>';
    $html .= '<div class="col-wrap clearfix">';
    $html .= '<div class="col-one">';
    $html .= '<h3>Startpunkt*:</h3>';
    $html .= '<input name="dsc-course-start-lat" id="dsc-course-start-lat" type="text" placeholder="Latitude (z.B. 52.505213)" /><input name="dsc-course-start-lon" id="dsc-course-start-lon" type="text" placeholder="Longitude (z.B. 9.988085)" />';
    $html .= '<div><input type="checkbox" name="dsc-open-endpoint" id="dsc-open-endpoint" /><span class="dsc-label">Endpunkt ist nicht Startpunkt</span></div>';
    $html .= '</div>';
    $html .= '<div class="col-two dsc-hidden" id="dsc-endpoint-wrap"><h3>Endpunkt:</h3><input name="dsc-course-end-lat" id="dsc-course-end-lat" type="text" placeholder="Endpunkt - Latitude" /><input name="dsc-course-end-lon" id="dsc-course-end-lon" type="text" placeholder="Endpunkt - Longitude" /></div>';
    $html .= '</div>';
    $html .= '<div class="col-wrap dsc-margin clearfix">';
    $html .= '<div class="col-one">';
    $html .= '<input type="text" name="dsc-length" id="dsc-length" placeholder="Streckenlänge in km" />';
    $html .= '</div>';
    $html .= '<div class="col-two">';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="col-wrap dsc-margin clearfix">';
    $html .= '<p>Deine Strecke detailierter angeben? Dann füge weitere <strong>Wegpunkte</strong> hinzu:</p>';
    $html .= '<div class="dsc-waypoint-wrap">';
    $html .= '<div class="col-one"><input type="text" name="dsc-waypoints-lat[]" placeholder="Latitude" /></div>';
    $html .= '<div class="col-two"><input type="text" name="dsc-waypoints-lon[]" placeholder="Longitude" /></div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<a href="#" id="dsc-waypoint-add">+ weiteren Wegpunkt hinzufügen</a>';
    $html .= '<div class="col-wrap dsc-margin clearfix">';
    $html .= '<div class="col-one">';
    $html .= '<h2>2. Beschreibe deine Strecke (optional):</h2>';
    $html .= '<textarea name="dsc-course-desc" id="dsc-course-desc" placeholder="Streckenbeschreibung/Besonderheiten"></textarea>';
    $html .= '</div>';
    $html .= '<div class="col-two">';
    $html .= '<h2>3. Deine Daten:</h2>';
    $html .= '<div><input name="dsc-course-blogger-name" id="dsc-course-blogger-name" type="text" placeholder="Name" /></div>';
    $html .= '<div><input name="dsc-course-blogger-url" id="dsc-course-blogger-url" type="text" placeholder="URL deines Blogs" /></div>';
    $html .= '<div><label>Profilbild:</label><input type="file" name="dsc-course-blogger-picture" id="dsc-course-blogger-picture" /></div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<p class="clear dsc-margin">Wenn du deine Strecke erfolgreich eingesendet hast, nimmst du automatisch am Gewinnspiel teil. Zusätzlich bekommst du als Dankeschön das Buch "Richtig Laufen" zugeschickt. Sende uns dafür eine Email mit deiner Adresse an <a href="mailto:kooperation@otto.de">kooperation@otto.de</a>. Viel Glück!</p>';
    $html .= '<p class="clearfix"><input type="submit" name="dsc-course-submit" class="dsc-btn" id="dsc-course-submit" /></p>';
    $html .= '<p><small>* Suche zuerst auf Google Maps den gewünschten Startpunkt. Mit Rechtsklick auf "Was ist hier?" erscheinen die Koordinaten in der Infobox links oben im Browserfenster. Die Zahl vor dem Komma ist die Latitude, die danach die Longitude. Kopiere so einfach alle wichtigen Punkte deiner Strecke und füge sie im Formular ein.</small></p>';
    $html .= '<p id="response-output"></p>';
    $html .= '</form><p id="dsc-success-msg">Vielen Dank - deine Laufstrecke wurde eingereicht!</p></div>';


    return $html;
}

function dsc_cta_button($attr) {
    $attr = shortcode_atts( array(
        'title' => 'Jetzt mitmachen!'
    ), $attr );
    $html = '<a href="#" class="dsc-btn dsc-btn-cta">';
    $html .= '<svg class="icon-pfeilchen dsc-icon-pfeilchen"><path d="M1.694,11.995C0.689,12.175,0.005,7.739,0,5.975C-0.003,4.21,0.614-0.174,1.637,0.005 C2.092,0.085,11.786,5.64,11.786,5.64s0.195,0.119,0.197,0.285c-0.001,0.168-0.191,0.278-0.191,0.278S2.16,11.911,1.694,11.995z"/></svg>';
    $html .= '<span>'.$attr['title'].'</span>';
    $html .= '</a>';

    return $html;
}

function dsc_feed($attr) {
    function getComplexityString($val) {
        if($val<=5000) {
            return "einfach";
        } else if( $val <= 10000) {
            return "mittel";
        }
        return "schwer";
    }

    $attr = shortcode_atts( array(
        'title' => 'Alle Strecken'
    ), $attr );

    // Get Courses
    $raw_courses = get_posts(
        array(
            'post_type' => 'laufstrecke',
            'post_status' => 'publish'
        )
    );

    $html = '<div class="dsc-feed">';
    $html .= '<div class="col-xs-12"><hr></div>';
    $html .= '<h2>'.$attr['title'].'</h2>';
    $html .= '<div class="dsc-row-wrap">';
    $i=0;
    foreach($raw_courses as $course) {
        if($i % 2 == 0 && $i>0) {
            $html .= '</div><div class="dsc-row-wrap">';
        }
        // Check if pic exists
        $img_src_url = '';
        if ( has_post_thumbnail($course->ID)) { // Check if Thumbnail exists
            $imgArr = wp_get_attachment_image_src(get_post_thumbnail_id($course->ID), 'small', false);
            $img_src_url = $imgArr[0];
        }
        $date = new DateTime($course->post_date);
        $start_json = json_decode(get_post_meta($course->ID, 'startpunkt_json')[0], true);
        $start_city = $start_json['results'][0]['address_components'][4]['long_name'];
        $start_zip = $start_json['results'][0]['address_components'][7]['long_name'];

        $length = get_post_meta($course->ID, 'blogger_laenge')[0];
        $length_txt = $length . " km";

        $html .= '<div class="dsc-item" data-course_id="'.$course->ID.'">';
        $html .= '<img height="48" width="48" src="'.$img_src_url.'" alt="" />';
        $html .= '<div class="dsc-feed-infos">';
        $html .= '<div class="dsc-row"><span class="dsc-date">'.$date->format("d.m.Y").'</span>, ';
        $html .= '<a href="'.get_post_meta($course->ID, 'blogger_url')[0].'" class="dsc-blogger-name" target="_blank">'.get_post_meta($course->ID, 'blogger_name')[0].'</a>';
        $html .= '</div>';
        $html .= '<div class="dsc-row dsc-subline">';
        $html .= '<span class="dsc-length">'.str_replace(".",",",$length_txt).'</span> - <span class="dsc-complexity">'.getComplexityString($length).'</span> | ';
        $html .= '<span class="dsc-city">'.$start_zip.' '.$start_city.'</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $i++;
    }
    $html .= '</div>'; // Row
    $html .= '</div>'; // Wrapper <div>
    return $html;
}

function dsc_cta_widget($attr,  $content = null)
{
    $attr = shortcode_atts(array(
        'title' => 'Jetzt mitmachen!'
    ), $attr);

    if(!$content) {
        $content = 'Zeige uns deine Lieblingsstrecke und gewinne!';
    }
    $html = '<div class="dsc-course-cta-widget">';
    $html .= '<div class="dsc-wrap-inner">';
    $html .= '<p>'.$content.'</p>';
    $html .= '<a href="#" id="dsc-course-cta" class="dsc-btn btn-cta">
                <svg class="icon-pfeilchen dsc-icon-pfeilchen"><path d="M1.694,11.995C0.689,12.175,0.005,7.739,0,5.975C-0.003,4.21,0.614-0.174,1.637,0.005 C2.092,0.085,11.786,5.64,11.786,5.64s0.195,0.119,0.197,0.285c-0.001,0.168-0.191,0.278-0.191,0.278S2.16,11.911,1.694,11.995z"></path></svg>
                <span class="">'.$attr['title'].'</span></a>';
    $html .= '</div></div>';

    return $html;
}

function dsc_prices($attr) {
    $attr = shortcode_atts(array(
        'title' => 'Jetzt mitmachen!'
    ), $attr);
    $url = plugin_dir_url(dirname( __FILE__));
    $img_path = $url . 'assets/img';
    $html = '<div id="prices" class="row">';
    $html .= '<div class="col-md-12"><h2>Wer bei unserer Aktion mitmacht, kann folgendes gewinnen</h2></div>';
    $html .= '<div class="col-md-4">';
    $html .= '<img src="'.$img_path.'/preis-1.jpg" alt="Zwei Garmin Laufuhren im Wert von jeweils 399,99 Euro">';
    $html .= '<p class=""><span class="dsc-number">1</span>Zwei Garmin Laufuhren im Wert von jeweils 399,99 Euro</p>';
    $html .= '</div>';
    $html .= '<div class="col-md-4">';
    $html .= '<img src="'.$img_path.'/preis-2.jpg" alt="Zwei Garmin Laufuhren im Wert von jeweils 399,99 Euro">';
    $html .= '<p class=""><span class="dsc-number">2</span>Fünf Striiv Activity Tracker im Wert von 79,99 Euro</p>';
    $html .= '</div>';
    $html .= '<div class="col-md-4">';
    $html .= '<p class=""><span class="dsc-number">3</span>Zwei Gutscheine im Wert von jeweils 50 Euro, einlösbar für das gesamte Otto-Sortiment</p>';
    $html .= '</div>';
    $html .= '</div>';

    return $html;

}

?>