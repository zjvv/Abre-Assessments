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
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
	
	//Settings
	if(superadmin())
	{	
		echo "<form id='form-assessments-settings' method='post' enctype='multipart/form-data' action='modules/".basename(__DIR__)."/updatesettings.php'>";
			echo "<div class='page_container page_container_limit mdl-shadow--4dp'>";
				echo "<div class='page'>";
				
				
					$query = "SELECT * FROM assessments_settings";
					$dbreturn = databasequery($query);
					foreach ($dbreturn as $value)
					{
						$Certica_URL=$value["Certica_URL"];
						$Certica_AccessKey=$value["Certica_AccessKey"];
					}
						
					//Settings
					echo "<div class='row'>";
						echo "<div class='col l12'>";
							echo "<div class='input-field col s12'><h5>Certica Solutions</h5><br></div>";
							echo "<div class='input-field col s12'>";
						    	echo "<input placeholder='Enter Certica REST API Base URL' value='$Certica_URL' id='certicabaseurl' name='certicabaseurl' type='text'>";
								echo "<label class='active' for='certicabaseurl'>Certica REST API Base URL</label>";
						    echo "</div>";
							echo "<div class='input-field col s12'>";
						    	echo "<input placeholder='Enter Certica Customer AccessKey' value='$Certica_AccessKey' id='certicaaccesskey' name='certicaaccesskey' type='text'>";
								echo "<label class='active' for='certicaaccesskey'>Certica Customer AccessKey</label>";
						    echo "</div>";
						echo "</div>";  
					echo "</div>";
					
					//Save Button
					echo "<div class='row'>";
						echo "<div class='col s12'><div class='col s12'>";
							echo "<button type='submit' class='modal-action waves-effect btn-flat white-text' style='background-color: ".sitesettings("sitecolor")."'>Save Changes</button>";	
						echo "</div></div>";
					echo "</div>";
					
				echo "</div>";
			echo "</div>";
		echo "</form>";
	}
	
?>

<script>
	
	//Save Settings
	var form = $('#form-assessments-settings');
	$(form).submit(function(event)
	{
		event.preventDefault();
		var data = new FormData($(this)[0]);
		var url = $(form).attr('action');
		$.ajax({ type: 'POST', url: url, data: data, contentType: false, processData: false })
					
		//Show the notification
		.done(function(response)
		{
			location.reload();
		})		
	});
	
</script>