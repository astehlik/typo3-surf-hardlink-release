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
 * @covers \De\SWebhosting\TYPO3Surf\HardlinkReleaseRegisterer
 */
class HardlinkReleaseRegistererTest extends TestCase
{
    /**
     * @param $workflow
     * @param $application
     * @return void
     */
    public function callRegisterer($workflow, $application): void
    {
        $registerer = new HardlinkReleaseRegisterer();
        $registerer->replaceSymlinkWithHardlinkRelease($workflow, $application);
    }

    public function testHardLinkReleaseTaskIsRegistered(): void
    {
        $workflow = $this->createMock(Workflow::class);
        $application = $this->createMock(Application::class);

        $workflow->expects(self::once())
            ->method('addTask')
            ->with(HardlinkReleaseTask::class, 'switch', $application);

        $this->callRegisterer($workflow, $application);
    }

    public function testSymlinkReleaseTaskIsRemoved(): void
    {
        $workflow = $this->createMock(Workflow::class);
        $application = $this->createMock(Application::class);

        $workflow->expects(self::once())
            ->method('removeTask')
            ->with(SymlinkReleaseTask::class);

        $this->callRegisterer($workflow, $application);
    }
}
