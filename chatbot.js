jQuery(document).ready(function($) {
    $('#chatbot-send').click(function() {
        var message = $('#chatbot-input').val();
        if (message.trim() === '') return;
        
        $('#chatbot-messages').append('<div class="user-message">' + message + '</div>');
        $('#chatbot-input').val('');

        $.post(chatbotAjax.ajax_url, {
            action: 'chatbot_response',
            message: message
        }, function(response) {
            $('#chatbot-messages').append('<div class="bot-message">' + response.response + '</div>');
        });
    });
});
