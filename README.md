# Nate's simple Form Builder (PHP / JS)
This is a simple class that I extracted from a project I worked on in the past. It can be used for simplifying form building & saving with PHP. The original code includes many types of components but I have simplified it for everyone who needs this as a base class.

# Class supports
Form, Input, Select, Textarea, Label, Paragraph, On/Off Toggle, Field group, Button group

# Rendering
On this example, rendering is being done by PHP but you could easily get this done by using any frontend language. You can easily generate the required fields (with or without a form) on a custom endpoint.

The original code includes dynamic fields, form validation, data/url encryption and much more but I didn't want to confuse anyone, so here is the base class's usage:
```php

// Form
$form = new Component();
$form->addForm('save-form', '/app/ajax/save_form.php', 'col-md-12', $redirect = '/index.php')
    // Components
    ->addLabel( _('Title'))
    ->addInput( ['type' => 'text', 'id' => 'title', 'required' => true, 'class' => 'form-control title', 'placeholder' => _('Title..'), 'value' => 'Form Title')

    ->addLabel( _('Category'))
    ->addSelect( ['class' => 'form-control category', 'id' => 'category', 'selected_val' => 'test_1'], array(
        ['value' => 'test_1', 'label' => 'Test 1'],
        ['value' => 'test_2', 'label' => 'Test 2']
    ))

    ->addLabel( _('Active'))
    ->addToggle( _('Status'), $checked = true, 'active')

    // Submmit
    ->addButton( ['type' => 'submit', 'id' => 'save', 'class' => 'form-control', 'value' => _('Save')]);

// Render
echo $form->renderForm();
```

Unifying forms can make everything more simple. For example, having a single "save" method for all forms:
```javascript
class Forms {
    form_url = '';
    redirect = '';

    constructor( form_url = '', redirect = '') {
        this.form_url = form_url;
        this.redirect = redirect
    }

    // Requires "constructor"
    saveFormData( data) {
        if( this.form_url !== '' && this.redirect !== '') {
            if( Object.keys( data).length) {
                // "this.redirect" is not available inside the ajax function scope
                var redirect = this.redirect;

                $.ajax({
                    url: this.form_url,
                    type: 'post',
                    data: { data: data},
                    success: function( res) {
                        var operand = ( redirect.indexOf('?') !== -1) ? '&' : '?';
                        if( res == null || res == '') res = '1';
                        window.location.href = redirect + operand + 'success=' + res;
                    }
                });
            }
        } else {
            alert('Missing form url AND/OR redirect url');
        }
    }
}

```
