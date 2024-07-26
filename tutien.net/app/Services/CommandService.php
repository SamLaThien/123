<?php

namespace App\Services;

class CommandService
{
    public function runBackgroundCommand($command, $arguments = null)
    {
        shell_exec($this->getFullCommand($command, $arguments) . '> /dev/null 2>/dev/null &');
    }

    public function runCommand($command, $arguments)
    {
        shell_exec($this->getFullCommand($command, $arguments));
    }

    public function getFullCommand($command, $arguments)
    {
        return 'php ' . $this->getArtisanPath() . ' ' . $command . ' ' . $this->extractArguments($arguments);
    }

    private function getArtisanPath()
    {
        return base_path() . '/artisan';
    }

    private function extractArguments($arguments)
    {
        if (!is_array($arguments)) {
            return $arguments;
        }

        $str = '';
        foreach ($arguments as $key => $argument) {
            if (is_string($key)) {
                $str .= $key . '="' . $argument . '" ';
            } else {
                $str .= '"' . $argument . '" ';
            }
        }

        return $str;
    }
}
