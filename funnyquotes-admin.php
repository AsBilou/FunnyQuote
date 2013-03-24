<?php

add_action('admin_menu','funny_quotes_admin_menu');

/* Création de la fonction qui ajoutera les elements au menu*/
function funny_quotes_admin_menu(){
    add_menu_page('Funny Quotes Options','Funny Quotes','manage_options','funny_quotes','funny_quotes_options','images/comment-grey-bubble.png');
    add_submenu_page('funny_quotes','Ajouter quotes','Ajouter quotes','manage_options','add_funny_quotes','add_funny_quotes_options');
}

/* Fonction qui affiche la liste des quotes enregistré */
function funny_quotes_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    echo '<div class="wrap">';
        funny_quotes_display();
    echo '</div>';
}

/* Fonction qui affiche le formulaire pour créer un quote */
function add_funny_quotes_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    echo '<div class="wrap">';
        funny_quotes_add_display();
    echo '</div>';
}

//Fonction qui gere l'affiche de la liste des quotes
function funny_quotes_display(){

    //Ajout de la classe WP_List_Table class
    if(!class_exists('WP_List_Table')){
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    }
    //Ajout de la classe liste_quote
    require_once(ABSPATH . 'wp-content/plugins/FunnyQuotes/liste_quote.php' );

    //Prepare le tableau des éléments
    $wp_list_table = new liste_quote();
    $wp_list_table->prepare_items();

    //Affiche le tableau
    $wp_list_table->display();
}

//Fonction qui gere l'affichage de la page d'ajout de quote.
function funny_quotes_add_display(){

    global $wpdb;
    
    //Lors de la supression
    if($_GET['action']=='delete'){
        //Apres vérification javascript, on supprime l'entré dans la base de données
        $id = $_GET['quote'];
        $table_name = $wpdb->prefix . "funny_quotes";

        $query = "DELETE FROM ".$table_name." WHERE id = ".$id.";";

        $wpdb->query($query);

        ?>
            <h2>Quote supprimé avec succès</h2>
            <a class="button" href="admin.php?page=funny_quotes">Liste des quotes</a>
            <a class="button" href="admin.php?page=add_funny_quotes">Ajouter une nouvelle quote</a>
        <?php

    }elseif($_GET['action']=='edit'){//Lors de l'édition
        if($_POST){//Apres envoie du formulaire de la quote édité
        
            //On crée une requete et on l'envoi a la base de données.
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

        }else{// Affichage du formulaire d'édition avec les doonées pré-remplis.
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
    
    //Si on envoi le forumaire de création
    if($_POST && !$_GET['action']){
        
        //On crée la requete et on l'envoi a la base de données.
        $table_name = $wpdb->prefix . "funny_quotes";

        $wpdb->insert($table_name , array('author' => $_POST['author'] ,
            'quote' => $_POST['newcitation']));
        ?>
            <h2>Quote ajouté avec succès</h2>
            <a class="button" href="admin.php?page=funny_quotes">Liste des quotes</a>
            <a class="button" href="admin.php?page=add_funny_quotes">Ajouter une nouvelle quote</a>
        <?php
    }
    elseif(!$_GET['action']){//Affichage du formulaire vide pour ajouter une nouvelle quote.
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