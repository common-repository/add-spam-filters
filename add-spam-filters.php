<?php
/**
* Plugin Name: Add Spam Filters
* Plugin URI: https://www.addspamfilters.com/
* Description: Stop Spam User Registrations by Adding StopForumSpam.com, emailable.com, and Email Domain Extension Filters.
* Version: 1.0
* Author: Kegan Quimby
* Author URI: https://www.keganquimby.com/
* Text Domain: adaptsites_addspamfilters
**/

function adaptsites_addspamfilters( $errors, $sanitized_user_login, $user_email ) {
    
// Get Plugin Settings to $options variable
$options = get_option( 'adaptsites_addspamfilters_settings' );

// Set Master Error Variable to Reduce theChecker API Calls
// If Error Occurs Before theChecker API Call, Do Not Check Email With theChecker
$hasErrorOccurred = 0;

    
// If StopForumSpam Is Enabled
if ($options['adaptsites_addspamfilters_select_field_0'] == 1)
{
    
    // Check IP Address and save to $ip variable
    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) 
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) 
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } 
    else 
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // Check StopForumSpam for IP Address and Submitted Email Address
    $stopforumspambody = wp_remote_retrieve_body( wp_remote_get( "http://api.stopforumspam.org/api?email=" . $user_email . "&ip=" . $ip . "&json" ) );
    
    if( is_wp_error( $stopforumspambody ) ) {
        
        // If Error Checking StopForumSpam Results, Add Registration Error
        $errors->add( 'stopforumspam_error', __( '<strong>ERROR</strong>: Could Not Validate Email. Try again.', 'adaptsites_addspamfilters' ) );
        $hasErrorOccurred = 1;
        
        return $errors; // Bail early
    }

    // Decode CURL Response
    $obj = json_decode( $stopforumspambody, true );

    // Check If Email Is In StopForumSpam Results
    if ($obj['email']['appears'] >= 1)
    {
    
    // If Email Is In StopForumSpam Results, Add Registration Error
    $errors->add( 'stopforumspam_error', __( '<strong>ERROR</strong>: Please Enter A Valid Email', 'adaptsites_addspamfilters' ) );
    $hasErrorOccurred = 1;

    }
    
    
    
}

// If Domain Extensions Is Enabled
if (($options['adaptsites_addspamfilters_select_field_1'] == 1) && ($hasErrorOccurred == 0))
{
    // Check First Text Field
    if ($options['adaptsites_addspamfilters_text_field_2'] != "")
    {
        if (substr($user_email, (-1 * strlen($options['adaptsites_addspamfilters_text_field_2']))) == $options['adaptsites_addspamfilters_text_field_2'] )
        {
            // Add Domain Extension Match Error
            $errors->add( 'stopforumspam_error', __( '<strong>ERROR</strong>: Please Enter A Valid Email', 'adaptsites_addspamfilters' ) );
            $hasErrorOccurred = 1;
        
        }
        
    }
    
    // Check Second Text Field
    if ($options['adaptsites_addspamfilters_text_field_3'] != "")
    {
        if (substr($user_email, (-1 * strlen($options['adaptsites_addspamfilters_text_field_3']))) == $options['adaptsites_addspamfilters_text_field_3'] )
        {
            // Add Domain Extension Match Error
            $errors->add( 'stopforumspam_error', __( '<strong>ERROR</strong>: Please Enter A Valid Email', 'adaptsites_addspamfilters' ) );
            $hasErrorOccurred = 1;
        
        }
        
    }
    
    // Check Third Text Field
    if ($options['adaptsites_addspamfilters_text_field_4'] != "")
    {
        if (substr($user_email, (-1 * strlen($options['adaptsites_addspamfilters_text_field_4']))) == $options['adaptsites_addspamfilters_text_field_4'] )
        {
            // Add Domain Extension Match Error
            $errors->add( 'stopforumspam_error', __( '<strong>ERROR</strong>: Please Enter A Valid Email', 'adaptsites_addspamfilters' ) );
            $hasErrorOccurred = 1;
        }
        
    }
}

// If theChecker Is Enabled and has API key
if (($options['adaptsites_addspamfilters_select_field_5'] == 1) && ($options['adaptsites_addspamfilters_text_field_8'] != "") && ($hasErrorOccurred == 0))
{
    
    // Check If Email is Valid Using TheChecker
    $theCheckerResponseBody = wp_remote_retrieve_body( wp_remote_get( "https://api.emailable.com/v1/verify?email=" . $user_email . "&api_key=" . $options['adaptsites_addspamfilters_text_field_8'] ) );
    
    if( is_wp_error( $theCheckerResponseBody ) ) {
        
        // The Checker Response Validation
        $errors->add( 'checkerio_error', __( '<strong>ERROR</strong>: Could Not Validate Email. Try again.', 'adaptsites_addspamfilters' ) );
        
        return $errors; // Bail early
    }

    // Decode TheChecker Response
    $theCheckerResponseResult = json_decode($theCheckerResponseBody)->{'result'};

           
    if ($options['adaptsites_addspamfilters_select_field_6'] == 1)
    {
        if ($options['adaptsites_addspamfilters_select_field_7'] == 1)
        {
            // Accepting Both Deliverable and Risky
            if (($theCheckerResponseResult != 'deliverable' ) && ($theCheckerResponseResult != 'risky' ) )
            {
            $errors->add( 'checkerio_error', __( '<strong>ERROR</strong>: ' . $theCheckerResponseResult . ' Email. Please Enter A Valid Email', 'adaptsites_addspamfilters' ) );
            }
        
        }
        else
        {
            // Accepting Deliverable Only
            if ($theCheckerResponseResult != 'deliverable' )
            {
            $errors->add( 'checkerio_error', __( '<strong>ERROR</strong>: ' . $theCheckerResponseResult . ' Email. Please Enter A Valid Email', 'adaptsites_addspamfilters' ) );
            }
        }
        
    }
    else
    {
        // Accepting Risky Only
        if ($options['adaptsites_addspamfilters_select_field_7'] == 1)
        {
            if ($theCheckerResponseResult != 'risky' )
            {
            $errors->add( 'checkerio_error', __( '<strong>ERROR</strong>: ' . $theCheckerResponseResult . ' Email. Please Enter A Valid Email', 'adaptsites_addspamfilters' ) );
            }
        }
            
    }

}



// Return Errors
return $errors;

}

add_filter( 'registration_errors', 'adaptsites_addspamfilters', 5, 3 );


add_action( 'admin_menu', 'adaptsites_addspamfilters_add_admin_menu' );
add_action( 'admin_init', 'adaptsites_addspamfilters_settings_init' );


function adaptsites_addspamfilters_add_admin_menu(  ) { 

	add_submenu_page( 'users.php', 'Add Spam Filters', 'Add Spam Filters', 'manage_options', 'add_spam_filters', 'adaptsites_addspamfilters_options_page' );

}


function adaptsites_addspamfilters_settings_init(  ) { 

	register_setting( 'addspamfiltersPage', 'adaptsites_addspamfilters_settings' );
	
	// StopForumSpam Settings

	add_settings_section(
		'adaptsites_addspamfilters_stopforumspam_section', 
		__( 'StopForumSpam Settings', 'adaptsites_addspamfilters' ), 
		'adaptsites_addspamfilters_stopforumspam_section_callback', 
		'addspamfiltersPage'
	);

	add_settings_field( 
		'adaptsites_addspamfilters_select_field_0', 
		__( 'Check User Registrations Against StopForumSpam?', 'adaptsites_addspamfilters' ), 
		'adaptsites_addspamfilters_select_field_0_render', 
		'addspamfiltersPage', 
		'adaptsites_addspamfilters_stopforumspam_section' 
	);
	
	// Domain Extension Settings
	
	add_settings_section(
		'adaptsites_addspamfilters_domainextension_section', 
		__( 'Domain Extension Settings', 'adaptsites_addspamfilters' ), 
		'adaptsites_addspamfilters_domainextension_section_callback', 
		'addspamfiltersPage'
	);

	add_settings_field( 
		'adaptsites_addspamfilters_select_field_1', 
		__( 'Check User Registration Domain Extensions?', 'adaptsites_addspamfilters' ), 
		'adaptsites_addspamfilters_select_field_1_render', 
		'addspamfiltersPage', 
		'adaptsites_addspamfilters_domainextension_section' 
	);

	add_settings_field( 
		'adaptsites_addspamfilters_text_field_2', 
		__( 'First Extension', 'adaptsites_addspamfilters' ), 
		'adaptsites_addspamfilters_text_field_2_render', 
		'addspamfiltersPage', 
		'adaptsites_addspamfilters_domainextension_section' 
	);

	add_settings_field( 
		'adaptsites_addspamfilters_text_field_3', 
		__( 'Second Extension', 'adaptsites_addspamfilters' ), 
		'adaptsites_addspamfilters_text_field_3_render', 
		'addspamfiltersPage', 
		'adaptsites_addspamfilters_domainextension_section' 
	);

	add_settings_field( 
		'adaptsites_addspamfilters_text_field_4', 
		__( 'Third Extension', 'adaptsites_addspamfilters' ), 
		'adaptsites_addspamfilters_text_field_4_render', 
		'addspamfiltersPage', 
		'adaptsites_addspamfilters_domainextension_section' 
	);
	
	// theChecker Settings
	
	add_settings_section(
		'adaptsites_addspamfilters_thechecker_section', 
		__( 'theChecker Settings', 'adaptsites_addspamfilters' ), 
		'adaptsites_addspamfilters_thechecker_section_callback', 
		'addspamfiltersPage'
	);

	add_settings_field( 
		'adaptsites_addspamfilters_select_field_5', 
		__( 'Check User Registrations Against Emailable.com?', 'adaptsites_addspamfilters' ), 
		'adaptsites_addspamfilters_select_field_5_render', 
		'addspamfiltersPage', 
		'adaptsites_addspamfilters_thechecker_section' 
	);
	
	add_settings_field( 
		'adaptsites_addspamfilters_select_field_6', 
		__( 'Allow Deliverable (Mailbox Exists) Email Registrations (Recommended)' ), 
		'adaptsites_addspamfilters_select_field_6_render', 
		'addspamfiltersPage', 
		'adaptsites_addspamfilters_thechecker_section' 
	);

	add_settings_field( 
		'adaptsites_addspamfilters_select_field_7', 
		__( 'Allow Risky (Mailbox appears to exist, may be catch-all, disposable, or role based email address) Email Registrations (Recommended)' ), 
		'adaptsites_addspamfilters_select_field_7_render', 
		'addspamfiltersPage', 
		'adaptsites_addspamfilters_thechecker_section' 
	);

	add_settings_field( 
		'adaptsites_addspamfilters_text_field_8', 
		__( 'Enter your Emailable.com API Key From <a href="https://app.emailable.com/api" target="_blank">https://app.emailable.com/api</a>', 'adaptsites_addspamfilters' ), 
		'adaptsites_addspamfilters_text_field_8_render', 
		'addspamfiltersPage', 
		'adaptsites_addspamfilters_thechecker_section' 
	);

}


function adaptsites_addspamfilters_select_field_0_render(  ) { 

	$options = get_option( 'adaptsites_addspamfilters_settings' );
	?>
	<select name='adaptsites_addspamfilters_settings[adaptsites_addspamfilters_select_field_0]'>
		<option value='1' <?php selected( $options['adaptsites_addspamfilters_select_field_0'], 1 ); ?>>Yes</option>
		<option value='2' <?php selected( $options['adaptsites_addspamfilters_select_field_0'], 2 ); ?>>No</option>
	</select>
	<?php

}


function adaptsites_addspamfilters_select_field_1_render(  ) { 

	$options = get_option( 'adaptsites_addspamfilters_settings' );
	?>
	<select name='adaptsites_addspamfilters_settings[adaptsites_addspamfilters_select_field_1]'>
		<option value='1' <?php selected( $options['adaptsites_addspamfilters_select_field_1'], 1 ); ?>>Yes</option>
		<option value='2' <?php selected( $options['adaptsites_addspamfilters_select_field_1'], 2 ); ?>>No</option>
	</select>
	<?php

}


function adaptsites_addspamfilters_text_field_2_render(  ) { 

	$options = get_option( 'adaptsites_addspamfilters_settings' );
	?>
	<input type='text' name='adaptsites_addspamfilters_settings[adaptsites_addspamfilters_text_field_2]' value='<?php echo $options['adaptsites_addspamfilters_text_field_2']; ?>'>
	<?php

}


function adaptsites_addspamfilters_text_field_3_render(  ) { 

	$options = get_option( 'adaptsites_addspamfilters_settings' );
	?>
	<input type='text' name='adaptsites_addspamfilters_settings[adaptsites_addspamfilters_text_field_3]' value='<?php echo $options['adaptsites_addspamfilters_text_field_3']; ?>'>
	<?php

}


function adaptsites_addspamfilters_text_field_4_render(  ) { 

	$options = get_option( 'adaptsites_addspamfilters_settings' );
	?>
	<input type='text' name='adaptsites_addspamfilters_settings[adaptsites_addspamfilters_text_field_4]' value='<?php echo $options['adaptsites_addspamfilters_text_field_4']; ?>'>
	<?php

}


function adaptsites_addspamfilters_select_field_5_render(  ) { 

	$options = get_option( 'adaptsites_addspamfilters_settings' );
	?>
	<select name='adaptsites_addspamfilters_settings[adaptsites_addspamfilters_select_field_5]'>
		<option value='1' <?php selected( $options['adaptsites_addspamfilters_select_field_5'], 1 ); ?>>Yes</option>
		<option value='2' <?php selected( $options['adaptsites_addspamfilters_select_field_5'], 2 ); ?>>No</option>
	</select>
	<?php

}

function adaptsites_addspamfilters_select_field_6_render(  ) { 

	$options = get_option( 'adaptsites_addspamfilters_settings' );
	?>
	<select name='adaptsites_addspamfilters_settings[adaptsites_addspamfilters_select_field_6]'>
		<option value='1' <?php selected( $options['adaptsites_addspamfilters_select_field_6'], 1 ); ?>>Yes</option>
		<option value='2' <?php selected( $options['adaptsites_addspamfilters_select_field_6'], 2 ); ?>>No</option>
	</select>
	<?php

}

function adaptsites_addspamfilters_select_field_7_render(  ) { 

	$options = get_option( 'adaptsites_addspamfilters_settings' );
	?>
	<select name='adaptsites_addspamfilters_settings[adaptsites_addspamfilters_select_field_7]'>
		<option value='1' <?php selected( $options['adaptsites_addspamfilters_select_field_7'], 1 ); ?>>Yes</option>
		<option value='2' <?php selected( $options['adaptsites_addspamfilters_select_field_7'], 2 ); ?>>No</option>
	</select>
	<?php

}

function adaptsites_addspamfilters_text_field_8_render(  ) { 

	$options = get_option( 'adaptsites_addspamfilters_settings' );
	?>
	<input type='text' name='adaptsites_addspamfilters_settings[adaptsites_addspamfilters_text_field_8]' value='<?php echo $options['adaptsites_addspamfilters_text_field_8']; ?>'>
	<?php

}


function adaptsites_addspamfilters_stopforumspam_section_callback(  ) { 

	echo __( 'Do you want to use <a href="https://www.stopforumspam.com/" target="_blank">https://www.stopforumspam.com/</a> to check user registrations?', 'adaptsites_addspamfilters' );

}

function adaptsites_addspamfilters_domainextension_section_callback(  ) { 

	echo __( 'Do you want to check the last few characters of a submitted email addresses for domain extensions like .ru or even free email services like yahoo.com? Check the checkbox and enter one email domain (ex: yahoo.com ) or one domain extension (ex: .ru ) to a box in the boxes below to not allow registrations that include an email with those domains or extensions.', 'adaptsites_addspamfilters' );

}

function adaptsites_addspamfilters_thechecker_section_callback(  ) { 

	echo __( 'Do you want to use Emailable.com to check the validity of emails people are using to register for your site?" target="_blank">Emailable.com</a>.', 'adaptsites_addspamfilters' );

}


function adaptsites_addspamfilters_options_page(  ) { 

		?>
		<form action='options.php' method='post'>

			<h2>Add Spam Filters</h2>

			<?php
			settings_fields( 'addspamfiltersPage' );
			do_settings_sections( 'addspamfiltersPage' );
			submit_button();
			?>

		</form>
		<?php

}
