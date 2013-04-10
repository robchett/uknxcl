<?php
function new_graph($height, $width) {
    echo "<canvas id='b' height='$height px' width='$width px' style='border:1px solid silver'></canvas>";
    $height -= 20;
    $width -= 40;
    $numberOfFlights = array();
    $totalScore = array();
    for ($i = 1991; $i < 2012; $i++) {
        $result = execute("SELECT COUNT(ID) FROM flight WHERE Season=$i");
        $t = mysql_fetch_array($result);
        $numberOfFlights [] = $t [0];
        $result = execute("SELECT Score FROM flight WHERE Season=$i");
        $t = mysql_fetch_array($result);
        $totalScore [$i - 1991] = $t [0];
        while (($t = mysql_fetch_array($result)) != false) {
            $totalScore [$i - 1991] += $t [0];
        }
    }
    $max1 = max($numberOfFlights);
    $min1 = 0;
    $dif1 = $height / ($max1);
    $out1 = "context.moveTo(20," . ((-$numberOfFlights [0] * $dif1) + $height) . ");\n";
    foreach ($numberOfFlights as $b => $a) {
        $out1 .= "context.lineTo(($b*$width/20)+20," . ((-$a * $dif1) + $height) . ");\n";
    }
    $max2 = max($totalScore);
    $min2 = 0;
    $dif2 = $height / ($max2 - $min2);
    $out2 = "context.moveTo(20," . ((-$totalScore [0] * $dif2) + $height) . ");\n";
    foreach ($totalScore as $a => $b) {
        $out2 .= "context.lineTo(($a*$width/20)+20," . ((-$b * $dif2) + $height) . ");\n";
    }
    echo "<br/>
<script type='text/javascript'>
    var b_canvas = document.getElementById('b');
    var context = b_canvas.getContext('2d');
        for (var x = 20.5; x < $width; x += ($width/20)) {
          context.moveTo(x, 0);
          context.lineTo(x, $height);
        }
        for (var y = 0.5; y < $height; y += 25) {
          context.moveTo(20, y);
          context.lineTo($width+20, y);
        }
    context.strokeStyle = '#eee';
    context.stroke();

    context.beginPath();
    context.moveTo(20, 0);
    context.lineTo(20, $height);
    context.lineTo($width+20, $height);
    context.lineTo($width+20, 0);
    context.strokeStyle = '#000';
    context.stroke();

    context.beginPath();

    $out1

    context.strokeStyle = '#F00';
    context.stroke();

    context.beginPath();
    context.moveTo(0, $height);

    $out2

    context.strokeStyle = '#00F';
    context.stroke();

    context.font = 'bold 12px sans-serif';
    context.fillStyle = '#F00';
    context.fillText('$max1', 0, 10);
    context.fillText('$min1', 0, $height);
    context.fillText('Number of flight', 10, $height/2);

    context.fillStyle = '#00F'
    context.fillText('$max2', $width-20, 10);
    context.fillText('$min2', $width+25, $height);
    context.fillText('Points scored', $width-50, ($height/2));

    context.fillStyle = '#000';
    for(i=91;i<100;i++){
        context.fillText(\"'\"+i, ($width/20)*(i-91)+10, $height+15);
    }
    context.fillStyle = '#000';
    for(i=00;i<12;i++){
        context.fillText(\"'\"+i, ($width/20)*(i+9)+10, $height+15);
    }

    context.fillText('', $width/2-20, $height);
</script>";
}

?>
