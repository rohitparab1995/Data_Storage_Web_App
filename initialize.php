<?php
# composer dependencies 
require __DIR__.'/vendor/autoload.php';

$config = [
 's3-access' => [
 'key' => 'AKIAIEQUVS3UGLSV77SQ',
 'secret' => '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',
 'bucket' => 'rohitparab',
 'region' => 'us-east-1',
 'version' => 'latest',
 'acl' => 'public-read',
 'private-acl' => 'private'
 ]
];

# initializing s3 
$s3 = Aws\S3\S3Client::factory([
 'credentials' => [
 'key' => $config['s3-access']['key'],
 'secret' => $config['s3-access']['secret']
 ],
 'version' => $config['s3-access']['version'],
 'region' => $config['s3-access']['region']
]);
?>

