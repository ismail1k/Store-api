<?php

namespace App\Listeners;

use \Orphans\GitDeploy\Events\GitDeployed;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;

class GitDeployedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        // Here you can setup something
    }

    /**
     * Handle the event.
     *
     * @param  ReactionAdded  $event
     * @return void
     */
    public function handle(GitDeployed $gitDeployed)
    {
        // Do some magic with event data $gitDeployed contains the commits

    }
}
