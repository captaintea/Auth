var App = {};

App.setSubmitListener = function (formSelector) {
    var self = this;
    $(formSelector).on('submit', function (event) {
        event.preventDefault();
        $.post($(this).attr('action'), $(this).serialize() + '&format=json', function (data) {
                data = JSON.parse(data);
                if (typeof data.success !== 'undefined') {
                    if (data.success == true) {
                        window.location = '/';
                    } else {
                        if (typeof data.error === 'string') {
                            self.displayError(data.error);
                        } else {
                            self.displayError(['Server error'])
                        }
                    }
                }
            }, 'json'
        ).error(function() {
            self.displayError(['Server error'])
        }).always(function() {
            App.enableControls();
        });
        App.disableControls();
    });
};

App.disableControls = function() {
    $('input, button, select').attr('disabled','disabled');
};

App.enableControls = function() {
    $('input, button, select').removeAttr('disabled');
};

App.displayError = function (errorText) {
    var $errorArea = $('.js-error');
    $errorArea.find('.js-error-text').html(errorText);
    $errorArea.removeClass('hidden');
    console.log($errorArea);
    setTimeout(function () {
        $errorArea.addClass('hidden');
    }, 3000)
};
