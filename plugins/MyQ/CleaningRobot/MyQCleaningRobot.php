<?php

namespace Plugins\MyQ\CleaningRobot;

use Plugins\MyQ\CleaningRobot\Robot\RobotBasicMovements;

class MyQCleaningRobot extends RobotBasicMovements
{

    public $visited = [];
    public $cleaned = [];


    public $output;

    function __construct($file_input, $file_output)
    {
        ob_start();
        $this::sayHi();
        echo "Validatig User Input\n";
        $this->map = $file_input['map'];
        $this->current = $file_input['start'];
        $this->commands = $file_input['commands'];
        $this->battery = $file_input['battery'];

        echo "User Input OK!\n";

        $this->output = $file_output;        
    }

    public function start()
    {
        $this::updateVisited();

        foreach ($this->commands as $command) {
            switch ($command) {
                case 'TL':
                    echo "\nExecuting Turn Left\n";
                    $this::turnLeft();
                    break;
                case 'TR':
                    echo "\nExecuting Turn Right\n";
                    $this::turnRight();
                    break;
                case 'A':
                // TODO: Validation HERE
                    echo "\nExecuting Advance\n";
                    $this::advance();
                    $this::updateVisited();
                    break;
                case 'B':
                    echo "\nExecuting Go Back\n";
                    $this::back();
                    $this::updateVisited();
                    break;
                case 'C':
                    echo "\nExecuting Clean\n";
                    $this::clean();
                    break;
            }
        }
        $this::powerOff();
    }

    public function updateVisited()
    {
        echo "Updating Visited Cells\n";
        array_push($this->visited, [
            'X' => $this->current['X'],
            'Y' => $this->current['Y']
        ]);
    }

    public function clean()
    {
        $this::updateVisitedBatteryStatus(5);

        echo "Updating Cleaned Cells\n";
        array_push($this->cleaned, [
            'X' => $this->current['X'],
            'Y' => $this->current['Y']
        ]);
    }

    public function powerOff($err=null)
    {
        $this::createJSON();

        if ($err) {
            die();
        }

        $this::createLOG();
        $this::sayGoodBye();
        ob_end_clean();
        die();
    }

    public function createJSON()
    {
        echo "Creating Final Values...\n";
        $result = [
            'visited' => array_unique($this->visited, SORT_REGULAR),
            'cleaned' => array_unique($this->cleaned, SORT_REGULAR),
            'final' => $this->current,
            'battery' => $this->battery
        ];

        echo "Creating Final JSON file...\n";
        $fp = fopen($this->output, 'w');

        echo "Writing Contents...\n";
        fwrite($fp, json_encode($result, JSON_PRETTY_PRINT));
        fclose($fp);

        return true;
    }

    public function createLOG()
    {
        $var = ob_get_contents();       

        $fp = fopen('debug.log', 'w');
        fwrite($fp, print_r($var, true));
        fclose($fp);

        return true;
    }
}