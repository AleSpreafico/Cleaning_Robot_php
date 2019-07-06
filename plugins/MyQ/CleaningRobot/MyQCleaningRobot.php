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

        
    }

    public function start()
    {
        $this::updateVisited();

        foreach ($this->commands as $command) {
            switch ($command) {
                case 'TL':
                    $this::turnLeft();
                    break;
                case 'TR':
                    $this::turnRight();
                    break;
                case 'A':
                // TODO: Validation HERE
                    $this::advance();
                    $this::updateVisited();
                    break;
                case 'B':
                    $this::back();
                    $this::updateVisited();
                    break;
                case 'C':
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

        echo json_encode($result);

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
        echo "Check Battery Status...\n";
        if ($this->battery < 5) {
            echo "Battery Status Too Low\n";
            return false;
        }

        echo "Updating Battery\n";
        $this->battery -= 5;

        echo "Updating Cleaned Cells\n";
        array_push($this->cleaned, [
            'X' => $this->current['X'],
            'Y' => $this->current['Y']
        ]);
    }


}