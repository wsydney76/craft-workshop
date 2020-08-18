$('#sidebar .heading').click(
    function() {
        toggleVisibilityOfNextElement($(this));
    }
);

function toggleVisibilityOfNextElement(previousElement) {

    element = previousElement.next();

    if (element.length == 0) {
        return;
    }

    if (element.hasClass('heading')) {
        return;
    }

    if (element.css('display') == 'none') {
        element.css('display', '')
        previousElement.removeClass('collapsed')
    } else {
        element.css('display', 'none')
        previousElement.addClass('collapsed')
    }

    toggleVisibilityOfNextElement(element);
}
