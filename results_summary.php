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
	require(dirname(__FILE__) . '/../../configuration.php'); 
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php'); 
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
	require_once('../../core/abre_functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
		
		$Assessment_ID=htmlspecialchars($_GET["assessmentid"], ENT_QUOTES);
		
		echo "<div class='row'>";
			echo "<div class='col l6 s12'>";
				echo "<select class='browser-default' id='filter1'>";
					echo "<option value='course'>View by Course</option>";
					echo "<option value='group'>View by Group</option>";
					if(superadmin()){ echo "<option value='all'>View All Results</option>"; }
				echo "</select>";
			echo "</div>";
			echo "<div class='col l6 s12'>";
				echo "<select class='browser-default' id='filter2'>";
				echo "</select>";				
			echo "</div>";
		echo "</div>";
		echo "<div id='p2' class='mdl-progress mdl-js-progress mdl-progress__indeterminate landingloadergrid' style='width:100%;'></div>";
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
					});
				}
				else
				{
					$(".resultsgrid").load('modules/<?php echo basename(__DIR__); ?>/results_summary_results.php?assessmentid=<?php echo $Assessment_ID; ?>&groupid='+FilterSelect, function()
					{
						$(".landingloadergrid").hide();
						$(".resultsgrid").show();
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