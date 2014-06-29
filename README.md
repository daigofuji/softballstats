softballstats
=============

This is a fork of David Carlo's excellent SoftballStats app.  Found here: http://sourceforge.net/projects/softballstats/ 


What is SoftballStats?
----------------------
SoftballStats is a collection of PHP4 scripts and a MySQL database that 
track the stats, games, players, and every play made in multiple 
softball/baseball seasons.  SoftballStats also compiles statistics for each
player.

There 2 groups of scripts:
1. Stat scripts - These scripts display stats, player info, and game info
   for the current season.
2. Admin scripts - this is where the Stat Master enters the season info,
   game info, player info, and every play made in all games.


Using SoftballStats
-------------------
1.  Install SoftballStats.  See INSTALL.
2.  Use the Season Admin page to create a new season.  Make sure you set
    this season to Current.
3.  Use the Game Admin page to add in all the games for your season.  Even
    the ones you have not played yet.  Your team mates will be able to use
    the Game Info page to see the schedule.
4.  Use the Player Admin page to add in all your players.
5.  After you start your season, you should have a sheet filled out by your
    'Score Whore' ;) with all the plays.  Take each play, in order if
    possible, and enter it using the Play Admin page.
    SoftballStats tracks the following mandatory plays:

      Double
      Double Play
      Fielders Choice
      Fouled Out
      Ground Out
      Home Run
      Pop Out
      RBI
      Reached on Error
      Run Scored
      Sacrifice
      Single
      Strike Out
      Triple
      Walk

    And the following optional plays:

      Nice F*ing Play
      Error (1st Base)
      Error (2nd Base)
      Error (3rd Base)
      Error (Catcher)
      Error (Left Center Field)
      Error (Left Field)
      Error (Pitcher)
      Error (Right Center Field)
      Error (Right Field)
      Error (Short Stop)

6.  Take a look at all the Stat pages to see info/stats based on your
    season.
7.  Shoot out an email to your friends/team mates with a link to
    https://<your server>/softballstats/

[Hint] Make sure to fill in all the required fields marked w/ a '*' on all
       admin pages


Files in SoftballStats
----------------------
*.php              - Display scripts (player info, game info, stats)
admin/*.php        - Admin scripts (season, game, player, play admin)
docs/*             - Documentation (READ THIS)
contrib/mysql.txt  - MySQL table defs & demo season
contrib/*.psd      - Photoshop 5.0 files for creating web graphics
config/config.php  - File for storing config options & functions
config/*er.php     - Scripts to generate html page headers & footers
gifs/*             - Web graphics


Installing SoftballStats
------------------------
See INSTALL


License
-------
GNU General Public License (GPL)
See LICENSE
Please send an email to me at dev@swillers.com letting me know if SoftballStats is
working/not working for you.


Disclaimer
----------
This software is provided without warranty.  If it misbehaves, shoot it.


SoftballStats written by David Carlo <dev@swillers.com>

