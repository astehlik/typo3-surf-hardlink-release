<?php

declare(strict_types=1);

namespace De\SWebhosting\TYPO3Surf\Tests\Unit;

use De\SWebhosting\TYPO3Surf\HardlinkReleaseRegisterer;
use De\SWebhosting\TYPO3Surf\HardlinkReleaseTask;
use PHPUnit\Framework\TestCase;
use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Workflow;
use TYPO3\Surf\Task\SymlinkReleaseTask;

/**
 * @covers HardlinkReleaseRegisterer
 */
class HardlinkReleaseRegistererTest extends TestCase
{
    public function test()
    {
        $workflow = $this->createMock(Workflow::class);
        $application = $this->createMock(Application::class);

        $workflow->expects($this->once())
            ->method('removeTask')
            ->with(SymlinkReleaseTask::class);

        $workflow->expects($this->once())
            ->method('addTask')
            ->with(HardlinkReleaseTask::class, 'switch', $application);

        $registerer = new HardlinkReleaseRegisterer();
        $registerer->replaceSymlinkWithHardlinkRelease($workflow, $application);
    }
}
