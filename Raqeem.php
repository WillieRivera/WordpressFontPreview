<?php
/**
 * @package Raqeem
 * @version 1.0.0
 */
/*
Plugin Name: Raqeem
Plugin URI: http://mesbec.com/
Description: Preview fonts based on font-weight,font-size,custom font-family
Copyright:Picture Technology
*/

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

add_shortcode('font_preview_content', array( 'Raqeem', 'font_preview_content' ));

class Raqeem {
    // Our code will go here

    public function __construct() {
        // Hook into the admin menu
        add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );
        $table_name="wp_font_preview";
        $sql = 'CREATE TABLE '.$table_name.'(
                id INTEGER NOT NULL AUTO_INCREMENT,
                font_name VARCHAR(30),
                font_family VARCHAR(30),
                designer VARCHAR(60),
                font_file VARCHAR(60),
                font_image VARCHAR(60),
                PRIMARY KEY (id))';
        maybe_create_table('wp_font_preview',$sql);


    }

    public function create_plugin_settings_page() {

        // Add the menu item and page
        $page_title = 'Raqeem';
        $menu_title = 'Raqeem';
        $capability = 'manage_options';
        $slug = 'font-preview-options';
        $callback = array( $this, 'plugin_settings_page_content' );
        $icon = 'dashicons-visibility';
        $position = 100;
        add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );

        $parent_slug = 'font-preview-options';
        $page_title = 'Add Font';
        $menu_title = 'Add Font';
        $capability = 'manage_options';
        $slug = 'Font Preview Plugin';
        $callback = array( $this, 'plugin_settings_add_font' );
        $icon = 'dashicons-visibility';
        $position = 100;
        add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
        
    }

    public function handle_add_font(){

        global $wpdb;


        //File(ttf,otf) Upload
        $target_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'ttf'.DIRECTORY_SEPARATOR;
        $target_file = $target_dir . basename($_FILES["font_file"]["name"]);
        move_uploaded_file($_FILES["font_file"]["tmp_name"], $target_file);



        //Image(png,svg) Upload
        $target_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR;
        $target_file = $target_dir . basename($_FILES["font_image"]["name"]);
        move_uploaded_file($_FILES["font_image"]["tmp_name"], $target_file);

        
        //echo($_POST['designer']);
        
        $wpdb->insert('wp_font_preview',array(
            'font_name'=>$_POST['font_name'],
            'font_family'=>$_POST['font_family'],
            'designer'=>$_POST['designer'],
            'font_file'=>$_FILES["font_file"]["name"],
            'font_image'=>$_FILES["font_image"]["name"],
        ));

        echo "<script>alert('Font Added Successfully!')</script>";
        //echo '123';
    }

    public function handle_edit_font(){

        global $wpdb;
        $font_id=$_POST['font_id'];

        $font=$wpdb->get_row("SELECT * FROM wp_font_preview WHERE id = ".$font_id);

        //echo($font_id);
        //File(ttf,otf) Upload
        $new_font_file=$font->font_file;
        if(isset($_FILES["font_file"]["name"])&&$_FILES["font_file"]["name"]!="")
        {
            
            $target_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'ttf'.DIRECTORY_SEPARATOR;
            //echo($target_dir.$font->font_file);
            unlink($target_dir.$font->font_file);
            $target_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'ttf'.DIRECTORY_SEPARATOR;
            $target_file = $target_dir . basename($_FILES["font_file"]["name"]);
            move_uploaded_file($_FILES["font_file"]["tmp_name"], $target_file);
            $new_font_file=$_FILES["font_file"]["name"];
        }

        
        //Image(png,svg) Upload
        $new_font_image=$font->font_image;
        if(isset($_FILES["font_image"]["name"])&&$_FILES["font_image"]["name"]!="")
        {
            $target_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR;
            //echo($target_dir.$font->font_image);
            unlink($target_dir.$font->font_image);
            $target_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR;
            $target_file = $target_dir . basename($_FILES["font_image"]["name"]);
            move_uploaded_file($_FILES["font_image"]["tmp_name"], $target_file);
            $new_font_image=$_FILES["font_image"]["name"];
        }

        

        $where = [ 'id' => $font_id ]; 

        $wpdb->update('wp_font_preview',array(
            'font_name'=>$_POST['font_name'],
            'font_family'=>$_POST['font_family'],
            'designer'=>$_POST['designer'],
            'font_file'=> $new_font_file,
            'font_image'=> $new_font_image,
            ),$where
        );
        echo "<script>alert('Font Updated Successfully!')</script>";
    }

    public function plugin_settings_add_font() {

        if ( isset( $_POST['updated'] ) && 'true' === $_POST['updated'] ) {
			$this->handle_add_font();
        }
        
        ?>
		<div class="wrap">
		<h1>Add New Font</h1>

		<form method="POST" enctype="multipart/form-data">
			<input type="hidden" name="updated" value="true" />
			<table class="form-table">

				<tr valign="top">
					<th scope="row">Font Name</th>
					<td>
                        <input name="font_name" type="text" value="<?php echo get_option( 'menu_image_size_1', '' ) ; ?>" />
                    </td>
                </tr>
                <tr valign="top">
					<th scope="row">Font Family</th>
					<td>
                        <input name="font_family" type="text" value="<?php echo get_option( 'menu_image_size_1', '' ) ; ?>" />
                        <span class="helper"><?php _e( 'This is used in CSS', 'menu-image' ); ?></span>
                    </td>
                </tr>
                <tr valign="top">
					<th scope="row">Designer</th>
					<td>
                        <input name="designer" type="text" value="<?php echo get_option( 'menu_image_size_1', '' ) ; ?>" />
                    </td>
                </tr>
                <tr valign="top">
					<th scope="row">Font File</th>
					<td>
                        <input type="file" name="font_file" type="text" value="<?php echo get_option( 'menu_image_size_1', '' ) ; ?>" />
                        <span class="helper"><?php _e( '(Choose .ttf or .otf file)', 'menu-image' ); ?></span>
                    </td>
                </tr>
                <tr valign="top">
					<th scope="row">Font Image</th>
					<td>
                        <input type="file" name="font_image" type="text" value="<?php echo get_option( 'menu_image_size_1', '' ) ; ?>" />
                        <span class="helper"><?php _e( '(Choose .png or .svg file)', 'menu-image' ); ?></span>
                    </td>
                </tr>
				
                
			</table>

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
            </p>
            
		</form>
		</div>
        <?php
        
    }

    public function handle_delete_font(){

        global $wpdb;
        $font_id=$_POST['font_id'];

        $font=$wpdb->get_row("SELECT * FROM wp_font_preview WHERE id = ".$font_id."");

        //echo $font->font_name;

        $target_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR;
        unlink($target_dir.$font->font_image);

        $target_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'ttf'.DIRECTORY_SEPARATOR;
        unlink($target_dir.$font->font_file);


        $wpdb->delete( 'wp_font_preview', array( 'id' => $font_id ) );


        echo "<script>alert('Font Deleted Successfully!')</script>";
    }

    public function plugin_settings_page_content() {

        if ( isset( $_POST['edited'] ) && 'true' === $_POST['edited'] ) {
            $this->handle_edit_font();
        }

        if ( isset( $_POST['edit'] ) && 'true' === $_POST['edit'] ) {
                global $wpdb;
                $font_id=$_POST['font_id'];
        
                $font=$wpdb->get_row("SELECT * FROM wp_font_preview WHERE id = ".$font_id."");
            ?>
            
            <div class="wrap">
                <h1>Edit Font</h1>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="edited" id="edited" value="true" />
                    <input type="hidden" name="font_id" id="font_id" value="<?php echo $font->id; ?>" />
                    <table class="form-table">

                        <tr valign="top">
                            <th scope="row">Font Name</th>
                            <td>
                                <input name="font_name" type="text" value="<?php echo $font->font_name ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Font Family</th>
                            <td>
                                <input name="font_family" type="text" value="<?php echo $font->font_family  ?>" />
                                <span class="helper"><?php _e( 'This is used in CSS', 'menu-image' ); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Designer</th>
                            <td>
                                <input name="designer" type="text" value="<?php  echo $font->designer  ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Font File</th>
                            <td>
                                <input type="file" name="font_file" type="text" value="<?php  ?>" />
                                <span class="helper"><?php _e( '(Choose .ttf or .otf file)', 'menu-image' ); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Font Image</th>
                            <td>
                                <input type="file" name="font_image" type="text" value="<?php echo get_option( 'menu_image_size_1', '' ) ; ?>" />
                                <span class="helper"><?php _e( '(Choose .png or .svg file)', 'menu-image' ); ?></span>
                            </td>
                        </tr>
                        
                        
                    </table>

                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
                        <input type="submit" name="submit" id="submit" class="button button-secondary btnCancel" value="Cancel">
                    </p>
                    <script type="text/javascript">
                    
                        (function ($) {
                            $(".btnCancel").click(function(e){
                                $("#edited").val('false');
                            });
                        })(jQuery);

                    </script>
                </form>
            </div>
            <?php
            return;
        }

        if ( isset( $_POST['updated'] ) && 'true' === $_POST['updated'] ) {
			$this->handle_delete_font();
        }

        ?>

		<div class="wrap">
		<h1>Installed Fonts</h1>

		<form method="POST" class="font_form">
            <input type="hidden" name="updated" value="true" />
            <input type="hidden" name="font_id" id="font_id" value="" />
            <input type="hidden" name="edit" id="edit_clicked" value="false" />
			<table class="wp-list-table widefat fixed striped toplevel_page_gf_edit_forms">
                <thead>
                    <tr>
                        <th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
                            <a href="javascript:none;">
                                <span>Font Name</span>
                            </a>
                        </th>
                        <th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
                            <a href="javascript:none;">
                                <span>Font Family</span>
                            </a>
                        </th>
                        <th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
                            <a href="javascript:none;">
                                <span>Designer</span>
                            </a>
                        </th>
                        <th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
                            <a href="javascript:none;">
                                <span>Font Image</span>
                            </a>
                        </th>
                        <th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
                            <a href="javascript:none;">
                                <span>Action</span>
                            </a>
                        </th>
                    </tr>
                    
                </thead>
                <tbody>
                    <?php
                        global $wpdb;
                        $results = $wpdb->get_results( "SELECT * FROM wp_font_preview");

                        if(!empty($results))
                        {
                            
                            foreach($results as $row){

                                
                                echo "<tr>";
                                echo "<td class='id'>".$row->id."</td>";
                                echo "<td>".$row->font_name."</td>";
                                echo "<td>".$row->font_family."</td>";
                                echo "<td>".$row->designer."</td>";
                                
                                $target_dir = plugin_dir_url( __FILE__ ).'/image/';
                                $target_file = $target_dir.$row->font_image;

                                echo "<td><img src='".$target_file."'/></td>";
                                
                                echo "<td><input type='submit' class='button button-secondary btnDelete' style='margin-right:15px;' value='Delete'>";
                                echo "<input type='submit' class='button button-primary btnEdit' style='width:60px;' value='Edit'></td> ";
                                echo "</tr>";

                            }
                        }
                    ?>
                    
                </tbody>
                <style>
                    td img{
                        width:60px;
                        height:60px;
                    }
                    tr .id{
                        display:none;
                    }
                </style>
                <script type="text/javascript">

                    
                    (function ($) {
                        $("tr .button").click(function(e){
                            
                            var id=$(this).parent().parent().find(".id").html();
                            $(".font_form").find("#font_id").val(id);

                            //alert(id);
                        })
                        $(".btnEdit").click(function(e){
                            $("#edit_clicked").val('true');
                        });
                    })(jQuery);

                </script>
			</table>
		</form>
		</div>
		<?php
    }
}









//remove_shortcode('font_preview_content');


new Raqeem();