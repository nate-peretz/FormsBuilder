<?php
    namespace Component;

    use \Helpers\Template;

    class Component {
        public $form = [];

        public function addForm( $key, $url = '', $class = '', $redirect = '', $cols = 1) {
            // Check for required fields
            if( ! $key) throw new \Exception('missing key');

            $this->form = array(
                'key' => $key,
                'url' => $url,
                'class' => $class,
                'redirect' => $redirect,
                'cols' => $cols,
                'fields' => []
            );

            return $this;
        }

        /**
         * Types
         * text, hidden, number, email, textarea, title (For admin use)
         */
        public function addInput( $data) {
            // Check for required fields
            if( ! isset( $data['type'])) throw new \Exception('missing fields');

            if( ! isset( $this->form['fields'])) $this->form['fields'] = [];
            array_push( $this->form['fields'], $data);

            return $this;
        }

        public function addShortcodes( $shortcodes) {
            $data = array(
                'type' => 'shortcodes',
                'data' => $shortcodes
            );
            array_push( $this->form['fields'], $data);
            return $this;
        }

        /**
         * Types
         * button, submit
         */
        public function addButton( $data) {
            // Check for required fields
            $required_fields = ['type', 'value'];
            foreach( $required_fields as $field) {
                if( ! in_array( $field, array_keys( $data))) throw new \Exception('missing fields');
            }

            if( ! isset( $this->form['fields'])) $this->form['fields'] = [];
            array_push( $this->form['fields'], $data);

            return $this;
        }

        public function addLabel( $value) {
            // Check for required fields
            if( ! $value || empty( $value)) throw new \Exception('empty label');

            array_push( $this->form['fields'], array(
                'type'  => 'label',
                'value' => $value
            ));

            return $this;
        }

        public function addParagraph( $text) {
            // Check for required fields
            if( ! isset( $this->form['fields'])) $this->form['fields'] = [];

            array_push( $this->form['fields'], array(
                'type'  => 'paragraph',
                'value' => $text
            ));

            return $this;
        }

        public function addSelect( $data, $options = []) {
            if( ! $options || empty( $options)) $options = ['value' => '', 'label' => 'בחר'];

            // Shortcut for default selection
            if( isset( $data['selected_val']) && ! empty( $data['selected_val'])) {
                foreach( $options as $k => $v) {
                    if( ! isset( $v['value'])) $v['value'] = '';

                    if( $v['value'] == $data['selected_val']) {
                        $options[ $k]['selected'] = true;
                    }
                }
                unset( $data['selected_val']);
            }
            if( ! isset( $data['type'])) $data['type'] = 'select';

            if( ! isset( $this->form['fields'])) $this->form['fields'] = [];
            array_push( $this->form['fields'], array_merge( $data, ['options' => $options]));

            return $this;
        }

        public function addToggle( $label = '', $checked = false, $id = '') {
            $field = array(
                'type' => 'toggle',
                'label' => $label,
                'checked' => $checked
            );
            if( ! empty( $id)) $field['id'] = $id;

            array_push( $this->form['fields'], $field);

            return $this;
        }

        /**
         * Field groups can hold up to 6 fields in 1 group
         * According to bootstrap's containers
         */
        public function addFieldGroup( $fields_arr, $title = '', $attributes = [], $buttons = []) {
            if( ! isset( $this->form['fields'])) $this->form['fields'] = [];

            array_push( $this->form['fields'], array(
                'type' => 'field_group',
                'title' => $title,
                'fields' => $fields_arr->form['fields'],
                'attributes' => $attributes,
                'buttons' => $buttons
            ));

            return $this;
        }

        public function addButtonGroup( $buttons_arr) {
            if( ! isset( $this->form['fields'])) $this->form['fields'] = [];

            array_push( $this->form['fields'], array(
                'type' => 'button_group',
                'buttons' => $buttons_arr->form['fields']
            ));

            return $this;
        }

        /**
         * Render
         */
        public function renderForm( $form = true) {
            $output = ''; $buttons = '';

            // Columns setup
            $cols = ( isset( $this->form['cols'])) ? $this->form['cols'] : 1;
            $total_fields = count( $this->form['fields']); // Including label fields
            $max_col_fields = ( $total_fields % $cols == 0) ? $total_fields / $cols : intval( $total_fields / $cols) +1;
            $col_class = 'col-md-' . ( 12 / $cols);

            // Open form
            if( $form) $output .= '<form id="'. $this->form['key'] .'" class="'. $this->form['class'] .'" name="'. $this->form['key'] .'" action="'. $this->form['url'] .'" method="post" data-redirect="'. $this->form['redirect'] .'">';
            if( $cols > 1) $output .= '<div class="row">';
            $field_counter = 0;

            // Add fields
            foreach( $this->form['fields'] as $id => $field) {
                // Open column
                if( $cols > 1 && ( $field_counter == 0 || $field_counter == $max_col_fields)) {
                    $output .= '<div class="'. $col_class .'">';
                }

                if( in_array( $field['type'], ['text', 'hidden', 'number', 'email'])) {
                    $output .= Template::renderInput( $field);

                } else if( $field['type'] == 'textarea') {
                    $output .= Template::renderTextarea( $field);

                } else if( $field['type'] == 'shortcodes') {
                    $output .= Template::renderShortcodes( $field['data']);

                } else if( $field['type'] == 'label') {
                    $output .= Template::renderLabel( $field);

                } else if( in_array( $field['type'], ['button', 'submit'])) {
                    // If cols > 1, we want to keep the buttons 100% so we add them later ($buttons)
                    if( $cols > 1) {
                        $buttons .= Template::renderButton( $field);
                    } else {
                        $output .= Template::renderButton( $field);
                    }

                } else if( $field['type'] == 'select') {
                    $output .= Template::renderSelect( $field);

                } else if( $field['type'] == 'field_group') {
                    $output .= Template::renderFieldGroup( $field, $field['title']);

                } else if( $field['type'] == 'toggle') {
                    $output .= Template::renderToggle( $field);

                } else if( $field['type'] == 'button_group') {
                    $output .= Template::renderButtonGroup( $field);

                } else if( $field['type'] == 'paragraph') {
                    $output .= Template::renderParagraph( $field);
                }

                $field_counter++;

                // Close column
                if( $cols > 1 && ( $field_counter == $total_fields || $field_counter == $max_col_fields)) {
                    $output .= '</div>'; // .col-md-?
                }

                // Reset field counter
                if( $field_counter == $max_col_fields) $field_counter = 0;
            }

            // Close form
            if( $cols > 1) {
                $output .= '</div>'; // .row
                $output .= "<div class=\"col-md-12\">$buttons</div>";
            }

            if( $form) $output .= '</form>';
            return $output;
        }
    }
