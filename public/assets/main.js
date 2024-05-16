$(() => {
    const BOARD_API_ENDPOINT = '/api/board/current';
    const ADMIN_API_CREATE = '/api/championship';
    const ADMIN_API_UPDATE = '/api/championship/current';
    let current = {};

    $.ajaxSetup({
        dataType: 'json',
        error: (x, status, error) => {
            alert(
                `An error occurred: ${status}\nError: [${x.status}] ${error}\n\nReload the page and try again!`
            );
        },
    });

    const visibilityMap = {
        '.board': (status) => status !== undefined,
        '.control': true,
        '.empty-board': [undefined],
        '.create-btn': ['error', 'finished'],
        '.iterate-btn': (status) => ['error', 'finished'].indexOf(status) === -1,
        '.play-off': ['qualifying', 'playoff_quarter', 'playoff_semifinal', 'playoff_final', 'playoff_3d_place', 'finished'],
    };

    const toggleVisibility = (championship) => {
        const toHide = [];
        const toShow = [];

        Object.keys(visibilityMap).forEach((selector) => {
            const rule = visibilityMap[selector];
            let show;
            if (typeof rule === 'function') {
                show = rule(championship?.status);
            } else if (Array.isArray(rule)) {
                show = rule.indexOf(championship.status) >= 0;
            } else if (typeof rule == "boolean") {
                show = rule;
            } else {
                show = rule === championship.status;
            }
            (show ? toShow : toHide).push(selector)
        })

        if (toHide.length) {
            $(toHide.join(',')).addClass('hidden');
        }
        if (toShow.length) {
            $(toShow.join(',')).removeClass('hidden');
        }
    }

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

    const createDivisionTable = (divisionName, scoreTable) => {
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
            const row = $(`<tr><td class="team-title">${rowIndex + 1}. ${hostRow.teamName}</td></tr>`)
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
        const { championship } = data;
        const { divisions, playoff } = championship;

        toggleVisibility(championship);

        const divisionsBox = $('.score-tables').html('');
        Object.keys(divisions).forEach((divisionName) => {
            divisionsBox.append(
                createDivisionTable(divisionName, divisions[divisionName])
            );
        })

        Object.keys(playoff).forEach((stage) => {
            playoff[stage].forEach((game, gameIndex) => {
                const { teams, score } = game;
                teams.forEach((teamName, teamIndex) => {
                    $(`#${stage}-name-${gameIndex}${teamIndex + 1}`).html(teamName);
                });
                if (score) {
                    $(`#${stage}-score-${gameIndex}`).html(`${score[0]}:${score[1]}`);
                }
            });
        });
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
