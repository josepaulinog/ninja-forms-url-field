(function($) {
    var URLFieldController = Marionette.Object.extend({
        initialize: function() {
            this.listenTo(Backbone.Radio.channel('fields'), 'change:modelValue', this.validateURL);
            this.listenTo(Backbone.Radio.channel('forms'), 'submit:response', this.highlightErrors);
        },

        validateURL: function(model) {
            if (model.get('type') !== 'url') return;

            var value = model.get('value');
            var fieldID = model.get('id');
            var required = model.get('required');

            if (required && !value) {
                Backbone.Radio.channel('fields').request('add:error', fieldID, 'required-error', nfURLField.requiredErrorMessage);
            } else if (value && !this.isValidURL(value)) {
                Backbone.Radio.channel('fields').request('add:error', fieldID, 'invalid-url', nfURLField.invalidURLMessage);
            } else {
                Backbone.Radio.channel('fields').request('remove:error', fieldID, 'required-error');
                Backbone.Radio.channel('fields').request('remove:error', fieldID, 'invalid-url');
            }
        },

        isValidURL: function(url) {
            var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
                '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
                '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
                '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
            return pattern.test(url);
        },

        highlightErrors: function(response) {
            if (response.errors && response.errors.fields) {
                _.each(response.errors.fields, function(fieldError, fieldID) {
                    Backbone.Radio.channel('fields').request('add:error', fieldID, 'required-error', fieldError);
                });
            }
        }
    });

    $(document).ready(function() {
        new URLFieldController();
    });
})(jQuery);