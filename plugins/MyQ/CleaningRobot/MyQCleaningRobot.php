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
        $this::updateVisited($this->current);
        $this::turnLeft();
        $this::advance();
        echo json_encode($this->commands);
    }

    public function updateVisited($current)
    {
        echo "Updating Visited Cells\n";
        array_push($this->visited, [
            'X' => $current['X'],
            'Y' => $current['Y']
        ]);
    }

    public function showVisited()
    {
        echo json_encode($this->visited);
    }


}