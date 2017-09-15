<?php

function gf_field_cf_init() {

	class GF_Field_CF extends GF_Field {

		/**
		 * @var string $type The field type.
		 */
		public $type = 'codice-fiscale';

		/**
		 * Return the field title, for use in the form editor.
		 *
		 * @return string
		 */
		public function get_form_editor_field_title() {
			return esc_attr__( 'Codice Fiscale', 'gravityforms-pivacf-fields' );
		}

		/**
		 * Assign the field button to the Advanced Fields group.
		 *
		 * @return array
		 */
		public function get_form_editor_button() {
			return array(
				'group' => 'advanced_fields',
				'text' => $this->get_form_editor_field_title(),
			);
		}

		/**
		 * The settings which should be available on the field in the form editor.
		 *
		 * @return array
		 */
		function get_form_editor_field_settings() {
			return array(
				'label_setting',
				'input_class_setting',
				'admin_label_setting',
				'visibility_setting',
				'conditional_logic_field_setting',
			);
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		/**
		 * Define the fields inner markup.
		 *
		 * @param array $form The Form Object currently being processed.
		 * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
		 * @param null|array $entry Null or the Entry Object currently being edited.
		 *
		 * @return string
		 */
		public function get_field_input( $form, $value = '', $entry = null ) {
			$form_id = absint( $form[ 'id' ] );
			$is_entry_detail = $this->is_entry_detail();
			$is_form_editor = $this->is_form_editor();

			$html_input_type = 'text';

			$logic_event = !$is_form_editor && !$is_entry_detail ? $this->get_conditional_logic_event( 'keyup' ) : '';
			$id = ( int ) $this->id;
			$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

			$value = esc_attr( $value );
			$size = $this->size;
			$class_suffix = $is_entry_detail ? '_admin' : '';
			$class = $size . $class_suffix;

			$max_length = is_numeric( $this->maxLength ) ? "maxlength='{$this->maxLength}'" : '';

			$tabindex = $this->get_tabindex();
			$disabled_text = $is_form_editor ? 'disabled="disabled"' : '';
			$placeholder_attribute = $this->get_field_placeholder_attribute();
			$required_attribute = $this->isRequired ? 'aria-required="true"' : '';
			$invalid_attribute = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';

			$input = "<input name='input_{$id}' id='{$field_id}' type='{$html_input_type}' value='{$value}' class='{$class}' {$max_length} {$tabindex} {$logic_event} {$placeholder_attribute} {$required_attribute} {$invalid_attribute} {$disabled_text}/>";

			return sprintf( "<div class='ginput_container ginput_container_text'>%s</div>", $input );
		}

		public function validate( $value, $form ) {
			if ( preg_match( '/^[a-z]{6}[0-9]{2}[a-z][0-9]{2}[a-z][0-9]{3}[a-z]$/i', $value ) === 0 ) {
				$this->failed_validation = true;
				$this->validation_message = empty( $this->errorMessage ) ? __( 'Fiscal Code is not valid.', 'gravityforms-pivacf-fields' ) : $this->errorMessage;
            }
		}

	}

	GF_Fields::register( new GF_Field_CF() );
}

add_action( 'gform_loaded', 'gf_field_cf_init', 9999 );
