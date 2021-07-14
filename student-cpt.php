<?php
/**
 * Plugin Name: Student CPT
 * Description: Adds student CPT and fields
 * Author: Kristian Vassilev
 * Version: 1.0.0
 *
 * @package student-cpt
 */

/**
 * Creates the student post type
 *
 * @return void
 */
function ob_student_post_type() {

	$args = array(
		'labels'      => array(
			'name'          => 'Students',
			'singular_name' => 'student',
		),
		'public'      => true,
		'has_archive' => true,
		'menu_icon'   => 'dashicons-universal-access',
		'supports'    => array( 'title', 'editor', 'thumbnail', 'excerpt', 'catecogy', 'content' ),
	);

	register_post_type( 'student', $args );
}
// adds the student post type upon WordPress init.
add_action( 'init', 'ob_student_post_type' );

/**
 * Adds enrolment taxonomy
 *
 * @return void
 */
function ob_enrolment_taxonomy() {

	$args = array(
		'labels'       => array(
			'name'          => 'Subjects',
			'singular_name' => 'Subject',
		),
		'public'       => true,
		'hierarchical' => true, // true for category, false for tag.
	);
	register_taxonomy( 'subjects', array( 'student' ), $args );
}

add_action( 'init', 'ob_enrolment_taxonomy' );


// ----> Student Post Type Meta Boxes.


/**
 * Adds location metabox to student post type
 *
 * @return void
 */
function ob_student_location_add_meta_box() {
	add_meta_box( 'student_location', 'Student location', 'ob_student_location_callback', 'student', 'side' );
}

/**
 * Adds address metabox to student post type
 *
 * @return void
 */
function ob_student_address_add_meta_box() {
	add_meta_box( 'student_address', 'Student address', 'ob_student_address_callback', 'student', 'side' );
}

/**
 * Adds birth date metabox to student post type
 *
 * @return void
 */
function ob_student_birth_date_add_meta_box() {
	add_meta_box( 'student_birth_date', 'Student birth date', 'ob_student_birth_date_callback', 'student', 'side' );
}

/**
 * Adds class/grade metabox to student post type
 *
 * @return void
 */
function ob_student_class_add_meta_box() {
	add_meta_box( 'student_class', 'Student class', 'ob_student_class_callback', 'student', 'side' );
}

/**
 * Adds the student location form to the edit screen
 *
 * @param object $post is WordpPress post (object).
 * @return void
 */
function ob_student_location_callback( $post ) {
	wp_nonce_field( 'ob_save_student_location', 'ob_student_location_meta_box_nonce' );

	$value = get_post_meta( $post->ID, '_student_location_value_key', true );

	echo '<label for="ob_student_location_field">Lives In (Country, City): </label>';
	echo '<input type="text" id="ob_student_location_field" name="ob_student_location_field" value="' . esc_attr( $value ) . '"size="25" />';
}

/**
 * Adds the student address form to the edit screen
 *
 * @param object $post is WordpPress post (object).
 * @return void
 */
function ob_student_address_callback( $post ){
	wp_nonce_field( 'ob_save_student_address', 'ob_student_address_meta_box_nonce' );

	$value = get_post_meta( $post->ID, '_student_address_value_key', true );

	echo '<label for="ob_student_address_field">Address: </label>';
	echo '<input type="text" id="ob_student_address_field" name="ob_student_address_field" value="' . esc_attr( $value ) . '"size="25" />';
}

/**
 * Adds the birth date form to the edit screen
 *
 * @param object $post is WordpPress post (object).
 * @return void
 */
function ob_student_birth_date_callback( $post ){
	wp_nonce_field( 'ob_save_student_birth_date', 'ob_student_birth_date_meta_box_nonce' );

	$value = get_post_meta( $post->ID, '_student_birth_date_value_key', true );

	echo '<label for="ob_student_birth_date_field">Birth date: </label>';
	echo '<input type="date" id="ob_student_birth_date_field" name="ob_student_birth_date_field" value="' . esc_attr( $value ) . '"size="25" />';
}

/**
 * Adds the student class/grade form to the edit screen
 *
 * @param object $post is WordpPress post (object).
 * @return void
 */
function ob_student_class_callback( $post ){
	wp_nonce_field( 'ob_save_student_class', 'ob_student_class_meta_box_nonce' );

	$value = get_post_meta( $post->ID, '_student_class_value_key', true );

	echo '<label for="ob_student_class_field">Class / Grade: </label>';
	echo '<input type="text" id="ob_student_class_field" name="ob_student_class_field" value="' . esc_attr( $value ) . '"size="25" />';
}

// Calls the functions to add the meta boxes.
add_action( 'add_meta_boxes', 'ob_student_location_add_meta_box' );
add_action( 'add_meta_boxes', 'ob_student_address_add_meta_box' );
add_action( 'add_meta_boxes', 'ob_student_birth_date_add_meta_box' );
add_action( 'add_meta_boxes', 'ob_student_class_add_meta_box' );

/**
 * Verifies, sanitizes and saves the user input for the location field to the db
 *
 * @param string $post_id is the id of the current post.
 * @return void
 */
function ob_save_student_location( $post_id ) {
	if ( ! isset( $_POST['ob_student_location_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['ob_student_location_meta_box_nonce'], 'ob_save_student_location' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['ob_student_location_field'] ) ) {
		return;
	}

	$ob_student_location_data = sanitize_text_field( $_POST['ob_student_location_field'] );

	update_post_meta( $post_id, '_student_location_value_key', $ob_student_location_data );

}

add_action( 'save_post', 'ob_save_student_location' );



/**
 * Verifies, sanitizes and saves the user input for the address field to the db
 *
 * @param string $post_id is the id of the current post.
 * @return void
 */
function ob_save_student_address( $post_id ) {
	if ( ! isset( $_POST['ob_student_address_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['ob_student_address_meta_box_nonce'], 'ob_save_student_address' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['ob_student_address_field'] ) ) {
		return;
	}

	$ob_student_address_data = sanitize_text_field( $_POST['ob_student_address_field'] );

	update_post_meta( $post_id, '_student_address_value_key', $ob_student_address_data );
}

add_action( 'save_post', 'ob_save_student_address' );



/**
 * Verifies, sanitizes and saves the user input for the birth date field to the db
 *
 * @param string $post_id is the id of the current post.
 * @return void
 */
function ob_save_student_birth_date( $post_id ) {
	if ( ! isset( $_POST['ob_student_birth_date_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['ob_student_birth_date_meta_box_nonce'], 'ob_save_student_birth_date' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['ob_student_birth_date_field'] ) ) {
		return;
	}

	$ob_student_birth_date_data = sanitize_text_field( $_POST['ob_student_birth_date_field'] );

	update_post_meta( $post_id, '_student_birth_date_value_key', $ob_student_birth_date_data );

}

add_action( 'save_post', 'ob_save_student_birth_date' );



/**
 * Verifies, sanitizes and saves the user input for the class / grade field to the db
 *
 * @param string $post_id is the id of the current post.
 * @return void
 */
function ob_save_student_class( $post_id ) {
	if ( ! isset( $_POST['ob_student_class_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['ob_student_class_meta_box_nonce'], 'ob_save_student_class' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['ob_student_class_field'] ) ) {
		return;
	}

	$ob_student_class_data = sanitize_text_field( $_POST['ob_student_class_field'] );

	update_post_meta( $post_id, '_student_class_value_key', $ob_student_class_data );

}

add_action( 'save_post', 'ob_save_student_class' );



/**
 * Adds the checkbox.js script
 *
 * @return void
 */
function ob_add_checkbox_script() {
	wp_enqueue_script( 'checkbox', plugins_url( 'checkbox.js', __FILE__ ), array( 'jquery' ), false, true );
	wp_localize_script( 'checkbox', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
};

// use admin_enqueue_scripts to enque to the admin only.
add_action( 'admin_enqueue_scripts', 'ob_add_checkbox_script' );
add_action( 'wp_ajax_checkbox', 'checkbox' );



// Adds custom column to Post Type "students".
add_action( 'manage_student_posts_columns', 'ob_student_custom_column_head' );

/**
 * Tackles the "header" of the column
 *
 * @param object $columns is the custom enable/disable column.
 * @return void
 */
function ob_student_custom_column_head( $columns ) {
	// $columns contains all currently available columns (Default).
	$columns['enable_student'] = 'Active Student'; // Adding to all currently present.
	return $columns;
}

// Gets the content of the status column.
add_action( 'manage_student_posts_custom_column', 'ob_student_custom_column_data', 10, 2 );
/**
 * Checks if the status of a given student is active or not (true or false)
 *
 * @param object $column is the custom enable/disable column.
 * @param object $post_id is the id of the WordPress post.
 * @return void
 */
function ob_student_custom_column_data( $column, $post_id ) {
	$current_status = get_post_meta( $post_id, '_is_active_student', true );
	switch ( $column ) {
		case 'enable_student':
			?>
			<input type="checkbox" name="activeStudent" id="activeStudent_<?php echo $post_id; ?>" 
			<?php
			if (
				'true' === $current_status ) {
				echo 'checked'; }
			?>
				>
			<?php
	}
}



/**
 * AJAX handler
 *
 * @return void
 */
function checkbox() {
	$checkbox_value = sanitize_text_field( $_POST['isActive'] );
	$student_id = $_POST['studentId'];
	update_post_meta( $student_id, '_is_active_student', $checkbox_value );

	wp_send_json_success( $checkbox_value, $student_id );
}
