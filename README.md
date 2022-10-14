# TYPO3 Surf hardlink release

[![Run tests and linting](https://github.com/astehlik/typo3-surf-hardlink-release/actions/workflows/test.yml/badge.svg)](https://github.com/astehlik/typo3-surf-hardlink-release/actions/workflows/test.yml)

This repo provides a task for [TYPO3 Surf](https://github.com/TYPO3/Surf)
to release your application with hardlinks instead of softlinks.

## How it works

When the application is released, the release directory is copied to next using hardlinks:

```bash
cp -al 20201228192426 next
```

To switch the release to the new version, the directories current and next are moved:

```bash
mv ./current ./previous
mv ./next ./current
```

## How to use

In the `registerTasks()` method of your application you can use the `HardlinkReleaseRegisterer` to
replace the symlink release with a hardlink release:

```php
(new \De\SWebhosting\TYPO3Surf\HardlinkReleaseRegisterer())
    ->replaceSymlinkWithHardlinkRelease($workflow, $this);
```
