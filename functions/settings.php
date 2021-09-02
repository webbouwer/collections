<?php
/* CollectionsSettings */

// source https://stackoverflow.com/questions/45735437/how-to-save-a-select-option-dropdown-in-a-wordpress-plugin
// maybe should just use http://wpsettingsapi.jeroensormani.com/ for settings page?
class CollectionsSettings {

    private $dropdown_option_setting_options;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'dropdown_option_setting_add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'dropdown_option_setting_page_init' ) );
    }

    public function dropdown_option_setting_add_plugin_page() {
        add_options_page(
            'Collections Settings', // page_title
            'Collections', // menu_title
            'manage_options', // capability
            'dropdown-option-setting', // menu_slug
            array( $this, 'dropdown_option_setting_create_admin_page' ) // function
        );
    }

    public function dropdown_option_setting_create_admin_page() {
        $this->dropdown_option_setting_options = get_option( 'dropdown_option_setting_option_name' ); ?>

        <div class="wrap">
            <h2>Collections Settings</h2>
            <p>Some basic collection options</p>
            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <?php
                    settings_fields( 'dropdown_option_setting_option_group' );
                    do_settings_sections( 'dropdown-option-setting-admin' );
                    submit_button();
                ?>
            </form>
        </div>
    <?php }

    public function dropdown_option_setting_page_init() {
        register_setting(
            'dropdown_option_setting_option_group', // option_group
            'dropdown_option_setting_option_name', // option_name
            array( $this, 'dropdown_option_setting_sanitize' ) // sanitize_callback
        );

        add_settings_section(
            'dropdown_option_setting_setting_section', // id
            'Settings', // title
            array( $this, 'dropdown_option_setting_section_info' ), // callback
            'dropdown-option-setting-admin' // page
        );

        add_settings_field(
            'dropdown_option_0', // id
            'Select collection display', // title
            array( $this, 'dropdown_option_0_callback' ), // callback
            'dropdown-option-setting-admin', // page
            'dropdown_option_setting_setting_section' // section
        );
    }

    public function dropdown_option_setting_sanitize($input) {
        $sanitary_values = array();
        if ( isset( $input['dropdown_option_0'] ) ) {
            $sanitary_values['dropdown_option_0'] = $input['dropdown_option_0'];
        }
        // https://wordpress.stackexchange.com/questions/107546/add-settings-field-exclude-categories-reading-wp-category-checklist
        return $sanitary_values;
    }

    public function dropdown_option_setting_section_info() {

    }

    public function dropdown_option_0_callback() {
        ?> <select name="dropdown_option_setting_option_name[dropdown_option_0]" id="dropdown_option_0">
            <?php $selected = (isset( $this->dropdown_option_setting_options['dropdown_option_0'] ) && $this->dropdown_option_setting_options['dropdown_option_0'] === 'basic') ? 'selected' : '' ; ?>
            <option value="basic" <?php echo $selected; ?>>Basic list</option>
            <?php $selected = (isset( $this->dropdown_option_setting_options['dropdown_option_0'] ) && $this->dropdown_option_setting_options['dropdown_option_0'] === 'grid') ? 'selected' : '' ; ?>
            <option value="grid" <?php echo $selected; ?>>Ajax grid and list</option>
        </select> <?php
    }

}

/*
// source https://codex.wordpress.org/Creating_Options_Pages
class CollectionsSettings
{

    private $options;

    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'Collection Settings',
            'manage_options',
            'my-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }


    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'my_option_name' );
        ?>
        <div class="wrap">
            <h1>Collection Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }


    public function page_init()
    {
        register_setting(
            'my_option_group', // Option group
            'my_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            '- TODO - UNDER CONSTRUCTION -', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );

        add_settings_field(
            'id_number', // ID
            'ID Number', // Title
            array( $this, 'id_number_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section
        );

        add_settings_field(
            'title',
            'Title',
            array( $this, 'title_callback' ),
            'my-setting-admin',
            'setting_section_id'
        );

        // + https://wordpress.stackexchange.com/questions/59364/settings-api-how-to-create-a-multi-checkbox-with-blog-categories
    }

    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        return $new_input;
    }

    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    public function id_number_callback()
    {
        printf(
            '<input type="text" id="id_number" name="my_option_name[id_number]" value="%s" />',
            isset( $this->options['id_number'] ) ? esc_attr( $this->options['id_number']) : ''
        );
    }

    public function title_callback()
    {
        printf(
            '<input type="text" id="title" name="my_option_name[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }

}
*/
