// canvas onclick listener
function canvasOnclick (event){

    var x, y, posX, posY;

    if (event.pageX || event.pageY) { 
        x = event.pageX;
        y = event.pageY;
    }
    else { 
        x = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft; 
        y = event.clientY + document.body.scrollTop + document.documentElement.scrollTop; 
    } 

    x -= canvas.offsetLeft;
    y -= canvas.offsetTop;

    posX = Math.floor (x / PXX);
    posY = Math.floor (y / PXY); 
    
    if (!markFlag)
        openBox (posX, posY);
    else
        mark (posX, posY);
    
    fillGameDataIntoSpans (); 
}

function fillGameDataIntoSpans (){
    document.getElementById ("openBoxText").innerText = "opened: " + openedBoxes;
    document.getElementById ("markText").innerText = "marked: " + markedBoxes;
}

// open box 
function openBox (x, y){
    if (infofield [y][x] == SHOWED_VALUE) return;

    // if bomb, prepare to game over
    if (field [y][x] == BOMB_VALUE){
        infofield [y][x] = SHOWED_VALUE;
        decorateNet ();
        drawNet ();
        openedBoxes += 1;
        fillGameDataIntoSpans ();
        gameOver ("Game Over");
        return;
    }
    // if there are no bombs around (0)
    else if (field [y][x] == 0)
        openZeros (x, y);
    // and else only make it showed
    else{
        if (infofield [y][x] == MARKED_VALUE) markedBoxes -= 1;
        infofield [y][x] = SHOWED_VALUE;
        openedBoxes += 1;
    }
    
    decorateNet (); // FUNCTION FROM drawFunctions.js
    drawNet (); // FUNCTION FROM drawFunctions.js

    if (cBoxX * cBoxY - openedBoxes == bombs)
        gameOver ("You Win");
}

// open zeros around
// warning: bad code
function openZeros (x, y){
    if ((x < 0) || (x >= cBoxX) || (y < 0) || (y >= cBoxY) || (infofield [y][x] == SHOWED_VALUE)) return;

    if (infofield [y][x] == MARKED_VALUE) markedBoxes -= 1;
    // make showed box
    infofield [y][x] = SHOWED_VALUE;
    openedBoxes += 1;

    if (field [y][x] == 0){
        openZeros (x - 1, y - 1); // LEFT, UP
        openZeros (x, y - 1); // MIDDLE, UP
        openZeros (x + 1, y - 1); // RIGHT, UP
        openZeros (x - 1, y); // LEFT, MIDDLE
        openZeros (x + 1, y); // RIGHT, MIDDLE
        openZeros (x - 1, y + 1); // LEFT, DOWN
        openZeros (x, y + 1); // MIDDLE, DOWN
        openZeros (x + 1, y + 1); // RIGHT, DOWN
    }
    /*
        and if we move
        infofield [y][x] = SHOWED_VALUE;
        here, there will be too much recursion
        so because that, this code is before if(field [y][x])....
   */
}

// mark box, can't mark if infofield [y][x] contains SHOWED_VALUE
function mark (x, y){
    if (infofield [y][x] == CLOSED_VALUE){
        infofield [y][x] = MARKED_VALUE;
        markedBoxes += 1;
    }
    
    else if (infofield [y][x] == MARKED_VALUE){
        infofield [y][x] = CLOSED_VALUE;
        markedBoxes -= 1;
    }
    
    // if net is big, better will be move that in if & else if block   
    decorateNet (); // FUNCTION FROM drawFunctions.js
    drawNet (); // FUNCTION FROM drawFunctions.js 
}

// game over
function gameOver (text){
    alert (text);
    if (confirm ("start new?")){
        // FUNCTION FROM initVariables.js
        simpleInit (); // I think... it can't work in unusual moments...
        decorateNet (); // FUNCTION FROM drawFunctions.js
        drawNet (); // FUNCTION FROM drawFunctions.js
    }
    else
        // simple disable canvas onclick if answer no
        canvas.onclick = null;        
}

// show/ hide settings
function showSettings (){
    var div = document.getElementById ("menu");
    if (div.className == "hidden")
        div.className = "visible";
    else
        div.className = "hidden";
}

// starts game
function startGame (){
    if (!confirm ("really restart game?")) return;

    // FUNCTION FROM initVariables.js
    simpleInit (); // I think... it can't work in unusual moments...
    decorateNet (); // FUNCTION FROM drawFunctions.js
    drawNet (); // FUNCTION FROM drawFunctions.js

}

// set new settings
function makeChanges (){
    var bombsCount = document.getElementById ("bombsCount").value;
    var xCount = document.getElementById ("xCount").value;
    var yCount = document.getElementById ("yCount").value;

    if (isNumeric (bombsCount) && bombsCount > 0)
        bombs = bombsCount;
    if (isNumeric (xCount) && xCount > 0)
        cBoxX = xCount;
    if (isNumeric (yCount) && yCount > 0)
        cBoxY = yCount;

    simpleInit (); // I think... it can't work in unusual moments...
    decorateNet (); // FUNCTION FROM drawFunctions.js
    drawNet (); // FUNCTION FROM drawFunctions.js

}

// is value numeric or not
function isNumeric(n) {
  return !isNaN(parseInt(n)) && isFinite(n);
}

// set mark step 0/1
function setMark (){
    markFlag = !markFlag;
    
    if (markFlag)
        document.getElementById ("markButton").className = "isMarked";
    else
        document.getElementById ("markButton").className = "notMarked";
}

// show game info
function showInfo (){
    var div = document.getElementById ("info");
    if (div.className == "hidden")
        div.className = "visible";
    else
        div.className = "hidden";
}
