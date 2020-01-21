// when tab opened
window.onload = function (){
    oneInit ();
    init ();
    drawNet ();
};

function oneInit (){
    canvas = document.getElementById ('main_field');
    ctx = canvas.getContext ('2d');
    // default conf
    xCount = defaultXCount;
    yCount = defaultYCount;
    sqrX = defaultSqrX;
    sqrY = defaultSqrY;
    gameOver = 0;
    stopFlag = 0;
    delay = defaultDelay;
}

// init arrs default vals
function initArraysDefault (){
    field = new Array (yCount);
    evofield = new Array (yCount);

    for (var i = 0; i < yCount; ++i){
        field [i] = new Array (xCount);
        evofield [i] = new Array (xCount);
        
        for (var j = 0; j < xCount; ++j){
            field [i][j] = spaceCellId;
            evofield [i][j] = spaceCellId;
        }
    }
}

// data initialization && canvas onclick event && other
function init () {

    if (sqrX == 0 && xCount != 0) sqrX = Math.trunc (canvas.width / xCount);
    else if (sqrX == 0 && xCount == 0) sqrX = defaultSqrX;
    if (sqrY == 0 && yCount != 0) sqrY = Math.trunc (canvas.height / yCount);
    else if (sqrX == 0 && xCount == 0) sqrY = defaultSqrY;

    if (xCount == 0) xCount = Math.trunc (canvas.width / sqrX);
    if (yCount == 0) yCount = Math.trunc (canvas.height / sqrY);
    
    initArraysDefault ();
    canvas.addEventListener('click', canvasOnclick, false);
}
