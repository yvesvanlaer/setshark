SetShark, a bridge between Setlist.FM and Grooveshark
-------------------------------------------------------------------------------

Requires:
- A VIP user account (paid users)
- An API-key

How to use:
- Go to Setlist.FM
- Look for your setlist --> http://www.setlist.fm/setlist/blink182/2013/wiltern-theatre-los-angeles-ca-4bc48be2.html
- Copy "4bc48be2" (that's the ID of the playlist)
- Go to your localhost and make sure the URL is something like this: http://setshark.dev/jsonparse.php?setlistID=4bc48be2

Please keep in mind that this is just a test, it works most of the time.
- Sometimes the flaw is on the Setlist.FM side, when a setlist is not inputted correctly.
- Sometimes the flaw is on the Grooveshark side, when they do not have the song in the library or tinysong gives the wrong song id.

To do:
- Make it more accurate.
