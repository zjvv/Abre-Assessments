

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
	
	//Create assessment table
	if(!$resultcurriculum = $db->query("SELECT * FROM assessments")){ }
	$db->close();
	
	$pageview=1;
	$drawerhidden=1;
	$pageorder=4;
	$pagetitle="Assessments";
	$description="An assessment creator and delivery system.";
	$version="1.0.1";
	$repo="abreio/Abre-Assessments";
	$pageicon="assessment";
	$pagepath="assessments";