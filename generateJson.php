<?php
ini_set('max_execution_time', 300);
set_time_limit(300);

function generateJson(){
    $file = fopen("data/datasets-covid-19/data/time-series-19-covid-combined.csv","r");
    $count = 0;
    $jsonData = [];
    $countryList = [];
    $previousCountry = "";

    while (($data = fgetcsv($file)) !== FALSE) {
        $country = $data[1];
        $date = $data[0];
        $deaths = $data[6];
        if($count > 0){
            $countryList[] = $previousCountry = preg_replace('/[\*]+/', '', $country);

            if (isset($jsonData[$country][$date])) {
                $newDeaths =  (int) $deaths + (int) $jsonData[$country][$date];
                $jsonData[$country][$date] = $newDeaths;
            } else {
                $jsonData[$country][$date] = (int) $deaths;
            }        
        }
        $count++;
    }

    $countryList = array_flip(array_flip($countryList));
    $countriesShownSettings = [ 
        'China' => ['color' => 'Black', 'population' => 1439323776],
        'France' => ['color' => 'Blue', 'population' => 65273511],  
        'Italy' => ['color' => 'Purple', 'population' => 60484644],
        'Korea, South' => ['color' => 'Grey', 'population' => 51269185],
        'Netherlands' => ['color' => 'Orange', 'population' => 17134872], 
        'Spain' => ['color' => 'DarkGreen', 'population' => 46754778],
        'United Kingdom' => ['color' => 'Red', 'population' => 67886011],
        'Germany' => ['color' => 'Brown', 'population' => 83783942],
    ];
    krsort($countriesShownSettings);
    $order = 0;
    foreach($countriesShownSettings as $key => $v){
        $order++;
        $countriesShownSettings[$key]['order'] = $order;
    }


    $superstructure = [];
    $order = 0;
    foreach($countryList as $country) {
        if(array_key_exists($country, $countriesShownSettings)){
            $structure = [
                "order" => $countriesShownSettings[$country]['order'],
                "name" => $country,
                "show" => true,
                "color" => $countriesShownSettings[$country]['color'],
                "totalDead" => 0,
            ];
            $order++;
            $counter = 0;
            $totalDead = 0;
            foreach($jsonData as $countryKey => $j) {
                if($country == $countryKey){
                    foreach ($j as $key => $value){
                        if ($value > 10){
                            $row = [
                                'date' => $key,
                                'day' => $counter,
                                'deaths' => (int) $value,
                                'deathsPerMillion' => (int) $value / (int) $countriesShownSettings[$country]['population'] * 1000000,
                                'deathsOnDay' => (int) $value - $totalDead,
                            ];
                            if($row['deaths'] >= 10){
                                $history[] = $row;
                            }
                            $structure["history"][] = $row;
                            $totalDead = $value;
                            $counter++;
                        }
                    }  
                }
            }
            $structure['totalDead'] = (int) $totalDead;
            $superstructure[] = $structure;
        }
    }
    return $superstructure;
}
$returnedArray = generateJson();
$NewJSON = fopen('covid.json', 'w');
fwrite($NewJSON, json_encode($returnedArray));
fclose($NewJSON);
echo "Finished!\n";