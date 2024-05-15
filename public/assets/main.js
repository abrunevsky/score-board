$(() => {
    const BOARD_API_ENDPOINT = '/api/board/current';
    const ADMIN_API_CREATE = '/api/championship';
    const ADMIN_API_UPDATE = '/api/championship/current';
    let current = {};

    $.ajaxSetup({ dataType: 'json' });

    const lockControls = (flag) => {
        $('.control button').prop('disabled', flag);
    };

    const loadBoard = () => {
        $('.loading').show();
        return $.getJSON(BOARD_API_ENDPOINT).then((data) => {
            $('.loading').hide();
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
        scoreTable.forEach((row, index) => {
            scoreHeaders.append(`<th>${index + 1}</th>`)
        });
        scoreHeaders.append('<th>Score</th>')

        const scoreBody = table.find('tbody')
        scoreTable.forEach((hostRow, rowIndex) => {
            const teamName = teamsDictionary[hostRow.teamId];
            const row = $(`<tr><td class="team-title">${rowIndex + 1}. ${teamName}</td></tr>`)
            scoreTable.forEach((guestRow) => {
                let cell = $('<td/>');
                if (guestRow.teamId in hostRow.scores) {
                    const score = hostRow.scores[guestRow.teamId];
                    if (score) {
                        cell.append(`${score[0]}:${score[1]}`);
                    }
                } else {
                    cell.addClass('empty-cell');
                }
                row.append(cell)
            });
            row.append(`<td>${hostRow.total}</td>`)

            scoreBody.append(row);
        });

        return table;
    };

    const setCurrent = (data) => {
        current = data;
        const { championship, teams } = data;

        if (championship === null) {
            $('.iterate-btn').hide();
            $('.empty-board, .create-btn').show();
        } else if (championship.status === 'finished') {
            $('.iterate-btn').hide();
            $('.create-btn').show();
        } else {
            $('.iterate-btn').show();
            $('.empty-board, .create-btn').hide();
        }

        if (championship) {
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

    $('.create-btn').on('click', ({ target }) => {
        if (current.championship === null || window.confirm('Are you sure?')) {
            const bidirectional = $(target).data('bidirectional');
            $('.loading').show();
            lockControls(true);
            $.ajax({ url: ADMIN_API_CREATE, method: 'POST', data: { bidirectional }}).then(() => {
                location.reload();
            });
        }
    });

    $('.iterate-btn').on('click', ({ target }) => {
        const finalize = $(target).data('finalize');
        if (!Boolean(finalize) || window.confirm('Are you sure?')) {
            $('.loading').show();
            lockControls(true);
            $.ajax({  url: ADMIN_API_UPDATE, method: 'PUT', data: { finalize }}).then(() => {
                loadBoard().then(() => {
                    lockControls(false);
                });
            });
        }
    });

    loadBoard();
});
