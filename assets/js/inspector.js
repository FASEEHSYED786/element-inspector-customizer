jQuery(document).ready(function($) {
    var isInspecting = false;

    function toggleInspection() {
        if (!isInspecting) {
            $('#eic-inspect-button').text('Stop Inspecting');
            $('body').on('mouseover.eic', '*', function(e) {
                e.stopPropagation();
                $(this).addClass('eic-highlight');
            }).on('mouseout.eic', '*', function(e) {
                e.stopPropagation();
                $(this).removeClass('eic-highlight');
            }).on('click.eic', '*', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var element = $(this);
                var action = prompt('Choose action: hide, color, bg-color, custom-css:', '');
                if (action === 'hide') {
                    element.addClass('eic-hidden');
                    var customCSS = $('#eic_custom_css').val();
                    customCSS += '\n' + element.prop('tagName').toLowerCase() + '.' + element.attr('class').split(' ').join('.') + ' { display: none !important; }';
                    $('#eic_custom_css').val(customCSS);
                } else if (action === 'color') {
                    var color = prompt('Enter text color:', '');
                    if (color) {
                        element.css('color', color).addClass('eic-custom-style');
                        var customCSS = $('#eic_custom_css').val();
                        customCSS += '\n' + element.prop('tagName').toLowerCase() + '.' + element.attr('class').split(' ').join('.') + ' { color: ' + color + ' !important; }';
                        $('#eic_custom_css').val(customCSS);
                    }
                } else if (action === 'bg-color') {
                    var bgColor = prompt('Enter background color:', '');
                    if (bgColor) {
                        element.css('background-color', bgColor).addClass('eic-custom-style');
                        var customCSS = $('#eic_custom_css').val();
                        customCSS += '\n' + element.prop('tagName').toLowerCase() + '.' + element.attr('class').split(' ').join('.') + ' { background-color: ' + bgColor + ' !important; }';
                        $('#eic_custom_css').val(customCSS);
                    }
                } else if (action === 'custom-css') {
                    var css = prompt('Enter custom CSS for this element:', '');
                    if (css) {
                        element.attr('style', css).addClass('eic-custom-style');
                        var customCSS = $('#eic_custom_css').val();
                        customCSS += '\n' + element.prop('tagName').toLowerCase() + '.' + element.attr('class').split(' ').join('.') + ' { ' + css + ' }';
                        $('#eic_custom_css').val(customCSS);
                    }
                }
                return false;
            });
        } else {
            $('#eic-inspect-button').text('Inspect Elements');
            $('body').off('mouseover.eic mouseout.eic click.eic');
        }
        isInspecting = !isInspecting;
    }

    $('#eic-inspect-button').click(toggleInspection);
});
