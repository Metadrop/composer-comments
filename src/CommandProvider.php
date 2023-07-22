<?php

namespace Metadrop\Composer\Comments;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

/**
 * List of all Composer commands provided by this package.
 *
 * @internal
 */
class CommandProvider implements CommandProviderCapability {

  /**
   * {@inheritdoc}
   */
  public function getCommands() {
    return array(new CommentsCommand);
  }
}
