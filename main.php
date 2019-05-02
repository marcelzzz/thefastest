<?php

require_once("vendor/autoload.php");

function secondsToHumanTime($value)
{
    if ($value > 86400) {
        return gmdate("d " . $this->hm->vars->texts['days'] . " H:i:s", $value);
    } elseif ($value > 3600) {
        return gmdate("H:i:s", $value);
    } else {
        return gmdate("i:s", $value);
    }
}

$showResult = false;

if (isset($_REQUEST['process']) && isset($_FILES['fitFile'])) {
    $showResult = true;

    $resultFound = false;

    $targetDistance = (int)$_REQUEST['distance'];

    $fileName = 'fit' . date("His") . random_int(0, 999) . '.fit'; //random file name in case i have multiple uploads at the same time (very unlikely)

    move_uploaded_file($_FILES['fitFile']['tmp_name'], $fileName); //saving the fit file

    $parser = new \adriangibbons\phpFITFileAnalysis($fileName, array('units' => 'raw')); //parsing the file

    unlink($fileName); //deleting the file

    $distances = array(0); //$distances[$time] = distance from start at $time seconds
    $deltaDistances = array(0); //$deltaDistances[$time] = distance traveled from $deltaDistances[$time - 1];

    $startTimestamp = false; //unix timestamp of the start of activity

    /*
    echo "<pre>";
    print_r($parser->data_mesgs['record']['distance']);
    echo "</pre>";
    */

    foreach ($parser->data_mesgs['record']['distance'] as $timestamp => $distance) {
        if ($startTimestamp === false) {
            $startTimestamp = $timestamp - 1; //if I never recorded any timestamp, it means this is the timestamp of the start
        }

        $relativeTime = $timestamp - $startTimestamp; //second passed from the start

        $previousRelativeTime = count($distances) - 1;

        if ($relativeTime - $previousRelativeTime > 1) {
            //if i have more than 1 second between recods it either means the watch skipped a few seconds or the watch was paused
            $gapLength = $relativeTime - $previousRelativeTime;
            $distanceIncrements = ($distance - $distances[$previousRelativeTime]) / $gapLength; 
            //I create fake records to fill the gap; the distance is incremented with the arithmetic mean of the distance over the missing time
            //if the watch was paused it means I will probably stretch a few metersover a ot of seconds - this does not affect the algorithm because I am looking for the best time over a contigous running period; if the activity has a lot of pauses and no contigous run for the target distance, a result will still be found but it will include the pauses lengths
            for ($i = 1; $i < $gapLength; $i++) {
                $distances[$previousRelativeTime + $i] = $distances[$previousRelativeTime] + $distanceIncrements * $i;
                $deltaDistances[$previousRelativeTime + $i] = $distanceIncrements;
            }
        }

        $distances[$relativeTime] = $distance;

        if (isset($distances[$relativeTime - 1])) {
            $deltaDistances[$relativeTime] = $distances[$relativeTime] - $distances[$relativeTime - 1];
        }
    }

    $totalDistance = $distances[count($distances) - 1]; //the total distance of the activity is the last distance record

    if ($totalDistance >= $targetDistance) {
        //I'm searching for a best time only if the target distance is smaller than the entire activity distance
        /*
        echo "<pre>";
        print_r($distances);
        echo "</pre>";
        */

        $startIndex = 0;  //the index where when currentDistance started when it eventually reaches targetDistance
        $currentIndex = 1; //the index used to loop the deltaDistances array
        $currentDistance = 0; //the distance at the current pass

        $minTimeForTargetDistance = false; //the minimum time I am searchin for
        $minFoundAt = false; //the index that minimum time started

        while ($currentIndex < count($deltaDistances)) {
            $currentDistance += $deltaDistances[$currentIndex];

            while ($currentDistance >= $targetDistance) {
                //if i passed the target distance I calculate hom much it took me
                $timeForTargetDistance = $currentIndex - $startIndex;

                if ($minTimeForTargetDistance === false || $timeForTargetDistance < $minTimeForTargetDistance) {
                    //if it's a new minimum I record it and where it started
                    $minTimeForTargetDistance = $timeForTargetDistance;
                    $minFoundAt = $startIndex;
                }

                $startIndex++; //I move the startIndex 1 second further
                $currentDistance -= $deltaDistances[$startIndex]; //and remove the distance traveled in that second from the currentDistance
            }

            $currentIndex++;
        }

        $resultFound = true;
    } 

    $title = "Results";
} else {

    $title = "The fastest";
}
