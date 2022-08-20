# TYPO3 Surf hardlink release

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
