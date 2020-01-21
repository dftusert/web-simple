// Pixel count of width and height for 1 cell
var sqrX, sqrY;
// matrix
var field, evofield;
// variables for drawing
var canvas, ctx;
// cells count
var xCount, yCount;
// bool var
var gameOver;
// delay, can be modified by user
var delay;
// stop going flag
var stopFlag;
// simple cellId
const simpleCellId = 0;
// sp cellId
const spaceCellId = -1;
// default delay
const defaultDelay = 1000;
// other consts
const defaultSqrX = 50;
const defaultSqrY = 50;
const defaultXCount = 10;
const defaultYCount = 10;
