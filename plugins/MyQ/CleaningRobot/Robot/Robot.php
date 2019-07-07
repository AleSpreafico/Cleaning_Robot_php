<?php

namespace Plugins\MyQ\CleaningRobot\Robot;

class RobotBasicMovements
{
    public $map;
    public $current;
    public $commands;
    public $battery;

    public $control = [
        'N', 'E', 'S', 'W'
    ];

    static function sayHi()
    {
        echo "\nHello!!\n";
    }
    static function sayGoodBye()
    {
        echo "\nThanks For Using me!!\n\n";
    }

    public function getInitialPosition($start)
    {
        $data = [
            'X' => $start['X'],
            'Y' => $start['Y'],
            'facing' => $start['facing']
        ];
        
        return $data;
    }

    public function setCurrentFacing($value)
    {
        echo "Updating current robot facing\n";
        $this->current['facing'] = $this->control[$value];
        return true;
    }

    public function setPosition($axis, $value)
    {
        echo "Set New Position\n";
        switch ($axis) {
            case 'Y':
                $this->current['Y'] = $value;
                break;
            case 'X':
                $this->current['X'] = $value;
                break;
        }

    }

    public function turnLeft()
    {
        $this::updateVisitedBatteryStatus(1);

        $index = array_search($this->current['facing'], $this->control);

        if ($index === 0) {
            $index = 3;
        } else {
            $index -= 1;
        }

        $this::setCurrentFacing($index);

        echo  "Turned Left\n";

        return true;
    }

    public function turnRight()
    {
        $this::updateVisitedBatteryStatus(1);

        $index = array_search($this->current['facing'], $this->control);

        if ($index === 3) {
            $index = 0;
        } else {
            $index += 1;
        }

        $this::setCurrentFacing($index);

        echo  "Turned Right\n";

        return true;
    }

    public function advance()
    {
        switch ($this->current['facing']) {
            case 'N':
                $x_axis = $this->current['X'];
                $y_axis = $this->current['Y'] - 1;
                if ( isset($this->map[$y_axis][$x_axis]) && $this->map[$y_axis][$x_axis] != 'C' ) {                    
                    $this::setPosition('Y', $y_axis);
                } else {
                    $this::backOffStrategy();
                }
                
                break;
            case 'S':
                $x_axis = $this->current['X'];
                $y_axis = $this->current['Y'] + 1;
                if (isset($this->map[$y_axis][$x_axis]) && $this->map[$y_axis][$x_axis] != 'C') {                   
                    $this::setPosition('Y', $y_axis);
                } else {
                    $this::backOffStrategy();
                }
                break;
            case 'W':
                $x_axis = $this->current['X'] - 1;
                $y_axis = $this->current['Y'];
                if (isset($this->map[$y_axis][$x_axis]) && $this->map[$y_axis][$x_axis] != 'C') {                    
                    $this::setPosition('X', $x_axis);
                } else {
                    $this::backOffStrategy();
                }
                break;
            case 'E':
                $x_axis = $this->current['X'] + 1;
                $y_axis = $this->current['Y'];
                if (isset($this->map[$y_axis][$x_axis]) && $this->map[$y_axis][$x_axis] != 'C') {                    
                    $this::setPosition('X', $x_axis);
                } else {
                    $this::backOffStrategy();
                }
                break;
        }
        $this::updateVisitedBatteryStatus(2);

        echo "Advanced\n";
    }

    public function back()
    {
        $this::updateVisitedBatteryStatus(3);

        switch ($this->current['facing']) {
            case 'N':
                $y_axis = $this->current['Y'] + 1;
                $this::setPosition('Y', $y_axis);
                break;
            case 'S':
                $y_axis = $this->current['Y'] - 1;
                $this::setPosition('Y', $y_axis);
                break;
            case 'W':
                $x_axis = $this->current['X'] + 1;
                $this::setPosition('X', $x_axis);
                break;
            case 'E':
                $x_axis = $this->current['X'] - 1;
                $this::setPosition('X', $x_axis);
                break;
        }
    }

    public function backOffStrategy()
    {
        $back_off_commands = [
            ['TR','A','TL'],
            ['TR','A','TR'],
            ['TR','A','TR'],
            ['TR','TB','TR','A'],
            ['TL','TL','A']
        ];

        foreach ($back_off_commands as $key => $commands) {

            $current_location = $this::getInitialPosition($this->current);
            $number_of_sequence = $key+1;
            echo "\n\nOoops! Obstacle Found\n";
            echo "Initializing Back Off Strategy sequence: n${number_of_sequence}\n\n";

            foreach ($commands as $command) {
                switch ($command) {
                    case 'TL':
                        $this::turnLeft();
                        break;
                    case 'TR':
                        $this::turnRight();
                        break;
                    case 'A':
                        $this::advance();
                        $this::updateVisited();
                        break;
                    case 'B':
                        $this::back();
                        $this::updateVisited();
                        break;
                }
            }
            if ($current_location != $this::getInitialPosition($this->current)) {
                break;
            }
            
        }
    }

    public function updateVisitedBatteryStatus($energy_consumed)
    {
        echo "Check Battery Status...\n";
        echo "Consuming: ${energy_consumed} units.\n";
        if ($this->battery < $energy_consumed) {
            echo "Battery Status Too Low\n";
            echo "Shutting Down...\n";
            $this::powerOff('power_error');
        }
        $this->battery -= $energy_consumed;
        echo "Remaining Battery Units: {$this->battery}\n";
        echo "Updating Battery\n";
    }
}