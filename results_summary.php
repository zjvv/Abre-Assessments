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
		
		$Assessment_ID=htmlspecialchars($_GET["assessmentid"], ENT_QUOTES);
		
		//Check if user is the owner of the assessment
		$query = "SELECT * FROM assessments where ID='$Assessment_ID' and Owner='".$_SESSION['useremail']."'";
		$dbreturn = databasequery($query);
		$owner=count($dbreturn);
		
		echo "<div class='row'>";
			echo "<div class='col l6 s12'>";
				echo "<select class='browser-default' id='filter1'>";
					echo "<option value='course'>View by Course</option>";
					echo "<option value='group'>View by Group</option>";
					if(AdminCheck($_SESSION['useremail']) or superadmin()){ echo "<option value='teacher'>View by Teacher</option>"; }
					if($owner!=0 or superadmin()){ echo "<option value='all'>View All Results</option>"; }
				echo "</select>";
			echo "</div>";
			echo "<div class='col l6 s12'>";
				echo "<select class='browser-default' id='filter2'></select>";				
			echo "</div>";
		echo "</div>";
		echo "<div class='row'><div class='col s12'><div id='p2' class='mdl-progress mdl-js-progress mdl-progress__indeterminate landingloadergrid' style='width:100%;'></div></div></div>";
		echo "<div class='resultsgrid'></div>";
		
	}

?>

<script>
	
	$(function() 
	{
		$(".landingloadergrid").hide();
		$(".resultsgrid").hide();
		$("#filter2").hide();
		
		function ReturnFilterValue()
		{
			var category = $('#filter1').val();		
			if($('#filter2').val() != null) 
			{
				var FilterSelect = $('#filter2').val();
				if(category=='course')
				{
					$(".resultsgrid").load('modules/<?php echo basename(__DIR__); ?>/results_summary_results.php?assessmentid=<?php echo $Assessment_ID; ?>&course='+FilterSelect, function()
					{
						$(".landingloadergrid").hide();
						$(".resultsgrid").show();
						mdlregister();
					});
				}
				if(category=='group')
				{
					$(".resultsgrid").load('modules/<?php echo basename(__DIR__); ?>/results_summary_results.php?assessmentid=<?php echo $Assessment_ID; ?>&groupid='+FilterSelect, function()
					{
						$(".landingloadergrid").hide();
						$(".resultsgrid").show();
						mdlregister();
					});
				}
				if(category=='teacher')
				{
					$(".resultsgrid").load('modules/<?php echo basename(__DIR__); ?>/results_summary_results.php?assessmentid=<?php echo $Assessment_ID; ?>&staffid='+FilterSelect, function()
					{
						$(".landingloadergrid").hide();
						$(".resultsgrid").show();
						mdlregister();
					});
				}
			}
			else
			{
				$(".landingloadergrid").hide();
			}
		}
		
		//Load Page
		$("#filter2").show();
		$("#filter2").load('modules/<?php echo basename(__DIR__); ?>/results_dropdown.php?category=course', function()
		{ 
			ReturnFilterValue();
		});	
		
    	//Filter 1 Change
    	$('#filter1').change(function()
    	{
	    	var category = $(this).val();
			$(".landingloadergrid").show();
			$(".resultsgrid").hide();
			
			if(category!="all")
			{
				$("#filter2").show();
				$("#filter2").load('modules/<?php echo basename(__DIR__); ?>/results_dropdown.php?category='+category, function()
				{ 
					ReturnFilterValue();
				});				
			}
			else
			{
				$("#filter2").hide();
				$(".resultsgrid").load('modules/<?php echo basename(__DIR__); ?>/results_summary_results.php?assessmentid=<?php echo $Assessment_ID; ?>', function()
				{
					$(".landingloadergrid").hide();
					$(".resultsgrid").show();
					mdlregister();
				});
			}

		});	
		
    	//Filter 2 Change
    	$('#filter2').change(function()
    	{
	    	$(".landingloadergrid").show();
	    	ReturnFilterValue();
		});	
			
	});
		
</script>