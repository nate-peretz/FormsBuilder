<?php
    require_once( $_SERVER['DOCUMENT_ROOT'] .'/app.php');

    use \Component\Component;
    use \Helpers\Template;

    Template::Header(); ?>

    <section class="main h-100">
        <div class="col-md-12 h-100">
            <div class="row h-100">
                <!-- Content -->
                <div class="col-md-2 spacer"></div>

                <div class="col-md-8 content">
                    <header>
                        <h1><?= $app_name ?></h1>
                        <p><?= $app_desc; ?></p>
                    </header>

                    <article>
                        <?php
                            $redirect = '/index.php';

                            $form = new Component();
                            $form->addForm('save-form', '/app/ajax/save_form.php', 'col-md-12', $redirect)
                                ->addLabel( _('Title'))
                                ->addInput( ['type' => 'text', 'id' => 'title', 'required' => true, 'class' => 'form-control title', 'placeholder' => _('Title..'), 'value' => ( isset( $paragraph['title'])) ? $paragraph['title'] : ''])

                                ->addLabel( _('Category'))
                                ->addSelect( ['class' => 'form-control category', 'id' => 'category', 'selected_val' => 'test_1'], array(
                                    ['value' => 'test_1', 'label' => 'Test 1'],
                                    ['value' => 'test_2', 'label' => 'Test 2']
                                ))

                                ->addLabel( _('Active'))
                                ->addToggle( _('Status'), ( ! isset( $paragraph['active']) || $paragraph['active'] == 1), 'active')
                                
                                ->addButton( ['type' => 'submit', 'id' => 'save', 'class' => 'form-control', 'value' => _('Save')]);

                            echo $form->renderForm();
                        ?>
                    </article>

                    <div class="col-md-2 spacer"></div>
                </div><!-- .content -->
            </div><!-- .row -->
        </div><!-- .col-md-12 -->
    </section><!-- .main -->

    <?php
    Template::Footer();
