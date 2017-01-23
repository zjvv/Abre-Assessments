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
		
		echo "<div id='displayassessment'>"; include "session_display_all_view.php"; echo "</div>";
		
	}

?>
		
<script>
			
	//Process the profile form
	$(function()
	{
	
		//Delete assessment
		$( ".deletesession" ).unbind().click(function(event) {
			event.preventDefault();
			var result = confirm("Are you sure you want to delete this session? This will remove all assessment data.");
			if (result) {

				//Make the post request
				var address = $(this).attr("href");
				$.ajax({
					type: 'POST',
					url: address,
					data: '',
				})
																
				//Show the notification
				.done(function(response){	
					
					mdlregister();												
					var notification = document.querySelector('.mdl-js-snackbar');
					var data = { message: response };
					notification.MaterialSnackbar.showSnackbar(data);
					
					$('#content_holder').load('modules/<?php echo basename(__DIR__); ?>/session_display_all.php', function() { init_page(); });
						
				})
			}
		});	
					
	});
	
				
</script>