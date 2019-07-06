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
        // TODO remember to validate
        $this::sayHi();
        echo "Validatig User Input\n";
        $this->map = $this->getMap($file_input['map']);
        $this->current = $this->getInitialPosition($file_input['start']);
        $this->commands = $this->getInputCommands($file_input['commands']);
        $this->battery = $this->getBatteryLevel($file_input['battery']);

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

        // $this::sayGoodBye();

        // $result = [
        //     'visited' => $this->visited,
        //     'cleaned' => $this->cleaned,
        //     'final' => $this->current,
        //     'battery' => $this->battery
        // ];

        // $fp = fopen($this->output, 'w');
        // fwrite($fp, json_encode($result, JSON_PRETTY_PRINT));
        // fclose($fp);

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
        // foreach ($this->visited as $key => $coordinates) {
        //     # code...
        // }
        $result = [
            'visited' => $this->visited,
            'cleaned' => $this->cleaned,
            'final' => $this->current,
            'battery' => $this->battery
        ];
        
        $fp = fopen($this->output, 'w');
        fwrite($fp, json_encode($result, JSON_PRETTY_PRINT));
        fclose($fp);

        if ($err) {
            die();
        }

        $this::sayGoodBye();
        die();
    }
}