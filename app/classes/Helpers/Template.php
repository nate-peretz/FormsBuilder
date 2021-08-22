<?php
    namespace Helpers;

    class Template {
        public static function Header() {
            include $_SERVER['DOCUMENT_ROOT'] .'/app/views/template/header.php';
        }

        public static function Footer() {
            include $_SERVER['DOCUMENT_ROOT'] .'/app/views/template/footer.php';
        }

        public static function getComponent( $name, $data = []) {
            $template = file_get_contents( $_SERVER['DOCUMENT_ROOT'] ."/app/views/template/components/$name.html");

            // Create options for select
            $options = '';
            if( isset( $data['options'])) {
                foreach( $data['options'] as $i => $v) {
                    $value    = $data['options'][ $i]['value'];
                    $name     = $data['options'][ $i]['label'];
                    $selected = ( $value == $data['[value]']) ? 'selected' : '';
                    $options .= '<option value="'. $value .'" '. $selected .'>'. $name .'</option>';
                }
            }
            $template = str_replace('</select>', $options .'</select>', $template);
            unset( $data['options']);

            // Shortcodes
            foreach( $data as $k => $v) $template = str_replace( $k, $v, $template);
            return $template;
        }

        public static function getTemplateHTML( $name) {
            return file_get_contents( $_SERVER['DOCUMENT_ROOT'] ."/app/views/template/components/$name.html");
        }

        /**
         * Component rendering functions
         */
        public static function renderInput( $data) {
            $output = '<div class="form-group">';
                $output .= '<input ';
                    $output .= self::renderAttributes( $data);
                $output .= '>';
            $output .= '</div>';

            return $output;
        }

        public static function renderLabel( $data) {
            $output = '<strong>';
                $output .= $data['value'];
            $output .= '</strong>';

            return $output;
        }

        public static function renderParagraph( $data) {
            $output = '<p>';
                $output .= $data['value'];
            $output .= '</p>';

            return $output;
        }

        public static function renderTextarea( $data) {
            if( ! isset( $data['value'])) $data['value'] = '';

            $output = '<div class="form-group">';
                $output .= '<textarea ';
                    $output .= self::renderAttributes( $data, ['value']);
                $output .= '>'. $data['value'] .'</textarea>';
            $output .= '</div>';

            return $output;
        }

        public static function renderButton( $data) {
            if( ! isset( $data['value'])) $data['value'] = '';
            if( $data['type'] == 'button') unset( $data['type']);

            $output = '<div class="save-btn">';
                $output .= '<button ';
                    foreach( $data as $k => $v) {
                        $output .= self::renderAttributes( $data, ['value']);
                    }
                $output .= '>'. $data['value'] .'</button>';
            $output .= '</div>';

            return $output;
        }

        public static function renderSelect( $data) {
            $output = '<div class="form-group">';
                $output .= '<select ';
                $output .= self::renderAttributes( $data, ['options', 'type']);
                $output .= '>';

                if( isset( $data['options']) && ! empty( $data['options'])) {
                    foreach( $data['options'] as $option) {
                        if( ! isset( $option['label'])) continue;
                        if( ! isset( $option['value'])) $option['value'] = '';

                        $disabled = '';
                        if( isset( $option['disabled']) && $option['disabled'] == 1) $disabled = 'disabled';

                        $selected = ( isset( $option['selected']) && $option['selected'] == 1) ? 'selected' : '';
                        $output .= '<option '. $selected .' value="'. $option['value'] .'" '. $disabled .'>'. $option['label'] .'</option>';
                    }
                }
            $output .= '</select></div>';

            return $output;
        }

        public static function renderFieldGroup( $data, $title = '') {
            // Must contain fields
            if( ! isset( $data['fields'])) throw new \Exception('field group missing fields');
            if( ! isset( $data['title']))  unset( $data['title']);

            $repeater = false;

            // Check if this is a repeater
            $search_repeater_in_options = ( isset( $data['fields'][1]['options'])) ? $data['fields'][1]['options'] : false;
            if( $search_repeater_in_options) {
                foreach( $search_repeater_in_options as $field) {
                    if( isset( $field['value']) && $field['value'] == 'repeater' && isset( $field['selected'])) {
                        $repeater = true;
                    }
                }
            }
            if( $repeater === true) $title = '<i title="כל השדות תחת חוצץ זה ניתנים לשכפול על-ידי המשתמש" class="fa fa-repeat" aria-hidden="true"></i>' . $title;

            // Check if this is an evidence uploader
            if( isset( $data['attributes']['data-upload']) && $data['attributes']['data-upload'] == 1) {
                $title = '<i title="העלאת קבצי ראיות" class="fa fa-cloud-upload" aria-hidden="true"></i>' . $title;
            }

            // Aggregate attributes
            $attributes = self::renderAttributes( $data['attributes']);

            // Create buttons
            $buttons_html = self::renderFieldGroupButtons( $data['buttons']);

            // Open
            $output = ( ! empty( $data['fields'])) ? '<div class="row field-group" '. $attributes .'><div class="col-md-12"><div class="card">' : '';
            $cols = count( $data['fields']);
            if( $cols > 6) throw new \Exception('field_group supports only 6 fields');

            // Title
            $output .= "<div class=\"card-header\"><h3>$title</h3>$buttons_html</div>";

            // Render fields
            $output .= '<div class="card-body" style="display: none;">';
                $output .= self::fieldGroupRenderFeields( $data['fields'], $cols);
            $output .= '</div>';

            // Close
            $output .= ( ! empty( $data)) ? '</div></div></div>' : '';
            return $output;
        }

        public static function renderToggle( $data) {
            $checked = ( isset( $data['checked']) && $data['checked'] == true) ? 'checked' : '';
            $label = $data['label'];
            $id = $data['id'];

            $output = '<div class="form-group toggle">';
                $output .= "<label>$label</label>";
                $output .= "<input type=\"checkbox\" $checked ". ( ( $id) ? "id=$id" : "") .">";
            $output .= '</div>';

            return $output;
        }

        public static function renderButtonGroup( $data) {
            $cols = count( $data['buttons']);

            $output = '<div class="row add-cond">';
                foreach( $data['buttons'] as $button) {
                    $col_class = 'col-md-' . ( 12 / $cols);
                    $output .= '<div class="'. $col_class .'">';
                        $output .= self::renderButton( $button);
                    $output .= '</div>';
                }
            $output .= '</div>';

            return $output;
        }

        public static function renderFieldGroupButtons( $keys) {
            $output = '<div class="field-group-buttons"><ul>';

            foreach( $keys as $key) {
                if( $key == 'delete') {
                    $output .= '<li class="delete-field-group"><i class="fa fa-trash-o text-danger" aria-hidden="true"></i></li>';

                } else if( $key == 'edit') {
                    $output .= '<li class="edit-field-group"><i class="fa fa-pencil text-purple" aria-hidden="true"></i></li>';

                } else if( $key == 'edit-conditions') {-
                    $output .= '<li class="edit-field-group wide"><a href="#" class="edit-cond-link" class="text-purple">ניהול תנאים</a></li>';

                } else if( $key == 'settings') {
                    $output .= '<li class="edit-field-group"><i class="fa fa-cog text-purple" aria-hidden="true"></i></li>';

                } else if( $key == 'duplicate') {
                    $output .= '<li class="duplicate-field-group"><i class="fa fa-files-o text-success" aria-hidden="true"></i></li>';

                } else if( $key == 'info') {
                    $output .= '<li class="info-field-group"><i class="fa fa-info-circle text-primary" aria-hidden="true"></i></li>';
                }
            }

            $output .= '</ul></div>';
            return $output;
        }

        /**
         * Private functions
         */
        private static function renderAttributes( $attributes, $exclude = []) {
            $output = '';

            foreach( $attributes as $k => $v) {
                if( in_array( $k, $exclude)) continue;
                $output .= "$k=\"$v\" ";
            }

            return $output;
        }

        private static function fieldGroupRenderFeields( $field_data, $cols) {
            $output = '<div class="row">';
                foreach( $field_data as $field) {
                    $col_class = 'col-md-' . ( 12 / $cols);

                    $output .= '<div class="'. $col_class .'">';

                        if( in_array( $field['type'], ['text', 'hidden', 'number', 'email'])) {
                            $output .= self::renderInput( $field);
        
                        } else if( $field['type'] == 'textarea') {
                            $output .= self::renderTextarea( $field);

                        } else if( $field['type'] == 'label') {
                            $output .= self::renderLabel( $field);
        
                        } else if( in_array( $field['type'], ['button', 'submit'])) {
                            $output .= self::renderButton( $field);
        
                        } else if( $field['type'] == 'select') {
                            $output .= self::renderSelect( $field);

                        } else if( $field['type'] == 'paragraph') {
                            $output .= self::renderParagraph( $field);
                        }

                    $output .= '</div>';
                }
            $output .= '</div>';

            return $output;
        }
    }
