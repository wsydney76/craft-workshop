function callWatchlist(url) {
    $.ajax({url: url, dataType: 'json',
        success: function(response) {
            if (response.success) {
                toggleWatchlistButtons();
            }
            showMessage(response.message);

        },
        error: function(response) {
            alert('Error: ' + response.statusText);
        }
    });
}

function toggleWatchlistButtons() {
    if ($('#btn-add').css('display') == 'none') {
        $('#btn-delete').hide();
        $('#btn-add').show();
    } else {
        $('#btn-add').hide();
        $('#btn-delete').show();
    }
}

function showMessage(message) {
    $('#toast-body').html(message);
    $('#toast').toast('show');
}

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
