<?php
$python_script = escapeshellcmd("python3 ../python/fraud_preprocessing.py");
$output = shell_exec($python_script);

echo "Python Output: " . $output;
?>


<?php
# $python_script = escapeshellcmd("python3 ../python/fraud_detection.py");
#$output = shell_exec($python_script);

#echo "Fraud detection complete. Check results in: /data/fraud_predictions.csv";
?>
