$(() => {
    const BOARD_API_ENDPOINT = '/api/board/current';
    const CONTROL_API_ENTRYPOINT = '/api/championship';
    let current = {};

    $.ajaxSetup({ dataType: 'json'});

    const loadBoard = () => {
        $('.loading').show();
        $.getJSON(BOARD_API_ENDPOINT).then((data) => {
            $('.loading').hide();
            console.log('request to', BOARD_API_ENDPOINT, 'returned', data);
            setCurrent(data);
        });
    };

    const setCurrent = (data) => {
        current = data;

        if (data.championship === null) {
            $('.iterate-btn').hide();
            $('.empty-board, .create-btn').show();
            $('.control').removeClass('hidden');
        }


    }

    $('.create-btn').click(({ target }) => {
        if (current.championship === null || window.confirm('Are you sure?')) {
            const bidirectional = Boolean($(target).data('bidirectional'));
            $.post(CONTROL_API_ENTRYPOINT, { bidirectional }).then(() => {
                loadBoard();
            });
        }
    });

    loadBoard();
});
