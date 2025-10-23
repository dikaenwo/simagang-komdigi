<?php
/**
 * Menu Items
 * All Project Menu
 * @category  Menu List
 */

class Menu{
	
	
			public static $navbarsideleft = array(
		array(
			'path' => 'home', 
			'label' => 'Home', 
			'icon' => ''
		),
		
		array(
			'path' => 'absen_masuk', 
			'label' => 'Absen Masuk', 
			'icon' => ''
		),
		
		array(
			'path' => 'users', 
			'label' => 'Users', 
			'icon' => ''
		),
		
		array(
			'path' => 'laporan', 
			'label' => 'Laporan', 
			'icon' => ''
		)
	);
		
	
	
			public static $role = array(
		array(
			"value" => "magang", 
			"label" => "magang", 
		),
		array(
			"value" => "admin", 
			"label" => "admin", 
		),);
		
			public static $jenis_kelamin = array(
		array(
			"value" => "Laki-Laki", 
			"label" => "Laki-Laki", 
		),
		array(
			"value" => "Perempuan", 
			"label" => "Perempuan", 
		),);
		
			public static $jenjang_pendidikan = array(
		array(
			"value" => "S1/D4", 
			"label" => "S1/D4", 
		),
		array(
			"value" => "SMA/SMK", 
			"label" => "SMA/SMK", 
		),);
		
}