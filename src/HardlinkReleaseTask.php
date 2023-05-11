<?php

declare(strict_types=1);

namespace De\SWebhosting\TYPO3Surf;

use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Domain\Model\Task;
use TYPO3\Surf\Domain\Service\ShellCommandServiceAwareInterface;
use TYPO3\Surf\Domain\Service\ShellCommandServiceAwareTrait;

class HardlinkReleaseTask extends Task implements ShellCommandServiceAwareInterface
{
    use ShellCommandServiceAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param string[] $options
     */
    public function execute(Node $node, Application $application, Deployment $deployment, array $options = []): void
    {
        $escapedReleasesDir = escapeshellarg($node->getReleasesPath());
        $escapedRelativeReleaseDir = escapeshellarg('./' . $deployment->getReleaseIdentifier());

        $commands = [
            'cd ' . $escapedReleasesDir,
            'rm -rf ./next',
            'cp -al ' . $escapedRelativeReleaseDir . ' ./next',
            'rm -rf ./previous',
            'if [ -e ./current ]; then mv ./current ./previous; fi',
            'mv ./next ./current',
        ];

        $this->shell->executeOrSimulate($commands, $node, $deployment);

        $logMessage = 'Node "' . $node->getName() . '" ' . ($deployment->isDryRun() ? 'would be' : 'is') . ' live!';
        $this->logger->notice('<success>' . $logMessage . '</success>');
    }

    /**
     * @param string[] $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function rollback(Node $node, Application $application, Deployment $deployment, array $options = []): void
    {
        $escapedReleasesDir = escapeshellarg($node->getReleasesPath());

        $commands = [
            'cd ' . $escapedReleasesDir,
            'rm -rf ./current',
            'if [ -e ./previous ]; then mv ./previous ./current; fi',
        ];

        $this->shell->execute($commands, $node, $deployment, true);
    }

    /**
     * @param string[] $options
     */
    public function simulate(Node $node, Application $application, Deployment $deployment, array $options = []): void
    {
        $this->execute($node, $application, $deployment, $options);
    }
}
