<?php

add_action('admin_menu','funny_quotes_admin_menu');

function funny_quotes_admin_menu(){
    add_menu_page('Funny Quotes Options','Funny Quotes','manage_options','funny_quotes','funny_quotes_options','images/comment-grey-bubble.png');
    add_submenu_page('funny_quotes','Ajouter quotes','Ajouter quotes','manage_options','add_funny_quotes','add_funny_quotes_options');
}

function funny_quotes_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    echo '<div class="wrap">';
        funny_quotes_display();
    echo '</div>';
}


function add_funny_quotes_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    echo '<div class="wrap">';
        funny_quotes_add_display();
    echo '</div>';
}

function funny_quotes_display(){

    //Our class extends the WP_List_Table class, so we need to make sure that it's there
    if(!class_exists('WP_List_Table')){
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    }

    require_once(ABSPATH . 'wp-content/plugins/FunnyQuotes/liste_quote.php' );

    //Prepare Table of elements
    $wp_list_table = new liste_quote();
    $wp_list_table->prepare_items();

    //Table of elements
    $wp_list_table->display();
}

function funny_quotes_add_display(){

    global $wpdb;

    if($_GET['action']=='delete'){
        $id = $_GET['quote'];
        $table_name = $wpdb->prefix . "funny_quotes";

        $query = "DELETE FROM ".$table_name." WHERE id = ".$id.";";

        $wpdb->query($query);

        ?>
            <h2>Quote supprimé avec succès</h2>
            <a class="button" href="admin.php?page=funny_quotes">Liste des quotes</a>
            <a class="button" href="admin.php?page=add_funny_quotes">Ajouter une nouvelle quote</a>
        <?php

    }elseif($_GET['action']=='edit'){
        if($_POST){
            $id = $_GET['quote'];
            $quote = $_POST['newcitation'];
            $author = $_POST['author'];
            $table_name = $wpdb->prefix . "funny_quotes";

            $query = "UPDATE ".$table_name." SET author='".$author."',quote='".$quote."' WHERE id=".$id.";";

            $wpdb->query($query);

            ?>
                <h2>Quote édité avec succès</h2>
                <a class="button" href="admin.php?page=funny_quotes">Liste des quotes</a>
                <a class="button" href="admin.php?page=add_funny_quotes">Ajouter une nouvelle quote</a>
            <?php

        }else{
            $id = $_GET['quote'];

            $query = "SELECT * FROM ".$wpdb->prefix . "funny_quotes WHERE id = ".$id.";";
            $quote = $wpdb->get_results($query);

            ?>
                <h2>Éditer la citation !</h2>

                <form action="admin.php?page=add_funny_quotes&quote=<?php echo $id;?>&action=edit" method="post" name="addquote" id="post">
                    <div id="titlediv">
                        <div id="titlewrap">
                            <input id="title" type="text" autocomplete="off" value="<?php echo $quote[0]->author;?>" size="30" name="author" placeholder="Auteur de la citation">
                            <textarea class="newcitation" name="newcitation" placeholder="Votre citation" rows="5" maxlength="255"><?php echo $quote[0]->quote;?></textarea>
                            <input class="button button-primary button-large" type="submit" value="Editer" id="publish">
                        </div>
                    </div>
                </form>
            <?php
        }
    }

    if($_POST && !$_GET['action']){

        $table_name = $wpdb->prefix . "funny_quotes";

        $wpdb->insert($table_name , array('author' => $_POST['author'] ,
            'quote' => $_POST['newcitation']));
        ?>
            <h2>Quote ajouté avec succès</h2>
            <a class="button" href="admin.php?page=funny_quotes">Liste des quotes</a>
            <a class="button" href="admin.php?page=add_funny_quotes">Ajouter une nouvelle quote</a>
        <?php
    }
    elseif(!$_GET['action']){
        ?>
        <h2>Ajouter une nouvelle citation drôle !</h2>

        <form action="admin.php?page=add_funny_quotes" method="post" name="addquote" id="post">
            <div id="titlediv">
                <div id="titlewrap">
                    <input id="title" type="text" autocomplete="off" value="" size="30" name="author" placeholder="Auteur de la citation">
                    <textarea class="newcitation" name="newcitation" placeholder="Votre citation" rows="5" maxlength="255"></textarea>
                    <input class="button button-primary button-large" type="submit" value="Envoyer" id="publish">
                </div>
            </div>
        </form>
        <?php
    }
}