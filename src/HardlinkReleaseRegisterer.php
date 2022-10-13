<?php

declare(strict_types=1);

namespace De\SWebhosting\TYPO3Surf;

use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Workflow;
use TYPO3\Surf\Task\SymlinkReleaseTask;

class HardlinkReleaseRegisterer
{
    public function replaceSymlinkWithHardlinkRelease(Workflow $workflow, ?Application $application = null): void
    {
        $workflow->removeTask(SymlinkReleaseTask::class);
        $workflow->addTask(HardlinkReleaseTask::class, 'switch', $application);
    }
}
