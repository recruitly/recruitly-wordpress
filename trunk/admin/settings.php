<?php
/**
 * Renders Plugin Settings:
 * Users can enter their company name and API Key using settings page.
 */
function recruitly_wordpress_settings()
{

	if(!current_user_can('administrator')){
		recruitly_admin_notice('Please login as administrator to configure Recruitly!!','error');
		exit;
    }

    if( isset($_POST['recruitly_apiserver']) && isset($_POST['recruitly_apikey']))
    {
        //Verify NONCE
        if(wp_verify_nonce( $_POST['recruitly_nonce'], 'recruitly_save_action' )){

	        //Sanitize API Key
	        $apiKey= sanitize_text_field($_POST['recruitly_apikey']);

	        //Sanitize API Server
	        $apiServer = sanitize_text_field($_POST['recruitly_apiserver']);

            //Sanitize Custom Post Name
            $customPostType = sanitize_text_field($_POST['recruitly_customposttype']);

            if($customPostType===null || trim($customPostType)===""){
                recruitly_admin_notice('Custom Post Type Name is Required!','error');
                exit;
            }

	        //Validate and Update Options
	        if(strpos($apiServer, 'recruitly.io') !== false) {

		        update_option( 'recruitly_apiserver', $apiServer );
		        update_option( 'recruitly_apikey', $apiKey );
                update_option( 'recruitly_refresh', ( int ) $_POST[ 'recruitly_refresh' ] );
                update_option( 'recruitly_customposttype', $customPostType );

                if(!post_type_exists($customPostType))
                {
                    recruitly_admin_notice('Recruitly setup is in progress...','info');

                    recruitly_wordpress_setup_post_type();

                    recruitly_wordpress_setup_taxonomies();

                    add_submenu_page('edit.php?post_type='+$customPostType, 'Recruitly Jobs Admin', 'Settings', 'edit_posts', basename(__FILE__), 'recruitly_wordpress_settings');

                    recruitly_admin_notice('Recruitly setup completed successfully...','success');

                }

                recruitly_wordpress_insert_post_type();

	        } else {
		        recruitly_admin_notice('Invalid API Server!','error');
		        exit;
            }

        }else{
	        recruitly_admin_notice('Invalid Request!','error');
            exit;
        }
    }

    $refresh = get_option( 'recruitly_refresh' )
    ?>
    <div class="wrap">
        <?php    echo "<h2>" . __( 'Recruitly Wordpress Integration', 'Recruitly' ) . "</h2>"; ?>
        <p><a href="https://recruitly.io" target="_blank" title="Recruitly">Recruitly</a> is a cloud-based recruitment software for the agencies in the UK.</p>
        <p>The Recruitly plugin for WordPress helps users setup Job Board on the website within minutes, all you need is an API Key to get started. You can obtain your API key from your Recruitly account.</p>
        <p>If you don't know the API Key or API Server please talk to one of the members at Recruitly to get one.</p>
        <form name="recruitly_configuration_form" method="post" action="">

            <table class="widefat">
                <thead>
                <tr><th>Parameter</th><th>Value</th></tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <?php _e("Custom Post Type Name: " ); ?>
                    </td>
                    <td>
                        <input title="Custom Post Type Name" type="text" name="recruitly_customposttype" required value="<?php echo esc_attr(get_option('recruitly_customposttype','recruitlyjobs')); ?>" size="40"/>
                        <p>Appears in your URL structure for your Recruitly jobs.</p>
                        <p><code><?php echo esc_attr(get_option('siteurl','')); ?>/<?php echo esc_attr(get_option('recruitly_customposttype','recruitlyjobs')); ?>/</code>.
                        <p class="description" style="color:red">Please note that changing this value will invalidate your existing job links.</p>
                    </td>
                </tr>
                <tr>
                    <td><?php _e("API Key: " ); ?></td>
                    <td><input title="API Key" type="text" name="recruitly_apikey" value="<?php echo esc_attr(get_option('recruitly_apikey','')); ?>" required size="40"/></td>
                </tr>
                <tr>
                    <td><?php _e("API Server: " ); ?></td>
                    <td><input title="API Server" type="text" name="recruitly_apiserver" value="<?php echo esc_attr(get_option('recruitly_apiserver','')); ?>" required size="60"/></td>
                </tr>
                <tr>
                    <td><?php _e("Refresh Interval: " ); ?></td>
                    <td>
                        <select name="recruitly_refresh">
                            <option value="15" <?php echo $refresh == 15 ? 'selected' : ''?>>15 Minutes</option>
                            <option value="30" <?php echo $refresh == 30 ? 'selected' : ''?>>30 Minutes</option>
                            <option value="60" <?php echo $refresh == 60 ? 'selected' : ''?>>60 Minutes</option>
                            <option value="" <?php echo !$refresh ? 'selected' : ''?>>Overnight</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="hidden" name="recruitly_api_details" value="1">
	                    <?php
	                    wp_nonce_field( 'recruitly_save_action' ,'recruitly_nonce');
	                    submit_button( __( 'Update Configuration', 'Recruitly' ) );
	                    ?>
                    </td>
                </tr>
            </table>

        </form>

    </div>
<?php
}?>