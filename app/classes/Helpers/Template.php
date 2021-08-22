<?php
    namespace Helpers;

    use \Controllers\QuestionSettingsController;
    use \Controllers\QuestionsController;
    use \Controllers\AnswersController;

    class Template {
        public static function Header() {
            include $_SERVER['DOCUMENT_ROOT'] .'/app/views/template/header.php';
        }

        public static function Footer() {
            include $_SERVER['DOCUMENT_ROOT'] .'/app/views/template/footer.php';
        }

        public static function Sidebar() {
            include $_SERVER['DOCUMENT_ROOT'] .'/app/views/template/sidebar.php';
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

        public static function addPagination( $total_pages, $current_page, $url) {
            $pagination_html = '';

            if( $total_pages) {
                $pagination_html .= '<ul class="pagination">';

                for( $i = 0; $i < $total_pages; $i++) {
                    // URL
                    $new_page_url = preg_replace('/&page(.*)/', '&page='. ( $i +1), $url);
                    if( ! strpos( $new_page_url, '&page=') !== false) $new_page_url .= '&page='. ( $i +1);

                    // Class
                    $current_class = ( $current_page == ( $i +1)) ? 'current' : '';
                    $pagination_html .= '<li><a class="btn m-2 '. $current_class .'" href="'. $new_page_url .'">'. ( $i +1) .'</a></li>';
                }

                $pagination_html .= '</ul>';
            }

            echo $pagination_html;
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

        public static function renderShortcodes( $shortcodes) {
            if( ! $shortcodes || empty( $shortcodes)) return '';
            $output = '<div class="col-md-12">';
                $output .= '<div class="shortcodes">';

                foreach( $shortcodes as $shortcode) {
                    $output .= '<a href="#" class="add-shortcode" data-toggle="tooltip" title="'. _('Copy shortcode') .'">'. $shortcode .'</a>';
                }

                $output .= '</div>';
            $output .= '</div>';
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

        public static function renderQuillEditor( $data) {
            if( ! isset( $data['value'])) $data['value'] = '';

            $output = '<div class="form-group quill-editor">';
                $output .= '<div '. self::renderAttributes( $data, ['value']) .'>'. html_entity_decode( $data['value']) .'</div>';
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
            $settings_count = '';
            if( in_array('settings', $data['buttons'])) $settings_count = self::countFieldGroupSettings( $data['attributes']);
            $output .= "<div class=\"card-header\"><h3>$title &nbsp; <small class=\"text-danger\">$settings_count</small></h3>$buttons_html</div>";

            // Render fields
            $output .= '<div class="card-body" style="display: none;">';
                $output .= self::fieldGroupRenderFeieldsAndAnswers( $data['fields'], $cols);

                // Add settings sections when "settings" button exists
                if( in_array('settings', $data['buttons'])) {
                    $output .= self::fieldGroupRenderSettings( $data['attributes']);
                }

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

        public static function showSuccess( $status) {
            if( isset( $status['success'])) $status = $status['success'];
            $status = intval( $status);

            if( $status == 1) {
                echo '<div class="alert alert-success" role="alert">'. _('The information was saved successfully') .'</div>';
            } else if( isset( $_GET['success'])) {
                echo '<div class="alert alert-danger" role="alert">'. _('An error occured. Please try again') .'</div>';
            }
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

        private static function renderAnswer( $data) {
            $output = '<div class="form-group answer col-md-3">';
                $output .= self::renderFieldGroupButtons( ['delete']);
                $output .= '<input class="form-control bg-yellow answer" ';
                    $output .= self::renderAttributes( $data);
                $output .= '>';
            $output .= '</div>';

            return $output;
        }

        private static function fieldGroupRenderFeieldsAndAnswers( $field_data, $cols) {
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

            // Get answers for "select" type fields
            if( isset( $field['options']) && is_array( $field['options'])) {
                foreach( $field['options'] as $option) {
                    if( isset( $option['value']) && $option['value'] == 'select' && isset( $option['selected']) && $option['selected'] == 1) {
                        if( isset( $data['attributes']['data-question-id'])) {
                            $question_id = intval( $data['attributes']['data-question-id']);

                            // Continue only if question exists
                            if( $question_id > 0 ) {
                                $answers = AnswersController::getRows( ['question_id' => $question_id]);

                                if( $answers) {
                                    foreach( $answers as $answer) {
                                        $output .= self::renderAnswer( array(
                                            'data-question-id' => $answer['question_id'],
                                            'data-answer-id' => $answer['id'],
                                            'data-delete' => '0',
                                            'value' => $answer['answer']
                                        ));
                                    }

                                    $output .= self::renderButton( ['type' => 'button', 'id' => 'add_answer', 'class' => 'form-control', 'value' => 'הוספת תשובה']);
                                }
                            }
                        }
                    }
                }
            }

            return $output;
        }

        private static function fieldGroupRenderSettings( $data) {
            // Buttons
            $output = '<a class="q_settings" href="#"><i class="fa fa-cog text-purple" aria-hidden="true"></i> הגדרות <small>תנאים להצגת שאלה זו</small></a>';

            // Question settings
            $question_settings = QuestionSettingsController::getRows( ['parent_question_id' => $data['data-question-id']]);
            $output .= '<div class="question-settings" style="display: none;">';
                $output .= '<ul>';
                    foreach( $question_settings as $q_setting) {
                        $id = $q_setting['id'];
                        $question = QuestionsController::getRowById( $q_setting['question_id'])['question'];
                        $answer = AnswersController::getRowById( $q_setting['answer_id'])['answer'];

                        if( $question && $answer) {
                            $output .= "<li data-question-setting-id=\"$id\"><i class=\"fa fa-trash-o text-danger\" aria-hidden=\"true\" data-toggle=\"tooltip\" title=\"מחיקת תנאי\"></i><span title=\"$question\">$question</span><span class=\"badge bg-primary text-white\">$answer</span></li>";
                        }
                    }
                $output .= '</ul>';
                $output .= '<a class="btn add-setting" href="#">הוספת התניה</a>';
            $output .= '</div>';

            return $output;
        }

        private static function countFieldGroupSettings( $data) {
            $question_settings = count( QuestionSettingsController::getRows( ['parent_question_id' => $data['data-question-id']]));
            return ( $question_settings > 0) ? "($question_settings הגדרות)" : '';
        }
    }
