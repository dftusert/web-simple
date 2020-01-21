// when window loads
window.onload = function (){
    baseInit ();
    simpleInit ();
    // decorateNet before drawNet because we
    // will not see net if we call drawNet before decorateNet
    decorateNet (); // FUNCTION FROM drawFunctions.js
    drawNet (); // FUNCTION FROM drawFunctions.js
}

// init for each window loads
function baseInit (){
    canvas = document.getElementById ("field");
    ctx = canvas.getContext ("2d");
}

// for each game starts/window loads
function simpleInit (){
    if (cBoxX == null)
        cBoxX = CBOXX_DEFAULT;
    if (cBoxY == null)
        cBoxY = CBOXY_DEFAULT;
    if (bombs == null)
        bombs = BOMBS_DEFAULT;

    // checking correct set...
    if (cBoxX * cBoxY < bombs)
        bombs = cBoxX * cBoxY;   

    // set opened boxes 0
    openedBoxes = 0;

    // set marked boxes 0
    markedBoxes = 0;

    // set next step open
    markFlag = 0;

    // that's bad, but...
    canvas.width = cBoxX * PXX;
    canvas.height = cBoxY * PXY;

    initFields ();

    canvas.onclick = canvasOnclick; // FUNCTION FROM eventTriggers.js // sh|...

    // in some situations it can show wrong flag state, so fix it by
    document.getElementById ("markButton").className = "notMarked";
    // and this
    fillGameDataIntoSpans (); // FUNCTION FROM eventTriggers.js

    // add bombs info
    document.getElementById ("bombsText").innerText = "bombs: " + bombs;
}

// init fields, then fill
function initFields (){
    field = new Array (cBoxY);
    infofield = new Array (cBoxY);
 
    for (var i = 0; i < cBoxY; ++i){
        field [i] = new Array (cBoxX);
        infofield [i] = new Array (cBoxX);

        for (var j = 0; j < cBoxX; ++j){
            field [i][j] = 0;
            infofield [i][j] = CLOSED_VALUE;
        }
    }
    setBombs (); //  set bombs to field array
    refillArrayField ();
}

// set bombs
function setBombs (){
    var coords;
    for (var i = 0; i < bombs; ++i){
        coords = getCoords ();
        field [coords [1]][coords[0]] = BOMB_VALUE;
    }
}

// very slow... optimize
// get coords -> set bomb at array field using this coords
// setting bomb in FUNCTION setBombs
function getCoords (){
    x = Math.floor (Math.random () * cBoxX);
    y = Math.floor (Math.random () * cBoxY);

    if (field [y][x] == 0) // see func initFields
        return [x, y];
    return getCoords (); // this make it very slow...
}

// add more info about bombs
function refillArrayField (){
    for (var i = 0; i < cBoxY; ++i){
        for (var j = 0; j < cBoxX; ++j){
            if (field [i][j] != BOMB_VALUE)
                field [i][j] = getAroundBombsCount (j, i);
        }
    }
}

// get bombs around box
function getAroundBombsCount (x, y){
    var bombsCount = 0;
      
    // Y-X coordinats of rel boxes 

    // UP-LEFT BOX
    if ( (x - 1 >= 0) && (y - 1 >= 0) && (field [y - 1][x - 1] == BOMB_VALUE) )
        bombsCount += 1;

    // UP-MIDDLE BOX
    if ( (y - 1 >= 0) && (field [y - 1][x] == BOMB_VALUE) )
        bombsCount += 1;

    // UP-RIGHT BOX
    if ( (x + 1 < cBoxX) && (y - 1 >= 0) && (field [y - 1][x + 1] == BOMB_VALUE) )
        bombsCount += 1;

    // MIDDLE-LEFT BOX
    if ( (x - 1 >= 0) && (field [y][x - 1] == BOMB_VALUE) )
        bombsCount += 1;

    // MIDDLE-RIGHT BOX
    if ( (x + 1 < cBoxX) && (field [y][x + 1] == BOMB_VALUE) )
        bombsCount += 1;

    // DOWN-LEFT BOX
    if ( (y + 1 < cBoxY) && (x - 1 >= 0) && (field [y + 1][x - 1] == BOMB_VALUE) )
        bombsCount += 1;

    // DOWN-MIDDLE BOX
    if ( (y + 1 < cBoxY) && (field [y + 1][x] == BOMB_VALUE) )
        bombsCount += 1;

    // DOWN-RIGHT BOX
    if ( (y + 1 < cBoxY) && (x + 1 < cBoxX) && (field [y + 1][x + 1] == BOMB_VALUE) )
        bombsCount += 1;

    return bombsCount;
}
