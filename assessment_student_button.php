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
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require_once('permissions.php');
	
?>
	
	<div class='fixed-action-btn buttonpin assessmentsubmitbutton'>
		<a class='btn-floating btn-large waves-effect waves-light' style='background-color: <?php echo sitesettings("sitecolor"); ?>' id="createassessmenttooltip" href='#' data-assessmentid='<?php echo $Assessment_ID; ?>'><i class='large material-icons'>check</i></a>
		<div class="mdl-tooltip mdl-tooltip--left" for="createassessmenttooltip">Turn In</div>
	</div>
	
<script>
	
	$(function()
	{
		
 			
		//Load the question
		$("#createassessmenttooltip").unbind().click(function(event)
		{
			event.preventDefault();
			var AssessmentID = $(this).data('assessmentid');

  			var notification = document.querySelector('.mdl-js-snackbar');
			var data = { message: 'Are you sure you want to turn in this assessment?', actionHandler: function(event) {
				
				$.post( "/modules/<?php echo basename(__DIR__); ?>/assessment_student_end.php", { AssessmentID: AssessmentID })
				.done(function( data ) {
					location.reload();
  				});
				
			}, actionText: 'Turn In', timeout:10000 };
			notification.MaterialSnackbar.showSnackbar(data);


 		});
			
	});
	
</script>