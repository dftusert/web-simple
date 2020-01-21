import java.util.Random;
import java.util.Scanner;

class Config
{
    static final int DCELLVALUE = 0;
    static final int BOMBCELLVALUE = -1;
    static final int STATECLOSED = -1;
    static final int STATEMARKED = 0;
    static final int STATEOPENED = 1;

    static final char SYMMARKED = '?';
    static final char SYMBOMB = '*';
    static final char SYMCLOSED = '-';

    static final int DBOMBCOUNT = 10;
    static final int DWIDTH = 10;
    static final int DHEIGHT = 10;
}

class Cell
{
    private int cellValue;
    private int state;

    Cell ( int cellValue, int state )
    {
        this.cellValue = cellValue;
        this.state = state;
    }

    Cell ()
    {
        this ( Config.DCELLVALUE, Config.STATECLOSED );
    }

    public void setState ( int state )
    {
        this.state = state;
    }

    public void setCellValue ( int cellValue )
    {
        this.cellValue = cellValue;
    }

    public int getCellValue ()
    {
        return cellValue;
    }

    public int getState ()
    {
        return state;
    }
}

class Field
{
    private final int WIDTH;
    private final int HEIGHT;
    private final int BOMBS;

    private Cell field [][];

    private int openedCells;
    private int markedCells;

    Field ( int width, int height, int bombs )
    {
        WIDTH = width;
        HEIGHT = height;
        BOMBS = bombs;

        openedCells = 0;
        markedCells = 0;

        field = new Cell [ HEIGHT ][ WIDTH ];

        for ( int i = 0; i < HEIGHT; ++i )
            for ( int j = 0; j < WIDTH; ++j )
                field [ i ][ j ] = new Cell ( );

        fillFieldBombs ();
        refillField ();
    }

    Field ()
    {
        this ( Config.DWIDTH, Config.DHEIGHT, Config.DBOMBCOUNT );
    }

    public int [] getRandomCoords ()
    {
        int coords [] = new int [ 2 ];
        Random rand = new Random ();

        coords [ 0 ] = rand.nextInt ( WIDTH );
        coords [ 1 ] = rand.nextInt ( HEIGHT );

        if ( field [ coords [ 1 ] ][ coords [ 0 ] ].getCellValue () == Config.BOMBCELLVALUE )
            return getRandomCoords ();

        return coords;
    }

    public void fillFieldBombs ()
    {
        for ( int i = 0; i < BOMBS; ++i )
        {
            int coords [] = getRandomCoords ();

            field [ coords [ 1 ] ][ coords [ 0 ] ].setCellValue ( Config.BOMBCELLVALUE );
        }
    }

    public void refillField ()
    {
        for ( int i = 0; i < HEIGHT; ++i )
            for ( int j = 0; j < WIDTH; ++j )
                if ( field [ i ][ j ].getCellValue () != Config.BOMBCELLVALUE )
                    field [ i ][ j ].setCellValue ( getCountOfBombsNear ( j, i ) );
    }

    public int getCountOfBombsNear ( int x, int y )
    {
        int bombsNear = 0;

        // X-Y
        // LEFT-UP
        if ( x > 0 && y > 0 && field [ y - 1 ][ x - 1 ].getCellValue () == Config.BOMBCELLVALUE )
            bombsNear++;

        // MIDDLE-UP
        if ( y > 0 && field [ y - 1 ][ x ].getCellValue () == Config.BOMBCELLVALUE )
            bombsNear++;

        // RIGHT-UP
        if ( x < ( WIDTH - 1 ) && y > 0 && field [ y - 1 ][ x + 1 ].getCellValue () == Config.BOMBCELLVALUE )
            bombsNear++;

        // LEFT-MIDDLE
        if ( x > 0 && field [ y ][ x - 1 ].getCellValue () == Config.BOMBCELLVALUE )
            bombsNear++;

        // RIGHT-MIDDLE
        if ( x < ( WIDTH - 1 ) && field [ y ][ x + 1 ].getCellValue () == Config.BOMBCELLVALUE )
            bombsNear++;

        // LEFT-DOWN
        if ( x > 0 && y < ( HEIGHT - 1 ) && field [ y + 1 ][ x - 1 ].getCellValue () == Config.BOMBCELLVALUE )
            bombsNear++;

        // MIDDLE-DOWN
        if ( y < ( HEIGHT - 1 ) && field [ y + 1 ][ x ].getCellValue () == Config.BOMBCELLVALUE )
            bombsNear++;

        // RIGHT-DOWN
        if ( x < ( WIDTH - 1 ) && y < ( HEIGHT - 1 ) && field [ y + 1 ][ x + 1 ].getCellValue () == Config.BOMBCELLVALUE )
            bombsNear++;

        return bombsNear;
    }

    public boolean openCell ( int x, int y )
    {
        if ( field [ y ][ x ].getState () == Config.STATEOPENED )
            return true;

        if ( field [ y ][ x ].getState () == Config.STATEMARKED )
            markedCells--;

        field [ y ][ x ].setState ( Config.STATEOPENED );
        openedCells++;

        if ( field [ y ][ x ].getCellValue () == Config.BOMBCELLVALUE )
            return false;

        if ( field [ y ][ x ].getCellValue () == 0 )
            zeroCellNearOpen ( x, y );

        return true;
    }

    public void zeroCellNearOpen ( int x, int y )
    {
        // X-Y
        // LEFT-UP
        if ( x > 0 && y > 0 )
            openCell ( x - 1, y - 1 );

        // MIDDLE-UP
     	if ( y > 0 )
            openCell ( x, y - 1 );

        // RIGHT-UP
        if ( x < ( WIDTH - 1 ) && y > 0 )
            openCell ( x + 1, y - 1 );

        // LEFT-MIDDLE
        if ( x > 0 )
            openCell ( x - 1, y );

        // RIGHT-MIDDLE
        if ( x < ( WIDTH - 1 ) )
            openCell ( x + 1, y );

        // LEFT-DOWN
        if ( x > 0 && y < ( HEIGHT - 1 ) )
            openCell ( x - 1, y + 1 );

        // MIDDLE-DOWN
        if ( y < ( HEIGHT - 1 ) )
            openCell ( x, y + 1 );

        // RIGHT-DOWN
        if ( x < ( WIDTH - 1 ) && y < ( HEIGHT - 1 ) )
            openCell ( x + 1, y + 1 );
    }

    public void markCell ( int x, int y )
    {
        if ( field [ y ][ x ].getState () == Config.STATECLOSED )
        {
            field [ y ][ x ].setState ( Config.STATEMARKED );
            markedCells++;
        }

        else if ( field [ y ][ x ].getState () == Config.STATEMARKED )
        {
            field [ y ][ x ].setState ( Config.STATECLOSED );
            markedCells--;
        }
    }

    public void printField ()
    {
        for ( int i = 0; i < HEIGHT; ++i )
        {
            for ( int j = 0; j < WIDTH; ++j )
            {
                if ( field [ i ][ j ].getState () == Config.STATECLOSED )
                    System.out.print ( Config.SYMCLOSED );

                if ( field [ i ][ j ].getState () == Config.STATEOPENED &&
                     field [ i ][ j ].getCellValue () != Config.BOMBCELLVALUE )
                    System.out.print ( field [ i ][ j ].getCellValue () );

                if ( field [ i ][ j ].getCellValue () == Config.BOMBCELLVALUE &&
                     field [ i ][ j ].getState () == Config.STATEOPENED )
                    System.out.print ( Config.SYMBOMB );

                if ( field [ i ][ j ].getState () == Config.STATEMARKED )
                    System.out.print ( Config.SYMMARKED );

                System.out.print ( " " );
            }

            System.out.println ();
        }
    }

    public int getOpenedCells ()
    {
        return openedCells;
    }

    public int getMarkedCells ()
    {
        return markedCells;
    }

    public int getBombs ()
    {
        return BOMBS;
    }

    public int getWidth ()
    {
        return WIDTH;
    }

    public int getHeight ()
    {
        return HEIGHT;
    }
}

class Game
{
    private Field gameField;
    private boolean gameState;

    Game ( int width, int height, int bombs )
    {
        gameField = new Field ( width, height, bombs );
        gameState = true;
    }

    Game ()
    {
        gameField = new Field ();
        gameState = true;
    }

    public boolean win ()
    {
        if ( gameField.getOpenedCells () == gameField.getWidth () * gameField.getHeight () - gameField.getBombs () )
            return true;
        return false;
    }

    public void gameStep ( String what, int x, int y )
    {
        if ( what.equals ( "mark" ) )
            gameField.markCell ( x, y );

        else
            gameState = gameField.openCell ( x, y ) && !win ();
    }

    public void printStat ()
    {
        System.out.print ( "Bombs: " + gameField.getBombs () + " " );
        System.out.print ( "Opened cells: " + gameField.getOpenedCells () + " " );
        System.out.println ( "Marked cells: " + gameField.getMarkedCells () );

        gameField.printField ();
    }

    public boolean getGameState ()
    {
        return gameState;
    }

    public int getGameFieldWidth ()
    {
        return gameField.getWidth ();
    }

    public int getGameFieldHeight ()
    {
        return gameField.getHeight ();
    }
}

public class Minesweeper
{
    public static void main ( String [] args )
    {
        Game minesweeper = new Game ( 5, 5, 3 );
        Scanner scan = new Scanner ( System.in );
        String cmd;
        int x, y;

        while ( minesweeper.getGameState () )
        {
            minesweeper.printStat ();

            System.out.print ( "input: cmd x y: " );

            cmd = scan.next ();
            x = scan.nextInt () - 1;
            y = scan.nextInt () - 1;

            if ( x < minesweeper.getGameFieldWidth () && y < minesweeper.getGameFieldHeight ()
                 && ( cmd.equals ( "mark" ) || cmd.equals ( "open" ) ) )
                minesweeper.gameStep ( cmd, x, y );
        }

        minesweeper.printStat ();

        if ( minesweeper.win () )
            System.out.println ( "WIN" );

        else
            System.out.println ( "LOOSE" );
    }
}
