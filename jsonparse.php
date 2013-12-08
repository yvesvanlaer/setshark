<?php

	require("lib/config.php");
	require("lib/gsAPI.php");

	$gs = new gsAPI($myKey, $mySecret); //note: you can also change the default key/secret in gsAPI.php
	$sessionID = $gs->startSession();
	$user = $gs->authenticate($myUserLogin, $myUserPassword);
	if (empty($user) || $user['UserID'] < 1) {
	    // Login failed. invalid username/password
	    exit;
	}
	
	$setlistID = $_GET['setlistID']; // 6bc6da2a (Frank Turner)

	$json = file_get_contents("http://api.setlist.fm/rest/0.1/setlist/".$setlistID.".json");
	$data = json_decode($json);

	$artist = $data->setlist->artist->{'@name'};
	$tour = $data->setlist->{'@tour'};
	$venue = $data->setlist->venue->{'@name'};
	$playlistexist = FALSE;

	print 'Artist: ' . $artist . '<br />';
	print 'Tour: ' . $tour . '<br />';
	print 'Venue: ' . $venue . '<br /><br />';
	
	$playlistname = $artist;
	if ($tour != "") { $playlistname.' - '.$tour; }
	if ($venue != "") { $playlistname.' - '.$venue; }

	// check if playlist already exists
	print 'checking if playlist exists<br />';
	debug_to_console($user);
	var_dump($user);
	$playlists = $gs->getUserPlaylists();
	print 'something blocks this<br />';
	//print_r($playlists);
	if (!is_array($playlists)) {
	    //something failed.
	    print 'something failed<br />';
	    exit;
	}

	print 'scanning your playlists<br />';
	foreach ($playlists as $playlist) {
	    if ($playlist["PlaylistName"] == $playlistname) {
	    	print 'This playlist has already been created<br />';
	    	print 'Playlist: <a href="http://grooveshark.com/#!/playlist/'.$playlist["PlaylistName"].'/'.$playlist["PlaylistID"].'" target="_blank">'.$playlist["PlaylistName"].'</a><br />';
	    	$playlistexist = TRUE;
	    } else {
	    	//print 'Playlist does not exist<br /><br />';
	    }
	}
	
	debug_to_console($playlistexist);
	print_r($playlistexist);

	if (!$playlistexist) {
		print 'create song array<br />';
		$songs = array();
		print '<pre>';
		var_dump($data->setlist->sets->set);
		print '</pre>';

		foreach ($data->setlist->sets->set as $key => $value) {
			print 'work goddammit';
			foreach ($value->song as $key2 => $value2) {
				if((strlen($value2->{'@name'}) != 0) && (!isset($value2->cover))) {
					array_push($songs, $value2->{'@name'});
				}
			}
		}
		debug_to_console($songs);
		print '<pre>';
		var_dump($songs);
		print '</pre>';

		// need tiny song
		// apikey: 78ccedf973f5244d57b98075d7f2f6e5
		if (!empty($songs)) {
			$songids = array();
			foreach ($songs as $key => $value) {
				$url = "http://tinysong.com/b/".str_replace(" ", "+", $artist)."+".str_replace(" ", "+", $value)."?format=json&key=78ccedf973f5244d57b98075d7f2f6e5";
				$tinyquery = file_get_contents($url);
				$songdata = json_decode($tinyquery);
				array_push($songids, $songdata->SongID);
			}
			
			$gs->createPlaylist($playlistname, $songids);

			print '<br /><br />DONE! CHECK OUT YOUR NEW PLAYLIST: '.$playlistname.'<br />';
			$playlists = $gs->getUserPlaylists();
			if (!is_array($playlists)) {
			    //something failed.
			    exit;
			}
			foreach ($playlists as $playlist) {
			    if ($playlist["PlaylistName"] == $playlistname) {
			    	print 'Playlist: <a href="http://grooveshark.com/#!/playlist/'.$playlist["PlaylistName"].'/'.$playlist["PlaylistID"].'" target="_blank">'.$playlist["PlaylistName"].'</a><br />';
			    	$playlistexist = TRUE;
			    }
			}
		}
	}





	/**
	 * Send debug code to the Javascript console
	 */ 
	function debug_to_console($data) {
	    if(is_array($data) || is_object($data))
		{
			echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
		} else {
			echo("<script>console.log('PHP: ".$data."');</script>");
		}
	}
?>