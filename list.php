<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  
  <link rel="stylesheet" href="css/style2.css">
</head>

<body>
<div class="bgImage"></div>
<svg class="blobCont">
		<image xlink:href="http://html-color.org/pt/ECDEDE.jpg" mask="url(#mask)" width="100%" height="100%" preserveAspectRatio="xMidYMid slice" />
		<defs>
			<mask id="mask" x="0" y="0">
				<g style="filter: url(#gooey)">
          <circle class="blob" cx="10%" cy="10%" r="80" fill="white" stroke="white"/>
					<circle class="blob" cx="50%" cy="10%" r="40" fill="white" stroke="white"/>
					<circle class="blob" cx="17%" cy="15%" r="70" fill="white" stroke="white"/>
					<circle class="blob" cx="90%" cy="20%" r="120" fill="white" stroke="white"/>
					<circle class="blob" cx="30%" cy="25%" r="30" fill="white" stroke="white"/>
          <circle class="blob" cx="50%" cy="60%" r="80" fill="white" stroke="white"/>
					<circle class="blob" cx="70%" cy="90%" r="10" fill="white" stroke="white"/>
					<circle class="blob" cx="90%" cy="60%" r="90" fill="white" stroke="white"/>
					<circle class="blob" cx="30%" cy="90%" r="80" fill="white" stroke="white"/>
          <circle class="blob" cx="10%" cy="10%" r="80" fill="white" stroke="white"/>
					<circle class="blob" cx="50%" cy="10%" r="20" fill="white" stroke="white"/>
					<circle class="blob" cx="17%" cy="15%" r="70" fill="white" stroke="white"/>
					<circle class="blob" cx="40%" cy="20%" r="120" fill="white" stroke="white"/>
					<circle class="blob" cx="30%" cy="25%" r="30" fill="white" stroke="white"/>
          <circle class="blob" cx="80%" cy="60%" r="80" fill="white" stroke="white"/>
					<circle class="blob" cx="17%" cy="10%" r="100" fill="white" stroke="white"/>
					<circle class="blob" cx="40%" cy="60%" r="90" fill="white" stroke="white"/>
					<circle class="blob" cx="10%" cy="50%" r="80" fill="white" stroke="white"/>
				</g>
			</mask>
			<filter id="gooey" height="130%">
				<feGaussianBlur in="SourceGraphic" stdDeviation="15" result="blur" />
				<feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -7" result="goo" />
    	</filter>
		</defs>
</svg>	


<?php 

# initializes everything 
require_once __DIR__.'/initialize.php';

session_start();
$email = $_SESSION['email'];
$userfolder=$email."_Contents";


function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    // $bytes /= pow(1024, $pow);
     $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

# lets list our files on s3 
	try{ 
 		# initializing our object 
 		$files = $s3->getIterator('ListObjects', [ # this is a Generator Object (its yields data rather than returning) 
 		'Bucket' => $config['s3-access']['bucket'],
		'Prefix' => $userfolder.'/' 
 		]); 
?>

<div class="container"> <br><br>
	<h2 style="color:#303235; font-family:Cooper Black;">The Contents of Your Folder<br/></h2> <br>  
  	
	<div class="table-responsive" style="max-height:80%;">          
  	<table class="table">
    		<thead>
      			<tr style="color:#303235; font-family:Britannic Bold; font-size:20px;">
        		<th>File Name</th>
        		<th>File Size</th>
        		<th> Download Link</th>
      			</tr>
    		</thead>
    		<tbody>


<?php
 # printing our data 
 foreach($files as $file)
{ 
	 # whether file is private or not 
 	$file_acl = $s3->getObjectAcl([ 
 	'Bucket' => $config['s3-access']['bucket'], 
 	'Key' => $file['Key'] 
	 ]); 
	
	$object_details=$s3->getObject([ 
 	'Bucket' => $config['s3-access']['bucket'], 
 	'Key' => $file['Key'] 
	 ]);

 	$is_private = true; 
 	foreach($file_acl['Grants'] as $grant){ 
 	if( 
 	isset($grant['Grantee']['URI']) && 
 	$grant['Grantee']['URI'] == 'http://acs.amazonaws.com/groups/global/AllUsers' && 
 	$grant['Permission'] == 'READ' 
 	) $is_private = false; # this file is not private 
 	} 
 
 	# applicable url 
 	if($is_private == false){ 
 	$file_url = $s3->getObjectUrl($config['s3-access']['bucket'], $file['Key']); 
 	}else{ 
 	$url_creator = $s3->getCommand('GetObject', [ 
 	'Bucket' => $config['s3-access']['bucket'], 
 	'Key' => $file['Key'] 
 	]); 
 	$file_url = $s3->createPresignedRequest($url_creator, '+2 minutes')->getUri(); 
 	} 
 
 
?>



      			<tr style="color:#303235; font-family:Centaur; font-size:20px;">
        		<td>
			<?php 
			$fileName = explode("/",$file['Key']);
 			printf("%s",$fileName[count($fileName)-1] ); 
	    		?>
			</td>

        		<td>
			<?php
			printf("%s",formatBytes($object_details["ContentLength"]));
			// printf("%s",$is_private ? 'Private' : 'Public');
			?>
			</td>
        		
			<td>
			<a style="color:#303235;" href="<?php echo $file_url;?>" download>  < DOWNLOAD >  </a>
			</td>
      			</tr>
    


<?php
 }
?>
		</tbody>
  	</table>
  </div>
</div>

<?php 
}catch(Exception $ex){ 
 echo "Error Occurred\n", $ex->getMessage(); 
} 

?>