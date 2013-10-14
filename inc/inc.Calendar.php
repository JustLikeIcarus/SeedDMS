<?php
//    Copyright (C) 2010 Matteo Lucarelli
// 
//    Some code from PHP Calendar Class Version 1.4 (5th March 2001)
//    (C)2000-2001 David Wilkinson
//    URL:   http://www.cascade.org.uk/software/php/calendar/
//    Email: davidw@cascade.org.uk
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

// DB //////////////////////////////////////////////////////////////////////////

function getEvents($day, $month, $year){

	global $db;

	$date = mktime(12,0,0, $month, $day, $year);
	
	$queryStr = "SELECT * FROM tblEvents WHERE start <= " . $date . " AND stop >= " . $date;
	$ret = $db->getResultArray($queryStr);	
	return $ret;
}

function getEventsInInterval($start, $stop){

	global $db;

	$queryStr = "SELECT * FROM tblEvents WHERE ( start <= " . (int) $start . " AND stop >= " . (int) $start . " ) ".
	                                       "OR ( start <= " . (int) $stop . " AND stop >= " . (int) $stop . " ) ".
	                                       "OR ( start >= " . (int) $start . " AND stop <= " . (int) $stop . " )";
	$ret = $db->getResultArray($queryStr);	
	return $ret;
}

function addEvent($from, $to, $name, $comment ){

	global $db,$user;

	$queryStr = "INSERT INTO tblEvents (name, comment, start, stop, date, userID) VALUES ".
		"(".$db->qstr($name).", ".$db->qstr($comment).", ".(int) $from.", ".(int) $to.", ".mktime().", ".$user->getID().")";
	
	$ret = $db->getResult($queryStr);
	return $ret;
}

function getEvent($id){

	if (!is_numeric($id)) return false;

	global $db;
	
	$queryStr = "SELECT * FROM tblEvents WHERE id = " . (int) $id;
	$ret = $db->getResultArray($queryStr);
	
	if (is_bool($ret) && $ret == false) return false;
	else if (count($ret) != 1) return false;
		
	return $ret[0];	
}

function editEvent($id, $from, $to, $name, $comment ){

	if (!is_numeric($id)) return false;

	global $db;
	
	$queryStr = "UPDATE tblEvents SET start = " . (int) $from . ", stop = " . (int) $to . ", name = " . $db->qstr($name) . ", comment = " . $db->qstr($comment) . ", date = " . mktime() . " WHERE id = ". (int) $id;
	$ret = $db->getResult($queryStr);	
	return $ret;
}

function delEvent($id){

	if (!is_numeric($id)) return false;
	
	global $db;
	
	$queryStr = "DELETE FROM tblEvents WHERE id = " . (int) $id;
	$ret = $db->getResult($queryStr);	
	return $ret;
}

?>
