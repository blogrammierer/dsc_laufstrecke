<?php
class Dsc_Course_CTA_Widget extends WP_Widget
{
    private $default_title =  'Jetzt mitmachen';
    private $default_desc = 'Zeige uns deine Lieblingsstrecke und gewinne!';

    public function __construct()
    {
        parent::__construct(
            'dsc_course_cta_widget',
            'Laufstrecken CTA Widget',
            array(
                'description' => 'Widget mit CTA für das Eintragen von Laufstrecken.'
            )
        );
    }

    public function widget($args, $instance)
    {
        extract($args);

        $html = "";
        $html .= '<div class="dsc-course-cta-widget">';
        $html .= '<div class="dsc-wrap-inner">';
        if(!empty($instance['desc'])) {
            $html .= '<p>'.$instance['desc'].'</p>';
        }
        if(!empty($instance['title'])) {
            $html .= '<a href="#" id="dsc-course-cta" class="dsc-btn btn-cta">'.$instance['title'].'</a>';
        }
        $html .= '</div></div>';

        // Ausgabe
        echo $before_widget;
        echo $html;
        echo $after_widget;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $title = $this->default_title;
        $desc = $this->default_desc;

        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        if(isset($instance['desc'])) {
            $count = $instance['desc'];
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'desc' ); ?>"><?php _e( 'Beschreibung:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'desc' ); ?>" name="<?php echo $this->get_field_name( 'desc' ); ?>" type="text" value="<?php echo esc_attr( $desc ); ?>">
        </p>

    <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) :  $this->default_desc;
        $instance['desc'] = ( !empty($new_instance['desc']) ) ? $new_instance['desc'] : $this->default_desc;

        return $instance;
    }
}

//add_action('widgets_init', 'dsc_register_widgets');

function dsc_register_widgets() {
    //register_widget('Dsc_Course_CTA_Widget');
}
?>