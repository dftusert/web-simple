// canvas click
function canvasOnclick(event) {
        var posX;
        var posY;

        // feature, need to fix
        if (Math.trunc ((event.pageX - canvas.offsetLeft) / sqrX) > xCount - 1)
            posX = xCount - 1;
        else 
            posX = Math.trunc ((event.pageX - canvas.offsetLeft) / sqrX);
        // and again
        if(Math.trunc ((event.pageY - canvas.offsetTop) / sqrY) > yCount - 1)
            posY = yCount - 1;
        else 
            posY = Math.trunc ((event.pageY - canvas.offsetTop) / sqrY);
        
        if (field [posY][posX] == spaceCellId) field [posY][posX] = simpleCellId;
        else field [posY][posX] = spaceCellId;
        
        drawNet ();
        drawCells ();
}
// field settings button clicked - show/hide conf block
function field_settingsClick (){
    var field_settingsDiv = document.getElementById ('field_settings');
 
    if (field_settingsDiv.getAttribute ('class') == 'hide')
        field_settingsDiv.setAttribute ('class', 'show');
    else
        field_settingsDiv.setAttribute ('class', 'hide');
}
// change sqrX and sqrY vars vals
function changeXYPX_onclick (){
    var val = parseInt(document.getElementById ('sqrX').value);
    if (val < canvas.width && val > 0) sqrX = val;
    val = parseInt(document.getElementById ('sqrY').value);
    if (val < canvas.height && val > 0) sqrY = val;
    xCount = 0;
    yCount = 0;
    init ();
    drawNet ();
}
// change xCount, yCount vars vals
function changeCount_onclick (){
    var val = parseInt(document.getElementById ('xCount').value);
    if (val < canvas.width && val > 0) xCount = val;
    val = parseInt(document.getElementById ('yCount').value);
    if (val < canvas.height && val > 0) yCount = val;
    sqrX = 0;
    sqrY = 0;
    init ();
    drawNet ();
}
// change canvas width && height
function changeCanvas_onclick (){
    var val = parseInt(document.getElementById ('canvasX').value);
    if (val > 0) canvas.setAttribute ('width', val);
    val = parseInt(document.getElementById ('canvasY').value);
    if (val > 0) canvas.setAttribute ('height', val);
    sqrX = 0;
    sqrY = 0;
    init ();
    drawNet ();
}

function makeStep (){
    if (gameOver == 0 && stopFlag == 0) step ();
}

function setNewGame (){
    gameOver = 0;
    init ();
    drawNet ();
}

function makeGo (){
    go ();
}

function pause (){
    if (stopFlag == 0) stopFlag = 1;
    else stopFlag = 0;
}
