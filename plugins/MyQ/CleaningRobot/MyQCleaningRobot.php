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
                    echo "\n\nExecuting Turn Left\n";
                    $this::turnLeft();
                    break;
                case 'TR':
                    echo "\n\nExecuting Turn Right\n";
                    $this::turnRight();
                    break;
                case 'A':
                // TODO: Validation HERE
                    echo "\n\nExecuting Advance\n";
                    $this::advance();
                    $this::updateVisited();
                    break;
                case 'B':
                    echo "\n\nExecuting Go Back\n";
                    $this::back();
                    $this::updateVisited();
                    break;
                case 'C':
                    echo "\n\nExecuting Clean\n";
                    $this::clean();
                    break;
            }
        }

        $this::sayGoodBye();

        $result = [
            'visited' => $this->visited,
            'cleaned' => $this->cleaned,
            'final' => $this->current,
            'battery' => $this->battery
        ];

        // echo json_encode($result);
        // file_put_contents($this->output, json_encode($result, JSON_PRETTY_PRINT));
        $fp = fopen($this->output, 'w');
        fwrite($fp, json_encode($result, JSON_PRETTY_PRINT));
        fclose($fp);
    }

    public function updateVisited()
    {
        echo "Updating Visited Cells\n";
        array_push($this->visited, [
            'X' => $this->current['X'],
            'Y' => $this->current['Y']
        ]);
    }

    public function showVisited()
    {
        echo json_encode($this->visited);
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


}