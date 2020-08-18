$(document).ready(function() {
    $('.screening').click(function() {
        var id = $(this).data('id');
        var site = $(this).data('site');
        var url = '/' + site + '/ajax/screening/' + id;

        $.ajax({
            url: url,
            type: 'get',
            success: function(response) {
                $('#modal-content').html(response);
                $('#details-modal').modal('show');
            },
            error: function(response) {
                alert('Error: ' + response.statusText);
            }
        });
    });

});


