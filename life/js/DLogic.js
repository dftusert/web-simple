// draw net
function drawNet (){
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    for (var i = 0; i <= xCount; ++i){
        ctx.beginPath ();
        ctx.moveTo (i * sqrX, 0);
        ctx.lineTo (i * sqrX, yCount * sqrY);
        ctx.stroke ();
        ctx.closePath ();
    }
    for (var i = 0; i <= yCount; ++i){
        ctx.beginPath ();
        ctx.moveTo (0, i * sqrY);
        ctx.lineTo (xCount * sqrX, i * sqrY);
        ctx.stroke ();
        ctx.closePath ();
    }
}
// draw cells on net
function drawCells (){
    for (var i = 0; i < yCount; ++i){
        for (var j = 0; j < xCount; ++j)
            drawCell (j, i);
    }
}
// draw one cell if cell not spaceCell
function drawCell (coordX, coordY){
    if (field [coordY][coordX] == 0){
        ctx.beginPath ();
        ctx.fillRect (coordX * sqrX, coordY * sqrY, sqrX, sqrY);
        ctx.fill ();
        ctx.closePath ();
    }
}
// one step of game, step ()
function step () {
    var neigbours;
    // says that no one cell has been modified (died/born)
    var gameOverTrigger1 = 0;
    // says that all cell are died
    var gameOverTrigger2 = 0;

    for (var y = 0; y < yCount; ++y){
        for (var x = 0; x < xCount; ++x){
            neigbours = getNeigbourCount (x, y);
            if (neigbours > 3 || neigbours < 2) evofield [y][x] = spaceCellId;
            else if (neigbours == 3) evofield [y][x] = simpleCellId;
            else evofield [y][x] = field [y][x];

            if (evofield [y][x] == field [y][x]) ++gameOverTrigger1;
            if (evofield [y][x] == 0) ++gameOverTrigger2;
        }
    }
    if (gameOverTrigger1 == xCount * yCount || gameOverTrigger2 == xCount * yCount){
        gameOver = 1;
        if (confirm ("Game over, start new?")){
            init ();
            drawNet ();
        }
    }
   // deploy new evo on field
   else
       for (var i = 0; i < yCount; ++i)
           for (var j = 0; j < xCount; ++j)
               field [i][j] = evofield [i][j];
   drawNet ();
   drawCells ();
}

// neigbours count around cell
function getNeigbourCount (x, y){
    var neigbours = 0;
    var coords;

    coords = transformCoords (x - 1, y - 1);
    if (field [coords [1]][coords[0]] != -1) ++neigbours;

    coords = transformCoords (x, y - 1);
    if (field [coords [1]][coords[0]] != spaceCellId) ++neigbours;

    coords = transformCoords (x - 1, y);
    if (field [coords [1]][coords[0]] != spaceCellId) ++neigbours;

    coords = transformCoords (x + 1, y + 1);
    if (field [coords [1]][coords[0]] != spaceCellId) ++neigbours;

    coords = transformCoords (x, y + 1);
    if (field [coords [1]][coords[0]] != spaceCellId) ++neigbours;

    coords = transformCoords (x + 1, y);
    if (field [coords [1]][coords[0]] != spaceCellId) ++neigbours;
    
    coords = transformCoords (x + 1, y - 1);
    if (field [coords [1]][coords[0]] != spaceCellId) ++neigbours;

    coords = transformCoords (x - 1, y + 1);
    if (field [coords [1]][coords[0]] != spaceCellId) ++neigbours;

    return neigbours;
}

// transform coords
function transformCoords (x, y){
    if (x < 0) x = xCount - 1;
    if (y < 0) y = yCount - 1;
    if (x == xCount) x = 0;
    if (y == yCount) y = 0;
    return [x, y];
}

function go (){
    if (gameOver == 0 && stopFlag == 0){
        step ();
        setTimeout(go, delay);
    }
}
