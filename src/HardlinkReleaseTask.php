<?php

declare(strict_types=1);

namespace De\SWebhosting\TYPO3Surf;

use Symfony\Component\OptionsResolver\OptionsResolver;
use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Domain\Model\Task;
use TYPO3\Surf\Domain\Service\ShellCommandServiceAwareInterface;
use TYPO3\Surf\Domain\Service\ShellCommandServiceAwareTrait;

/**
 * It takes the following options:
 *
 * * sudo - If set to true, the "cp" and "rm" commands are prefixed with "sudo". This is necessary if the release
 *   directory contains files or directories that are not owned by the deployment user. Defaults to false.
 *
 * Example:
 *  // Set the option only for this task
 *  $workflow->setTaskOptions(HardlinkReleaseTask::class, ['sudo' => true]);
 *
 *  // Set the option globally, e.g. for a specific node
 *  $node->setOption(HardlinkReleaseTask::class . '[sudo]', true);
 */
class HardlinkReleaseTask extends Task implements ShellCommandServiceAwareInterface
{
    use ShellCommandServiceAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param array<string,mixed> $options
     */
    public function execute(Node $node, Application $application, Deployment $deployment, array $options = []): void
    {
        $options = $this->configureOptions($options);
        $sudoPrefix = (bool)$options['sudo'] ? 'sudo ' : '';

        $escapedReleasesDir = escapeshellarg($node->getReleasesPath());
        $escapedRelativeReleaseDir = escapeshellarg('./' . $deployment->getReleaseIdentifier());

        $commands = [
            'cd ' . $escapedReleasesDir,
            $sudoPrefix . 'rm -rf ./next',
            $sudoPrefix . 'cp -al ' . $escapedRelativeReleaseDir . ' ./next',
            $sudoPrefix . 'rm -rf ./previous',
            'if [ -e ./current ]; then mv ./current ./previous; fi',
            'mv ./next ./current',
        ];

        $this->shell->setLogger($this->logger);
        $this->shell->executeOrSimulate($commands, $node, $deployment);

        $logMessage = 'Node "' . $node->getName() . '" ' . ($deployment->isDryRun() ? 'would be' : 'is') . ' live!';
        $this->logger->notice('<success>' . $logMessage . '</success>');
    }

    /**
     * @param array<string,mixed> $options
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function rollback(Node $node, Application $application, Deployment $deployment, array $options = []): void
    {
        $options = $this->configureOptions($options);
        $sudoPrefix = (bool)$options['sudo'] ? 'sudo ' : '';

        $escapedReleasesDir = escapeshellarg($node->getReleasesPath());

        $commands = [
            'cd ' . $escapedReleasesDir,
            $sudoPrefix . 'rm -rf ./current',
            'if [ -e ./previous ]; then mv ./previous ./current; fi',
        ];

        $this->shell->setLogger($this->logger);
        $this->shell->execute($commands, $node, $deployment, true);
    }

    /**
     * @param array<string,mixed> $options
     */
    public function simulate(Node $node, Application $application, Deployment $deployment, array $options = []): void
    {
        $this->execute($node, $application, $deployment, $options);
    }

    protected function resolveOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('sudo');
        $resolver->setAllowedTypes('sudo', 'bool');
        $resolver->setDefault('sudo', false);
    }
}
