<?php


class liste_quote extends WP_List_Table {

    /*
     * Constructeur de la classe
     */
    function __construct() {
        parent::__construct( array(
            'singular'=> 'wp_list_text_link', //Singular label
            'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
            'ajax'	=> false //We won't support Ajax for this table
        ) );
    }

    /*
     * Gestion de l'entete et pied de page
     */
    function extra_tablenav( $which ) {
        if ( $which == "top" ){
            //The code that goes before the table is here
            echo "<h2>Funny Quotes<a href='admin.php?page=add_funny_quotes' class='add-new-h2'>+ Ajouter une nouvelle citation</a></h2>";
        }
    }

    /**
     * Définie les colonnes a afficher dans le tableau
     */
    function get_columns() {
        return $columns= array(
            'col_id'=>__('ID'),
            'col_author'=>__('Author'),
            'col_quote'=>__('Quote'),
            'col_action'=>__('Action'),
        );
    }

    /**
     * Défini les colonnes qui sont triable
     */
    public function get_sortable_columns() {
        return $sortable = array(
            /*'col_id'=>'id',
            'col_author'=>'author',*/
        );
    }

    /*
     * Organise les paramatres pour les afficher
     */
    function prepare_items() {
        global $wpdb, $_wp_column_headers;
        $screen = get_current_screen();

        /* -- Création de la requete -- */
        $table_name = $wpdb->prefix . "funny_quotes";
        $query="SELECT * FROM ".$table_name;

        /* -- Récuperation des colonnes a trier -- */

        $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
        $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
        if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

        /* -- Récuperation du nombre total de quote -- */
        
        $totalitems = $wpdb->query($query);

        //Nombre de quote par page
        $perpage = 10;
        //Gestion des pages
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
        
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }

        $totalpages = ceil($totalitems/$perpage);
        if(!empty($paged) && !empty($perpage)){
            $offset=($paged-1)*$perpage;
            $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
        }

        $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ) );

        /* — Gestions des colonnes — */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        /* -- Récupération des quotes -- */
        $this->items = $wpdb->get_results($query);
    }

    /*
     * Affiche les informations
     */
    function display_rows() {

        //Récuperation des quotes
        $records = $this->items;

        //Récuperation des colonnes
        list( $columns, $hidden ) = $this->get_column_info();
        if(!empty($records)){
            //pour chaque quote
            foreach($records as $rec){

                echo '<tr id="record_'.$rec->id.'">';
                foreach ( $columns as $column_name => $column_display_name ) {

                    $class = "class='$column_name column-$column_name'";
                    $style = "";
                    if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
                    $attributes = $class . $style;

                    $editlink  = '/wp-admin/link.php?action=edit&id='.(int)$rec->id;

                    switch ( $column_name ) {
                        case "col_id":	echo '<td '.$attributes.'>'.stripslashes($rec->id).'</td>';	break;
                        case "col_author": echo '<td '.$attributes.'>'.stripslashes($rec->author).'</td>'; break;
                        case "col_quote": echo '<td '.$attributes.'>'.stripslashes($rec->quote).'</td>'; break;
                        case "col_action": echo '<td '.$attributes.'><a class="button" style="margin-right:10px;" href="admin.php?page=add_funny_quotes&quote='.stripslashes($rec->id).'&action=edit">Editer</a><a class="button" onclick="return confirm(\'Etes-vous sur de vouloir supprimer la quote ?\')" href="admin.php?page=add_funny_quotes&quote='.stripslashes($rec->id).'&action=delete">Supprimer</a></td>'; break;
                    }

                }

                echo'</tr>';

                }
            }
        }

}