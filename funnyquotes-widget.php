<?php

add_action( 'widgets_init', create_function( '', 'register_widget( "funny_quotes_widget" );' ) );

/**
 * Adds Foo_Widget widget.
 */
class funny_quotes_widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'funny_quotes_widget', // Base ID
            'Funny Quotes', // Name
            array( 'description' => __( 'Add funny quotes anywhere on your website', 'text_domain' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {

        global $wpdb;

        $table_name = $wpdb->prefix . "funny_quotes";

        $query = "SELECT * FROM ".$table_name.";";

        $nbQuotes = $wpdb->query($query);
        $quotes = $wpdb->get_results($query);

        $random = rand(0,$nbQuotes-1);
        $quote = $quotes[$random];

        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );

        $display = '<p>"'.$quote->quote.'"</p><cite> - '.$quote->author.'</cite>';

        echo $before_widget;
        if ( ! empty( $title ) )
            echo $before_title . $title . $after_title;
        echo __( $display, 'text_domain' );
        echo $after_widget;
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
        $instance['title'] = strip_tags( $new_instance['title'] );

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'Votre titre', 'text_domain' );
        }
        ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <?php
    }

} // class Foo_Widget

