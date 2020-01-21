/***variables***/

// variables for canvas and canvas context
var canvas, ctx;

// count of boxes (X and Y)
var cBoxX, cBoxY;

// count of bombs;
var bombs;

// fields
var field, infofield;

// count of opened boxes
var openedBoxes;

// marked boxes
var markedBoxes;

// is next step is to mark ?
var markFlag;

/***************/

/***constants***/

// default count of boxes
const CBOXX_DEFAULT = 10;
const CBOXY_DEFAULT = 10;

// pixels for 1 box
const PXX = 100;
const PXY = 100;

// default count of bombs
// be careful to set this const
const BOMBS_DEFAULT = 10;

// other consts
const BOMB_INDEX = 9;   // in bombColors array
const CLOSED_INDEX = 10;
const MARKED_INDEX = 11;

const BOMB_VALUE = -1;  // on field can be random but except [0...8]

const SHOWED_VALUE = 1; // on infofield (how to fill boxes with color)
const MARKED_VALUE = 0;
const CLOSED_VALUE = -1; 

// colors for every  number
// 0, 1, 2, 3, 4, 5, 6, 7, 8, bomb, closed, marked
const BOMBCOLORS = [
                   "rgb(255, 255, 255)",  // 0 bombs around
                   "rgb(0, 255, 0)",      // 1 bombs around
                   "rgb(0, 0, 255)",      // 2 bombs around
                   "rgb(0, 255, 255)",    // 3 bombs around
                   "rgb(200, 200, 200)",  // 4 bombs around
                   "rgb(120, 120, 120)",  // 5 bombs around
                   "rgb(130, 130, 130)",  // 6 bombs around
                   "rgb(128, 128, 128)",  // 7 bombs around
                   "rgb(50, 50, 50)",     // 8 bombs around
                   "rgb(0, 0, 0)",         // bomb (9)
                   "rgb(153, 153, 153)",  // closed (10)
                   "rgb(255, 0, 0)",      // marked (11)
                   ];

/**************/
