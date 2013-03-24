<?php

//Ajout du widget a la liste des widgets
add_action( 'widgets_init', create_function( '', 'register_widget( "funny_quotes_widget" );' ) );

/**
 * Cration d'une class pour le widget.
 */
class funny_quotes_widget extends WP_Widget {

    /**
     * Constructeur du plugin
     */
    public function __construct() {
        parent::__construct(
            'funny_quotes_widget', // Base ID
            'Funny Quotes', // Name
            array( 'description' => __( 'Add funny quotes anywhere on your website', 'text_domain' ), ) // Args
        );
    }

    /*
     * Affichage du widget sur la page.
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

    /*
     * Mise a jout du titre du widget
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = strip_tags( $new_instance['title'] );

        return $instance;
    }

    /*
     * Encart administratif du widget.
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

}

