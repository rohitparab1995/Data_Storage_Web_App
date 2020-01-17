<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
   <title>S3 Uploader</title> 
   <link rel="stylesheet" href="css/style2.css">  
</head>
<body>
<div class="container">  
  <div class="centered">
  <h1><span style="color:#303235;">S3 Uploader</span></h1><br/>
  <form action="" method="post" enctype="multipart/form-data">
  <input class="button button3" name="theFile" type="file" />
  <input class="button button3" name="Submit" type="submit" value="Upload">
</form>
<br>

<form action="list.php" method="post" enctype="multipart/form-data">
    <input class="button button3" name="list" type="submit" value="View Uploaded Files">
</form>

<br>
 <span style="color:#303235;"> 
  <div id="status"></div><br>
  <div id="successful"></div>
</span>
  
  </div>
</div>
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

try{ 
if(isset($_POST['Submit'])){

    //retreive post variables
    $fileName = $_FILES['theFile']['name'];
    $fileTempName = $_FILES['theFile']['tmp_name'];



$userfolder=$email."_Contents";

 $request_status = $s3->putObject([ 
 'Bucket' => $config['s3-access']['bucket'], 
 'Key' => $userfolder.'/'.$fileName, # folder for each user will be on s3 (this would be automatically created) 
 'Body' => fopen($fileTempName, 'rb'), 
 'ACL' => $config['s3-access']['private-acl'],

'@http' => [
        'progress' => function ($downloadTotalSize, $downloadSizeSoFar, $uploadTotalSize, $uploadSizeSoFar) {
            ?>
	
<script>
document.getElementById("status").innerHTML="<progress value=<?php echo $uploadSizeSoFar?> max=<?php echo $uploadTotalSize?>></progress>";
</script>


<?php
        }
    ]

 
 ]); 
 
 # printing result 
 ?>
 

<script>
document.getElementById("successful").innerHTML="Data Uploaded Successfully.";
</script>

<?php

}
}
catch(Exception $ex){ 
 
?>
<script>
document.getElementById("successful").innerHTML="Please select a file.";

</script> 
<?php

} 


?>


</body>
</html>
