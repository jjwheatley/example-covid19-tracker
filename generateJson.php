<?php
ini_set('max_execution_time', 300);
set_time_limit(300);

function generateJson(){
    $file = fopen("data/datasets-covid-19/data/time-series-19-covid-combined.csv","r");
    $count = 0;
    $jsonData = [];
    $countryList = [];
    $previousCountry = "";
    $previousDeaths = 0;
    while (($data = fgetcsv($file)) !== FALSE) {
        if($count > 0){
            $countryList[] = $previousCountry = preg_replace('/[\*]+/', '', $data[1]);

            // $row = [
            //     'Country' => $data[1],
            //     'Date' => $data[0],
            //     'Deaths' => $data[7]
            // ];
            // var_dump($data);exit();
            if (isset($jsonData[$data[1]][$data[0]])) {
                // var_dump('test');exit();
                $new =  (int) $data[6] + (int) $jsonData[$data[1]][$data[0]];
                // var_dump($new);exit();
                $jsonData[$data[1]][$data[0]] = $new;
            } else {
                $jsonData[$data[1]][$data[0]] = $data[6];
            }
        
        }
        $count++;
    }

    $countryList = array_flip(array_flip($countryList));
    $countriesShownSettings = [ 
        'China' => ['color' => 'Black'],
        'France' => ['color' => 'Blue'],  
        'Italy' => ['color' => 'Purple'],
        'Korea, South' => ['color' => 'Grey'],
        'Netherlands' => ['color' => 'Orange'], 
        'Spain' => ['color' => 'DarkGreen'],
        'United Kingdom' => ['color' => 'Red'],
        'Germany' => ['color' => 'Brown'],
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