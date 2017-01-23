<?php
	
	/*
	* Copyright 2015 Hamilton City School District	
	* 	
	* This program is free software: you can redistribute it and/or modify
    * it under the terms of the GNU General Public License as published by
    * the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
	* 
    * This program is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    * GNU General Public License for more details.
	* 
    * You should have received a copy of the GNU General Public License
    * along with this program.  If not, see <http://www.gnu.org/licenses/>.
    */
	
	//Required configuration files
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php');
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require_once(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
	
	//Variables
	$score=mysqli_real_escape_string($db, $_POST["score"]);
	$scoredOn=mysqli_real_escape_string($db, $_POST["scoredOn"]);
	$itemId=mysqli_real_escape_string($db, $_POST["itemId"]);
	$itemResponse=mysqli_real_escape_string($db, $_POST["itemResponse"]);
	$scoreGUID=mysqli_real_escape_string($db, $_POST["scoreGUID"]);
		
	//Add the score record
	$stmt = $db->stmt_init();
	$sql = "INSERT INTO assessments_scores (User, Score, ScoredOn, ItemID, Response, Score_GUID) VALUES ('".$_SESSION['useremail']."', '$score', '$scoredOn', '$itemId', '$itemResponse', '$scoreGUID');";
	$stmt->prepare($sql);
	$stmt->execute();
	$stmt->close();
	$db->close();
	
?>