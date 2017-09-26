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
	require(dirname(__FILE__) . '/../../configuration.php');
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{	


		$Assessment_ID=htmlspecialchars($_GET["id"], ENT_QUOTES);
		$sqllookup = "SELECT * FROM assessments where ID='$Assessment_ID'";
		$result2 = $db->query($sqllookup);
		$setting_preferences=mysqli_num_rows($result2);
		while($row = $result2->fetch_assoc())
		{
			
			$Title=htmlspecialchars($row["Title"], ENT_QUOTES);
			$Grade=htmlspecialchars($row["Grade"], ENT_QUOTES);
			$Subject=htmlspecialchars($row["Subject"], ENT_QUOTES);

			echo "<div class='page_container'>";
				echo "<div class='row'><div class='center-align' style='padding:20px;'><h3 style='font-weight:600;'>$Title</h3><h6 style='color:#777;'>$Subject &#183; Grade Level: $Grade</h6></div></div>";			
		}
		
		
		$sqllookup = "SELECT * FROM assessments_questions where Assessment_ID='$Assessment_ID' order by Question_Order, ID";
		$result2 = $db->query($sqllookup);
		while($row = $result2->fetch_assoc())
		{	
			$Question_ID=htmlspecialchars($row["ID"], ENT_QUOTES);
			
			$sqllookup = "SELECT * FROM assessments_answers where Assessment_ID='$Assessment_ID' and Question_ID='$Question_ID'";
			$result2 = $db->query($sqllookup);
			while($row = $result2->fetch_assoc())
			{	
				$Student_FirstName=htmlspecialchars($row["Student_FirstName"], ENT_QUOTES);
				$Student_LastName=htmlspecialchars($row["Student_Lastname"], ENT_QUOTES);
				
				echo "$Student_FirstName $Student_LastName";
			}
						
		}
		
	}


?>