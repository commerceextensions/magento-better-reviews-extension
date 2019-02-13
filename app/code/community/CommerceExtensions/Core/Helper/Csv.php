<?php

class CommerceExtensions_Core_Helper_Csv extends Mage_Core_Helper_Abstract
{	
  public function createCsv($tablename,$collection)
  {  
	  $table     = $tablename;
	  $fields    = array_keys($collection->getFirstItem()->getData());
	  $file_name = $tablename.'.csv';
	  $separator = ",";
	  
	  if (!empty($fields)) {
	  
		  $fields = is_array($fields) ? $fields : array($fields);
		  
		  $list = $collection->toArray();											  		  		  
		  $header = array();
		  
		  for($i = 0; $i < count($fields); $i++) {				
			  $header[] = $fields[$i];				
		  }
		  
		  $header = implode($separator, $header);					
		  
		  $data = $header."\n";

		  foreach($list['items'] as $row) {			  
			  $line = null;
			  foreach($row as $value) {
				  if ($this->isEmpty($value)) {
					  $value = $separator;
				  } else {
					  $value = str_replace('"', '""', $value);
					  $value = '"'.$value.'"'.$separator;
				  }
				  $line .= $value;
			  }
			  $data .= rtrim(trim($line),$separator)."\n";
		  }
		  
		  $data = str_replace("\r", "", $data);
		  return $data;
	  }		  
  }
  
  public function createCsvFromArray($array)
  {  
  	  $firstKey  = key($array);
	  $fields    = array_keys($array[$firstKey]);
	  $separator = ",";
	  
	  if (!empty($fields)) {
	  
		  $fields = is_array($fields) ? $fields : array($fields);
		  
		  $list = $array;											  		  		  
		  $header = array();
		  
		  for($i = 0; $i < count($fields); $i++) {				
			  $header[] = $fields[$i];				
		  }
		  
		  $header = implode($separator, $header);					
		  
		  $data = $header."\n";

		  foreach($list as $row) {			  
			  $line = null;
			  foreach($row as $value) {
				  if ($this->isEmpty($value)) {				  
					  $value = $separator;
				  } else {
					  $value = str_replace('"', '""', $value);
					  $value = '"'.$value.'"'.$separator;
				  }
				  $line .= $value;
			  }
			  $data .= rtrim(trim($line),$separator)."\n";
		  }
		  
		  $data = str_replace("\r", "", $data);
		  return $data;
	  }		  
  }  
  
  public function isEmpty($value = null) 
  {	  
	  return empty($value) ? true : false;	  
  }  	
	
  public function getExtension($csv)
  {		
	$name = explode('.',$csv['name']);
	return end($name);		
  }
  
  public function getErrors($csv)
  {	  
	return $csv['error'];	  
  }
  
  public function getContentNoHeaders($csv)
  {
	$file = $csv['tmp_name'];
	if (($handle = fopen($file, "r")) !== false) {		
		$out  = array();														
		$line = 1;
		
		while (($row = fgetcsv($handle, 0, ',', '"')) !== FALSE) {			
		  foreach($row as $key => $value) {				
			  $out[$line][$key] = $value;		
		  }			
		  $line++;		  
		}		
		fclose($handle);
	}	
	return $out;	  	  
  }
  
  public function getContent($csv)
  {	  
	$file = $csv['tmp_name'];
	if (($handle = fopen($file, "r")) !== false) {		
		$keys = array();
		$out  = array();														
		$line = 1;
		
		while (($row = fgetcsv($handle, 0, ',', '"')) !== FALSE) {			
		  foreach($row as $key => $value) {				
			if ($line === 1)
			  $keys[$key] = trim($value);
			else
			  $out[$line][$key] = $value;		
		  }			
		  $line++;		  
		}		
		fclose($handle);
	}	
	return array('columns' => $keys, 'data' => $out);	  
  }
  
  public function getDataColumnsAsKey($csv)
  {	  
	$csv = $this->getContent($csv);
	$columns = array_values($csv['columns']);
	foreach($csv['data'] as $key => $value) {		
	  foreach($value as $v_key => $v_value) {										
		$values[$key][$columns[$v_key]] = $v_value;					
	  }				
	}
	return $values;				  
  }
  
  public function hasRequiredColumns($columns,array $requiredColumns)
  {
	$missingColumns = array_diff($requiredColumns,$columns);
	return empty($missingColumns) ? true : false;
  }
      	
}