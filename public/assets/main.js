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

    const createDivisionTable = (divisionName, scoreTable, teamsDictionary) => {
        const table = $(
            `<div class="box">
                <table>
                    <thead>
                    <tr><th class="name">${divisionName}</th></tr>
                    <tr class="teams-title"><th>Teams</th></tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>`
        );

        const scoreHeaders = table.find('thead .teams-title');
        Object.keys(scoreTable).forEach((hostId, index) => {
            scoreHeaders.append(`<td>${index + 1}</td>`)
        });

        const scoreBody = table.find('tbody')
        Object.keys(scoreTable).forEach((hostId, rowIndex) => {
            const row = $(`<tr><td class="team-title">${rowIndex + 1}. ${teamsDictionary[hostId]}</td></tr>`)
            const hostScores = scoreTable[hostId];
            Object.keys(scoreTable).forEach((guestId, calIndex) => {
                let cell = $('<td/>');
                if (guestId in hostScores) {
                    const score = hostScores[guestId];
                    if (score) {
                        cell.append(`${score[0]}:${score[1]}`);
                    }
                } else {
                    cell.addClass('empty-cell');
                }
                row.append(cell)
            });
            scoreBody.append(row);
        });

        console.log('--- > ', divisionName);

        return table;
    };

    const setCurrent = (data) => {
        current = data;
        const { championship, teams } = data;

        if (championship === null) {
            $('.iterate-btn').hide();
            $('.empty-board, .create-btn').show();
        } else {
            $('.iterate-btn').show();
            $('.empty-board, .create-btn').hide();

            $('.board').removeClass('hidden');

            if (['playoff', 'finished'].indexOf(championship.status) >= 0) {
                $('.play-off').removeClass('hidden');
            }
        }

        $('.control').removeClass('hidden');

        const teamsDictionary = {};
        teams.forEach(({ id, name }) => teamsDictionary[id] = name);

        const divisionsBox = $('.score-tables').html('');
        Object.keys(championship.divisions).forEach((divisionName) => {
            divisionsBox.append(
                createDivisionTable(divisionName, championship.divisions[divisionName], teamsDictionary)
            );
        })
    }

    $('.create-btn').click(({ target }) => {
        if (current.championship === null || window.confirm('Are you sure?')) {
            const bidirectional = Boolean($(target).data('bidirectional'));
            $.post(CONTROL_API_ENTRYPOINT, { bidirectional }).then(() => {
                location.reload();
            });
        }
    });

    loadBoard();
});
