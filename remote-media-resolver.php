<?php
/*
Plugin Name: Remote Media Resolver
Description: Redirect 404s referencing the uploads directory to a production URL with the same file path.
Author: Tyler Hebenstreit
*/

class RemoteMediaResolver {
    function __construct() {
        add_action( 'init', [$this, 'resolve'] );
        add_action( 'admin_menu', [$this, 'admin_menu'], 99 );
    }

    function admin_menu() {
        add_options_page( 'Remote Media Resolver', 'Remote Media Resolver', 'manage_options', 'remote-media', [$this, 'settings_page'] );
    }

    function settings_page() {
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized user' );
        }

        if ( isset( $_POST['submit'] ) ) {
            check_admin_referer( 'rmr_settings' );

            $host = sanitize_text_field( $_POST['remote_host'] );
            $base_path = trim( $this->get_upload_path(), '/' );
            $host = str_ireplace( $base_path, '', $host );
            $host = trim( $host, '/' );
            if ( $host ) {
                update_option('rmr_remote_host', explode('.', $host));
            } else {
                delete_option( 'rmr_remote_host' );
            }
        }

        $remote_host_arr = get_option( 'rmr_remote_host', [] );
        $remote_host = implode( $remote_host_arr, '.' );
        ?>
        <div class="wrap">
            <h1>Remote Media Resolver Settings</h1>
            <form method="POST">
                <?php wp_nonce_field( 'rmr_settings' ); ?>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <label for="remote-host">Remote Host Base URL<br/><small>(do not include content path)</small></label>
                        </th>
                        <td>
                            <input type="text" name="remote_host" id="remote-host" value="<?php echo $remote_host; ?>" autocomplete="off">
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" name="submit" value="Save" class="button button-primary button-large">
                </p>
            </form>
        </div>
        <?php
    }

    function resolve() {
        $remote_host_arr = get_option( 'rmr_remote_host', false );
        if ( !$remote_host_arr ) return;

        $remote_host = implode( $remote_host_arr, '.' );
        $request = $_SERVER['REQUEST_URI'];
        $path = $this->get_path( $request );
        $base_path = $this->get_upload_path();
        if ( !stristr( $path, $base_path ) ) return;

        header( 'HTTP/1.0 301 Moved Permanently');
        header( "Location: $remote_host/$path" );
        exit;
    }

    function get_path( $url ) {
        $args = parse_url( $url );
        return $args['path'];
    }

    function get_upload_path() {
        $args = wp_upload_dir();
        $base = $args['baseurl'];
        return $this->get_path( $base );
    }
}

new RemoteMediaResolver();
