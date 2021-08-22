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
