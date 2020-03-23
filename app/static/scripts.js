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

// connect();

// UI -------------------------------
function inputDigit (elem) {
    if ((elem.value > '0' && elem.value <= '9') && elem.value.length === 1) {
        if (sendDigit(elem.value)) {
            elem.style.display = 'none';
            elem.parentElement.lastElementChild.textContent = elem.value;
            elem.parentElement.lastElementChild.style.display = 'inline';

            var className = 'player' + window.sudoku.player;
            elem.parentElement.classList.add(className);
        }
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

function makeRQ(number) {
    var regexp = /(\d)_(\d)/;
    var res = regexp.exec('zzz_1_2');
    return {
        "x": res[1],
        "y": res[2],
        "player": window.sudoku.player,
        "id": window.sudoku.id,
        "value": number
    };
}

function fillField(field) {
    for (var i = 0; i < field.length; i++) {
        setEnemyDigit(field[i]);
    }
}

function fillGameInfo(id, player, host, port) {
    window.sudoku = {};
    window.sudoku.id = id;
    window.sudoku.player = player;
    window.sudoku.host = host;
    window.sudoku.port = port;
}

// NET -------------------------
function connect() {
    var nameEl = document.getElementById('playerName');

    var rq = new XMLHttpRequest();
    rq.open('POST', 'http://sudoku.local/connect', false);

    rq.send(JSON.stringify({"name": nameEl.value}));

    if (rq.status !== 200) {
        alert('Не удалось начать игру. Обновите страницу, чтобы попробовать ещё раз');

        return;
    }

    var rs = JSON.parse(rq.responseText);

    fillField(rs.field);
    fillGameInfo(rs.id, rs.player, rs.host, rs.port);
    connectToGame();
}

function connectToGame() {
    if ('ws' in window.sudoku) {
        console.log('Закрываем старое соединение');

        window.sudoku.ws.close();

        console.log('Старое соединение закрыто');
    }

    // var url = 'ws://' + window.sudoku.host + ':' + window.sudoku.port;
    var url = 'ws://sudoku.local:8000';
    window.sudoku.ws = new WebSocket(url);
    window.sudoku.ws.onopen = function() {
        console.log("Соединение установлено.");
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
    console.log(data);
}

function sendDigit(val) {
    var rq = makeRQ(val);

    console.log(rq);

    return true;
}

// Other --------------------------
function showTop() {
    // todo открыть в новой вкладке топ игроков
}