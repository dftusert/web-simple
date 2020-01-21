// ! remove ctx.beginPath (); / ctx.closePath (); if not needed !

// draw net
function drawNet (){    

    var maxBox = Math.max (cBoxX, cBoxY);

    for (var i = 0; i <= maxBox; ++i){
        // draw | lines
        if (cBoxX >= i){
            ctx.beginPath ();
            
            ctx.closePath ();
            ctx.fill ();

            ctx.beginPath ();
            ctx.moveTo (i * PXX, 0);
            ctx.lineTo (i * PXX, cBoxY * PXY);
            ctx.closePath ();
            ctx.stroke ();
        }
        // draw -- lines
        if (cBoxY >= i){
            ctx.beginPath ();
            ctx.moveTo (0, i * PXY);
            ctx.lineTo (cBoxX * PXX, i * PXY);
            ctx.closePath ();
            ctx.stroke ();
        }
    }
}

function decorateNet (){
    ctx.clearRect (0, 0, canvas.width, canvas.height);

    for (var i = 0; i < cBoxY; ++i){
        for (var j = 0; j < cBoxX; ++j){
        
            if (infofield [i][j] == SHOWED_VALUE){
                if (field [i][j] == BOMB_VALUE)
                    ctx.fillStyle = BOMBCOLORS [BOMB_INDEX];
            else
                ctx.fillStyle = BOMBCOLORS [field [i][j]];
            }
            
            else if (infofield [i][j] == MARKED_VALUE)
                ctx.fillStyle = BOMBCOLORS [MARKED_INDEX];
            
            else if (infofield [i][j] == CLOSED_VALUE)
                ctx.fillStyle = BOMBCOLORS [CLOSED_INDEX];
           
            // not used else because new options might be added

            ctx.beginPath ();
            ctx.fillRect (j * PXX, i * PXY, PXX, PXY);
            ctx.closePath ();
            ctx.fill ();
        
            ctx.fillStyle = "#000000";
            if (infofield [i][j] == SHOWED_VALUE){ // then write all info
                ctx.beginPath ();
                ctx.fillText (field [i][j], (j + 0.4) * PXX, (i + 0.5) * PXY);
                ctx.closePath ();
                ctx.fill ();
            }
       }
    }
}

