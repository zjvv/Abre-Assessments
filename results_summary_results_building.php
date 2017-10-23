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
	require(dirname(__FILE__) . '/../../configuration.php'); 
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php'); 
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
	require_once('../../core/abre_functions.php');
	require_once('functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
		
		//Get Passed Variables
		$Assessment_ID=htmlspecialchars($_GET["assessmentid"], ENT_QUOTES);
		
			//Building Dropdown
			echo "<div class='row'>";
			echo "<div class='col l4 s12'>";
			if(isset($_GET["building"]))
			{
				$building=$_GET["building"];
				
				echo "<select id='buildingteacher' multiple>";
					echo "<option value='' disabled selected>Choose a Teacher/Teachers</option>";
					$query = "SELECT * FROM Abre_Staff where SchoolCode='$building' order by LastName";
					$dbreturn = databasequery($query);
					foreach ($dbreturn as $value)
					{	
						$LastName=$value['LastName'];
						$FirstName=$value['FirstName'];
						$StaffID=$value['StaffID'];
						
						echo "<option value='$StaffID'>$LastName, $FirstName</option>";
					}
				echo "</select>";
			}
			echo "</div>";
			echo "</div>";
			
		//Results Area
		echo "<div class='row' style='margin-top:-10px;'>";
			echo "<div class='col s12'><div id='p2' class='mdl-progress mdl-js-progress mdl-progress__indeterminate landingloadergridtcards' style='width:100%;'></div></div>";
			echo "<div id='teachercompareresults'></div>";
		echo "</div>";
		
	}

?>
		
<script>
			
	//Responsive fixed table header
	$(function()
	{
		
		$('select').material_select();
		$(".landingloadergridtcards").hide();
		$("#teachercompareresults").hide();
		
		function TeacherResults(Teachers)
		{
			$("#teachercompareresults").load('modules/Abre-Assessments/results_summary_results_teachercards.php?assessmentid=<?php echo $Assessment_ID; ?>&teachers='+Teachers, function(){ 
				$(".landingloadergridtcards").hide();
				$("#teachercompareresults").show();
			});
		}
		
    	//Filter Change
    	$('#buildingteacher').change(function()
    	{
	    	$(".landingloadergridtcards").show();
	    	$("#teachercompareresults").hide();
	    	var Teachers = $(this).val();
			TeacherResults(Teachers);
		});
					
	});
				
</script>