

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
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
	
	//Check for installation
	if(superadmin()){ require('installer.php'); }
	
	$pageview=1;
	$drawerhidden=1;
	$pageorder=4;
	$pagetitle="Assessments";
	$description="A common assessment tool that helps teachers identify and track student understanding.";
	$version="1.0.9";
	$repo="abreio/Abre-Assessments";
	$pageicon="assessment";
	$pagepath="assessments";
	require_once('permissions.php');
	
	?>
	
<script>
	
	$(function() 
	{

		//Duplicate course
		$(document).on("click", ".duplicateassessment", function ()
		{
			var AssessmentIDDuplicate = $(this).data('assessmentid');
			$.ajax({
				type: 'POST',
				url: 'modules/<?php echo basename(__DIR__); ?>/assessment_duplicate.php',
				data: { assessmentIDduplicateid : AssessmentIDDuplicate }
			})
			.done(function(response) {
				$("#content_holder").load( "modules/<?php echo basename(__DIR__); ?>/assessments_display_all.php", function(){
					mdlregister();
					var notification = document.querySelector('.mdl-js-snackbar');
					var data = { message: response };
					notification.MaterialSnackbar.showSnackbar(data);	
				});
			})
		});
			
	});	
	
		
</script>