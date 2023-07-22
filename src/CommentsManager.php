<?php

namespace Metadrop\Composer\Comments;

use Composer\Composer;


/**
 * Class to abstract the access to the comments.
 *
 * Comments are defined in the composer.json using the extra property like
 * this:
 *
 * ```json
 *   "extra": {
 *     "package-comments": {
 *       "vendor/pacakge1": "This is a comment for the package 1",
 *       "vendor/pacakge2": "This is a comment for the package 2",
 *     }
 *     [...]
 *   }
 *```
 */
class CommentsManager {

  /**
   * @var array Comments. Index is the pcakge name, value is the comment.
   */
  protected $comments = [];

  /**
   * @var Composer Composer object to get the comments form the composer.json
   * file.
   */
  protected Composer $composer;

  /**
   * Class constructor.
   *
   * @param Composer $composer Composer instance that the class should use.
   */
  function __construct(Composer $composer) {
    $this->composer = $composer;
  }

  /**
   * Initialize the instance.
   *
   * This method must be called before using the instance.
   */
  function intialize(): void {
    $this->readComments();
  }

  /**
   * Read the package comments from the composer.json file.
   */
  public function readComments(): void {
    $extra = $this->composer->getPackage()->getExtra();
    $this->comments = $extra['package-comments'] ?? [];
  }

  /**
   * Fetches the comment of a given package.
   *
   * @param string $packageName Package name to get the comment from.
   *
   * @return string String with the comment or NULL if there's no comment for
   * the given package.
   */
  public function getComment(string $packageName): string {
    return $this->comments[$packageName] ?? NULL;
  }

  /**
   * Fetches all comments declared on the composer.json file.
   *
   * @return array Array of strings with the all the package comments or an
   * empty array if there's no comments delcared on the composer.json file.
   */
  public function getComments(): array {
    return $this->comments ?? [];
  }

  /**
   * Reports if there is a package comments for a given package.
   *
   * @param string $packageName The name of the package to get the comment from.
   *
   * @return bool True if there is a comments for the given package, false if
   * not.
   */
  public function hasComment(string $packageName): bool {
    return isset($this->comments[$packageName]);
  }

  /**
   * Returns the numner of defined comments in the composer.json file.
   *
   * @return int Number of comments defined comments in the composer.json file.
   */
  public function commentCount(): int {
    return count($this->comments);
  }
}
