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
		
		//Assessment Lookup
		$sql = "SELECT * FROM assessments where ID='$Assessment_ID'";
		$result = $db->query($sql);
		while($row = $result->fetch_assoc())
		{
			$AssessmentOwner=htmlspecialchars($row["Owner"], ENT_QUOTES);
			$AssessmentTitle=htmlspecialchars($row["Title"], ENT_QUOTES);
			if($_SESSION['useremail']==$AssessmentOwner){ $owner=1; }else{ $owner=0; }
		}
		
		echo "<div class='row' style='margin-top:-10px;'>";
			echo "<div class='col s12'><h4>$AssessmentTitle</h4></div>";
		echo "</div>";
		
		echo "<div class='row' style='margin-top:-40px;'>";
			echo "<div class='col l4 s12'>";
				echo "<select id='filter1'>";
					echo "<option value='' disabled selected>Choose a View</option>";
					echo "<option value='course'>View by Course</option>";
					echo "<option value='group'>View by Group</option>";
					if(AdminCheck($_SESSION['useremail']) or superadmin()){ echo "<option value='teacher'>View by Teacher</option>"; }
					if($owner!=0 or superadmin()){ echo "<option value='all'>View All Results</option>"; }
				echo "</select>";
			echo "</div>";
			echo "<div class='col l4 s12'>";
				echo "<select id='filter2'></select>";
			echo "</div>";
			echo "<div class='col l4 s12'>";
				echo "<select id='filter3'></select>";				
			echo "</div>";
		echo "</div>";
		
		echo "<div class='row' id='reloadbutton' style='margin-top:-40px; margin-left:5px;'>";
				echo "<button class='modal-action waves-effect btn-flat white-text' id='reload' style='margin-left:5px; background-color:"; echo sitesettings("sitecolor"); echo "'>Refresh Results</button>";
		echo "</div>";
		
		
		echo "<div class='row'><div class='col s12'><div id='p2' class='mdl-progress mdl-js-progress mdl-progress__indeterminate landingloadergrid' style='width:100%;'></div></div></div>";
		echo "<div class='row' style='margin-top:-20px;'><div class='resultsgrid'></div></div>";
		
	}

?>

<script>
	
	$(function() 
	{
		
		$('select').material_select();
		
		$(".landingloadergrid").hide();
		$(".resultsgrid").hide();
		$("#reloadbutton").hide();
		
		function Loading()
		{
			$(".landingloadergrid").show();
			$(".resultsgrid").empty();
		}
		
		function Ready()
		{
			$(".landingloadergrid").hide();
			$(".resultsgrid").show();
			$('select').material_select();
			mdlregister();
		}
		
		function UpdateResults(Filter1,Filter2,Filter3)
		{
			if(Filter1=="all")
			{
				$(".resultsgrid").load('modules/Abre-Assessments/results_summary_results.php?assessmentid=<?php echo $Assessment_ID; ?>', function(){ Ready(); });
			}
			else if(Filter1=="course")
			{
				if(Filter2==null)
				{
					$("#filter2").load('modules/<?php echo basename(__DIR__); ?>/results_dropdown.php?category=course', function(){ Ready(); });
				}
				else
				{
					$(".resultsgrid").load('modules/<?php echo basename(__DIR__); ?>/results_summary_results.php?assessmentid=<?php echo $Assessment_ID; ?>&course='+Filter2, function(){ Ready(); });
				}			
			}
			else if(Filter1=="group")
			{
				if(Filter2==null)
				{
					$("#filter2").load('modules/<?php echo basename(__DIR__); ?>/results_dropdown.php?category=group', function(){ Ready(); });
				}
				else
				{
					$(".resultsgrid").load('modules/<?php echo basename(__DIR__); ?>/results_summary_results.php?assessmentid=<?php echo $Assessment_ID; ?>&groupid='+Filter2, function(){ Ready(); });
				}			
			}
			else if(Filter1=="teacher")
			{
				if(Filter2==null)
				{
					$("#filter2").load('modules/<?php echo basename(__DIR__); ?>/results_dropdown.php?category=teacher', function(){ Ready(); });
				}
				else
				{
					if(Filter3==null)
					{
						$("#filter3").load('modules/<?php echo basename(__DIR__); ?>/results_dropdown.php?category=courseteacher&staffid='+Filter2, function(){ Ready(); });
					}
					else
					{
						$(".resultsgrid").load('modules/<?php echo basename(__DIR__); ?>/results_summary_results.php?assessmentid=<?php echo $Assessment_ID; ?>&course='+Filter3+'&staffidpass='+Filter2, function(){ Ready(); });
					}
				}			
			}
			else
			{
				Ready();
			}
		}
		
    	//Filter Change
    	$('#filter1').change(function(){ $("#filter2").empty(); $("#filter3").empty(); });
    	$('#filter2').change(function(){ $("#filter3").empty(); });
    	$('#filter1,#filter2,#filter3').change(function()
    	{
	    	$("#reloadbutton").show();
	    	var Filter1 = $('#filter1').val();
	    	var Filter2 = $('#filter2').val();
	    	var Filter3 = $('#filter3').val();
			Loading();
	    	UpdateResults(Filter1,Filter2,Filter3);
		});
		
		//Reload Button
		$("#reload").unbind().click(function(){
			var Filter1 = $('#filter1').val();
	    	var Filter2 = $('#filter2').val();
	    	var Filter3 = $('#filter3').val();
			Loading();
			UpdateResults(Filter1,Filter2,Filter3);
		});

			
	});
		
</script>