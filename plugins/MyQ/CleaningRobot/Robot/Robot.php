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

    public function getInputCommands($commands)
    {
        return $commands;
    }

    public function getBatteryLevel($battery)
    {
        return $battery;
    }

    public function getMap($map)
    {
        return $map;
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
        echo "Check Battery Status...\n";
        if ($this->battery < 1) {
            echo "Battery Status Too Low\n";
            return false;
        }

        echo "Updating Battery\n";
        $this->battery -= 1;

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
        echo "Check Battery Status...\n";
        if ($this->battery < 1) {
            echo "Battery Status Too Low\n";
            return false;
        }

        echo "Updating Battery\n";
        $this->battery -= 1;

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
        echo "Check Battery Status...\n";
        if ($this->battery < 2) {
            echo "Battery Status Too Low\n";
            return false;
        }

        echo "Updating Battery\n";
        $this->battery -= 2;

        switch ($this->current['facing']) {
            case 'N':
                $y_axis = $this->current['Y'] - 1;
                $this::setPosition('Y', $y_axis);
                break;
            case 'S':
                $y_axis = $this->current['Y'] + 1;
                $this::setPosition('Y', $y_axis);
                break;
            case 'W':
                $x_axis = $this->current['X'] - 1;
                $this::setPosition('X', $x_axis);
                break;
            case 'E':
                $x_axis = $this->current['X'] + 1;
                $this::setPosition('X', $x_axis);
                break;
        }

        echo json_encode($this->current)."\n";
    }

    public function back()
    {
        
    }
}