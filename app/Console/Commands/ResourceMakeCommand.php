<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Toneflix\ResourceModifier\Commands\ResourceMakeCommand as ToneflixResourceMakeCommand;

#[AsCommand(name: 'make:resource')]
class ResourceMakeCommand extends ToneflixResourceMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:resource';
}
