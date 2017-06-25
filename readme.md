Backupz [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/php-backupz/server/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/php-backupz/server/?branch=master)
---

*Note: Still in development*

An highly configurable backup system written in PHP. Using the [flysystem][flysystem] for filesystem abstraction you can configure backups to your requirements.

### Supported filesystems
- [Local][flysystem-local]
- [SFTP][flysystem-sftp]

[flysystem]: http://flysystem.thephpleague.com
[flysystem-local]: https://flysystem.thephpleague.com/adapter/local/
[flysystem-sftp]: https://flysystem.thephpleague.com/adapter/sftp/