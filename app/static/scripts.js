/*
        var socket = new WebSocket("ws://localhost:8000");
        socket.onopen = function() {
            console.log("Соединение установлено.");
        };

        socket.onclose = function(event) {
            if (event.wasClean) {
                console.log('Соединение закрыто чисто');
            } else {
                console.log('Обрыв соединения'); // например, "убит" процесс сервера
            }
            alert('Код: ' + event.code + ' причина: ' + event.reason);
        };

        socket.onmessage = function(event) {
            console.log("Получены данные " + event.data);
        };

        socket.onerror = function(error) {
            console.log("Ошибка " + error.message);
        };
        var timerId = setInterval(function() {
            socket.send("тик!");
        }, 2000);
         */

// window.onclose(function (event) {
//     if ('sudoku' in window && 'ws' in window.sudoku) {
//         window.sudoku.ws.close();
//     }
// });

window.document.onload = function (event) {
    connect();
};


// UI -------------------------------
function inputDigit (elem) {
    if ((elem.value > '0' && elem.value <= '9') && elem.value.length === 1) {
        sendDigit(elem.id, elem.value);
    } else {
        elem.value = '';
    }
}

function reInputDigit(elem) {
    if (elem.classList.contains('.player0')) {
        return;
    }

    elem.style.display = 'none';
    elem.parentElement.firstElementChild.style.display = 'inline-block';
}

function setEnemyDigit(rq) {
    var cellName = 'cell_' + rq.x + '_' + rq.y;
    var className = 'player' + rq.player;

    var cell = document.getElementById(cellName);
    cell.classList.add(className);
    cell.textContent = rq.value;
}

function makeRQ(x, y, digit) {
    return {
        "action": "setDigitOnField",
        "x": x,
        "y": y,
        "player": window.sudoku.player,
        "gameId": window.sudoku.gameId,
        "value": digit
    };
}

function fillField(field) {
    for (var i = 0; i < field.length; i++) {
        setEnemyDigit(field[i]);
    }
}

function fillGameInfo(gameId, playerId, playerName, host, port) {
    if (!('sudoku' in window)) {
        window.sudoku = {};
    }

    window.sudoku.gameId = gameId;
    window.sudoku.player = playerId;
    window.sudoku.playerName = playerName;
    window.sudoku.host = host;
    window.sudoku.port = port;
}

// NET -------------------------
function connect() {
    console.log('connect');

    var nameEl = document.getElementById('playerName');

    var rq = new XMLHttpRequest();
    rq.open('POST', 'http://sudoku.local/connect', false);

    rq.send();

    if (rq.status !== 200) {
        alert('Не удалось начать игру. Обновите страницу, чтобы попробовать ещё раз');

        return;
    }

    var rs = JSON.parse(rq.responseText);

    fillGameInfo(rs.gameId, rs.player, nameEl.value, rs.host, rs.port);
    connectWS();
}

function connectWS() {
    if ('ws' in window.sudoku) {
        console.log('Закрываем старое соединение');

        window.sudoku.ws.close();

        console.log('Старое соединение закрыто');
    }

    var url = 'ws://' + window.sudoku.host + ':' + window.sudoku.port + '?player=' + window.sudoku.playerName;
    window.sudoku.ws = new WebSocket(url);
    window.sudoku.ws.onopen = function(event) {
        window.sudoku.ws.send(JSON.stringify({
            "action": "connectToGame",
            "gameId": null,
            "name": window.sudoku.playerName}));
    };

    window.sudoku.ws.onclose = function(event) {
        if (event.wasClean) {
            console.log('Соединение закрыто чисто');
        } else {
            console.log('Обрыв соединения');
        }
        console.log(event);
    };

    window.sudoku.ws.onmessage = function(event) {
        processMessage(event.data);
    };

    window.sudoku.ws.onerror = function(error) {
        console.log(error);
    };
}

function processMessage(data) {
    var rs = JSON.parse(data);

    switch (rs.action) {
        case 'connectToGame':
            connectedToGame(rs);
            break;
        case 'setDigitOnField':
            digitAccepted(rs);
            break;
        default:
            alert('ERROR: ' + JSON.stringify(rs))
    }
}

function connectedToGame(rs) {
    fillGameInfo(rs.gameId, rs.player, rs.host, rs.port);
    fillField(rs.field);
}

function sendDigit(id, val) {
    var regexp = /(\d)_(\d)/;
    var res = regexp.exec(id);
    var rq = makeRQ(parseInt(res[1]), parseInt(res[2]), parseInt(val));

    window.sudoku.ws.send(JSON.stringify(rq));

    return true;
}

function digitAccepted(rs) {
    if (!rs.success) {
        console.log(rs);
        return;
    }

    var elem = document.getElementById('in_' + rs.field[0].x + '_' + rs.field[0].y);
    elem.style.display = 'none';
    elem.parentElement.lastElementChild.textContent = rs.field[0].value;
    elem.parentElement.lastElementChild.style.display = 'inline';

    var className = 'player' + rs.field[0].player;
    elem.parentElement.classList.add(className);

    if (rs.gameOver) {
        alert('Игра закончена!');
    }
}

// Other --------------------------
function showTop() {
    // todo открыть в новой вкладке топ игроков
}