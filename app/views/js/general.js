jQuery( function($) {
    $(document).on('click', '#save', function(e) {
        e.preventDefault();
        // if( ! validateForm( '#' + $(this).closest('form').attr('id'))) return false;

        let form_url = $(this).closest('form').attr('action');
        let redirect = $(this).closest('form').attr('data-redirect');

        let data = { fields: []};
        let form = new Forms( form_url, redirect);

        /**
         * Collect static fields
         */
         $(this).closest('form').find('input:not([type="submit"]), textarea, select, div[type="quill-editor"]').each( function() {
            var value = ( $(this).attr('type') == 'checkbox') ? ( $(this).is(':checked') ? 'on' : 'off') : this.value;
            data[ this.id] = value;
        });

        // Save
        form.saveFormData( data);
    });
});
