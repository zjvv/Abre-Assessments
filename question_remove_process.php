<?php
	
	/*
	* Copyright (C) 2016-2017 Abre.io LLC
	*
	* This program is free software: you can redistribute it and/or modify
    * it under the terms of the Affero General Public License version 3
    * as published by the Free Software Foundation.
	*
    * This program is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    * GNU Affero General Public License for more details.
	*
    * You should have received a copy of the Affero General Public License
    * version 3 along with this program.  If not, see https://www.gnu.org/licenses/agpl-3.0.en.html.
    */
	
	//Required configuration files
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php');
	require_once(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
		
		$questionid=mysqli_real_escape_string($db, $_GET["questionid"]);
		
		//Delete Topic
		$stmtrecord = $db->prepare("DELETE from assessments_questions where ID = ?");
		$stmtrecord->bind_param("i",$questionid);	
		$stmtrecord->execute();
		$stmtrecord->close();
		
		//End
		$db->close();
		echo "The question has been removed.";	
		
	}		
	
?>